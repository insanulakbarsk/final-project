<?php
/**
 * Crowdfunding for WooCommerce - Functions - User Campaign Fields
 *
 * @version 3.0.0
 * @since   3.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'alg_wc_crdfnd_get_user_campaign_standard_fields' ) ) {
	/**
	 * alg_wc_crdfnd_get_user_campaign_standard_fields.
	 *
	 * @version 3.0.0
	 * @since   2.3.0
	 */
	function alg_wc_crdfnd_get_user_campaign_standard_fields() {
		return array(
			'desc' => array(
				'desc'      => __( 'Description', 'crowdfunding-for-woocommerce' ),
			),
			'short_desc' => array(
				'desc'      => __( 'Short Description', 'crowdfunding-for-woocommerce' ),
			),
			'image' => array(
				'desc'      => __( 'Image', 'crowdfunding-for-woocommerce' ),
			),
			'regular_price' => array(
				'desc'      => __( 'Regular Price', 'crowdfunding-for-woocommerce' ),
			),
			'sale_price' => array(
				'desc'      => __( 'Sale Price', 'crowdfunding-for-woocommerce' ),
			),
			'cats' => array(
				'desc'      => __( 'Categories', 'crowdfunding-for-woocommerce' ),
			),
			'tags' => array(
				'desc'      => __( 'Tags', 'crowdfunding-for-woocommerce' ),
			),
		);
	}
}

if ( ! function_exists( 'alg_wc_crdfnd_get_user_campaign_crowdfunding_fields' ) ) {
	/**
	 * alg_wc_crdfnd_get_user_campaign_crowdfunding_fields.
	 *
	 * @version 3.0.0
	 * @since   2.3.0
	 */
	function alg_wc_crdfnd_get_user_campaign_crowdfunding_fields() {
		return apply_filters( 'alg_crowdfunding_user_campaign_fields', array(
			'goal' => array(
				'desc'      => __( 'Goal', 'crowdfunding-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'type'      => 'price',
				'meta_name' => 'alg_crowdfunding_goal_sum',
			),
			'goal_backers' => array(
				'desc'      => __( 'Goal (Backers)', 'crowdfunding-for-woocommerce' ),
				'type'      => 'number',
				'meta_name' => 'alg_crowdfunding_goal_backers',
			),
			'goal_items' => array(
				'desc'      => __( 'Goal (Items)', 'crowdfunding-for-woocommerce' ),
				'type'      => 'number',
				'meta_name' => 'alg_crowdfunding_goal_items',
			),
			'start_date' => array(
				'desc'      => __( 'Time: Start Date', 'crowdfunding-for-woocommerce' ),
				'type'      => 'date',
				'meta_name' => 'alg_crowdfunding_startdate',
			),
			'start_time' => array(
				'desc'      => __( 'Time: Start Time', 'crowdfunding-for-woocommerce' ),
				'type'      => 'time',
				'meta_name' => 'alg_crowdfunding_starttime',
			),
			'end_date' => array(
				'desc'      => __( 'Time: End Date', 'crowdfunding-for-woocommerce' ),
				'type'      => 'date',
				'meta_name' => 'alg_crowdfunding_deadline',
			),
			'end_time' => array(
				'desc'      => __( 'Time: End Time', 'crowdfunding-for-woocommerce' ),
				'type'      => 'time',
				'meta_name' => 'alg_crowdfunding_deadline_time',
			),
			'add_to_cart_label_single' => array(
				'desc'      => __( 'Labels: Add to Cart Button Text (Single)', 'crowdfunding-for-woocommerce' ),
				'type'      => 'text',
				'meta_name' => 'alg_crowdfunding_button_label_single',
			),
			'add_to_cart_label_archives' => array(
				'desc'      => __( 'Labels: Add to Cart Button Text (Archive/Category)', 'crowdfunding-for-woocommerce' ),
				'type'      => 'text',
				'meta_name' => 'alg_crowdfunding_button_label_loop',
			),
			'open_pricing_default_price' => array(
				'desc'      => __( 'Open Pricing: Default Price', 'crowdfunding-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'type'      => 'price',
				'meta_name' => 'alg_crowdfunding_product_open_price_default_price',
			),
			'open_pricing_min_price' => array(
				'desc'      => __( 'Open Pricing: Min Price', 'crowdfunding-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'type'      => 'price',
				'meta_name' => 'alg_crowdfunding_product_open_price_min_price',
			),
			'open_pricing_max_price' => array(
				'desc'      => __( 'Open Pricing: Max Price', 'crowdfunding-for-woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'type'      => 'price',
				'meta_name' => 'alg_crowdfunding_product_open_price_max_price',
			),
		) );
	}
}

if ( ! function_exists( 'alg_wc_crdfnd_get_user_campaign_all_fields' ) ) {
	/**
	 * alg_wc_crdfnd_get_user_campaign_all_fields.
	 *
	 * @version 3.0.0
	 * @since   2.3.0
	 */
	function alg_wc_crdfnd_get_user_campaign_all_fields() {
		return array_merge( alg_wc_crdfnd_get_user_campaign_standard_fields(), alg_wc_crdfnd_get_user_campaign_crowdfunding_fields() );
	}
}
