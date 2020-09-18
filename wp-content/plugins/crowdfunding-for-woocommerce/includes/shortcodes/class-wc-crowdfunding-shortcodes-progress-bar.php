<?php
/**
 * Crowdfunding for WooCommerce - Progress Bar Shortcodes
 *
 * @version 3.0.0
 * @since   2.3.6
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_Shortcodes_Progress_Bar' ) ) :

class Alg_WC_Crowdfunding_Shortcodes_Progress_Bar extends Alg_WC_Crowdfunding_Shortcodes {

	/**
	 * Constructor.
	 *
	 * @version 2.3.6
	 * @since   2.3.6
	 */
	function __construct() {
		add_shortcode( 'product_crowdfunding_goal_remaining_progress_bar',         array( $this, 'alg_product_crowdfunding_goal_remaining_progress_bar' ) );
		add_shortcode( 'product_crowdfunding_goal_backers_remaining_progress_bar', array( $this, 'alg_product_crowdfunding_goal_backers_remaining_progress_bar' ) );
		add_shortcode( 'product_crowdfunding_goal_items_remaining_progress_bar',   array( $this, 'alg_product_crowdfunding_goal_items_remaining_progress_bar' ) );
		add_shortcode( 'product_crowdfunding_time_remaining_progress_bar',         array( $this, 'alg_product_crowdfunding_time_remaining_progress_bar' ) );
		// Deprecated
		add_shortcode( 'product_crowdfunding_goal_progress_bar',                   array( $this, 'alg_product_crowdfunding_goal_progress_bar' ) );
		add_shortcode( 'product_crowdfunding_time_progress_bar',                   array( $this, 'alg_product_crowdfunding_time_progress_bar' ) );
	}

	/**
	 * get_progress_bar.
	 *
	 * @version 2.7.0
	 * @since   2.3.0
	 */
	function get_progress_bar( $atts, $value, $max_value ) {
		if ( empty( $atts ) ) {
			$atts = array();
		}
		if ( ! isset( $atts['type'] ) ) {
			$atts['type'] = 'standard';
		}
		if ( $value < 0 ) {
			$value = 0;
		}
		if ( $max_value < 0 ) {
			$max_value = 0;
		}
		switch ( $atts['type'] ) {
			case 'line':
			case 'circle':
				return $this->get_js_progress_bar( $atts, $value, $max_value );
			default: // 'standard'
				return '<progress value="' . $value . '" max="' . $max_value . '"></progress>';
		}
	}

	/**
	 * get_js_progress_bar.
	 *
	 * @version 2.5.0
	 * @since   2.5.0
	 * @todo    [dev] `width` (maybe `100%`)
	 */
	function get_js_progress_bar( $atts, $value, $max_value ) {
		// Color
		if ( ! isset( $atts['color'] ) ) {
			$atts['color'] = '#2bde73';
		}
		// Text properties
		if ( ! isset( $atts['text_color'] ) ) {
			$atts['text_color'] = '#999';
		}
		if ( ! isset( $atts['text_position'] ) ) {
			$atts['text_position'] = 'right';
		}
		if ( ! isset( $atts['text_position_variable_max_left'] ) ) {
			$atts['text_position_variable_max_left'] = '75';
		}
		if ( ! isset( $atts['text_top'] ) ) {
			$atts['text_top'] = '30px';
		}
		// Style
		if ( ! isset( $atts['width'] ) ) {
			$atts['width'] = '200px';
		}
		if ( ! isset( $atts['height'] ) ) {
			$atts['height'] = ( 'line' === $atts['type'] ) ? '8px' : '200px';
		}
		if ( ! isset( $atts['style'] ) ) {
			$atts['style'] = '';
		}
		$atts['style'] = 'width:' . $atts['width'] . ';height:' . $atts['height'] . ';position:relative;' . $atts['style'];
		unset( $atts['width'] );
		unset( $atts['height'] );
		// Value
		$atts['value'] = ( 0 != $max_value ? $value / $max_value : 0 );
		// Forming progress bar attributes
		$bar_attributes = '';
		foreach ( $atts as $key => $value ) {
			$bar_attributes .= ' ' . $key . '="' . $value . '"';
		}
		// Forming the progress bar
		return '<div class="alg-progress-bar"' . $bar_attributes . '>' . '</div>';
	}

	/**
	 * alg_product_crowdfunding_time_remaining_progress_bar.
	 *
	 * @version 2.3.2
	 * @since   2.2.1
	 */
	function alg_product_crowdfunding_time_remaining_progress_bar( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}

		$deadline_datetime  = trim( get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline', true )  . ' ' . get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline_time', true ), ' ' );
		$startdate_datetime = trim( get_post_meta( $product_id, '_' . 'alg_crowdfunding_startdate', true ) . ' ' . get_post_meta( $product_id, '_' . 'alg_crowdfunding_starttime', true ), ' ' );

		$seconds_remaining = strtotime( $deadline_datetime ) - ( (int) current_time( 'timestamp' ) );
		$seconds_total     = strtotime( $deadline_datetime ) - strtotime( $startdate_datetime );

		return $this->output_shortcode( $this->get_progress_bar( $atts, $seconds_remaining, $seconds_total ), $atts );
	}

	/**
	 * alg_product_crowdfunding_time_progress_bar.
	 *
	 * @version     2.2.1
	 * @since       1.2.0
	 * @deprecated
	 */
	function alg_product_crowdfunding_time_progress_bar( $atts ) {
		return $this->alg_product_crowdfunding_time_remaining_progress_bar( $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_items_remaining_progress_bar.
	 *
	 * @version 3.0.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_items_remaining_progress_bar( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}
		$current_value = alg_wc_crdfnd_get_product_orders_data( 'total_items', $atts );
		$max_value     = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_items', true );
		return $this->output_shortcode( $this->get_progress_bar( $atts, $current_value, $max_value ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_backers_remaining_progress_bar.
	 *
	 * @version 3.0.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_backers_remaining_progress_bar( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}
		$current_value = alg_wc_crdfnd_get_product_orders_data( 'total_orders', $atts );
		$max_value     = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_backers', true );
		return $this->output_shortcode( $this->get_progress_bar( $atts, $current_value, $max_value ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_remaining_progress_bar.
	 *
	 * @version 3.0.0
	 * @since   2.2.0
	 */
	function alg_product_crowdfunding_goal_remaining_progress_bar( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}
		$current_value = alg_wc_crdfnd_get_product_orders_data( 'orders_sum', $atts );
		$max_value     = get_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_sum', true );
		return $this->output_shortcode( $this->get_progress_bar( $atts, $current_value, $max_value ), $atts );
	}

	/**
	 * alg_product_crowdfunding_goal_progress_bar.
	 *
	 * @version     2.2.0
	 * @since       1.2.0
	 * @deprecated
	 */
	function alg_product_crowdfunding_goal_progress_bar( $atts ) {
		return $this->alg_product_crowdfunding_goal_remaining_progress_bar( $atts );
	}

}

endif;

return new Alg_WC_Crowdfunding_Shortcodes_Progress_Bar();
