<?php
/**
 * Crowdfunding for WooCommerce - General Section Settings
 *
 * @version 3.1.6
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 * @author  WP Wham
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_Settings_General' ) ) :

class Alg_WC_Crowdfunding_Settings_General extends Alg_WC_Crowdfunding_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 3.0.0
	 * @since   1.0.0
	 */
	function __construct() {

		$this->id   = '';
		$this->desc = __( 'General', 'crowdfunding-for-woocommerce' );

		parent::__construct();

		add_filter( 'admin_head',   array( $this, 'add_update_data_now_confirmation_js' ) );
		add_action( 'admin_init',   array( $this, 'manual_products_data_update' ) );
	}

	/**
	 * manual_products_data_update.
	 *
	 * @version 2.5.0
	 * @since   2.5.0
	 */
	function manual_products_data_update() {
		if ( isset( $_GET['alg_wc_crowdfunding_products_data_update'] ) ) {
			do_action( 'alg_update_products_data_hook' );
			wp_safe_redirect( remove_query_arg( 'alg_wc_crowdfunding_products_data_update' ) );
			exit;
		}
	}

	/**
	 * add_update_data_now_confirmation_js.
	 *
	 * @version 2.5.0
	 * @since   2.5.0
	 * @todo    [dev] check JS coding standards
	 */
	function add_update_data_now_confirmation_js() {
		if (
			isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] &&
			isset( $_GET['tab'] ) && 'alg_crowdfunding' === $_GET['tab']  &&
			( ! isset( $_GET['section'] ) || '' === $_GET['section'] )
		) {
			echo '<script type="text/javascript">
				window.onload = function() {
					function alg_crowdfunding_data_update_now_confirmation() {
						return confirm("' . __( 'This may take a while... Are you sure?', 'crowdfunding-for-woocommerce' ) . '");
					}
					document.getElementById("alg_crowdfunding_products_data_update_now").onclick = alg_crowdfunding_data_update_now_confirmation;
				}
			</script>';
		}
	}

	/**
	 * get_settings.
	 *
	 * @version 3.1.6
	 * @since   1.0.0
	 */
	public static function get_settings() {

		$desc = '';
		if ( 'manual' != get_option( 'alg_crowdfunding_products_data_update', 'fifthteen' ) ) {
			if ( '' != get_option( 'alg_crowdfunding_products_data_update_cron_time', '' ) ) {
				$scheduled_time_diff = get_option( 'alg_crowdfunding_products_data_update_cron_time', '' ) - time();
				if ( $scheduled_time_diff > 60 ) {
					$desc = sprintf( __( '%s till next update.', 'crowdfunding-for-woocommerce' ),
						'<strong>' . human_time_diff( get_option( 'alg_crowdfunding_products_data_update_cron_time', '' ) ) . '</strong>' );
				} elseif ( $scheduled_time_diff > 0 ) {
					$desc = sprintf( __( '%s seconds till next update.', 'crowdfunding-for-woocommerce' ),
						'<strong>' . $scheduled_time_diff . '</strong>' );
				}
			}
			if ( '' != get_option( 'alg_crowdfunding_products_data_update_cron_run_time', '' ) ) {
				if ( '' != $desc ) {
					$desc .= ' ';
				}
				$desc .= sprintf( __( 'Previous update triggered at %s.', 'crowdfunding-for-woocommerce' ),
					'<strong>' . date( 'Y-m-d H:i:s', get_option( 'alg_crowdfunding_products_data_update_cron_run_time', '' ) ) . '</strong>' );
			}
		}

		$order_statuses = array(
			'wc-pending'    => _x( 'Pending Payment', 'Order status', 'woocommerce' ),
			'wc-processing' => _x( 'Processing', 'Order status', 'woocommerce' ),
			'wc-on-hold'    => _x( 'On Hold', 'Order status', 'woocommerce' ),
			'wc-completed'  => _x( 'Completed', 'Order status', 'woocommerce' ),
			'wc-cancelled'  => _x( 'Cancelled', 'Order status', 'woocommerce' ),
			'wc-refunded'   => _x( 'Refunded', 'Order status', 'woocommerce' ),
			'wc-failed'     => _x( 'Failed', 'Order status', 'woocommerce' ),
		);
		$order_statuses = apply_filters( 'wc_order_statuses', $order_statuses );

		$settings = array(
			array(
				'title'     => __( 'Crowdfunding Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_woocommerce_crowdfunding_options',
			),
			array(
				'title'     => __( 'WooCommerce Crowdfunding', 'crowdfunding-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Enable plugin', 'crowdfunding-for-woocommerce' ) . '</strong>',
				'desc_tip'  => '<a target="_blank" href="https://wpwham.com/products/crowdfunding-for-woocommerce/">' .
					__( 'Crowdfunding for WooCommerce plugin', 'crowdfunding-for-woocommerce' ) . '</a>' .
						( '' != get_option( 'alg_woocommerce_crowdfunding_version', '' ) ? ' v' . get_option( 'alg_woocommerce_crowdfunding_version', '' ) : '' ),
				'id'        => 'alg_woocommerce_crowdfunding_enabled',
				'default'   => 'yes',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_options',
			),

			array(
				'title'     => __( 'Status Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_woocommerce_crowdfunding_status_options',
			),
			array(
				'title'     => __( 'Order Statuses to Include in Calculations', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_order_statuses',
				'default'   => array( 'wc-completed' ),
				'type'      => 'multiselect',
				'options'   => $order_statuses,
				'class'     => 'chosen_select',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_status_options',
			),

			array(
				'title'     => __( 'Crowdfunding Buttons Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_woocommerce_crowdfunding_buttons_options',
			),
			array(
				'title'     => __( 'Default Button Label on Single Product Page', 'crowdfunding-for-woocommerce' ),
				'desc_tip'  => __( 'You can change this in product edit on per product basis', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_button_single',
				'default'   => __( 'Back This Project', 'crowdfunding-for-woocommerce' ),
				'type'      => 'textarea',
				'css'       => 'width:100%;',
			),
			array(
				'title'     => __( 'Default Button Label on Archive Pages', 'crowdfunding-for-woocommerce' ),
				'desc_tip'  => __( 'You can change this in product edit on per product basis', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_button_archives',
				'default'   => __( 'Read More', 'crowdfunding-for-woocommerce' ),
				'type'      => 'textarea',
				'css'       => 'width:100%;',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_buttons_options',
			),

			array(
				'title'     => __( 'Crowdfunding Messages Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_woocommerce_crowdfunding_messages_options',
			),
			array(
				'title'     => __( 'Message on Product Not Yet Started', 'crowdfunding-for-woocommerce' ),
				'desc'      => sprintf( __( 'You can use shortcodes here. For example: %s.', 'crowdfunding-for-woocommerce' ),
					'<code>' . '[product_crowdfunding_time_to_start]' . '</code>' ),
				'id'        => 'alg_woocommerce_crowdfunding_message_not_started',
				'default'   => __( '<strong>Not yet started!</strong>', 'crowdfunding-for-woocommerce' ),
				'type'      => 'textarea',
				'css'       => 'width:100%;height:100px;',
				'class'     => 'widefat',
			),
			array(
				'title'     => __( 'Message on Product Ended', 'crowdfunding-for-woocommerce' ),
				'desc'      => sprintf( __( 'You can use shortcodes here. For example: %s.', 'crowdfunding-for-woocommerce' ),
					'<code>' . '[product_crowdfunding_time_remaining]' . '</code>' ),
				'id'        => 'alg_woocommerce_crowdfunding_message_ended',
				'default'   => __( '<strong>Ended!</strong>', 'crowdfunding-for-woocommerce' ),
				'type'      => 'textarea',
				'css'       => 'width:100%;height:100px;',
				'class'     => 'widefat',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_messages_options',
			),

			array(
				'title'     => __( 'Variable Add to Cart Form Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_woocommerce_crowdfunding_variable_add_to_cart_options',
			),
			array(
				'title'     => __( 'Radio Buttons for Variable Products', 'crowdfunding-for-woocommerce' ),
				'desc'      => __( 'Enable', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_variable_add_to_cart_radio_enabled',
				'default'   => 'yes',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_variable_add_to_cart_options',
			),

			array(
				'title'     => __( 'Ending Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_woocommerce_crowdfunding_ending_options',
			),
			array(
				'title'     => __( 'End On Time Ended', 'crowdfunding-for-woocommerce' ),
				'desc'      => __( 'Enable', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_crowdfunding_end_on_time',
				'default'   => 'yes',
				'type'      => 'checkbox',
			),
			array(
				'title'     => __( 'End On Goal Reached', 'crowdfunding-for-woocommerce' ),
				'desc'      => __( 'Enable', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_crowdfunding_end_on_goal_reached',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'title'     => sprintf( __( 'Admin Email: %s', 'crowdfunding-for-woocommerce' ), __( 'Campaign Ended', 'crowdfunding-for-woocommerce' ) ),
				'desc'      => __( 'Send', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_wc_crowdfunding_email_campaign_added',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_ending_options',
			),

			array(
				'title'     => __( 'Products Data Update Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'desc'      => $desc,
				'id'        => 'alg_woocommerce_crowdfunding_data_update_options',
			),
			array(
				'title'     => __( 'Update Rate', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_crowdfunding_products_data_update',
				'default'   => 'fifthteen',
				'type'      => 'select',
				'class'     => 'wc-enhanced-select',
				'options'   => array(
					'minutely'   => __( 'Update every minute', 'crowdfunding-for-woocommerce' ),
					'fifthteen'  => __( 'Update every fifteen minutes', 'crowdfunding-for-woocommerce' ),
					'hourly'     => __( 'Update hourly', 'crowdfunding-for-woocommerce' ),
					'twicedaily' => __( 'Update twice daily', 'crowdfunding-for-woocommerce' ),
					'daily'      => __( 'Update daily', 'crowdfunding-for-woocommerce' ),
					'weekly'     => __( 'Update weekly', 'crowdfunding-for-woocommerce' ),
					'manual'     => __( 'Realtime (not recommended)', 'crowdfunding-for-woocommerce' ),
				),
				'desc'      => '<a class="button" href="' . add_query_arg( 'alg_wc_crowdfunding_products_data_update', '1' ) . '"' .
					' id="alg_crowdfunding_products_data_update_now">' . __( 'Update data now', 'crowdfunding-for-woocommerce' ) . '</a>',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_data_update_options',
			),

			array(
				'title'     => __( 'Advanced Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_woocommerce_crowdfunding_advanced_options',
			),
			array(
				'title'     => __( 'Log', 'crowdfunding-for-woocommerce' ),
				'desc'      => __( 'Enable', 'crowdfunding-for-woocommerce' ),
				'desc_tip'  => sprintf( __( 'Log will be saved in %s.', 'crowdfunding-for-woocommerce' ),
					'<a href="' . admin_url( 'admin.php?page=wc-status&tab=logs' ) . '">' . __( 'WooCommerce > Status > Logs', 'crowdfunding-for-woocommerce' ) . '</a>' ),
				'id'        => 'alg_crowdfunding_log_enabled',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'title'     => __( 'Add "Crowdfunding Data" Column', 'crowdfunding-for-woocommerce' ),
				'desc'      => __( 'Enable', 'crowdfunding-for-woocommerce' ),
				'desc_tip'  => __( 'Adds "Crowdfunding Data" column to the admin products list.', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_wc_crowdfunding_admin_product_data_column_enabled',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'desc'      => __( 'Data', 'crowdfunding-for-woocommerce' ),
				'desc_tip'  => __( 'You can use shortcodes and/or HTML here.', 'crowdfunding-for-woocommerce' ) . ' ' .
					sprintf( __( 'Ignored if %s option above is not enabled.', 'crowdfunding-for-woocommerce' ),
						'<em>' . __( 'Add "Crowdfunding Data" column', 'crowdfunding-for-woocommerce' ) . '</em>' ),
				'id'        => 'alg_wc_crowdfunding_admin_product_data_column',
				'default'   => '[product_crowdfunding_goal_remaining_progress_bar]',
				'type'    => 'textarea',
				'css'     => 'width:100%;height:100px;',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_woocommerce_crowdfunding_advanced_options',
			),

		);

		return $settings;
	}

}

endif;

return new Alg_WC_Crowdfunding_Settings_General();
