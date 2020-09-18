<?php
/**
 * Crowdfunding for WooCommerce - Product by User Section Settings
 *
 * @version 3.1.6
 * @since   2.3.0
 * @author  Algoritmika Ltd.
 * @author  WP Wham
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Alg_WC_Crowdfunding_Settings_Product_By_User' ) ) :

class Alg_WC_Crowdfunding_Settings_Product_By_User extends Alg_WC_Crowdfunding_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 3.0.0
	 * @since   2.3.0
	 */
	function __construct() {

		$this->id    = 'product_by_user';
		$this->desc  = __( 'User Campaigns', 'crowdfunding-for-woocommerce' );

		parent::__construct();
	}

	/**
	 * add_settings.
	 *
	 * @version 3.1.6
	 * @since   2.3.0
	 */
	public static function get_settings() {

		global $wp_roles;
		$all_roles = ( isset( $wp_roles ) && is_object( $wp_roles ) ) ? $wp_roles->roles : array();
		$all_roles = apply_filters( 'editable_roles', $all_roles );
		$all_roles = array_merge( array(
			'guest' => array(
				'name'         => __( 'Guest', 'crowdfunding-for-woocommerce' ),
				'capabilities' => array(),
			) ), $all_roles );
		$user_roles = array();
		foreach ( $all_roles as $_role_key => $_role ) {
			$user_roles[ $_role_key ] = $_role['name'];
		}

		$fields = alg_wc_crdfnd_get_user_campaign_all_fields();
		$fields_enabled_options  = array();
		$fields_required_options = array();
		$i = 0;
		$total_fields = count( $fields );
		foreach ( $fields as $field_id => $field_data ) {
			$i++;
			$checkboxgroup = '';
			if ( 1 === $i ) {
				$checkboxgroup = 'start';
			} elseif ( $total_fields === $i ) {
				$checkboxgroup = 'end';
			}
			$fields_enabled_options[] = array(
				'title'    => ( ( 1 === $i ) ? __( 'Additional Fields', 'crowdfunding-for-woocommerce' ) : '' ),
				'desc'     => $field_data['desc'],
				'id'       => 'alg_wc_crowdfunding_product_by_user_' . $field_id . '_enabled',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => $checkboxgroup,
			);
			$fields_required_options[] = array(
				'title'    => ( ( 1 === $i ) ? __( 'Is Required', 'crowdfunding-for-woocommerce' ) : '' ),
				'desc'     => $field_data['desc'],
				'id'       => 'alg_wc_crowdfunding_product_by_user_' . $field_id . '_required',
				'default'  => 'no',
				'type'     => 'checkbox',
				'checkboxgroup' => $checkboxgroup,
			);
		}

		$settings = array_merge(
			array(
				array(
					'title'    => __( 'User Campaigns Options', 'crowdfunding-for-woocommerce' ),
					'type'     => 'title',
					'desc'     => sprintf( __( 'Use %s shortcode to add form for your users.', 'crowdfunding-for-woocommerce' ),
						'<code>[product_crowdfunding_add_new_campaign]</code>' ),
					'id'       => 'alg_wc_crowdfunding_product_by_user_options',
				),
				array(
					'type'     => 'sectionend',
					'id'       => 'alg_wc_crowdfunding_product_by_user_options',
				),
				array(
					'title'    => __( 'Form Fields', 'crowdfunding-for-woocommerce' ),
					'type'     => 'title',
					'desc'     => __( '<em>Title</em> field is always enabled and required.', 'crowdfunding-for-woocommerce' ),
					'id'       => 'alg_wc_crowdfunding_product_by_user_fields_options',
				),
			),
			$fields_enabled_options,
			$fields_required_options,
			array(
				array(
					'type'     => 'sectionend',
					'id'       => 'alg_wc_crowdfunding_product_by_user_fields_options',
				),
				array(
					'title'    => __( 'More Options', 'crowdfunding-for-woocommerce' ),
					'type'     => 'title',
					'id'       => 'alg_wc_crowdfunding_product_by_user_more_options',
				),
				array(
					'title'    => __( 'User Visibility', 'crowdfunding-for-woocommerce' ),
					'desc_tip' => __( 'Limit form to selected user roles only. Leave empty to show to all users.', 'crowdfunding-for-woocommerce' ),
					'id'       => 'alg_wc_crowdfunding_product_by_user_user_visibility',
					'default'  => array(),
					'type'     => 'multiselect',
					'class'    => 'chosen_select',
					'options'  => $user_roles,
				),
				array(
					'title'    => __( 'Campaign (Product) Type', 'crowdfunding-for-woocommerce' ),
					'id'       => 'alg_wc_crowdfunding_product_by_user_product_type',
					'default'  => 'simple',
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'options'  => array(
						'simple'       => __( 'Simple', 'crowdfunding-for-woocommerce' ),
						'open_pricing' => __( 'Open Pricing', 'crowdfunding-for-woocommerce' ),
					),
				),
				array(
					'title'    => __( 'Campaign Status', 'crowdfunding-for-woocommerce' ),
					'id'       => 'alg_wc_crowdfunding_product_by_user_status',
					'default'  => 'draft',
					'type'     => 'select',
					'class'    => 'wc-enhanced-select',
					'options'  => get_post_statuses(),
				),
				array(
					'title'    => __( 'Require Unique Title', 'crowdfunding-for-woocommerce' ),
					'desc'     => __( 'Enable', 'crowdfunding-for-woocommerce' ),
					'id'       => 'alg_wc_crowdfunding_product_by_user_require_unique_title',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'Add "Campaigns" Tab to User\'s My Account Page', 'crowdfunding-for-woocommerce' ),
					'desc'     => __( 'Add', 'crowdfunding-for-woocommerce' ),
					'id'       => 'alg_wc_crowdfunding_product_by_user_add_to_my_account',
					'default'  => 'yes',
					'type'     => 'checkbox',
				),
				array(
					'desc'     => __( 'Add Edit Campaign Button', 'crowdfunding-for-woocommerce' ),
					'id'       => 'alg_wc_crowdfunding_product_by_user_add_to_my_account_edit',
					'default'  => 'yes',
					'type'     => 'checkbox',
				),
				array(
					'desc'     => __( 'Add Delete Campaign Button', 'crowdfunding-for-woocommerce' ),
					'id'       => 'alg_wc_crowdfunding_product_by_user_add_to_my_account_delete',
					'default'  => 'yes',
					'type'     => 'checkbox',
				),
				array(
					'title'    => __( 'Message: Campaign Successfully Added', 'crowdfunding-for-woocommerce' ),
					'id'       => 'alg_wc_crowdfunding_product_by_user_message_product_added',
					'default'  => __( '"%product_title%" successfully added!', 'crowdfunding-for-woocommerce' ),
					'type'     => 'text',
					'css'      => 'width:100%;',
				),
				array(
					'title'    => __( 'Message: Campaign Successfully Edited', 'crowdfunding-for-woocommerce' ),
					'id'       => 'alg_wc_crowdfunding_product_by_user_message_product_edited',
					'default'  => __( '"%product_title%" successfully edited!', 'crowdfunding-for-woocommerce' ),
					'type'     => 'text',
					'css'      => 'width:100%;',
				),
				array(
					'title'    => sprintf( __( 'Admin Email: %s', 'crowdfunding-for-woocommerce' ), __( 'Campaign Added', 'crowdfunding-for-woocommerce' ) ),
					'desc'     => __( 'Send', 'crowdfunding-for-woocommerce' ),
					'id'       => 'alg_wc_crowdfunding_product_by_user_email_campaign_added',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'title'    => sprintf( __( 'Admin Email: %s', 'crowdfunding-for-woocommerce' ), __( 'Campaign Edited', 'crowdfunding-for-woocommerce' ) ),
					'desc'     => __( 'Send', 'crowdfunding-for-woocommerce' ),
					'id'       => 'alg_wc_crowdfunding_product_by_user_email_campaign_edited',
					'default'  => 'no',
					'type'     => 'checkbox',
				),
				array(
					'type'     => 'sectionend',
					'id'       => 'alg_wc_crowdfunding_product_by_user_more_options',
				),
			)
		);
		return $settings;
	}
}

endif;

return new Alg_WC_Crowdfunding_Settings_Product_By_User();
