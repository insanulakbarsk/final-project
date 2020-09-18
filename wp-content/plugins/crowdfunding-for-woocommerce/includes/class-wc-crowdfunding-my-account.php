<?php
/**
 * Crowdfunding for WooCommerce - My Account
 *
 * @version 3.0.0
 * @since   2.3.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Crowdfunding_My_Account' ) ) :

class Alg_WC_Crowdfunding_My_Account {

	/**
	 * Constructor.
	 *
	 * @version 3.0.0
	 * @since   2.3.0
	 */
	function __construct() {
		if ( 'yes' === get_option( 'alg_woocommerce_crowdfunding_enabled', 'yes' ) ) {
			if ( 'yes' === get_option( 'alg_wc_crowdfunding_product_by_user_add_to_my_account', 'yes' ) ) {
				add_filter( 'woocommerce_account_menu_items',                      array( $this, 'add_my_products_tab_my_account_page' ) );
				add_action( 'woocommerce_account_crowdfunding-campaigns_endpoint', array( $this, 'add_my_products_content_my_account_page' ) );
				add_filter( 'the_title',                                           array( $this, 'change_my_products_endpoint_title' ) );
			}
		}
	}

	/*
	 * Change endpoint title.
	 *
	 * @version 2.3.1
	 * @since   2.3.1
	 * @param   string $title
	 * @return  string
	 * @see     https://github.com/woocommerce/woocommerce/wiki/2.6-Tabbed-My-Account-page
	 */
	function change_my_products_endpoint_title( $title ) {
		global $wp_query;
		$is_endpoint = isset( $wp_query->query_vars['crowdfunding-campaigns'] );
		if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
			// New page title.
			$title = __( 'Campaigns', 'crowdfunding-for-woocommerce' );
			remove_filter( 'the_title', array( $this, 'change_my_products_endpoint_title' ) );
		}
		return $title;
	}

	/**
	 * Custom help to add new items into an array after a selected item.
	 *
	 * @version 2.3.1
	 * @since   2.3.1
	 * @param   array $items
	 * @param   array $new_items
	 * @param   string $after
	 * @return  array
	 * @see     https://github.com/woocommerce/woocommerce/wiki/2.6-Tabbed-My-Account-page
	 */
	function insert_after_helper( $items, $new_items, $after ) {
		// Search for the item position and +1 since is after the selected item key.
		$position = array_search( $after, array_keys( $items ) ) + 1;
		// Insert the new item.
		$array = array_slice( $items, 0, $position, true );
		$array += $new_items;
		$array += array_slice( $items, $position, count( $items ) - $position, true );
		return $array;
	}

	/**
	 * add_my_products_tab_my_account_page.
	 *
	 * @version 2.3.1
	 * @since   2.3.0
	 */
	function add_my_products_tab_my_account_page( $items ) {
		$user_ID = get_current_user_id();
		if ( 0 != $user_ID ) {
			$args = array(
				'post_type'      => 'product',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'author'         => $user_ID,
				'meta_key'       => '_alg_crowdfunding_enabled',
				'meta_value'     => 'yes',
				'fields'         => 'ids',
			);
			$loop = new WP_Query( $args );
			if ( $loop->found_posts > 0 ) {
				$new_items = array( 'crowdfunding-campaigns' => __( 'Campaigns', 'crowdfunding-for-woocommerce' ) );
				return $this->insert_after_helper( $items, $new_items, 'orders' );
			}
		}
		return $items;
	}

	/**
	 * add_my_products_content_my_account_page.
	 *
	 * @version 3.0.0
	 * @since   2.3.0
	 */
	function add_my_products_content_my_account_page() {
		$user_ID = get_current_user_id();
		if ( 0 == $user_ID ) {
			return;
		}
		// Delete campaign
		if ( isset( $_GET['alg_crowdfunding_delete_product'] ) ) {
			$product_id = $_GET['alg_crowdfunding_delete_product'];
			$post_author_id = get_post_field( 'post_author', $product_id );
			if ( $user_ID != $post_author_id ) {
				echo '<p>' . __( 'Wrong user ID!', 'crowdfunding-for-woocommerce' ) . '</p>';
			} else {
				wp_delete_post( $product_id, true );
			}
		}
		// Edit campaign
		if ( isset( $_GET['alg_crowdfunding_edit_product'] ) ) {
			$product_id = $_GET['alg_crowdfunding_edit_product'];
			$post_author_id = get_post_field( 'post_author', $product_id );
			if ( $user_ID != $post_author_id ) {
				echo '<p>' . __( 'Wrong user ID!', 'crowdfunding-for-woocommerce' ) . '</p>';
			} else {
				echo do_shortcode( '[product_crowdfunding_add_new_campaign product_id="' . $product_id . '"]' );
			}
		}
		// Get user campaigns
		$offset = 0;
		$block_size = 256;
		$products = array();
		while( true ) {
			$args = array(
				'post_type'      => 'product',
				'post_status'    => 'any',
				'posts_per_page' => $block_size,
				'offset'         => $offset,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'author'         => $user_ID,
				'meta_key'       => '_alg_crowdfunding_enabled',
				'meta_value'     => 'yes',
				'fields'         => 'ids',
			);
			$loop = new WP_Query( $args );
			if ( ! $loop->have_posts() ) {
				break;
			}
			foreach ( $loop->posts as $post_id ) {
				$products[ strval( $post_id ) ] = array(
					'title'  => get_the_title( $post_id ),
					'status' => get_post_status( $post_id ),
				);
			}
			$offset += $block_size;
		}
		wp_reset_postdata();
		// Display user campaigns table
		if ( 0 != count( $products ) ) {
			$add_edit_button   = get_option( 'alg_wc_crowdfunding_product_by_user_add_to_my_account_edit', 'yes' );
			$add_delete_button = get_option( 'alg_wc_crowdfunding_product_by_user_add_to_my_account_delete', 'yes' );
			$table_data = array();
			$table_header = array( '', __( 'Status', 'crowdfunding-for-woocommerce' ), __( 'Title', 'crowdfunding-for-woocommerce' ) );
			if ( 'yes' === $add_edit_button || 'yes' === $add_delete_button ) {
				$table_header[] = __( 'Actions', 'crowdfunding-for-woocommerce' );
			}
			$table_data[] = $table_header;
			$i = 0;
			foreach ( $products as $_product_id => $_product_data ) {
				$i++;
				$table_row = array(
					get_the_post_thumbnail( $_product_id, array( 25, 25 ) ),
					'<code>'. $_product_data['status'] . '</code>',
					( 'publish' != $_product_data['status'] ? $_product_data['title'] : '<a href="' . get_permalink( $_product_id ) . '">' . $_product_data['title'] . '</a>' ),
				);
				$actions_cell = array();
				if ( 'yes' === $add_edit_button ) {
					$action_link = add_query_arg( 'alg_crowdfunding_edit_product',   $_product_id, remove_query_arg( array( 'alg_crowdfunding_edit_product_image_delete', 'alg_crowdfunding_delete_product' ) ) );
					$actions_cell[] ='<a class="button" href="' . $action_link . '">' . __( 'Edit', 'crowdfunding-for-woocommerce' ) . '</a>' . ' ';
				}
				if ( 'yes' === $add_delete_button ) {
					$action_link = add_query_arg( 'alg_crowdfunding_delete_product', $_product_id, remove_query_arg( array( 'alg_crowdfunding_edit_product_image_delete', 'alg_crowdfunding_edit_product' ) ) );
					$actions_cell[] = '<a class="button" href="' . $action_link . '" onclick="return confirm(\'' . __( 'Are you sure?', 'crowdfunding-for-woocommerce' ) . '\')">' . __( 'Delete', 'crowdfunding-for-woocommerce' ) . '</a>';
				}
				if ( ! empty( $actions_cell ) ) {
					$actions_cell = implode( ' ', $actions_cell );
					$table_row[] = $actions_cell;
				}
				$table_data[] = $table_row;
			}
			echo alg_wc_crdfnd_get_table_html( $table_data, array( 'table_class' => 'shop_table shop_table_responsive my_account_orders' ) );
		}
	}

}

endif;

return new Alg_WC_Crowdfunding_My_Account();
