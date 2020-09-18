<?php
/**
 * Crowdfunding for WooCommerce - Open Pricing Section Settings
 *
 * @version 3.1.6
 * @since   2.2.0
 * @author  Algoritmika Ltd.
 * @author  WP Wham
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_Settings_Open_Pricing' ) ) :

class Alg_WC_Crowdfunding_Settings_Open_Pricing extends Alg_WC_Crowdfunding_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 3.0.0
	 * @since   2.2.0
	 */
	function __construct() {

		$this->id   = 'open_pricing';
		$this->desc = __( 'Open Pricing (Name Your Price)', 'crowdfunding-for-woocommerce' );

		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.1.6
	 * @since   2.2.0
	 */
	public static function get_settings() {
		$settings = array(
			array(
				'title'    => __( 'Labels and Messages', 'crowdfunding-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_crowdfunding_product_open_price_messages_options',
			),
			array(
				'title'    => __( 'Frontend Label', 'crowdfunding-for-woocommerce' ),
				'id'       => 'alg_crowdfunding_product_open_price_label_frontend',
				'default'  => __( 'Name Your Price', 'crowdfunding-for-woocommerce' ),
				'type'     => 'text',
				'css'      => 'width:100%;',
			),
			array(
				'title'    => __( 'Message on Empty Price', 'crowdfunding-for-woocommerce' ),
				'id'       => 'alg_crowdfunding_product_open_price_messages_required',
				'default'  => __( 'Price is required!', 'crowdfunding-for-woocommerce' ),
				'type'     => 'text',
				'css'      => 'width:100%;',
			),
			array(
				'title'    => __( 'Message on Price too Low', 'crowdfunding-for-woocommerce' ),
				'id'       => 'alg_crowdfunding_product_open_price_messages_to_small',
				'default'  => __( 'Price is too low!', 'crowdfunding-for-woocommerce' ),
				'type'     => 'text',
				'css'      => 'width:100%;',
			),
			array(
				'title'    => __( 'Message on Price too High', 'crowdfunding-for-woocommerce' ),
				'id'       => 'alg_crowdfunding_product_open_price_messages_to_big',
				'default'  => __( 'Price is too high!', 'crowdfunding-for-woocommerce' ),
				'type'     => 'text',
				'css'      => 'width:100%;',
			),
			array(
				'title'    => __( 'Override Button Label on Archive Pages', 'crowdfunding-for-woocommerce' ),
				'desc'     => __( 'Override', 'crowdfunding-for-woocommerce' ),
				'desc_tip' => __( 'If disabled, will use standard label as set for crowdfunding products.', 'crowdfunding-for-woocommerce' ),
				'id'       => 'alg_wc_crowdfunding_product_open_price_add_to_cart',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'desc'     => __( 'Label', 'crowdfunding-for-woocommerce' ),
				'desc_tip' => __( 'Ignored if "Override Button Label on Archive Pages" is not enabled.', 'crowdfunding-for-woocommerce' ),
				'id'       => 'alg_wc_crowdfunding_product_open_price_add_to_cart_text',
				'default'  => __( 'Read more', 'woocommerce' ),
				'type'     => 'text',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_crowdfunding_product_open_price_messages_options',
			),
			array(
				'title'    => __( 'Template', 'crowdfunding-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_crowdfunding_open_price_template_options',
			),
			array(
				'title'    => __( 'Frontend Template', 'crowdfunding-for-woocommerce' ),
				'id'       => 'alg_crowdfunding_open_price_template',
				'default'  => '<label for="alg_crowdfunding_open_price">%title%</label> %input_field% %currency_symbol%',
				'type'     => 'textarea',
				'css'      => 'width:100%;height:50px;',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_crowdfunding_open_price_template_options',
			),
			array(
				'title'    => __( 'General Options', 'crowdfunding-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_crowdfunding_open_price_general_options',
			),
			array(
				'title'    => __( 'Hide Quantity Input Field', 'crowdfunding-for-woocommerce' ),
				'desc'     => __( 'Hide', 'crowdfunding-for-woocommerce' ),
				'desc_tip' => __( 'Hides quantity input field for the open pricing crowdfunding products.', 'crowdfunding-for-woocommerce' ),
				'id'       => 'alg_crowdfunding_open_price_hide_qty',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'title'    => __( 'Hide Original Price', 'crowdfunding-for-woocommerce' ),
				'desc'     => __( 'Hide', 'crowdfunding-for-woocommerce' ),
				'desc_tip' => __( 'Hides original WooCommerce price for the open pricing crowdfunding products.', 'crowdfunding-for-woocommerce' ),
				'id'       => 'alg_crowdfunding_open_price_hide_original_price',
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_crowdfunding_open_price_general_options',
			),
		);
		return $settings;
	}

}

endif;

return new Alg_WC_Crowdfunding_Settings_Open_Pricing();
