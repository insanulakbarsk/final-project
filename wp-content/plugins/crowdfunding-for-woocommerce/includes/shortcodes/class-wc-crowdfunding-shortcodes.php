<?php
/**
 * Crowdfunding for WooCommerce - Shortcodes
 *
 * @version 2.7.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_Shortcodes' ) ) :

class Alg_WC_Crowdfunding_Shortcodes {

	/**
	 * Constructor.
	 *
	 * @version 2.3.6
	 */
	function __construct() {
		return true;
	}

	/**
	 * output_shortcode.
	 *
	 * @version 2.7.0
	 * @since   1.0.0
	 */
	function output_shortcode( $value, $atts ) {
		if ( '' != $value || ( isset( $atts['show_if_zero'] ) && 'yes' === $atts['show_if_zero'] ) ) {
			if ( empty( $atts ) ) {
				$atts = array();
			}
			if ( ! isset( $atts['before'] ) ) {
				$atts['before'] = '';
			}
			if ( ! isset( $atts['after'] ) ) {
				$atts['after'] = '';
			}
			if ( isset( $atts['type'] ) ) {
				switch ( $atts['type'] ) {
					case 'price':
						$value = apply_filters( 'alg_crowdfunding_output_shortcode_price', wc_price( $value ), $value );
						break;
					case 'percent':
						if ( 0 != $atts['total_value'] ) {
							if ( ! isset( $atts['round_precision'] ) ) {
								$atts['round_precision'] = 0;
							}
							$value = round( $value / $atts['total_value'] * 100, $atts['round_precision'] );
						} else {
							$value = 100;
						}
						break;
				}
			}
			return $atts['before'] . $value . $atts['after'];
		}
		return '';
	}

}

endif;
