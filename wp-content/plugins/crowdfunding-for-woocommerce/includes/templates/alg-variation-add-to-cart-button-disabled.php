<?php
/**
 * Single variation cart button - Disabled
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @version 3.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
$_product_id = alg_wc_crdfnd_get_product_id_or_variation_parent_id( $product );
?>
<div class="woocommerce-variation-add-to-cart variations_button">
	<?php if ( ! $product->is_sold_individually() ) : ?>
		<?php woocommerce_quantity_input( array( 'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 ) ); ?>
	<?php endif; ?>
	<button type="submit" class="single_add_to_cart_button button alt disabled"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
	<input type="hidden" name="add-to-cart" value="<?php echo absint( $_product_id ); ?>" />
	<input type="hidden" name="product_id" value="<?php echo absint( $_product_id ); ?>" />
	<input type="hidden" name="variation_id" class="variation_id" value="0" />
</div>
