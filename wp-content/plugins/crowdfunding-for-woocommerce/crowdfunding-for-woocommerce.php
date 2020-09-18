<?php
/*
Plugin Name: Crowdfunding for WooCommerce
Plugin URI: https://wpwham.com/products/crowdfunding-for-woocommerce/
Description: Crowdfunding products for WooCommerce.
Version: 3.1.6
Author: WP Wham
Author URI: https://wpwham.com/
Text Domain: crowdfunding-for-woocommerce
Domain Path: /langs
Copyright: © 2019-2020 WP Wham
WC tested up to: 4.4
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Check if WooCommerce is active
$plugin = 'woocommerce/woocommerce.php';
if (
	! in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) &&
	! ( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
) return;

if ( 'crowdfunding-for-woocommerce.php' === basename( __FILE__ ) ) {
	// Check if Pro is active, if so then return
	$plugin = 'crowdfunding-for-woocommerce-pro/crowdfunding-for-woocommerce-pro.php';
	if (
		in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) ||
		( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
	) return;
}

if ( ! class_exists( 'Alg_Woocommerce_Crowdfunding' ) ) :

/**
 * Main Alg_Woocommerce_Crowdfunding Class
 *
 * @class   Alg_Woocommerce_Crowdfunding
 * @version 3.0.0
 */
final class Alg_Woocommerce_Crowdfunding {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 2.3.0
	 */
	public $version = '3.1.6';

	/**
	 * @var Alg_Woocommerce_Crowdfunding The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_Woocommerce_Crowdfunding Instance
	 *
	 * Ensures only one instance of Alg_Woocommerce_Crowdfunding is loaded or can be loaded.
	 *
	 * @static
	 * @return Alg_Woocommerce_Crowdfunding - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Alg_Woocommerce_Crowdfunding Constructor.
	 *
	 * @version 2.9.0
	 * @access  public
	 */
	function __construct() {

		// Set up localisation
		load_plugin_textdomain( 'crowdfunding-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );

		// Include required files
		$this->includes();

		// Settings & Scripts
		if ( is_admin() ) {
			// Backend
			$this->admin();
		} else {
			// Frontend
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			if (
				'yes' === get_option( 'alg_wc_crowdfunding_product_by_user_' . 'start_date' . '_enabled', 'no' ) ||
				'yes' === get_option( 'alg_wc_crowdfunding_product_by_user_' . 'start_time' . '_enabled', 'no' ) ||
				'yes' === get_option( 'alg_wc_crowdfunding_product_by_user_' . 'end_date'   . '_enabled', 'no' ) ||
				'yes' === get_option( 'alg_wc_crowdfunding_product_by_user_' . 'end_time'   . '_enabled', 'no' )
			) {
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
				add_action( 'init',               array( $this, 'register_admin_scripts' ) );
			}
		}
	}

	/**
	 * enqueue_scripts.
	 *
	 * @version 2.9.1
	 * @since   1.2.0
	 */
	function enqueue_scripts() {
		if (
			'yes' === get_option( 'alg_woocommerce_crowdfunding_enabled', 'yes' ) &&
			'yes' === get_option( 'alg_woocommerce_crowdfunding_variable_add_to_cart_radio_enabled', 'no' )
		) {
			wp_enqueue_script( 'alg-variations',   $this->plugin_url() . '/includes/js/alg-variations-frontend.js', array( 'jquery' ), $this->version );
		}

		wp_enqueue_script( 'alg-progress-bar-src', $this->plugin_url() . '/includes/js/progressbar.min.js',         array( 'jquery' ), $this->version );
		wp_enqueue_script( 'alg-progress-bar',     $this->plugin_url() . '/includes/js/alg-progressbar.js',         array( 'jquery' ), $this->version );
	}

	/**
	 * register_admin_scripts.
	 *
	 * @version 2.3.2
	 * @since   1.1.0
	 */
	function register_admin_scripts() {
		wp_register_script(
			'jquery-ui-timepicker',
			$this->plugin_url() . '/includes/js/jquery.timepicker.min.js',
			array( 'jquery' ),
			$this->version,
			true
		);
	}

	/**
	 * enqueue_admin_scripts.
	 *
	 * @version 2.3.3
	 * @todo    [dev] maybe 'jquery-ui-css' => '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css'
	 */
	function enqueue_admin_scripts() {
		wp_enqueue_script( 'jquery-ui-datepicker', false,                                                             array(),           $this->version );
		wp_enqueue_script( 'jquery-ui-timepicker' );
		wp_enqueue_script( 'alg-datepicker',       $this->plugin_url() . '/includes/js/alg-datepicker.js',            array( 'jquery' ), $this->version, true );
		wp_enqueue_style(  'jquery-ui-css',        '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css',     array(),           $this->version );
		wp_enqueue_style(  'alg-timepicker',       $this->plugin_url() . '/includes/css/jquery.timepicker.min.css',   array(),           $this->version );
		wp_enqueue_script( 'jquery-ui-dialog',     false,                                                             array(),           $this->version );
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 2.6.0
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$settings   = array( '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_crowdfunding' ) . '">' .
			__( 'Settings', 'woocommerce' ) . '</a>' );
		$unlock_all = apply_filters( 'alg_crowdfunding_option', array(
			'<a href="' . esc_url( 'https://wpwham.com/products/crowdfunding-for-woocommerce/' ) . '">' .
				__( 'Unlock all', 'crowdfunding-for-woocommerce' ) . '</a>',
		), 'settings_array' );
		return array_merge( $settings, $unlock_all, $links );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 3.0.0
	 */
	function includes() {
		// Functions
		require_once( 'includes/functions/wc-crowdfunding-functions-user-campaign-fields.php' );
		// Product edit meta box etc.
		require_once( 'includes/class-wc-crowdfunding-admin.php' );
		// Core
		$this->core = require_once( 'includes/class-wc-crowdfunding.php' );
	}

	/**
	 * add settings to WC status report
	 *
	 * @version 3.1.6
	 * @since   3.1.6
	 * @author  WP Wham
	 */
	public static function add_settings_to_status_report() {
		#region add_settings_to_status_report
		$protected_settings      = array( 'wpwham_woocommerce_crowdfunding_license' );
		$settings_general        = Alg_WC_Crowdfunding_Settings_General::get_settings();
		$settings_product_info   = Alg_WC_Crowdfunding_Settings_Product_Info::get_settings();
		$settings_open_pricing   = Alg_WC_Crowdfunding_Settings_Open_Pricing::get_settings();
		$settings_user_campaigns = Alg_WC_Crowdfunding_Settings_Product_By_User::get_settings();
		$settings = array_merge(
			$settings_general, $settings_product_info, $settings_open_pricing, $settings_user_campaigns
		);
		?>
		<table class="wc_status_table widefat" cellspacing="0">
			<thead>
				<tr>
					<th colspan="3" data-export-label="Crowdfunding Settings"><h2><?php esc_html_e( 'Crowdfunding Settings', 'crowdfunding-for-woocommerce' ); ?></h2></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $settings as $setting ): ?>
				<?php 
				if ( in_array( $setting['type'], array( 'title', 'sectionend' ) ) ) { 
					continue;
				}
				if ( isset( $setting['title'] ) ) {
					$title = $setting['title'];
				} elseif ( isset( $setting['desc'] ) ) {
					$title = $setting['desc'];
				} else {
					$title = $setting['id'];
				}
				$value = get_option( $setting['id'] ); 
				if ( in_array( $setting['id'], $protected_settings ) ) {
					$value = $value > '' ? '(set)' : 'not set';
				}
				?>
				<tr>
					<td data-export-label="<?php echo esc_attr( $title ); ?>"><?php esc_html_e( $title, 'crowdfunding-for-woocommerce' ); ?>:</td>
					<td class="help">&nbsp;</td>
					<td><?php echo is_array( $value ) ? print_r( $value, true ) : $value; ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		#endregion add_settings_to_status_report
	}

