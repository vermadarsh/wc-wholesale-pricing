<?php
/**
 * The Template for displaying wholesale products of this vendor.
 *
 * @package Wc_Wholesale_Pricing
 * @subpackage Wc_Wholesale_Pricing/public/templates
 */

$store_user = get_userdata( get_query_var( 'author' ) );
get_header( 'shop' );

/**
 * WooCommerce before main content action.
 */
do_action( 'woocommerce_before_main_content' );
?>
<div id="dokan-primary" class="dokan-single-store dokan-w8">
	<div id="dokan-content" class="store-review-wrap woocommerce" role="main">
		<?php dokan_get_template_part( 'store-header' ); ?>
		<div id="wholesale-products-container">
			<?php echo do_shortcode( '[wcwp_wholesale_ordering author="' . $store_user->ID . '"]' ); ?>
		</div>
	</div><!-- #content .site-content -->
</div><!-- #primary .content-area -->

<?php
/**
 * WooCommerce after main content action.
 */
do_action( 'woocommerce_after_main_content' );

get_footer();
