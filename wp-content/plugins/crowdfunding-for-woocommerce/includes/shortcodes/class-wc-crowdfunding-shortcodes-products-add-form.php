<?php
/**
 * Crowdfunding for WooCommerce - Products Add Form Shortcodes
 *
 * @version 3.0.0
 * @since   2.3.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Alg_WC_Crowdfunding_Products_Add_Form_Shortcodes' ) ) :

class Alg_WC_Crowdfunding_Products_Add_Form_Shortcodes {

	/**
	 * Constructor.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 * @todo    [dev] refill image on not validated (or after successful addition)?
	 * @todo    [dev] more messages options
	 * @todo    [dev] more styling options
	 * @todo    [dev] custom fields (#11843)
	 * @todo    [dev] variable products
	 * @todo    [dev] user selection of product type (simple, variable, open pricing)
	 */
	function __construct() {
		add_shortcode( 'product_crowdfunding_add_new_campaign', array( $this, 'alg_wc_crowdfunding_product_add_new' ) );
	}

	/**
	 * init.
	 *
	 * @version 3.0.0
	 * @since   2.3.0
	 */
	function init() {

		$this->standard_fields     = alg_wc_crdfnd_get_user_campaign_standard_fields();
		$this->crowdfunding_fields = alg_wc_crdfnd_get_user_campaign_crowdfunding_fields();
		$this->fields              = alg_wc_crdfnd_get_user_campaign_all_fields();

		$this->the_atts = array(
			'product_id'  => 0,
			'post_status' => get_option( 'alg_wc_crowdfunding_product_by_user_status', 'draft' ),
			'visibility'  => get_option( 'alg_wc_crowdfunding_product_by_user_user_visibility', array() ),
			'module'      => 'product_by_user',
			'module_name' => __( 'Product by User', 'crowdfunding-for-woocommerce' ),
		);

		foreach ( $this->fields as $field_id => $field_data ) {
			$this->the_atts[ $field_id . '_enabled' ]  = get_option( 'alg_wc_crowdfunding_product_by_user_' . $field_id . '_enabled',  'no' );
			$this->the_atts[ $field_id . '_required' ] = get_option( 'alg_wc_crowdfunding_product_by_user_' . $field_id . '_required', 'no' );
		}
	}

	/**
	 * wc_add_new_product.
	 *
	 * @version 3.0.0
	 * @since   2.3.0
	 * @todo    [dev] Emails: customizable recipient(s), subject, content
	 * @todo    [dev] (maybe) Emails: optional wrap in WC template
	 */
	function wc_add_new_product( $args, $shortcode_atts ) {

		$product_post = array(
			'post_title'    => $args['title'],
			'post_content'  => $args['desc'],
			'post_excerpt'  => $args['short_desc'],
			'post_type'     => 'product',
			'post_status'   => 'draft',
		);

		$is_update = true;
		if ( 0 == $shortcode_atts['product_id'] ) {
			$is_update  = false;
			$product_id = wp_insert_post( $product_post );
		} else {
			$product_id = $shortcode_atts['product_id'];
			wp_update_post( array_merge( array( 'ID' => $product_id ), $product_post ) );
		}

		// Emails
		if (
			( ! $is_update && 'yes' === get_option( 'alg_wc_crowdfunding_product_by_user_email_campaign_added', 'no' ) ) ||
			(   $is_update && 'yes' === get_option( 'alg_wc_crowdfunding_product_by_user_email_campaign_edited', 'no' ) )
		) {
			$email_heading = ( $is_update ?
				__( 'Crowdfunding Campaign Edited', 'crowdfunding-for-woocommerce' ) :
				__( 'Crowdfunding Campaign Added', 'crowdfunding-for-woocommerce' ) );
			$subject = sprintf( str_replace( '{site_title}', wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ), '[{site_title}]: %s' ), $email_heading );
			$content = sprintf( ( $is_update ?
				__( 'Crowdfunding campaign edited: %s', 'crowdfunding-for-woocommerce' ) :
				__( 'Crowdfunding campaign added: %s', 'crowdfunding-for-woocommerce' ) ),
					admin_url( 'post.php?post=' . $product_id . '&action=edit' ) );
			wc_mail( get_option( 'admin_email' ), $subject, alg_wc_crdfnd_wrap_in_wc_email_template( $content, $email_heading ) );
		}

		// Insert the post into the database
		if ( 0 != $product_id ) {

			wp_set_object_terms( $product_id, 'simple', 'product_type' );
			wp_set_object_terms( $product_id, $args['cats'], 'product_cat' );
			wp_set_object_terms( $product_id, $args['tags'], 'product_tag' );

			update_post_meta( $product_id, '_regular_price', $args['regular_price'] );
			update_post_meta( $product_id, '_sale_price',    $args['sale_price'] );
			update_post_meta( $product_id, '_price',         ( '' == $args['sale_price'] ? $args['regular_price'] : $args['sale_price'] ) );
			update_post_meta( $product_id, '_visibility',    'visible' );
			update_post_meta( $product_id, '_stock_status',  'instock' );

			$c = alg_wc_crdfnd_count_crowdfunding_products( $product_id ) + 1;
			update_post_meta( $product_id, '_' . 'alg_crowdfunding_enabled', ( $c >= apply_filters( 'alg_crowdfunding_option', 4, 'count' ) ? 'no' : 'yes' ) );
			update_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_sum',                         $args['goal'] );
			update_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_backers',                     $args['goal_backers'] );
			update_post_meta( $product_id, '_' . 'alg_crowdfunding_goal_items',                       $args['goal_items'] );
			update_post_meta( $product_id, '_' . 'alg_crowdfunding_startdate',                        $args['start_date'] );
			update_post_meta( $product_id, '_' . 'alg_crowdfunding_starttime',                        $args['start_time'] );
			update_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline',                         $args['end_date'] );
			update_post_meta( $product_id, '_' . 'alg_crowdfunding_deadline_time',                    $args['end_time'] );
			update_post_meta( $product_id, '_' . 'alg_crowdfunding_button_label_single',              $args['add_to_cart_label_single'] );
			update_post_meta( $product_id, '_' . 'alg_crowdfunding_button_label_loop',                $args['add_to_cart_label_archives'] );
			if ( 'open_pricing' === get_option( 'alg_wc_crowdfunding_product_by_user_product_type', 'simple' ) ) {
				update_post_meta( $product_id, '_' . 'alg_crowdfunding_product_open_price_enabled', 'yes' );
			}
			update_post_meta( $product_id, '_' . 'alg_crowdfunding_product_open_price_default_price', $args['open_pricing_default_price'] );
			update_post_meta( $product_id, '_' . 'alg_crowdfunding_product_open_price_min_price',     $args['open_pricing_min_price'] );
			update_post_meta( $product_id, '_' . 'alg_crowdfunding_product_open_price_max_price',     $args['open_pricing_max_price'] );

			do_action( 'alg_crowdfunding_user_campaign_save_fields', $product_id, $args );

			// Image
			if ( '' != $args['image'] && '' != $args['image']['tmp_name'] ) {
				$upload_dir = wp_upload_dir();
				$filename = $args['image']['name'];
				$file = ( wp_mkdir_p( $upload_dir['path'] ) ) ? $upload_dir['path'] : $upload_dir['basedir'];
				$file .= '/' . $filename;

				move_uploaded_file( $args['image']['tmp_name'], $file );

				$wp_filetype = wp_check_filetype( $filename, null );
				$attachment = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_title'     => sanitize_file_name( $filename ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				);
				$attach_id = wp_insert_attachment( $attachment, $file, $product_id );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
				wp_update_attachment_metadata( $attach_id, $attach_data );

				set_post_thumbnail( $product_id, $attach_id );
			}

			wp_update_post( array( 'ID' => $product_id, 'post_status' => $shortcode_atts['post_status'] ) );
		}

		return $product_id;
	}

	/**
	 * validate_args.
	 *
	 * @version 2.3.0
	 * @since   2.3.0
	 */
	function validate_args( $args, $shortcode_atts ) {
		$errors = '';
		if ( '' == $args['title'] ) {
			$errors .= '<li>' . __( 'Title is required!', 'crowdfunding-for-woocommerce' ) . '</li>';
		}

		if ( 'yes' === get_option( 'alg_wc_crowdfunding_product_by_user_require_unique_title', 'no' ) && 0 == $shortcode_atts['product_id'] ) {
			if ( ! function_exists( 'post_exists' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/post.php' );
			}
			if ( post_exists( $args['title'] ) ) {
				$errors .= '<li>' . __( 'Campaign with this title already exists!', 'crowdfunding-for-woocommerce' ) . '</li>';
			}
		}

		foreach ( $this->fields as $field_id => $field_data ) {
			if (  'yes' === $shortcode_atts[ $field_id . '_enabled' ] && 'yes' === $shortcode_atts[ $field_id . '_required' ] ) {
				$is_missing = false;
				if ( 'image' === $field_id && ( '' == $args[ $field_id ] || ! isset( $args[ $field_id ]['tmp_name'] ) || '' == $args[ $field_id ]['tmp_name'] ) ) {
					$is_missing = true;
				} elseif ( empty( $args[ $field_id ] ) ) {
					$is_missing = true;
				}
				if ( $is_missing ) {
					$errors .= '<li>' . sprintf( __( '%s is required!', 'crowdfunding-for-woocommerce' ), $field_data['desc'] ) . '</li>';
				}
			}
		}

		if ( $args['sale_price'] > $args['regular_price'] ) {
			$errors .= '<li>' . __( 'Sale price must be less than the regular price!', 'crowdfunding-for-woocommerce' ) . '</li>';
		}
		return ( '' === $errors ) ? true : $errors;
	}

	/**
	 * is_user_role.
	 *
	 * @version 2.4.0
	 * @since   2.3.2
	 * @return  bool
	 */
	function is_user_role( $user_role, $user_id = 0 ) {
		$the_user = ( 0 == $user_id ) ? wp_get_current_user() : get_user_by( 'id', $user_id );
		if ( ! isset( $the_user->roles ) || empty( $the_user->roles ) ) {
			$the_user->roles = array( 'guest' );
		}
		return ( isset( $the_user->roles ) && is_array( $the_user->roles ) && in_array( $user_role, $the_user->roles ) );
	}

	/**
	 * alg_wc_crowdfunding_product_add_new.
	 *
	 * @version 3.0.0
	 * @since   2.3.0
	 * @todo    [dev] `multipart` only if image...
	 * @todo    [dev] (maybe) `if ( ! $is_iser_visibility_ok ) { return '<p><a href="' . wp_login_url( get_permalink() ) . '" title="' . $atts['login_text'] . '">' . $atts['login_text'] . '</a></p>'; }`
	 */
	function alg_wc_crowdfunding_product_add_new( $atts ) {

		$this->init();

		$atts = wp_parse_args( $atts, $this->the_atts );

		// Check if privileges are ok
		if ( ! empty( $atts['visibility'] ) ) {
			$is_iser_visibility_ok = false;
			foreach ( $atts['visibility'] as $visibility ) {
				if ( 'admin' === $visibility ) {
					$visibility = 'administrator';
				}
				if ( $this->is_user_role( $visibility ) ) {
					$is_iser_visibility_ok = true;
					break;
				}
			}
			if ( ! $is_iser_visibility_ok ) {
				if ( ! is_user_logged_in() ) {
					ob_start();
					woocommerce_login_form();
					return ob_get_clean();
				} else {
					return '<p>' . __( 'Wrong user role!', 'crowdfunding-for-woocommerce' ) . '</p>';
				}
			}
		}

		$header_html       = '';
		$notice_html       = '';
		$input_fields_html = '';
		$footer_html       = '';

		$args = array(
			'title' => isset( $_REQUEST['alg_wc_crowdfunding_add_new_product_title'] ) ? $_REQUEST['alg_wc_crowdfunding_add_new_product_title'] : '',
			'cats'  => isset( $_REQUEST['alg_wc_crowdfunding_add_new_product_cats'] )  ? $_REQUEST['alg_wc_crowdfunding_add_new_product_cats']  : array(),
			'tags'  => isset( $_REQUEST['alg_wc_crowdfunding_add_new_product_tags'] )  ? $_REQUEST['alg_wc_crowdfunding_add_new_product_tags']  : array(),
			'image' => isset( $_FILES['alg_wc_crowdfunding_add_new_product_image'] )   ? $_FILES['alg_wc_crowdfunding_add_new_product_image']   : '',
		);
		foreach ( $this->fields as $field_id => $field_data ) {
			if ( ! isset( $args[ $field_id ] ) ) {
				$args[ $field_id ] = isset( $_REQUEST[ 'alg_wc_crowdfunding_add_new_product_' . $field_id ] ) ? $_REQUEST[ 'alg_wc_crowdfunding_add_new_product_' . $field_id ] : '';
			}
		}

		if ( isset( $_REQUEST['alg_wc_crowdfunding_add_new_product'] ) ) {
			if ( true === ( $validate_args = $this->validate_args( $args, $atts ) ) ) {
				$result = $this->wc_add_new_product( $args, $atts );
				if ( 0 == $result ) {
					// Error
					$notice_html .= '<div class="woocommerce"><ul class="woocommerce-error"><li>' . __( 'Error!', 'crowdfunding-for-woocommerce' ) . '</li></ul></div>';
				} else {
					// Success
					if ( 0 == $atts['product_id'] ) {
						$notice_html .= '<div class="woocommerce"><div class="woocommerce-message">' .
							str_replace(
								'%product_title%',
								$args['title'],
								get_option( 'alg_wc_crowdfunding_product_by_user_message_product_added', __( '"%product_title%" successfully added!', 'crowdfunding-for-woocommerce' ) ) ) .
							'</div></div>';
					} else {
						$notice_html .= '<div class="woocommerce"><div class="woocommerce-message">' .
							str_replace(
								'%product_title%',
								$args['title'],
								get_option( 'alg_wc_crowdfunding_product_by_user_message_product_edited', __( '"%product_title%" successfully edited!', 'crowdfunding-for-woocommerce' ) ) ) .
							'</div></div>';
					}
				}
			} else {
				$notice_html .= '<div class="woocommerce"><ul class="woocommerce-error">' . $validate_args . '</ul></div>';
			}
		}

		if ( isset( $_GET['alg_wc_crowdfunding_edit_product_image_delete'] ) ) {
			$product_id = $_GET['alg_wc_crowdfunding_edit_product_image_delete'];
			$post_author_id = get_post_field( 'post_author', $product_id );
			$user_ID = get_current_user_id();
			if ( $user_ID != $post_author_id ) {
				echo '<p>' . __( 'Wrong user ID!', 'crowdfunding-for-woocommerce' ) . '</p>';
			} else {
				$image_id = get_post_thumbnail_id( $product_id );
				wp_delete_post( $image_id, true );
			}
		}

		if ( 0 != $atts['product_id'] ) {
			$this->the_product = wc_get_product( $atts['product_id'] );
		}

		$header_html .= '<h3>';
		$header_html .= ( 0 == $atts['product_id'] ) ? __( 'Add New Product', 'crowdfunding-for-woocommerce' ) : __( 'Edit Product', 'crowdfunding-for-woocommerce' );
		$header_html .= '</h3>';
		$header_html .= '<form method="post" action="' . remove_query_arg( array( 'alg_wc_crowdfunding_edit_product_image_delete', 'alg_wc_crowdfunding_delete_product' ) ) . '" enctype="multipart/form-data">';

		$required_mark_html_template = '&nbsp;<abbr class="required" title="required">*</abbr>';

		$table_data = array();
		$input_style = 'width:100%;';
		$table_data[] = array(
			'<label for="alg_wc_crowdfunding_add_new_product_title">' . __( 'Title', 'crowdfunding-for-woocommerce' ) . $required_mark_html_template . '</label>',
			'<input required type="text" style="' . $input_style . '" id="alg_wc_crowdfunding_add_new_product_title" name="alg_wc_crowdfunding_add_new_product_title" value="' . ( ( 0 != $atts['product_id'] ) ? $this->the_product->get_title() : $args['title'] ) . '">'
		);
		if ( 'yes' === $atts['desc_enabled'] ) {
			$required_html      = ( 'yes' === $atts['desc_required'] ) ? ' required' : '';
			$required_mark_html = ( 'yes' === $atts['desc_required'] ) ? $required_mark_html_template : '';
			$table_data[] = array(
				'<label for="alg_wc_crowdfunding_add_new_product_desc">' . __( 'Description', 'crowdfunding-for-woocommerce' ) . $required_mark_html . '</label>',
				'<textarea' . $required_html . ' style="' . $input_style . '" id="alg_wc_crowdfunding_add_new_product_desc" name="alg_wc_crowdfunding_add_new_product_desc">' . ( ( 0 != $atts['product_id'] ) ? get_post_field( 'post_content', $atts['product_id'] ) : $args['desc'] ) . '</textarea>'
			);
		}
		if ( 'yes' === $atts['short_desc_enabled'] ) {
			$required_html      = ( 'yes' === $atts['short_desc_required'] ) ? ' required' : '';
			$required_mark_html = ( 'yes' === $atts['short_desc_required'] ) ? $required_mark_html_template : '';
			$table_data[] = array(
				'<label for="alg_wc_crowdfunding_add_new_product_short_desc">' . __( 'Short Description', 'crowdfunding-for-woocommerce' ) . $required_mark_html . '</label>',
				'<textarea' . $required_html . ' style="' . $input_style . '" id="alg_wc_crowdfunding_add_new_product_short_desc" name="alg_wc_crowdfunding_add_new_product_short_desc">' . ( ( 0 != $atts['product_id'] ) ? get_post_field( 'post_excerpt', $atts['product_id'] ) : $args['short_desc'] ) . '</textarea>'
			);
		}
		if ( 'yes' === $atts['image_enabled'] ) {
			$required_html      = ( 'yes' === $atts['image_required'] ) ? ' required' : '';
			$required_mark_html = ( 'yes' === $atts['image_required'] ) ? $required_mark_html_template : '';
			$new_image_field = '<input' . $required_html . ' type="file" id="alg_wc_crowdfunding_add_new_product_image" name="alg_wc_crowdfunding_add_new_product_image" accept="image/*">';
			if ( 0 != $atts['product_id'] ) {
				$the_field = ( '' == get_post_thumbnail_id( $atts['product_id'] ) ) ?
					$new_image_field :
					'<a href="' . add_query_arg( 'alg_wc_crowdfunding_edit_product_image_delete', $atts['product_id'] ) . '" onclick="return confirm(\'' . __( 'Are you sure?', 'crowdfunding-for-woocommerce' ) . '\')">' . __( 'Delete', 'crowdfunding-for-woocommerce' ) . '</a><br>' . get_the_post_thumbnail( $atts['product_id'], array( 50, 50 ) , array( 'class' => 'alignleft' ) );
			} else {
				$the_field = $new_image_field;
			}
			$table_data[] = array(
				'<label for="alg_wc_crowdfunding_add_new_product_image">' . __( 'Image', 'crowdfunding-for-woocommerce' ) . $required_mark_html . '</label>',
				$the_field
			);
		}
		if ( 'yes' === $atts['regular_price_enabled'] ) {
			$required_html      = ( 'yes' === $atts['regular_price_required'] ) ? ' required' : '';
			$required_mark_html = ( 'yes' === $atts['regular_price_required'] ) ? $required_mark_html_template : '';
			$table_data[] = array(
				'<label for="alg_wc_crowdfunding_add_new_product_regular_price">' . __( 'Regular Price', 'crowdfunding-for-woocommerce' ) . $required_mark_html . '</label>',
				'<input' . $required_html . ' type="number" min="0" step="0.01" id="alg_wc_crowdfunding_add_new_product_regular_price" name="alg_wc_crowdfunding_add_new_product_regular_price" value="' . ( ( 0 != $atts['product_id'] ) ? get_post_meta( $atts['product_id'], '_regular_price', true ) : $args['regular_price'] ) . '">'
			);
		}
		if ( 'yes' === $atts['sale_price_enabled'] ) {
			$required_html      = ( 'yes' === $atts['sale_price_required'] ) ? ' required' : '';
			$required_mark_html = ( 'yes' === $atts['sale_price_required'] ) ? $required_mark_html_template : '';
			$table_data[] = array(
				'<label for="alg_wc_crowdfunding_add_new_product_sale_price">' . __( 'Sale Price', 'crowdfunding-for-woocommerce' ) . $required_mark_html . '</label>',
				'<input' . $required_html . ' type="number" min="0" step="0.01" id="alg_wc_crowdfunding_add_new_product_sale_price" name="alg_wc_crowdfunding_add_new_product_sale_price" value="' . ( ( 0 != $atts['product_id'] ) ? get_post_meta( $atts['product_id'], '_sale_price', true ) : $args['sale_price'] ) . '">'
			);
		}
		if ( 'yes' === $atts['cats_enabled'] ) {
			$required_html      = ( 'yes' === $atts['cats_required'] ) ? ' required' : '';
			$required_mark_html = ( 'yes' === $atts['cats_required'] ) ? $required_mark_html_template : '';
			$current_product_categories = ( 0 != $atts['product_id'] ) ? get_the_terms( $atts['product_id'], 'product_cat' ) : $args['cats'];
			$product_categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
			$product_categories_as_select_options = '';
			foreach ( $product_categories as $product_category ) {
				$selected = '';
				if ( ! empty( $current_product_categories ) ) {
					foreach ( $current_product_categories as $current_product_category ) {
						if ( is_object( $current_product_category ) ) {
							$current_product_category = $current_product_category->slug;
						}
						$selected .= selected( $current_product_category, $product_category->slug, false );
					}
				}
				$product_categories_as_select_options .= '<option value="' . $product_category->slug . '" ' . $selected . '>' . $product_category->name .'</option>';
			}
			$table_data[] = array(
				'<label for="alg_wc_crowdfunding_add_new_product_cats">' . __( 'Categories', 'crowdfunding-for-woocommerce' ) . $required_mark_html . '</label>',
				'<select' . $required_html . ' multiple style="' . $input_style . '" id="alg_wc_crowdfunding_add_new_product_cats" name="alg_wc_crowdfunding_add_new_product_cats[]">' . $product_categories_as_select_options . '</select>'
			);
		}
		if ( 'yes' === $atts['tags_enabled'] ) {
			$required_html      = ( 'yes' === $atts['tags_required'] ) ? ' required' : '';
			$required_mark_html = ( 'yes' === $atts['tags_required'] ) ? $required_mark_html_template : '';
			$current_product_tags = ( 0 != $atts['product_id'] ) ? get_the_terms( $atts['product_id'], 'product_tag' ) : $args['tags'];
			$products_tags = get_terms( 'product_tag', 'orderby=name&hide_empty=0' );
			$products_tags_as_select_options = '';
			foreach ( $products_tags as $products_tag ) {
				$selected = '';
				if ( ! empty( $current_product_tags ) ) {
					foreach ( $current_product_tags as $current_product_tag ) {
						if ( is_object( $current_product_tag ) ) {
							$current_product_tag = $current_product_tag->slug;
						}
						$selected .= selected( $current_product_tag, $products_tag->slug, false );
					}
				}
				$products_tags_as_select_options .= '<option value="' . $products_tag->slug . '" ' . $selected . '>' . $products_tag->name .'</option>';
			}
			$table_data[] = array(
				'<label for="alg_wc_crowdfunding_add_new_product_tags">' . __( 'Tags', 'crowdfunding-for-woocommerce' ) . $required_mark_html . '</label>',
				'<select' . $required_html . ' multiple style="' . $input_style . '" id="alg_wc_crowdfunding_add_new_product_tags" name="alg_wc_crowdfunding_add_new_product_tags[]">' . $products_tags_as_select_options . '</select>'
			);
		}

		foreach ( $this->crowdfunding_fields as $field_id => $field_data ) {
			$table_data = $this->maybe_add_field_row(
				$field_id,
				$field_data,
				$field_data['type'],
				$field_data['desc'],
				$field_data['meta_name'],
				$atts,
				$args,
				$table_data
			);
		}

		$input_fields_html .= alg_wc_crdfnd_get_table_html( $table_data, array( 'table_class' => 'widefat', 'table_heading_type' => 'vertical', ) );

		$footer_html .= '<input type="submit" class="button" name="alg_wc_crowdfunding_add_new_product" value="' . ( ( 0 == $atts['product_id'] ) ? __( 'Add', 'crowdfunding-for-woocommerce' ) : __( 'Edit', 'crowdfunding-for-woocommerce' ) ) . '">';
		$footer_html .= '</form>';

		return $notice_html . $header_html . $input_fields_html . $footer_html;
	}

	/**
	 * maybe_add_field_row.
	 *
	 * @version 2.3.3
	 * @since   2.3.0
	 * @todo    [dev] `required_mark_html_template`
	 */
	function maybe_add_field_row( $field_id, $field_data, $type, $field_desc, $meta_name, $atts, $args, $table_data ) {
		if ( 'yes' === $atts[ $field_id . '_enabled' ] ) {
			$required_mark_html_template = '&nbsp;<abbr class="required" title="required">*</abbr>';
			$required_html      = ( 'yes' === $atts[ $field_id . '_required' ] ) ? ' required' : '';
			$required_mark_html = ( 'yes' === $atts[ $field_id . '_required' ] ) ? $required_mark_html_template : '';
			$value = ( ( 0 != $atts['product_id'] ) ? get_post_meta( $atts['product_id'], '_' . $meta_name, true ) : $args[ $field_id ] );
			$custom_attributes = '';
			switch ( $type ) {
				case 'price':
					$type = 'number';
					$custom_attributes = ' min="0" step="0.01"';
					break;
				case 'number';
					$custom_attributes = ' min="0"';
					break;
				case 'checkbox';
					$custom_attributes = checked( $value, 'yes', false );
					break;
				case 'date';
					$type = 'text';
					$custom_attributes = ' display="alg_crowdfunding_date"';
					break;
				case 'time';
					$type = 'text';
					$custom_attributes = ' display="alg_crowdfunding_time"';
					break;
			}
			if ( 'select' === $type ) {
				$the_field = '<select' . $required_html .
					' id="alg_wc_crowdfunding_add_new_product_' . $field_id . '"' .
					' name="alg_wc_crowdfunding_add_new_product_' . $field_id . '">';
				foreach ( $field_data['options'] as $option_key => $option_name ) {
					$the_field .= '<option value="' . $option_key . '" ' . selected( $option_key, $value, false ) . '>' . $option_name . '</option>';
				}
				$the_field .= '</select>';
			} else {
				$the_field = '<input' . $required_html .
					' type="' . $type . '"' . $custom_attributes .
					' id="alg_wc_crowdfunding_add_new_product_' . $field_id . '"' .
					' name="alg_wc_crowdfunding_add_new_product_' . $field_id . '"' .
					' value="' . $value . '">';
			}
			$table_data[] = array(
				'<label for="alg_wc_crowdfunding_add_new_product_' . $field_id . '">' . $field_desc . $required_mark_html . '</label>',
				$the_field,
			);
		}
		return $table_data;
	}
}

endif;

return new Alg_WC_Crowdfunding_Products_Add_Form_Shortcodes();
