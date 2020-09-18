<?php
/**
 * Crowdfunding for WooCommerce - Shortcodes General
 *
 * @version 3.0.0
 * @since   2.3.6
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_Shortcodes_General' ) ) :

class Alg_WC_Crowdfunding_Shortcodes_General extends Alg_WC_Crowdfunding_Shortcodes {

	/**
	 * Constructor.
	 *
	 * @version 3.0.0
	 * @since   2.3.6
	 */
	function __construct() {
		// General
		add_shortcode( 'crowdfunding_totals',                                      array( $this, 'alg_crowdfunding_totals' ) );
		// Products
		add_shortcode( 'product_crowdfunding_total_backers',                       array( $this, 'alg_product_crowdfunding_total_backers' ) );
		add_shortcode( 'product_crowdfunding_total_sum',                           array( $this, 'alg_product_crowdfunding_total_sum' ) );
		add_shortcode( 'product_crowdfunding_total_items',                         array( $this, 'alg_product_crowdfunding_total_items' ) );
		add_shortcode( 'product_crowdfunding_goal',                                array( $this, 'alg_product_crowdfunding_goal' ) );
		add_shortcode( 'product_crowdfunding_goal_backers',                        array( $this, 'alg_product_crowdfunding_goal_backers' ) );
		add_shortcode( 'product_crowdfunding_goal_items',                          array( $this, 'alg_product_crowdfunding_goal_items' ) );
		add_shortcode( 'product_crowdfunding_goal_remaining',                      array( $this, 'alg_product_crowdfunding_goal_remaining' ) );
		add_shortcode( 'product_crowdfunding_goal_backers_remaining',              array( $this, 'alg_product_crowdfunding_goal_backers_remaining' ) );
		add_shortcode( 'product_crowdfunding_goal_items_remaining',                array( $this, 'alg_product_crowdfunding_goal_items_remaining' ) );
		add_shortcode( 'product_crowdfunding_add_to_cart_form',                    array( $this, 'alg_product_crowdfunding_add_to_cart_form' ) );
		add_shortcode( 'product_crowdfunding_list_backers',                        array( $this, 'alg_product_crowdfunding_list_backers' ) );
		// Deprecated
		add_shortcode( 'product_total_orders',                                     array( $this, 'alg_product_total_orders' ) );
		add_shortcode( 'product_total_orders_sum',                                 array( $this, 'alg_product_total_orders_sum' ) );
		// Language
		add_shortcode( 'crowdfunding_translate',                                   array( $this, 'language_shortcode' ) );
	}

	/**
	 * language_shortcode.
	 *
	 * @version 3.0.0
	 * @since   3.0.0
	 */
	function language_shortcode( $atts, $content = '' ) {
		// E.g.: `[crowdfunding_translate lang="DE" lang_text="Unterstütze dieses Projekt" not_lang_text="Back this project"]`
		if ( isset( $atts['lang_text'] ) && isset( $atts['not_lang_text'] ) && ! empty( $atts['lang'] ) ) {
			return ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ?
				$atts['not_lang_text'] : $atts['lang_text'];
		}
		// E.g.: `[crowdfunding_translate lang="DE"]Unterstütze dieses Projekt[/crowdfunding_translate][crowdfunding_translate lang="ES"]Patrocina este proyecto[/crowdfunding_translate][crowdfunding_translate not_lang="DE,ES"]Back this project[/crowdfunding_translate]`
		return (
			( ! empty( $atts['lang'] )     && ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ) ||
			( ! empty( $atts['not_lang'] ) &&     defined( 'ICL_LANGUAGE_CODE' ) &&   in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['not_lang'] ) ) ) ) )
		) ? '' : $content;
	}

	/**
	 * alg_crowdfunding_totals.
	 *
	 * @version 3.0.0
	 * @since   2.9.0
	 * @todo    [dev] `backers`
	 * @todo    [dev] `backers_list`
	 * @todo    [dev] (maybe) rename `orders_sum` to `sum`, `total_items` to `items`, `total_orders` to `orders`
	 */
	function alg_crowdfunding_totals( $atts ) {
		// Atts
		if ( empty( $atts ) ) {
			$atts = array();
		}
		if ( ! isset( $atts['return_value'] ) || ! in_array( $atts['return_value'], array( 'orders_sum', 'total_items', 'total_orders', 'total_campaigns' ) ) ) {
			$atts['return_value'] = 'orders_sum';
		}
		if ( 'orders_sum' === $atts['return_value'] ) {
			$atts['type'] = 'price';
		}
		// Calculate
		$args  = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'meta_key'       => '_alg_crowdfunding_enabled',
			'meta_value'     => 'yes',
			'fields'         => 'ids',
		);
		if ( isset( $atts['product_ids'] ) ) {
			$args['post__in'] = array_map( 'trim', explode( ',', $atts['product_ids'] ) );
		}
		$loop  = new WP_Query( $args );
		$total = 0;
		if ( $loop->have_posts() ) {
			if ( 'total_campaigns' === $atts['return_value'] ) {
				$total = $loop->found_posts;
			} else {
				foreach ( $loop->posts as $product_id ) {
					$total += alg_wc_crdfnd_get_product_orders_data( $atts['return_value'], array( 'product_id' => $product_id ) );
				}
			}
		}
		// Output
		return $this->output_shortcode( $total, $atts );
	}

	/**
	 * alg_product_crowdfunding_list_backers.
	 *
	 * @version 3.0.0
	 * @since   2.8.0
	 * @todo    [dev] add this to a) admin "Crowdfunding Report"; b) admin product edit "Crowdfunding Orders Data" meta box;
	 */
	function alg_product_crowdfunding_list_backers( $atts ) {
		if ( isset( $atts['starting_offset'] ) ) {
			unset( $atts['starting_offset'] );
		}
		$output  = '';
		$backers = alg_wc_crdfnd_get_product_orders_data( 'backers', $atts );
		if ( ! empty( $backers ) ) {
			$backer_template = ( isset( $atts['backer_template'] ) ? $atts['backer_template'] : '%nr%. %first_name% %last_name% - %sum%' );
			$glue            = ( isset( $atts['glue'] )            ? $atts['glue']            : '<br>' );
			$date_format     = ( isset( $atts['date_format'] )     ? $atts['date_format']     : get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );
			$output          = array();
			$i               = 1;
			foreach ( $backers as $backer ) {
				$replaced_values = array(
					'%nr%' => $i,
				);
				foreach ( $backer as $key => $value ) {
					if ( 'sum' === $key ) {
						$replaced_values[ '%' . $key . '%' ] = wc_price( $value, array( 'currency' => $backer['order_currency'] ) );
					} elseif ( 'order_date' === $key ) {
						$replaced_values[ '%' . $key . '%' ] = date_i18n( $date_format, $value );
					} else {
						$replaced_values[ '%' . $key . '%' ] = $value;
					}
				}
				$output[] = str_replace( array_keys( $replaced_values ), $replaced_values, $backer_template );
				$i++;
			}
			$output = implode( $glue, $output );
		}
		return $this->output_shortcode( $output, $atts );
	}

	/**
	 * alg_product_crowdfunding_add_to_cart_form.
	 *
	 * @version 2.1.0
	 * @since   1.2.0
	 * @todo    [dev] maybe `output_shortcode()`
	 * @todo    [dev] `remove_filter \ add_filter( 'wc_get_template', array( $this, 'change_variable_add_to_cart_template' ), PHP_INT_MAX, 5 );`
	 */
	function alg_product_crowdfunding_add_to_cart_form( $atts ) {
		$the_product = isset( $atts['product_id'] )            ? wc_get_product( $atts['product_id'] ) : wc_get_product();
		$return      = ( $the_product->is_type( 'variable' ) ) ? woocommerce_variable_add_to_cart()    : woocommerce_simple_add_to_cart();
		return $return;
	}

	/**
	 * alg_product_crowdfunding_total_items.
	 *
	 * @version 2.7.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_total_items( $atts ) {
		if ( empty( $atts ) ) {
			$atts = array();
		}
		if ( isset( $atts['type'] ) && 'percent' === $atts['type'] ) {
			$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
			if ( ! $product_id ) {
				return '';
			}
			$atts['total_value'] = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_items', true );
		}
		return $this->output_shortcode( alg_wc_crdfnd_get_product_orders_data( 'total_items', $atts ), $atts );
	}

	/**
	 * alg_product_crowdfunding_total_backers.
	 *
	 * @version 3.0.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_total_backers( $atts ) {
		if ( empty( $atts ) ) {
			$atts = array();
		}
		if ( isset( $atts['type'] ) && 'percent' === $atts['type'] ) {
			$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
			if ( ! $product_id ) {
				return '';
			}
			$atts['total_value'] = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_backers', true );
		}
		return $this->output_shortcode( alg_wc_crdfnd_get_product_orders_data( 'total_orders', $atts ), $atts );
	}

	/**
	 * alg_product_total_orders.
	 *
	 * @version     2.2.0
	 * @since       1.0.0
	 * @deprecated
	 */
	function alg_product_total_orders( $atts ) {
		return $this->alg_product_crowdfunding_total_backers( $atts );
	}

	/**
	 * alg_product_crowdfunding_total_sum.
	 *
	 * @version 2.7.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_total_sum( $atts ) {
		if ( empty( $atts ) ) {
			$atts = array();
		}
		if ( isset( $atts['type'] ) && 'percent' === $atts['type'] ) {
			$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
			if ( ! $product_id ) {
				return '';
			}
			$atts['total_value'] = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_sum', true );
		} elseif ( ! isset( $atts['type'] ) ) {
			$atts['type'] = 'price';
		}
		return $this->output_shortcode( alg_wc_crdfnd_get_product_orders_data( 'orders_sum', $atts ), $atts );
	}

	/**
	 * alg_product_total_orders_sum.
	 *
	 * @version     2.2.0
	 * @since       1.0.0
	 * @deprecated
	 */
	function alg_product_total_orders_sum( $atts ) {
		return $this->alg_product_crowdfunding_total_sum( $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_items.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_items( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_items', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_backers.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_backers( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_backers', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal.
	 *
	 * @version 2.7.0
	 * @since   1.0.0
	 */
	function alg_product_crowdfunding_goal( $atts ) {
		if ( empty( $atts ) ) {
			$atts = array();
		}
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}
		$atts['type'] = 'price';
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_sum', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_items_remaining.
	 *
	 * @version 3.0.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_items_remaining( $atts ) {
		if ( empty( $atts ) ) {
			$atts = array();
		}
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}
		if ( isset( $atts['type'] ) && 'percent' === $atts['type'] ) {
			$atts['total_value'] = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_items', true );
		}
		return $this->output_shortcode( intval( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_items', true ) ) - alg_wc_crdfnd_get_product_orders_data( 'total_items', $atts ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_backers_remaining.
	 *
	 * @version 3.0.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_backers_remaining( $atts ) {
		if ( empty( $atts ) ) {
			$atts = array();
		}
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}
		if ( isset( $atts['type'] ) && 'percent' === $atts['type'] ) {
			$atts['total_value'] = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_backers', true );
		}
		return $this->output_shortcode( intval( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_backers', true ) ) - alg_wc_crdfnd_get_product_orders_data( 'total_orders', $atts ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_remaining.
	 *
	 * @version 3.0.0
	 * @since   1.0.0
	 */
	function alg_product_crowdfunding_goal_remaining( $atts ) {
		if ( empty( $atts ) ) {
			$atts = array();
		}
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}
		if ( isset( $atts['type'] ) && 'percent' === $atts['type'] ) {
			$atts['total_value'] = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_sum', true );
		} elseif ( ! isset( $atts['type'] ) ) {
			$atts['type'] = 'price';
		}
		return $this->output_shortcode( floatval( get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_sum', true ) ) - alg_wc_crdfnd_get_product_orders_data( 'orders_sum', $atts ), $atts );
	}

}

endif;

return new Alg_WC_Crowdfunding_Shortcodes_General();
