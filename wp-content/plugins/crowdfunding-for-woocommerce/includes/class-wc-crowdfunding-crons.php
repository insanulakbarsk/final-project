<?php
/**
 * Crowdfunding for WooCommerce - Crons Class
 *
 * @version 3.0.0
 * @since   2.3.0
 * @author  Tom Anbinder
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Alg_WC_Crowdfunding_Crons' ) ) :

class Alg_WC_Crowdfunding_Crons {

	/**
	 * Constructor.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	function __construct() {
		add_action( 'init',                          array( $this, 'schedule_the_events' ) );
		add_action( 'admin_init',                    array( $this, 'schedule_the_events' ) );
		add_action( 'alg_update_products_data_hook', array( $this, 'update_products_data' ) );
		add_filter( 'cron_schedules',                array( $this, 'cron_add_custom_intervals' ) );
	}

	/**
	 * On an early action hook, check if the hook is scheduled - if not, schedule it.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	function schedule_the_events() {
		$selected_interval = get_option( 'alg_crowdfunding_products_data_update', 'fifthteen' );
		$update_intervals = array(
			'minutely',
			'fifthteen',
			'hourly',
			'twicedaily',
			'daily',
			'weekly',
		);
		foreach ( $update_intervals as $interval ) {
			$event_hook = 'alg_update_products_data_hook';
			$event_timestamp = wp_next_scheduled( $event_hook, array( $interval ) );
			if ( $selected_interval === $interval ) {
				update_option( 'alg_crowdfunding_products_data_update_cron_time', $event_timestamp );
			}
			if ( ! $event_timestamp && $selected_interval === $interval ) {
				wp_schedule_event( time(), $selected_interval, $event_hook, array( $selected_interval ) );
			} elseif ( $event_timestamp && $selected_interval !== $interval ) {
				wp_unschedule_event( $event_timestamp, $event_hook, array( $interval ) );
			}
		}
	}

	/**
	 * On the scheduled action hook, run a function.
	 *
	 * @version 3.0.0
	 * @since   2.3.0
	 * @todo    [dev] maybe go through all ORDERS instead of PRODUCTS
	 */
	function update_products_data( $interval ) {
		if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_enabled', 'yes' ) ) {
			if ( 'manual' != get_option( 'alg_crowdfunding_products_data_update', 'fifthteen' ) ) {
				update_option( 'alg_crowdfunding_products_data_update_cron_run_time', time() );
				$do_log     = ( 'yes' === get_option( 'alg_crowdfunding_log_enabled', 'no' ) && function_exists( 'wc_get_logger' ) && ( $log = wc_get_logger() ) );
				// Adding initial `products_data_updated_time` for new products
				$offset     = 0;
				$block_size = 256;
				while( true ) {
					$args = array(
						'post_type'      => 'product',
						'post_status'    => 'any',
						'posts_per_page' => $block_size,
						'offset'         => $offset,
						'fields'         => 'ids',
						'meta_query'     => array(
							'relation' => 'AND',
							array(
								'key'     => '_' . 'alg_crowdfunding_enabled',
								'value'   => 'yes',
							),
							array(
								'key'     => '_' . 'alg_crowdfunding_' . 'products_data_updated_time',
								'compare' => 'NOT EXISTS',
							),
						)
					);
					$loop = new WP_Query( $args );
					if ( ! $loop->have_posts() ) {
						break;
					}
					foreach ( $loop->posts as $product_id ) {
						update_post_meta( $product_id, '_' . 'alg_crowdfunding_' . 'products_data_updated_time', 0 );
					}
					$offset += $block_size;
				}
				// Updating data
				$offset     = 0;
				$block_size = 256;
				while( true ) {
					$args = array(
						'post_type'      => 'product',
						'post_status'    => 'any',
						'posts_per_page' => $block_size,
						'offset'         => $offset,
						'orderby'        => 'meta_value_num',
						'meta_key'       => '_' . 'alg_crowdfunding_' . 'products_data_updated_time',
						'order'          => 'ASC',
						'fields'         => 'ids',
						'meta_query'     => array(
							array(
								'key'     => '_' . 'alg_crowdfunding_enabled',
								'value'   => 'yes',
							),
						),
					);
					$loop = new WP_Query( $args );
					if ( ! $loop->have_posts() ) {
						break;
					}
					foreach ( $loop->posts as $product_id ) {
						alg_wc_crdfnd_calculate_and_update_product_orders_data( $product_id );
						if ( $do_log ) {
							$log->log(
								'info',
								sprintf( __( 'Data updated for product ID %s.', 'crowdfunding-for-woocommerce' ), $product_id ),
								array( 'source' => 'crowdfunding_for_woocommerce' )
							);
						}
					}
					$offset += $block_size;
				}
			}
		}
	}

	/**
	 * cron_add_custom_intervals.
	 *
	 * @version 3.0.0
	 * @since   2.3.0
	 */
	function cron_add_custom_intervals( $schedules ) {
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display' => __( 'Once Weekly', 'crowdfunding-for-woocommerce' )
		);
		$schedules['fifthteen'] = array(
			'interval' => 900,
			'display' => __( 'Every Fifteen Minutes', 'crowdfunding-for-woocommerce' )
		);
		$schedules['minutely'] = array(
			'interval' => 60,
			'display' => __( 'Once a Minute', 'crowdfunding-for-woocommerce' )
		);
		return $schedules;
	}
}

endif;

return new Alg_WC_Crowdfunding_Crons();
