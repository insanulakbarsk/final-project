<?php
/**
 * Crowdfunding for WooCommerce - Functions
 *
 * @version 3.0.0
 * @since   2.3.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists( 'alg_wc_crdfnd_get_product_id_or_variation_parent_id' ) ) {
	/**
	 * alg_wc_crdfnd_get_product_id_or_variation_parent_id.
	 *
	 * @version 3.0.0
	 * @since   2.4.0
	 */
	function alg_wc_crdfnd_get_product_id_or_variation_parent_id( $_product ) {
		if ( ! $_product || ! is_object( $_product ) ) {
			return 0;
		}
		return ( version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' ) ?
			$_product->id : ( $_product->is_type( 'variation' ) ? $_product->get_parent_id() : $_product->get_id() ) );
	}
}

if ( ! function_exists( 'alg_wc_crdfnd_get_product_post_status' ) ) {
	/**
	 * alg_wc_crdfnd_get_product_post_status.
	 *
	 * @version 3.0.0
	 * @since   2.4.0
	 */
	function alg_wc_crdfnd_get_product_post_status( $_product ) {
		if ( ! $_product || ! is_object( $_product ) ) {
			return '';
		}
		return ( version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' ) ? $_product->post->post_status : $_product->get_status() );
	}
}

if ( ! function_exists( 'alg_wc_crdfnd_variation_radio_button' ) ) {
	/**
	 * alg_wc_crdfnd_variation_radio_button.
	 *
	 * @version 3.0.0
	 * @since   2.0.0
	 * @todo    [dev] `echo '<small>' . $variation['variation_description'] . '</small>'`
	 */
	function alg_wc_crdfnd_variation_radio_button( $_product, $variation ) {
		$attributes_html = '';
		$variation_attributes_display_values = array();
		$is_checked = true;
		foreach ( $variation['attributes'] as $attribute_full_name => $attribute_value ) {

			$attributes_html .= ' ' . $attribute_full_name . '="' . $attribute_value . '"';

			$attribute_name = $attribute_full_name;
			$prefix = 'attribute_';
			if ( substr( $attribute_full_name, 0, strlen( $prefix ) ) === $prefix ) {
				$attribute_name = substr( $attribute_full_name, strlen( $prefix ) );
			}

			$checked = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ?
				wc_clean( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $_product->get_variation_default_attribute( $attribute_name );
			if ( $checked != $attribute_value ) $is_checked = false;

			$terms = get_terms( $attribute_name );
			foreach ( $terms as $term ) {
				if ( is_object( $term ) && isset( $term->slug ) && $term->slug === $attribute_value && isset( $term->name ) ) {
					$attribute_value = $term->name;
				}
			}

			$variation_attributes_display_values[] = $attribute_value;

		}
		$variation_title = implode( ', ', $variation_attributes_display_values ) . ' (' . wc_price( $variation['display_price'] ) . ')';
		$variation_id    = $variation['variation_id'];
		$is_checked = checked( $is_checked, true, false );

		echo '<p>';
		echo '<input name="alg_variations" type="radio"' . $attributes_html . ' variation_id="' . $variation_id . '"' . $is_checked . '>' . ' ' . $variation_title;
		echo '<br>';
		echo '<small>' . get_post_meta( $variation_id, '_variation_description', true )  . '</small>';
		echo '</p>';
	}
}

if ( ! function_exists( 'alg_wc_crdfnd_get_table_html' ) ) {
	/**
	 * alg_wc_crdfnd_get_table_html.
	 *
	 * @version 3.0.0
	 * @since   2.3.0
	 */
	function alg_wc_crdfnd_get_table_html( $data, $args = array() ) {
		$defaults = array(
			'table_class'        => '',
			'table_style'        => '',
			'table_heading_type' => 'horizontal',
			'columns_classes'    => array(),
			'columns_styles'     => array(),
		);
		$args = array_merge( $defaults, $args );
		$table_class = ( '' == $args['table_class'] ) ? '' : ' class="' . $args['table_class'] . '"';
		$table_style = ( '' == $args['table_style'] ) ? '' : ' style="' . $args['table_style'] . '"';
		$html = '';
		$html .= '<table' . $table_class . $table_style . '>';
		$html .= '<tbody>';
		foreach( $data as $row_number => $row ) {
			$html .= '<tr>';
			foreach( $row as $column_number => $value ) {
				$th_or_td = ( ( 0 === $row_number && 'horizontal' === $args['table_heading_type'] ) || ( 0 === $column_number && 'vertical' === $args['table_heading_type'] ) ) ?
					'th' : 'td';
				$column_class = ( ! empty( $args['columns_classes'] ) && isset( $args['columns_classes'][ $column_number ] ) ) ?
					' class="' . $args['columns_classes'][ $column_number ] . '"' : '';
				$column_style = ( ! empty( $args['columns_styles'] ) && isset( $args['columns_styles'][ $column_number ] ) ) ?
					' style="' . $args['columns_styles'][ $column_number ] . '"' : '';

				$html .= '<' . $th_or_td . $column_class . $column_style . '>';
				$html .= $value;
				$html .= '</' . $th_or_td . '>';
			}
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}
}

if ( ! function_exists( 'alg_wc_crdfnd_get_product_orders_data' ) ) {
	/**
	 * alg_wc_crdfnd_get_product_orders_data.
	 *
	 * @version 3.0.0
	 * @since   2.3.0
	 */
	function alg_wc_crdfnd_get_product_orders_data( $return_value = 'total_orders', $atts ) {
		$product_id = isset( $atts['product_id'] ) ? $atts['product_id'] : get_the_ID();
		if ( ! $product_id ) {
			return '';
		}
		$saved_value = get_post_meta( $product_id, '_' . 'alg_crowdfunding_' . $return_value, true );
		if ( '' === $saved_value || 'manual' === get_option( 'alg_crowdfunding_products_data_update', 'fifthteen' ) ) {
			$calculated_value = alg_wc_crdfnd_calculate_product_orders_data( $return_value, $product_id );
			update_post_meta( $product_id, '_' . 'alg_crowdfunding_' . $return_value, $calculated_value );
			$return = $calculated_value;
		} else {
			$return = $saved_value;
		}
		if ( isset( $atts['starting_offset'] ) && 0 != $atts['starting_offset'] ) {
			$return += $atts['starting_offset'];
		}
		return $return;
	}
}

if ( ! function_exists( 'alg_wc_crdfnd_calculate_product_orders_data' ) ) {
	/**
	 * alg_wc_crdfnd_calculate_product_orders_data.
	 *
	 * @version 3.0.0
	 * @since   1.0.0
	 * @todo    [dev] `$item['line_tax']`
	 */
	function alg_wc_crdfnd_calculate_product_orders_data( $return_value = 'total_orders', $product_id ) {
		// Get product
		$the_product = wc_get_product( $product_id );
		if ( ! $the_product ) {
			return;
		}
		$is_wc_version_below_3_0_0 = version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' );
		// Calculate data
		$total_orders     = 0;
		$total_qty        = 0;
		$total_sum        = 0;
		$backers          = array();
		$order_statuses   = get_option( 'alg_woocommerce_crowdfunding_order_statuses', array( 'wc-completed' ) );
		$product_ids      = ( $the_product->is_type( 'grouped' ) ? $the_product->get_children() : array( $product_id ) );
		$date_query_after = trim( get_post_meta( $product_id, '_' . 'alg_crowdfunding_startdate', true ) . ' ' .
			get_post_meta( $product_id, '_' . 'alg_crowdfunding_starttime', true ), ' ' );
		$offset           = 0;
		$block_size       = 512;
		while ( true ) {
			$args = array(
				'post_type'      => 'shop_order',
				'post_status'    => $order_statuses,
				'posts_per_page' => $block_size,
				'offset'         => $offset,
				'orderby'        => 'date',
				'order'          => 'ASC',
				'date_query'     => array(
					array(
						'after'     => $date_query_after,
						'inclusive' => true,
					),
				),
				'fields'         => 'ids',
			);
			$loop = new WP_Query( $args );
			if ( ! $loop->have_posts() ) {
				break;
			}
			foreach ( $loop->posts as $order_id ) {
				$the_order  = wc_get_order( $order_id );
				$the_items  = $the_order->get_items();
				$item_found = false;
				foreach( $the_items as $item ) {
					if ( in_array( $item['product_id'], $product_ids ) ) {
						$sum        = $item['line_total'] + $item['line_tax'];
						$total_sum += $sum;
						$total_qty += $item['qty'];
						$backers[]  = array(
							'first_name'       => ( $is_wc_version_below_3_0_0 ? $the_order->billing_first_name : $the_order->get_billing_first_name() ),
							'last_name'        => ( $is_wc_version_below_3_0_0 ? $the_order->billing_last_name : $the_order->get_billing_last_name() ),
							'sum'              => $sum,
							'qty'              => $item['qty'],
							'order_id'         => $order_id,
							'order_date'       => strtotime( ( $is_wc_version_below_3_0_0 ? $the_order->post->post_date : $the_order->get_date_created() ) ),
							'order_currency'   => ( $is_wc_version_below_3_0_0 ? $the_order->get_order_currency() : $the_order->get_currency() ),
						);
						$item_found = true;
					}
				}
				if ( $item_found ) {
					$total_orders++;
				}
			}
			$offset += $block_size;
		}
		// Return result
		switch ( $return_value ) {
			case 'orders_sum':
				return $total_sum;
			case 'total_items':
				return $total_qty;
			case 'total_orders':
				return $total_orders;
			case 'backers':
				return $backers;
			default: // 'all'
				return array( 'orders_sum' => $total_sum, 'total_items' => $total_qty, 'total_orders' => $total_orders, 'backers' => $backers );
		}
	}
}

if ( ! function_exists( 'alg_wc_crdfnd_count_crowdfunding_products' ) ) {
	/**
	 * alg_wc_crdfnd_count_crowdfunding_products.
	 *
	 * @version 3.0.0
	 * @since   2.0.0
	 */
	function alg_wc_crdfnd_count_crowdfunding_products( $post_id ) {
		$args = array(
			'post_type'      => 'product',
			'post_status'    => array( 'any', 'trash' ),
			'posts_per_page' => 3,
			'meta_key'       => '_alg_crowdfunding_enabled',
			'meta_value'     => 'yes',
			'post__not_in'   => array( $post_id ),
			'fields'         => 'ids',
		);
		$loop = new WP_Query( $args );
		return $loop->found_posts;
	}
}

if ( ! function_exists( 'alg_wc_crdfnd_calculate_and_update_product_orders_data' ) ) {
	/**
	 * alg_wc_crdfnd_calculate_and_update_product_orders_data.
	 *
	 * @version 3.0.0
	 * @since   2.6.0
	 * @todo    [dev] Emails: customizable recipient(s), subject, content
	 * @todo    [dev] (maybe) Emails: optional wrap in WC template
	 */
	function alg_wc_crdfnd_calculate_and_update_product_orders_data( $product_id ) {
		$product_orders_data = alg_wc_crdfnd_calculate_product_orders_data( 'all', $product_id );
		update_post_meta( $product_id, '_' . 'alg_crowdfunding_' . 'orders_sum',   $product_orders_data['orders_sum'] );
		update_post_meta( $product_id, '_' . 'alg_crowdfunding_' . 'total_orders', $product_orders_data['total_orders'] );
		update_post_meta( $product_id, '_' . 'alg_crowdfunding_' . 'total_items',  $product_orders_data['total_items'] );
		update_post_meta( $product_id, '_' . 'alg_crowdfunding_' . 'backers',      $product_orders_data['backers'] );
		update_post_meta( $product_id, '_' . 'alg_crowdfunding_' . 'products_data_updated_time', time() );
		// Actions
		do_action( 'alg_wc_crowdfunding_campaign_orders_data_calculated', $product_id, $product_orders_data );
		if (
			function_exists( 'alg_wc_crowdfunding' ) && function_exists( 'wc_get_product' ) &&
			! alg_wc_crowdfunding()->core->is_active( wc_get_product( $product_id ) )
		) {
			do_action( 'alg_wc_crowdfunding_campaign_not_active', $product_id, $product_orders_data );
			if ( 'yes' != get_post_meta( $product_id, '_' . 'alg_crowdfunding_campaign_ended', true ) ) {
				update_post_meta( $product_id, '_' . 'alg_crowdfunding_campaign_ended', 'yes' );
				do_action( 'alg_wc_crowdfunding_campaign_ended', $product_id, $product_orders_data );
				// Emails
				if ( 'yes' === get_option( 'alg_wc_crowdfunding_email_campaign_added', 'no' ) ) {
					$email_heading = __( 'Crowdfunding Campaign Ended', 'crowdfunding-for-woocommerce' );
					$subject = sprintf( str_replace( '{site_title}', wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ), '[{site_title}]: %s' ), $email_heading );
					$content = sprintf( __( 'Crowdfunding campaign ended: %s', 'crowdfunding-for-woocommerce' ),
						admin_url( 'post.php?post=' . $product_id . '&action=edit' ) );
					wc_mail( get_option( 'admin_email' ), $subject, alg_wc_crdfnd_wrap_in_wc_email_template( $content, $email_heading ) );
				}
			}
		}
	}
}

if ( ! function_exists( 'alg_wc_crdfnd_wrap_in_wc_email_template' ) ) {
	/**
	 * alg_wc_crdfnd_wrap_in_wc_email_template.
	 *
	 * @version 3.0.0
	 * @since   2.9.0
	 */
	function alg_wc_crdfnd_wrap_in_wc_email_template( $content, $email_heading = '' ) {
		return alg_wc_crdfnd_get_wc_email_part( 'header', $email_heading ) .
			$content .
		str_replace( '{site_title}', wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ), alg_wc_crdfnd_get_wc_email_part( 'footer' ) );
	}
}

if ( ! function_exists( 'alg_wc_crdfnd_get_wc_email_part' ) ) {
	/**
	 * alg_wc_crdfnd_get_wc_email_part.
	 *
	 * @version 3.0.0
	 * @since   2.9.0
	 */
	function alg_wc_crdfnd_get_wc_email_part( $part, $email_heading = '' ) {
		ob_start();
		switch ( $part ) {
			case 'header':
				wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
				break;
			case 'footer':
				wc_get_template( 'emails/email-footer.php' );
				break;
		}
		return ob_get_clean();
	}
}
