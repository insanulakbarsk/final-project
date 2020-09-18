<?php
/**
 * Crowdfunding for WooCommerce - Settings
 *
 * @version 3.0.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Settings_Crowdfunding' ) ) :

class Alg_WC_Settings_Crowdfunding extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 3.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'alg_crowdfunding';
		$this->label = __( 'Crowdfunding', 'crowdfunding-for-woocommerce' );
		parent::__construct();
		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'maybe_unsanitize_option' ), PHP_INT_MAX, 3 );
	}

	/**
	 * maybe_unsanitize_option.
	 *
	 * @version 3.0.0
	 * @since   3.0.0
	 */
	function maybe_unsanitize_option( $value, $option, $raw_value ) {
		return ( ! empty( $option['alg_wc_crowdfunding_raw'] ) ? $raw_value : $value );
	}

	/**
	 * get_settings.
	 *
	 * @version 2.5.0
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), array(
			array(
				'title'     => __( 'Reset Options', 'crowdfunding-for-woocommerce' ),
				'type'      => 'title',
				'id'        => 'alg_wc_crowdfunding_reset_' . $current_section . '_options',
			),
			array(
				'title'     => __( 'Reset Section Settings', 'crowdfunding-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Reset', 'crowdfunding-for-woocommerce' ) . '</strong>',
				'id'        => 'alg_wc_crowdfunding_reset_' . $current_section,
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => 'alg_wc_crowdfunding_reset_' . $current_section . '_options',
			),
		) );
	}

	/**
	 * Save settings.
	 *
	 * @version 3.0.0
	 * @since   2.5.0
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 3.0.0
	 * @since   3.0.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( 'alg_wc_crowdfunding_reset_' . $current_section, 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['id'] ) ) {
					$id = explode( '[', $value['id'] );
					delete_option( $id[0] );
				}
			}
			add_action( 'admin_notices', array( $this, 'admin_notice_settings_reset' ) );
		}
	}

	/**
	 * admin_notice_settings_reset.
	 *
	 * @version 3.0.0
	 * @since   3.0.0
	 */
	function admin_notice_settings_reset() {
		echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
			__( 'Your settings have been reset.', 'crowdfunding-for-woocommerce' ) . '</strong></p></div>';
	}

}

endif;

return new Alg_WC_Settings_Crowdfunding();
