<?php
/**
 * Crowdfunding for WooCommerce
 *
 * @version 3.0.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding' ) ) :

class Alg_WC_Crowdfunding {

	/**
	 * Constructor.
	 *
	 * @version 3.0.0
	 * @todo    [dev] campaign authors (#12772)
	 * @todo    [dev] automatic refunds for non-finished campaigns (maybe via PayPal authorize or maybe bulk refund) (#12495) (#12159)
	 * @todo    [dev] payment collection directly to the campaign author (PayPal; bank etc.) (#11706)
	 */
	function __construct() {

		if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_enabled', 'yes' ) ) {

			// General - Buttons
			add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'change_add_to_cart_button_text_single' ),  PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_product_add_to_cart_text',        array( $this, 'change_add_to_cart_button_text_archive' ), PHP_INT_MAX, 2 );

			// General - Messages
			add_action( 'woocommerce_single_product_summary', array( $this, 'is_purchasable_html' ), 15 );

			// General - Variable Add to Cart Template
			if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_variable_add_to_cart_radio_enabled', 'no' ) ) {
				add_filter( 'wc_get_template', array( $this, 'change_variable_add_to_cart_template' ), PHP_INT_MAX, 5 );
			}

			// Product Info - Tab
			if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_product_tab_enabled', 'yes' ) ) {
				add_filter( 'woocommerce_product_tabs', array( $this, 'add_crowdfunding_product_tab' ), 98 );
			}

			// Product Info - Single Page
			if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_product_info_enabled', 'no' ) ) {
				add_action(
					get_option( 'alg_woocommerce_crowdfunding_product_info_filter', 'woocommerce_before_single_product_summary' ),
					array( $this, 'add_crowdfunding_product_info' ),
					get_option( 'alg_woocommerce_crowdfunding_product_info_filter_priority', 10 )
				);
			}

			// Product Info - Archives
			if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_product_info_archives_enabled', 'no' ) ) {
				add_action(
					get_option( 'alg_woocommerce_crowdfunding_product_info_archives_filter', 'woocommerce_after_shop_loop_item' ),
					array( $this, 'add_crowdfunding_product_info_archives' ),
					get_option( 'alg_woocommerce_crowdfunding_product_info_arch_filter_priority', 10 )
				);
			}

			// Product Info -  Price - Hide Variable Price
			if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_hide_variable_price', 'no' ) ) {
				add_action( 'woocommerce_get_price_html', array( $this, 'hide_variable_price' ), PHP_INT_MAX, 2 );
			}

			// Is purchasable
			add_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), PHP_INT_MAX, 2 );

			// Functions
			require_once( 'functions/wc-crowdfunding-functions.php' );

			// Open Pricing
			require_once( 'class-wc-crowdfunding-open-pricing.php' );

			// My Account
			require_once( 'class-wc-crowdfunding-my-account.php' );

			// Shortcodes
			require_once( 'shortcodes/class-wc-crowdfunding-shortcodes.php' );
			require_once( 'shortcodes/class-wc-crowdfunding-shortcodes-general.php' );
			require_once( 'shortcodes/class-wc-crowdfunding-shortcodes-time.php' );
			require_once( 'shortcodes/class-wc-crowdfunding-shortcodes-progress-bar.php' );
			require_once( 'shortcodes/class-wc-crowdfunding-shortcodes-products-add-form.php' );

			// Crons
			require_once( 'class-wc-crowdfunding-crons.php' );
		}

		register_activation_hook(   alg_wc_crowdfunding_get_file(), array( $this, 'add_my_products_endpoint_flush_rewrite_rules' ) );
		register_deactivation_hook( alg_wc_crowdfunding_get_file(), array( $this, 'add_my_products_endpoint_flush_rewrite_rules' ) );
		add_filter( 'query_vars',                                   array( $this, 'add_my_products_endpoint_query_var' ), 0 );
		add_action( 'init',                                         array( $this, 'add_my_products_endpoint' ) );
	}

	/**
	 * Flush rewrite rules on plugin activation.
	 *
	 * @version 2.3.1
	 * @since   2.3.1
	 */
	function add_my_products_endpoint_flush_rewrite_rules() {
		add_rewrite_endpoint( 'crowdfunding-campaigns', EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	}

	/**
	 * Add new query var.
	 *
	 * @version 2.3.1
	 * @since   2.3.1
	 * @param   array $vars
	 * @return  array
	 */
	function add_my_products_endpoint_query_var( $vars ) {
		$vars[] = 'crowdfunding-campaigns';
		return $vars;
	}

	/**
	 * Register new endpoint to use inside My Account page.
	 *
	 * @version 2.3.1
	 * @since   2.3.1
	 * @see     https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
	 */
	function add_my_products_endpoint() {
		add_rewrite_endpoint( 'crowdfunding-campaigns', EP_ROOT | EP_PAGES );
	}

	/**
	 * hide_variable_price.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function hide_variable_price( $price, $_product ) {
		if ( ! $this->is_crowdfunding_product( $_product ) ) return $price;
		if ( ! $_product->is_type( 'variable' ) ) return $price;
		return '';
	}

	/**
	 * change_variable_add_to_cart_template.
	 *
	 * @version 2.3.2
	 * @since   2.0.0
	 */
	function change_variable_add_to_cart_template( $located, $template_name, $args, $template_path, $default_path ) {
		$_product = wc_get_product();
		if ( $_product && $this->is_crowdfunding_product( $_product ) ) {
			if ( 'single-product/add-to-cart/variable.php' == $template_name ) {
				$located = untrailingslashit( realpath( plugin_dir_path( __FILE__ ) . '/..' ) ) . '/includes/templates/alg-add-to-cart-variable.php';
			} elseif ( 'single-product/add-to-cart/variation-add-to-cart-button.php' == $template_name ) {
				if ( ! $this->is_started( $_product ) || ! $this->is_active( $_product ) ) {
					$located = untrailingslashit( realpath( plugin_dir_path( __FILE__ ) . '/..' ) ) . '/includes/templates/alg-variation-add-to-cart-button-disabled.php';
				}
			}
		}
		return $located;
	}

	/**
	 * Returns false if already finished.
	 *
	 * @version 3.0.0
	 * @return  bool
	 */
	function is_active( $_product ) {
		$_product_id = alg_wc_crdfnd_get_product_id_or_variation_parent_id( $_product );
		if ( 'yes' === get_option( 'alg_crowdfunding_end_on_time', 'yes' ) ) {
			$end_date_str = get_post_meta( $_product_id, '_' . 'alg_crowdfunding_deadline', true );
			$end_time_str = get_post_meta( $_product_id, '_' . 'alg_crowdfunding_deadline_time', true );
			$end_datetime = ( '' != $end_date_str ) ? strtotime( trim( $end_date_str . ' ' . $end_time_str, ' ' ) ) : 0;
			if ( $end_datetime > 0 && ( $end_datetime - ( (int) current_time( 'timestamp' ) ) ) < 0 ) {
				return false;
			}
		}
		if ( 'yes' === get_option( 'alg_crowdfunding_end_on_goal_reached', 'no' ) ) {
			$goal_sum     = get_post_meta( $_product_id, '_' . 'alg_crowdfunding_goal_sum', true );
			$goal_backers = get_post_meta( $_product_id, '_' . 'alg_crowdfunding_goal_backers', true );
			$goal_items   = get_post_meta( $_product_id, '_' . 'alg_crowdfunding_goal_items', true );
			if ( '' != $goal_sum     && alg_wc_crdfnd_get_product_orders_data( 'orders_sum',   array( 'product_id' => $_product_id ) ) >= $goal_sum ) {
				return false;
			}
			if ( '' != $goal_backers && alg_wc_crdfnd_get_product_orders_data( 'total_orders', array( 'product_id' => $_product_id ) ) >= $goal_backers ) {
				return false;
			}
			if ( '' != $goal_items   && alg_wc_crdfnd_get_product_orders_data( 'total_items',  array( 'product_id' => $_product_id ) ) >= $goal_items ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Returns false if not started yet.
	 *
	 * @version 3.0.0
	 * @return  bool
	 */
	function is_started( $_product ) {
		$_product_id = alg_wc_crdfnd_get_product_id_or_variation_parent_id( $_product );
		$start_date_str = get_post_meta( $_product_id, '_' . 'alg_crowdfunding_startdate', true );
		$start_time_str = get_post_meta( $_product_id, '_' . 'alg_crowdfunding_starttime', true );
		$start_datetime = ( '' != $start_date_str ) ? strtotime( trim( $start_date_str . ' ' . $start_time_str, ' ' ) ) : 0;
		if ( $start_datetime > 0 && ( $start_datetime - ( (int) current_time( 'timestamp' ) ) ) > 0 ) return false;
		return true;
	}

	/**
	 * Returns false if the product cannot be bought.
	 *
	 * @version 2.0.0
	 * @return  bool
	 */
	function is_purchasable( $purchasable, $_product ) {
		if ( $this->is_crowdfunding_product( $_product ) && ( ! $this->is_started( $_product ) || ! $this->is_active( $_product ) ) ) {
			$purchasable = false;
		}
		return $purchasable;
	}

	/**
	 * is_purchasable_html.
	 *
	 * @version 3.0.0
	 * @return  bool
	 */
	function is_purchasable_html() {
		$_product = wc_get_product();
		if ( $this->is_crowdfunding_product( $_product ) && ! $this->is_purchasable ( true, $_product ) ) {
			if ( ! $this->is_started( $_product ) ) {
				echo do_shortcode( get_option( 'alg_woocommerce_crowdfunding_message_not_started', __( '<strong>Not yet started!</strong>', 'crowdfunding-for-woocommerce' ) ) );
			}
			if ( ! $this->is_active( $_product ) ) {
				echo do_shortcode( get_option( 'alg_woocommerce_crowdfunding_message_ended', __( '<strong>Ended!</strong>', 'crowdfunding-for-woocommerce' ) ) );
			}
		}
	}

	/**
	 * is_crowdfunding_product.
	 *
	 * @version 3.0.0
	 * @since   2.0.0
	 */
	function is_crowdfunding_product( $_product = '' ) {
		if ( '' == $_product ) $_product = wc_get_product();
		if ( '' == $_product ) return '';
		$_product_id = alg_wc_crdfnd_get_product_id_or_variation_parent_id( $_product );
		return ( 'yes' === get_post_meta( $_product_id, '_' . 'alg_crowdfunding_enabled', true ) );
	}

	/**
	 * change_add_to_cart_button_text_single.
	 *
	 * @version 3.0.0
	 * @since   2.0.0
	 * @todo    [dev] add info ("You can use shortcodes here...") in settings?
	 */
	function change_add_to_cart_button_text_single( $add_to_cart_text, $_product ) {
		if ( ! $this->is_crowdfunding_product( $_product ) ) {
			return $add_to_cart_text;
		}
		$_product_id = alg_wc_crdfnd_get_product_id_or_variation_parent_id( $_product );
		$the_text = get_post_meta( $_product_id, '_alg_crowdfunding_button_label_single', true );
		$the_text = ( '' != $the_text ) ? $the_text : get_option( 'alg_woocommerce_crowdfunding_button_single', __( 'Back This Project', 'crowdfunding-for-woocommerce' ) );
		return do_shortcode( $the_text );
	}

	/**
	 * change_add_to_cart_button_text_archive.
	 *
	 * @version 3.0.0
	 * @since   2.0.0
	 */
	function change_add_to_cart_button_text_archive( $add_to_cart_text, $_product ) {
		if ( ! $this->is_crowdfunding_product( $_product ) ) {
			return $add_to_cart_text;
		}
		$_product_id = alg_wc_crdfnd_get_product_id_or_variation_parent_id( $_product );
		$the_text = get_post_meta( $_product_id, '_alg_crowdfunding_button_label_loop', true );
		$the_text = ( '' != $the_text ) ? $the_text : get_option( 'alg_woocommerce_crowdfunding_button_archives', __( 'Read More', 'crowdfunding-for-woocommerce' ) );
		return do_shortcode( $the_text );
	}

	/**
	 * add_crowdfunding_product_tab.
	 *
	 * @version 3.0.0
	 */
	function add_crowdfunding_product_tab( $tabs ) {
		$_product = wc_get_product();
		if ( $this->is_crowdfunding_product( $_product ) ) {
			$tabs['crowdfunding'] = array(
				'title'    => get_option( 'alg_woocommerce_crowdfunding_product_tab_title', __( 'Crowdfunding', 'crowdfunding-for-woocommerce' ) ),
				'priority' => get_option( 'alg_woocommerce_crowdfunding_product_tab_priority', 40 ),
				'callback' => array( $this, 'crowdfunding_product_tab_callback' ),
			);
		}
		return $tabs;
	}

	/**
	 * crowdfunding_product_tab_callback.
	 *
	 * @version 3.0.0
	 */
	function crowdfunding_product_tab_callback() {
		$_product = wc_get_product();
		if ( $this->is_crowdfunding_product( $_product ) ) {
			$info_template = get_option( 'alg_woocommerce_crowdfunding_product_tab', $this->get_default_product_info( 'tab' ) );
			echo do_shortcode( $info_template );
		}
	}

	/**
	 * add_crowdfunding_product_info_archives.
	 *
	 * @version 3.0.0
	 * @since   1.2.0
	 */
	function add_crowdfunding_product_info_archives() {
		$_product = wc_get_product();
		if ( $this->is_crowdfunding_product( $_product ) ) {
			$info_template = get_option( 'alg_woocommerce_crowdfunding_product_info_archives', $this->get_default_product_info( 'archives' ) );
			echo do_shortcode( $info_template );
		}
	}

	/**
	 * add_crowdfunding_product_info.
	 *
	 * @version 3.0.0
	 */
	function add_crowdfunding_product_info() {
		$_product = wc_get_product();
		if ( $this->is_crowdfunding_product( $_product ) ) {
			$info_template = get_option( 'alg_woocommerce_crowdfunding_product_info', $this->get_default_product_info( 'single' ) );
			echo do_shortcode( $info_template );
		}
	}

	/**
	 * get_default_product_info.
	 *
	 * @version 3.0.0
	 * @since   3.0.0
	 */
	function get_default_product_info( $type ) {
		switch ( $type ) {
			case 'tab':
				return '<table>' . PHP_EOL .
					'[product_crowdfunding_total_backers before="<tr><td>Total Backers</td><td>" after="</td></tr>"]' . PHP_EOL .
					'[product_crowdfunding_total_sum before="<tr><td>Total Sum</td><td>" after="</td></tr>"]' . PHP_EOL .
					'[product_crowdfunding_goal before="<tr><td>Goal</td><td>" after="</td></tr>"]' . PHP_EOL .
					'[product_crowdfunding_goal_remaining before="<tr><td>Remaining</td><td>" after="</td></tr>"]' . PHP_EOL .
					'[product_crowdfunding_goal_progress_bar before="<tr><td>Remaining</td><td>" after="</td></tr>"]' . PHP_EOL .
					'[product_crowdfunding_startdate before="<tr><td>Start Date</td><td>" after="</td></tr>"]' . PHP_EOL .
					'[product_crowdfunding_deadline before="<tr><td>End Date</td><td>" after="</td></tr>"]' . PHP_EOL .
					'[product_crowdfunding_time_remaining before="<tr><td>Time Remaining</td><td>" after="</td></tr>"]' . PHP_EOL .
					'[product_crowdfunding_time_progress_bar before="<tr><td>Time Remaining</td><td>" after="</td></tr>"]' . PHP_EOL .
				'</table>';
			case 'archives':
				return
					'[product_crowdfunding_total_sum before="<p>Funds to Date: " after="</p>"]' . PHP_EOL .
					'[product_crowdfunding_goal before="<p>End Goal: " after="</p>"]' . PHP_EOL .
					'[product_crowdfunding_time_progress_bar before="<p>" after="</p>"]';
			case 'single':
				return
					'[product_crowdfunding_total_backers before="<p>Total Backers: " after="</p>"]' . PHP_EOL .
					'[product_crowdfunding_total_sum before="<p>Total Sum: " after="</p>"]' . PHP_EOL .
					'[product_crowdfunding_goal before="<p>Goal: " after="</p>"]' . PHP_EOL .
					'[product_crowdfunding_goal_remaining before="<p>Remaining: " after="</p>"]' . PHP_EOL .
					'[product_crowdfunding_goal_progress_bar before="<p>Remaining: " after="</p>"]' . PHP_EOL .
					'[product_crowdfunding_startdate before="<p>Start Date: " after="</p>"]' . PHP_EOL .
					'[product_crowdfunding_deadline before="<p>End Date: " after="</p>"]' . PHP_EOL .
					'[product_crowdfunding_time_remaining before="<p>Time Remaining: " after="</p>"]' . PHP_EOL .
					'[product_crowdfunding_time_progress_bar before="<p>Time Remaining: " after="</p>"]';
		}
	}

}

endif;

return new Alg_WC_Crowdfunding();
