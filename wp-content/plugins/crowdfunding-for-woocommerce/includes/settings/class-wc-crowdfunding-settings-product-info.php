<?php
/**
 * Crowdfunding for WooCommerce - Product Info Section Settings
 *
 * @version 3.1.6
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 * @author  WP Wham
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_Settings_Product_Info' ) ) :

class Alg_WC_Crowdfunding_Settings_Product_Info extends Alg_WC_Crowdfunding_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 3.0.0
	 */
	function __construct() {

		$this->id   = 'product_info';
		$this->desc = __( 'Product Info', 'crowdfunding-for-woocommerce' );

		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 3.1.6
	 */
	public static function get_settings() {

		$settings = array(

			array(
				'title' => __( 'Custom Product Tab', 'crowdfunding-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_woocommerce_crowdfunding_product_tab_options'
			),

			array(
				'title'     => __( 'Add Product Info to Custom Tab on Single Product Page', 'crowdfunding-for-woocommerce' ),
				'desc'      => __( 'Add', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_product_tab_enabled',
				'default'   => 'yes',
				'type'      => 'checkbox',
			),

			array(
				'title'     => __( 'Custom Tab Title', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_product_tab_title',
				'default'   => __( 'Crowdfunding', 'crowdfunding-for-woocommerce' ),
				'type'      => 'text',
			),

			array(
				'title'   => __( 'Info', 'crowdfunding-for-woocommerce' ),
				'id'      => 'alg_woocommerce_crowdfunding_product_tab',
				'default' => alg_wc_crowdfunding()->core->get_default_product_info( 'tab' ),
				'type'    => 'textarea',
				'css'     => 'width:100%;height:200px;',
				'alg_wc_crowdfunding_raw' => true,
			),

			array(
				'title'   => __( 'Order', 'crowdfunding-for-woocommerce' ),
				'id'      => 'alg_woocommerce_crowdfunding_product_tab_priority',
				'default' => 40,
				'type'    => 'number',
				'custom_attributes' => array('step' => '1', 'min' => '1', ),
			),

			array(
				'type' => 'sectionend',
				'id'   => 'alg_woocommerce_crowdfunding_product_tab_options'
			),

			array(
				'title' => __( 'Custom Product Info', 'crowdfunding-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_woocommerce_crowdfunding_product_info_options'
			),

			array(
				'title'     => __( 'Add Product Info to Single Product Page', 'crowdfunding-for-woocommerce' ),
				'desc'      => __( 'Add', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_product_info_enabled',
				'default'   => 'no',
				'type'      => 'checkbox',
			),

			array(
				'title'   => __( 'Info', 'crowdfunding-for-woocommerce' ),
				'id'      => 'alg_woocommerce_crowdfunding_product_info',
				'default' => alg_wc_crowdfunding()->core->get_default_product_info( 'single' ),
				'type'    => 'textarea',
				'css'     => 'width:100%;height:200px;',
				'alg_wc_crowdfunding_raw' => true,
			),

			array(
				'title'   => __( 'Position', 'crowdfunding-for-woocommerce' ),
				'id'      => 'alg_woocommerce_crowdfunding_product_info_filter',
				'default' => 'woocommerce_before_single_product_summary',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => array(
					'woocommerce_before_single_product_summary' => __( 'Before single product summary', 'crowdfunding-for-woocommerce' ),
					'woocommerce_after_single_product_summary'  => __( 'After single product summary',  'crowdfunding-for-woocommerce' ),
					'woocommerce_single_product_summary'        => __( 'Inside single product summary', 'crowdfunding-for-woocommerce' ),
				),
			),

			array(
				'title'   => __( 'Order', 'crowdfunding-for-woocommerce' ),
				'id'      => 'alg_woocommerce_crowdfunding_product_info_filter_priority',
				'default' => 10,
				'type'    => 'number',
				'custom_attributes' => array('step' => '1', 'min' => '1', ),
			),

			array(
				'type' => 'sectionend',
				'id'   => 'alg_woocommerce_crowdfunding_product_info_options'
			),

			array(
				'title' => __( 'Custom Product Info - Category View', 'crowdfunding-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_woocommerce_crowdfunding_product_info_archives_options'
			),

			array(
				'title'     => __( 'Add Product Info to Archives Pages', 'crowdfunding-for-woocommerce' ),
				'desc'      => __( 'Add', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_product_info_archives_enabled',
				'default'   => 'no',
				'type'      => 'checkbox',
			),

			array(
				'title'   => __( 'Info', 'crowdfunding-for-woocommerce' ),
				'id'      => 'alg_woocommerce_crowdfunding_product_info_archives',
				'default' => alg_wc_crowdfunding()->core->get_default_product_info( 'archives' ),
				'type'    => 'textarea',
				'css'     => 'width:100%;height:200px;',
				'alg_wc_crowdfunding_raw' => true,
			),

			array(
				'title'   => __( 'Position', 'crowdfunding-for-woocommerce' ),
				'id'      => 'alg_woocommerce_crowdfunding_product_info_archives_filter',
				'default' => 'woocommerce_after_shop_loop_item',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => array(
					'woocommerce_before_shop_loop_item'       => __( 'Before product', 'crowdfunding-for-woocommerce' ),
					'woocommerce_before_shop_loop_item_title' => __( 'Before product title', 'crowdfunding-for-woocommerce' ),
					'woocommerce_after_shop_loop_item'        => __( 'After product', 'crowdfunding-for-woocommerce' ),
					'woocommerce_after_shop_loop_item_title'  => __( 'After product title', 'crowdfunding-for-woocommerce' ),
				),
			),

			array(
				'title'   => __( 'Order', 'crowdfunding-for-woocommerce' ),
				'id'      => 'alg_woocommerce_crowdfunding_product_info_arch_filter_priority',
				'default' => 10,
				'type'    => 'number',
				'custom_attributes' => array('step' => '1', 'min' => '1', ),
			),

			array(
				'type' => 'sectionend',
				'id'   => 'alg_woocommerce_crowdfunding_product_info_archives_options'
			),

			array(
				'title' => __( 'Product Price', 'crowdfunding-for-woocommerce' ),
				'type'  => 'title',
				'id'    => 'alg_woocommerce_crowdfunding_product_info_price_options'
			),

			array(
				'title'     => __( 'Hide Main Price for Variable Crowdfunding Products', 'crowdfunding-for-woocommerce' ),
				'desc'      => __( 'Hide', 'crowdfunding-for-woocommerce' ),
				'id'        => 'alg_woocommerce_crowdfunding_hide_variable_price',
				'default'   => 'no',
				'type'      => 'checkbox',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'alg_woocommerce_crowdfunding_product_info_price_options'
			),

		);

		return $settings;
	}

}

endif;

return new Alg_WC_Crowdfunding_Settings_Product_Info();
