<?php
/**
 * Crowdfunding for WooCommerce - Time Shortcodes
 *
 * @version 3.0.0
 * @since   2.3.6
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_Shortcodes_Time' ) ) :

class Alg_WC_Crowdfunding_Shortcodes_Time extends Alg_WC_Crowdfunding_Shortcodes {

	/**
	 * Constructor.
	 *
	 * @version 2.3.6
	 * @since   2.3.6
	 */
	function __construct() {
		add_shortcode( 'product_crowdfunding_startdate',                           array( $this, 'alg_product_crowdfunding_startdate' ) );
		add_shortcode( 'product_crowdfunding_starttime',                           array( $this, 'alg_product_crowdfunding_starttime' ) );
		add_shortcode( 'product_crowdfunding_startdatetime',                       array( $this, 'alg_product_crowdfunding_startdatetime' ) );
		add_shortcode( 'product_crowdfunding_deadline',                            array( $this, 'alg_product_crowdfunding_deadline' ) );
		add_shortcode( 'product_crowdfunding_deadline_time',                       array( $this, 'alg_product_crowdfunding_deadline_time' ) );
		add_shortcode( 'product_crowdfunding_deadline_datetime',                   array( $this, 'alg_product_crowdfunding_deadline_datetime' ) );
		add_shortcode( 'product_crowdfunding_time_remaining',                      array( $this, 'alg_product_crowdfunding_time_remaining' ) );
		add_shortcode( 'product_crowdfunding_time_to_start',                       array( $this, 'alg_product_crowdfunding_time_to_start' ) );
	}

	/*
	 * get_date_diff.
	 *
	 * @version 2.3.2
	 * @since   2.3.2
	 * @see     https://gist.github.com/ozh/8169202
	 */
	function get_date_diff( $time1, $time2, $precision = 2 ) {
		// If not numeric then convert timestamps
		if ( ! is_int( $time1 ) ) {
			$time1 = strtotime( $time1 );
		}
		if ( ! is_int( $time2 ) ) {
			$time2 = strtotime( $time2 );
		}
		// If time1 > time2 then swap the 2 values
		if ( $time1 > $time2 ) {
			list( $time1, $time2 ) = array( $time2, $time1 );
		}
		// Set up intervals and diffs arrays
		$intervals = array( 'year', 'month', 'day', 'hour', 'minute', 'second' );
		$diffs     = array();
		foreach( $intervals as $interval ) {
			// Create temp time from time1 and interval
			$ttime = strtotime( '+1 ' . $interval, $time1 );
			// Set initial values
			$add = 1;
			$looped = 0;
			// Loop until temp time is smaller than time2
			while ( $time2 >= $ttime ) {
				// Create new temp time from time1 and interval
				$add++;
				$ttime = strtotime( "+" . $add . " " . $interval, $time1 );
				$looped++;
			}
			$time1 = strtotime( "+" . $looped . " " . $interval, $time1 );
			$diffs[ $interval ] = $looped;
		}
		$count = 0;
		$times = array();
		foreach( $diffs as $interval => $value ) {
			// Break if we have needed precision
			if( $count >= $precision ) {
				break;
			}
			// Add value and interval if value is bigger than 0
			if( $value > 0 ) {
				// Add value and interval to times array
				switch ( $interval ) {
					case 'year':
						$times[] = sprintf( _n( '%s year', '%s years', $value, 'crowdfunding-for-woocommerce' ), $value );
						break;
					case 'month':
						$times[] = sprintf( _n( '%s month', '%s months', $value, 'crowdfunding-for-woocommerce' ), $value );
						break;
					case 'day':
						$times[] = sprintf( _n( '%s day', '%s days', $value, 'crowdfunding-for-woocommerce' ), $value );
						break;
					case 'hour':
						$times[] = sprintf( _n( '%s hour', '%s hours', $value, 'crowdfunding-for-woocommerce' ), $value );
						break;
					case 'minute':
						$times[] = sprintf( _n( '%s minute', '%s minutes', $value, 'crowdfunding-for-woocommerce' ), $value );
						break;
					case 'second':
						$times[] = sprintf( _n( '%s second', '%s seconds', $value, 'crowdfunding-for-woocommerce' ), $value );
						break;
				}
				$count++;
			}
		}
		// Return string with times
		return implode( ', ', $times );
	}

	/**
	 * alg_product_crowdfunding_deadline_datetime.
	 *
	 * @version 3.0.0
	 * @since   1.1.0
	 */
	function alg_product_crowdfunding_deadline_datetime( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}
		return $this->output_shortcode(
			date_i18n( ( isset( $atts['date_format'] ) ? $atts['date_format'] : get_option( 'date_format' ) ),
				strtotime( get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline', true ) ) ) .
			' ' .
			date_i18n( ( isset( $atts['time_format'] ) ? $atts['time_format'] : get_option( 'time_format' ) ),
				strtotime( get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline_time', true ) ) ), $atts );
	}

	/**
	 * alg_product_crowdfunding_deadline_time.
	 *
	 * @version 2.1.0
	 * @since   1.1.0
	 */
	function alg_product_crowdfunding_deadline_time( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline_time', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_startdatetime.
	 *
	 * @version 3.0.0
	 * @since   1.1.0
	 */
	function alg_product_crowdfunding_startdatetime( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}
		return $this->output_shortcode(
			date_i18n( ( isset( $atts['date_format'] ) ? $atts['date_format'] : get_option( 'date_format' ) ),
				strtotime( get_post_meta( $product_id, '_' . 'alg_crowdfunding_startdate', true ) ) ) .
			' ' .
			date_i18n( ( isset( $atts['time_format'] ) ? $atts['time_format'] : get_option( 'time_format' ) ),
				strtotime( get_post_meta( $product_id, '_' . 'alg_crowdfunding_starttime', true ) ) ), $atts );
	}

	/**
	 * alg_product_crowdfunding_starttime.
	 *
	 * @version 2.1.0
	 * @since   1.1.0
	 */
	function alg_product_crowdfunding_starttime( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}
		return $this->output_shortcode( get_post_meta( $product_id, '_' . 'alg_crowdfunding_starttime', true ), $atts );
	}

	/**
	 * alg_product_crowdfunding_startdate.
	 *
	 * @version 3.0.0
	 * @since   1.0.0
	 */
	function alg_product_crowdfunding_startdate( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}
		return $this->output_shortcode( date_i18n( ( isset( $atts['date_format'] ) ? $atts['date_format'] : get_option( 'date_format' ) ),
			strtotime( get_post_meta( $product_id, '_' . 'alg_crowdfunding_startdate', true ) ) ), $atts );
	}

	/**
	 * alg_product_crowdfunding_deadline.
	 *
	 * @version 3.0.0
	 * @since   1.0.0
	 */
	function alg_product_crowdfunding_deadline( $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}
		return $this->output_shortcode( date_i18n( ( isset( $atts['date_format'] ) ? $atts['date_format'] : get_option( 'date_format' ) ),
			strtotime( get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline', true ) ) ), $atts );
	}

	/**
	 * alg_product_crowdfunding_time_to_start.
	 *
	 * @version 3.0.0
	 * @since   2.3.2
	 */
	function alg_product_crowdfunding_time_to_start( $atts ) {
		if ( empty( $atts ) ) {
			$atts = array();
		}
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}

		$from = ( int ) current_time( 'timestamp' );
		$to   = strtotime( trim( get_post_meta( $product_id, '_' . 'alg_crowdfunding_startdate', true ) . ' ' .
			get_post_meta( $product_id, '_' . 'alg_crowdfunding_starttime', true ), ' ' ) );

		if ( ! isset( $atts['campaign_will_start'] ) ) {
			$atts['campaign_will_start'] = __( 'The campaign will start in %s', 'crowdfunding-for-woocommerce' );
		}
		if ( ! isset( $atts['campaign_started'] ) ) {
			$atts['campaign_started']    = __( 'The campaign started %s ago', 'crowdfunding-for-woocommerce' );
		}

		if ( ! isset( $atts['precision'] ) ) {
			$atts['precision'] = 6;
		}

		if ( $from < $to ) {
			$return = sprintf( $atts['campaign_will_start'], $this->get_date_diff( $from, $to, $atts['precision'] ) );
		} else {
			$return = sprintf( $atts['campaign_started'],    $this->get_date_diff( $from, $to, $atts['precision'] ) );
		}

		return $this->output_shortcode( $return, $atts );
	}

	/**
	 * alg_product_crowdfunding_time_remaining.
	 *
	 * @version 2.7.0
	 * @since   1.0.0
	 * @todo    [dev] `human_time_diff`
	 */
	function alg_product_crowdfunding_time_remaining( $atts ) {
		if ( empty( $atts ) ) {
			$atts = array();
		}
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}

		$from = (int) current_time( 'timestamp' );
		$to   = strtotime( trim( get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline', true ) . ' ' . get_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline_time', true ), ' ' ) );

		if ( ! isset( $atts['campaign_will_end'] ) ) {
			$atts['campaign_will_end'] = __( 'The campaign will end in %s', 'crowdfunding-for-woocommerce' );
		}
		if ( ! isset( $atts['campaign_ended'] ) ) {
			$atts['campaign_ended']    = __( 'The campaign ended %s ago', 'crowdfunding-for-woocommerce' );
		}

		if ( ! isset( $atts['precision'] ) ) {
			$atts['precision'] = 6;
		}

		if ( $from < $to ) {
			$return = sprintf( $atts['campaign_will_end'], $this->get_date_diff( $from, $to, $atts['precision'] ) );
		} else {
			$return = sprintf( $atts['campaign_ended'],    $this->get_date_diff( $from, $to, $atts['precision'] ) );
		}
		return $this->output_shortcode( $return, $atts );
	}

}

endif;

return new Alg_WC_Crowdfunding_Shortcodes_Time();