	/**
	 * admin.
	 *
	 * @version 3.1.6
	 * @since   2.9.0
	 */
	function admin() {

		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );

		// Scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_init',            array( $this, 'register_admin_scripts' ) );

		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
		require_once( 'includes/settings/class-wc-crowdfunding-settings-section.php' );
		$this->settings = array();
		$this->settings['general']         = require_once( 'includes/settings/class-wc-crowdfunding-settings-general.php' );
		$this->settings['product-info']    = require_once( 'includes/settings/class-wc-crowdfunding-settings-product-info.php' );
		$this->settings['open-pricing']    = require_once( 'includes/settings/class-wc-crowdfunding-settings-open-pricing.php' );
		$this->settings['product-by-user'] = require_once( 'includes/settings/class-wc-crowdfunding-settings-product-by-user.php' );
		add_action( 'woocommerce_system_status_report', array( $this, 'add_settings_to_status_report' ) );

		// Version updated
		if ( get_option( 'alg_woocommerce_crowdfunding_version', '' ) !== $this->version ) {
			add_action( 'admin_init', array( $this, 'version_updated' ) );
		}
	}

	/**
	 * version_updated.
	 *
	 * @version 3.0.0
	 * @since   2.7.0
	 */
	function version_updated() {
		// Handling deprecated options
		$deprecated_options = array(
			'alg_wc_crowdfunding_product_by_user_message_product_successfully_added'  => 'alg_wc_crowdfunding_product_by_user_message_product_added',
			'alg_wc_crowdfunding_product_by_user_message_product_successfully_edited' => 'alg_wc_crowdfunding_product_by_user_message_product_edited',
		);
		foreach ( $deprecated_options as $old_option => $new_option ) {
			if ( false != ( $old_option_value = get_option( $old_option, false ) ) ) {
				update_option( $new_option, $old_option_value );
				delete_option( $old_option );
			}
		}
		// Version update finished
		update_option( 'alg_woocommerce_crowdfunding_version', $this->version );
	}

	/**
	 * Add Woocommerce settings tab to WooCommerce settings.
	 *
	 * @version 3.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = require_once( 'includes/settings/class-wc-settings-crowdfunding.php' );
		return $settings;
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
}

endif;

if ( ! function_exists( 'alg_wc_crowdfunding' ) ) {
	/**
	 * Returns the main instance of Alg_Woocommerce_Crowdfunding to prevent the need to use globals.
	 *
	 * @return Alg_Woocommerce_Crowdfunding
	 */
	function alg_wc_crowdfunding() {
		return Alg_Woocommerce_Crowdfunding::instance();
	}
}

if ( ! function_exists( 'alg_wc_crowdfunding_get_file' ) ) {
	/**
	 * alg_wc_crowdfunding_get_file.
	 *
	 * @version 2.3.1
	 * @since   2.3.1
	 */
	function alg_wc_crowdfunding_get_file() {
		return __FILE__;
	}
}

alg_wc_crowdfunding();
