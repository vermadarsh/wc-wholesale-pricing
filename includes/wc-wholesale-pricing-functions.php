<?php
/**
 * This file is used for writing all the re-usable custom functions.
 *
 * @since 1.0.0
 * @package Wc_Wholesale_Pricing
 * @subpackage Wc_Wholesale_Pricing/includes
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Get available user roles.
 *
 * @return array
 */
function wcwp_get_user_roles() {
	global $wp_roles;
	$roles = $wp_roles->roles;

	if ( empty( $roles ) ) {
		return array();
	}

	$user_roles = array();

	// Loop in through the available roles.
	foreach ( $roles as $role_index => $role ) {
		$user_roles[ $role_index ] = $role['name'];
	}

	// Add the site-visitor role.
	$user_roles['non-loggedin'] = apply_filters( 'wcwp_non_loggedin_role_title', __( 'Non-loggedin', 'wc-wholesale-pricing' ) );

	return $user_roles;
}

/**
 * Get allowed user roles for the wholesale pricing.
 *
 * @return array
 */
function wcwp_get_allowed_user_roles() {
	$allowed_roles = get_option( 'wcwp_wholesale_price_allowed_user_roles' );

	if ( empty( $allowed_roles ) || false === $allowed_roles ) {
		return array();
	}

	return $allowed_roles;
}

/**
 * Return the wholesale notice on product details page.
 *
 * @return string
 */
function wcwp_wholesale_notice_on_product_page() {
	$notice = get_option( 'wcwp_wholesale_notice_on_product_page' );

	if ( empty( $notice ) || false === $notice ) {
		return '';
	}

	return $notice;
}

/**
 * Return the wholesale notice title on cart item.
 *
 * @return string
 */
function wcwp_get_cart_item_notice_title() {
	$notice_title = get_option( 'wcwp_cart_item_notice_title' );

	if ( empty( $notice_title ) || false === $notice_title ) {
		return __( 'Wholesale Notice', 'wc-wholesale-pricing' );
	}

	return $notice_title;
}

/**
 * Return whether the wholesale tab to be shown on store single.
 *
 * @return boolean
 */
function wcwp_show_wholesale_tab_store_single() {
	$show_tab = get_option( 'wcwp_show_wholesale_tab_store_single' );

	if ( empty( $show_tab ) || false === $show_tab || 'no' === $show_tab ) {
		return false;
	}

	return true;
}

/**
 * Return whether the wholesale notice to be shown for cart items.
 *
 * @return boolean
 */
function wcwp_show_wholesale_notice_cart_item() {
	$show_notice = get_option( 'wcwp_show_wholesale_notice_cart_item' );

	if ( empty( $show_notice ) || false === $show_notice || 'no' === $show_notice ) {
		return false;
	}

	return true;
}

/**
 * Return the wholesale notice message on cart item.
 *
 * @return string
 */
function wcwp_get_cart_item_notice_message() {
	$notice_message = get_option( 'wcwp_cart_item_notice_message' );

	if ( empty( $notice_message ) || false === $notice_message ) {
		return __( 'Add [quantity] unit(s) more of this product to avail the wholesale price of [price].', 'wc-wholesale-pricing' );
	}

	return $notice_message;
}

/**
 * Return the whlesale min. quantity field.
 *
 * @param int $product_id Holds the product ID.
 * @param int $loop Holds the variation ID loop index.
 * @return string
 */
function wcwp_get_wholesale_minimum_quantity_field( $product_id, $loop = -1 ) {
	$min_qty_field_attributes = array(
		'id'                => ( -1 !== $loop ) ? "_wholesale_minimum_quantity_{$loop}" : '_wholesale_minimum_quantity',
		'name'              => ( -1 !== $loop ) ? "_wholesale_minimum_quantity[$loop]" : '_wholesale_minimum_quantity',
		'type'              => 'number',
		'value'             => get_post_meta( $product_id, 'wcwp_wholesale_minimum_quantity', true ),
		'description'       => __( 'This sets the min. quantity to be purchased to avail wholesale pricing option.', 'wc-wholesale-pricing' ),
		'desc_tip'          => true,
		'label'             => __( 'Wholesale Minimum Quantity', 'woocommerce' ),
		'wrapper_class'     => ( -1 !== $loop ) ? 'form-row form-row-first' : '',
		'custom_attributes' => array(
			'min'  => 1,
			'step' => 1,
		),
	);
	$min_qty_field            = woocommerce_wp_text_input( apply_filters( 'wcwp_wholesale_min_quantity_field_attributes', $min_qty_field_attributes, $product_id, $loop ) );

	return $min_qty_field;
}

/**
 * Return the whlesale price field.
 *
 * @param int $product_id Holds the product ID.
 * @param int $loop Holds the variation ID loop index.
 * @return string
 */
function wcwp_get_wholesale_price_field( $product_id, $loop = -1 ) {
	$price_field_attributes = array(
		'id'                => ( -1 !== $loop ) ? "_wholesale_price_{$loop}" : '_wholesale_price',
		'name'              => ( -1 !== $loop ) ? "_wholesale_price[$loop]" : '_wholesale_price',
		'type'              => 'number',
		'value'             => get_post_meta( $product_id, 'wcwp_wholesale_price', true ),
		'description'       => __( 'This sets the wholesale price for this product.', 'wc-wholesale-pricing' ),
		'desc_tip'          => true,
		'label'             => __( 'Wholesale Price', 'woocommerce' ),
		'wrapper_class'     => ( -1 !== $loop ) ? 'form-row form-row-last' : '',
		'custom_attributes' => array(
			'min'  => 0.01,
			'step' => 0.01,
		),
	);
	$price_field            = woocommerce_wp_text_input( apply_filters( 'wcwp_wholesale_min_quantity_field_attributes', $price_field_attributes, $product_id, $loop ) );

	return $price_field;
}

/**
 * Returns the attributes that should be ignored while displaying number type fields.
 *
 * @return array
 */
function wcwp_wp_kses_number_ignore_fields() {
	return array(
		'input' => array(
			'id'    => array(),
			'type'  => array(),
			'value' => array(),
			'min'   => array(),
			'step'  => array(),
		),
	);
}

/**
 * Function to decide, which of the product IDs to be considered.
 *
 * @param int $product_id Holds the product ID.
 * @param int $variation_id Holds the variation ID.
 * @return int
 */
function wcwp_product_id( $product_id, $variation_id ) {

	return ( 0 !== $variation_id ) ? $variation_id : $product_id;
}

/**
 * Get the wholesale price of the product, if is set.
 *
 * @param int $prod_id Holds the product ID.
 * @return boolean|array
 */
function wcwp_get_wholesale_price( $prod_id ) {

	if ( empty( $prod_id ) || ! is_int( $prod_id ) ) {
		return false;
	}

	$wholesale_min_qty = get_post_meta( $prod_id, 'wcwp_wholesale_minimum_quantity', true );
	$wholesale_price   = get_post_meta( $prod_id, 'wcwp_wholesale_price', true );

	// Return if the min. quantity is not available.
	if ( empty( $wholesale_min_qty ) || false === $wholesale_min_qty ) {
		return false;
	}

	// Return if the wholesale price is not available.
	if ( empty( $wholesale_price ) || false === $wholesale_price ) {
		return false;
	}

	return array(
		'min_qty' => (int) $wholesale_min_qty,
		'price'   => (float) $wholesale_price,
	);
}

/**
 * Return the wholesale notice by product ID.
 *
 * @param array $wholesale_data Holds the wholesale product data.
 * @return string
 */
function wcwp_get_product_wholesale_notice_html( $wholesale_data ) {
	$notice = wcwp_wholesale_notice_on_product_page();
	$notice = str_replace( '[min_quantity]', $wholesale_data['min_qty'], $notice );
	$notice = str_replace( '[price]', wc_price( $wholesale_data['price'] ), $notice );
	ob_start();
	?>
	<div class="woocommerce-message wcwp-wholesale-notice" role="alert">
		<p><?php echo wp_kses_post( nl2br( $notice ) ); ?></p>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Check if the wholesale price is available for the current user.
 *
 * @return boolean
 */
function wcwp_is_wholesale_available() {
	$allowed_roles = wcwp_get_allowed_user_roles();

	if ( empty( $allowed_roles ) ) {
		return true;
	}

	if ( is_user_logged_in() ) {
		// Check for the current user role.
		$current_user       = get_user_by( 'id', get_current_user_id() );
		$current_user_roles = ( ! empty( $current_user->roles ) ) ? $current_user->roles : array();

		// Return if the current user roles in empty.
		if ( empty( $current_user_roles ) ) {
			return false;
		}

		// If there's no common role, means the wholesale price is not allowed to the current user.
		$common_roles = array_intersect( $allowed_roles, $current_user_roles );

		// Hence, disallow.
		if ( empty( $common_roles ) ) {
			return false;
		}

		return true;
	} else {
		// Check if visitor is allowed for the wholesale price.
		if ( in_array( 'non-loggedin', $allowed_roles, true ) ) {
			return true;
		}
	}
}

/**
 * Return available wholesale products.
 *
 * @param int $posts_per_page Holds the posts per page.
 * @param int $author Holds the author ID.
 * @return array
 */
function wcwp_get_wholesale_products_list( $posts_per_page, $author ) {
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	$args = array(
		'posts_per_page' => $posts_per_page,
		'paged'          => $paged,
		'post_type'      => array( 'product', 'product_variation' ),
		'post_status'    => 'publish',
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => 'wcwp_wholesale_minimum_quantity',
				'compare' => 'EXISTS',
			),
			array(
				'key'     => 'wcwp_wholesale_price',
				'compare' => 'EXISTS',
			),
		),
	);

	// Add author if available.
	if ( false !== $author ) {
		$args['author'] = $author;
	}

	/**
	 * Wholesale products arguments filter.
	 *
	 * This filter helps to modify the arguments for retreiving wholesale products.
	 *
	 * @param array $args Holds the posts arguments.
	 * @return array
	 */
	$args = apply_filters( 'wcwp_wholesale_products_args', $args );

	return new WP_Query( $args );
}

/**
 * Return the html when there are no wholesale products available.
 *
 * @return string
 */
function wcwp_get_no_wholesale_products_html() {
	ob_start();
	?>
	<p class="woocommerce-info">
		<?php
		echo esc_html(
			apply_filters(
				'wcwp_no_wholesale_products_products_avaiability_message',
				__(
					'There\'s no wholesale product available.',
					'wc-wholesale-pricing'
				)
			)
		);
		?>
	</p>
	<?php
	return ob_get_clean();
}

/**
 * Return the HTML markup for the wholesale products table.
 *
 * @param array $products Holds the products array.
 * @return string
 */
function wcwp_get_wholesale_ordering_form_html( $products ) {
	$wholesale_ordering_table_cols = apply_filters(
		'wcwp_wholesale_ordering_table_columns',
		array(
			'thumbnail'            => array(
				'class' => 'product-thumbnail',
				'text'  => '&nbsp;',
			),
			'item-name'            => array(
				'class' => 'product-name',
				'text'  => __( 'Product', 'wc-wholesale-pricing' ),
			),
			'item-wholesale-price' => array(
				'class' => 'product-wholesale-price',
				'text'  => __( 'Wholesale Price', 'wc-wholesale-pricing' ),
			),
			'item-stock'           => array(
				'class' => 'product-stock',
				'text'  => __( 'Stock', 'wc-wholesale-pricing' ),
			),
			'item-quantity'        => array(
				'class' => 'product-quantity',
				'text'  => __( 'Quantity', 'wc-wholesale-pricing' ),
			),
			'item-actions'         => array(
				'class' => 'product-actions',
				'text'  => __( '#', 'wc-wholesale-pricing' ),
			),
		)
	);
	ob_start();
	?>
	<table class="wcwp-wholesale-ordering-table shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
		<thead>
		<tr>
			<?php
			if ( ! empty( $wholesale_ordering_table_cols ) && is_array( $wholesale_ordering_table_cols ) ) {
				foreach ( $wholesale_ordering_table_cols as $col ) {
					?>
					<th class="<?php echo esc_attr( $col['class'] ); ?>"><?php echo esc_html( $col['text'] ); ?></th>
					<?php
				}
			}
			?>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ( $products as $product ) {
			$product_id = $product->ID;
			$wc_product = wc_get_product( $product_id );
			?>
			<tr class="woocommerce-cart-form__cart-item cart_item" data-id="<?php echo esc_attr( $product_id ); ?>">
			<?php
			foreach ( $wholesale_ordering_table_cols as $col_index => $col ) {
				?>
				<td class="<?php echo esc_attr( $col['class'] ); ?>">
				<?php
				switch ( $col_index ) {
					case 'thumbnail':
						$attach_id = get_post_thumbnail_id( $product_id );
						$thumbnail = wcpe_get_image_src_by_id( $attach_id );
						?>
						<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>">
							<img width="324" height="324"
								src="<?php echo esc_url( $thumbnail ); ?>"
								class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail"
								alt="">
						</a>
						<?php
						break;

					case 'item-name':
						echo wp_kses_post( apply_filters( 'wcwp_wholesale_item_name', '<a href="' . esc_url( get_permalink( $product_id ) ) . '">' . esc_html( get_the_title( $product_id ) ) . '</a><br />', $product_id ) );
						echo wp_kses_post( apply_filters( 'wcpe_wholesale_item_cost', $wc_product->get_price_html(), $product_id ) );
						break;

					case 'item-wholesale-price':
						$wholesale_data = wcwp_get_wholesale_price( $product_id );
						$notice         = wcwp_wholesale_notice_on_product_page();
						$notice         = str_replace( '[min_quantity]', $wholesale_data['min_qty'], $notice );
						$notice         = str_replace( '[price]', wc_price( $wholesale_data['price'] ), $notice );
						echo wp_kses_post( $notice );
						break;

					case 'item-stock':
						echo wp_kses_post( wcwp_get_product_stock_status_message( $wc_product ) );
						break;

					case 'item-quantity':
						woocommerce_quantity_input(
							array(
								'input_name'  => "product-{$product_id}-quantity",
								'input_value' => 1, 
								'min_value'   => 1,
								'placeholder' => 0,
							)
						);
						break;

					case 'item-actions':
						?>
						<button type="button" class="single_add_to_cart_button wcwp-add-to-cart-wholesale-product"><?php echo esc_html( apply_filters( 'wcwp_wholesale_ordering_product_add_to_cart_button_text', __( 'Add to cart', 'wc-wholesale-pricing' ) ) ); ?></button>
						<?php
						break;

					default:
						echo wp_kses_post( apply_filters( "wcwp_wholesale_product_{$col_index}_data", '', $$product_id ) );
						break;
				}
				?>
				</td>
			<?php } ?>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<?php
	return ob_get_clean();
}

/**
 * Returns the image src by attachment ID.
 *
 * @param int $img_id Holds the attachment ID.
 * @return boolean|string
 */
function wcpe_get_image_src_by_id( $img_id ) {

	if ( empty( $img_id ) ) {
		return wc_placeholder_img_src();
	}

	return wp_get_attachment_url( $img_id );
}

/**
 * Get product stock status.
 *
 * @param object $wc_product Holds the WooCommerce product object.
 * @return array
 */
function wcwp_get_product_stock_status( $wc_product ) {
	// Get stock management.
	$manage_stock = $wc_product->get_manage_stock();

	if ( true === $manage_stock ) {
		$prod_backorders = $wc_product->get_backorders();
		$prod_stock      = $wc_product->get_stock_quantity();

		if ( 'yes' === $prod_backorders || 'notify' === $prod_backorders ) {
			$stock_text              = ( 'yes' === $prod_backorders ) ? __( 'In Stock', 'wc-wholesale-pricing' ) : __( 'Available on backorder', 'wc-wholesale-pricing' );
			$prod_stock_class        = 'product-in-stock backorders-allowed';
			$font_awesome_icon_class = 'fa-smile-o';
		} elseif ( $prod_stock > 0 ) {
			$stock_text              = __( 'In Stock', 'wc-wholesale-pricing' );
			$prod_stock_class        = 'product-in-stock backorders-not-allowed';
			$font_awesome_icon_class = 'fa-smile-o';
		} else {
			$stock_text              = __( 'Not In Stock', 'wc-wholesale-pricing' );
			$prod_stock_class        = 'product-not-in-stock';
			$font_awesome_icon_class = 'fa-frown-o';
		}
	} else {
		$prod_stock_status = $wc_product->get_stock_status();
		if ( 'instock' === $prod_stock_status || 'onbackorder' === $prod_stock_status ) {
			$stock_text              = __( 'In Stock', 'wc-wholesale-pricing' );
			$prod_stock_class        = 'product-in-stock';
			$font_awesome_icon_class = 'fa-smile-o';
		} else {
			$stock_text              = __( 'Not In Stock', 'wc-wholesale-pricing' );
			$prod_stock_class        = 'product-not-in-stock';
			$font_awesome_icon_class = 'fa-frown-o';
		}
	}

	return apply_filters(
		'wcwp_product_stock_status',
		array(
			'stock_text'              => $stock_text,
			'prod_stock_class'        => $prod_stock_class,
			'font_awesome_icon_class' => $font_awesome_icon_class,
		),
		$wc_product
	);
}
/**
 * Get product stock status string.
 *
 * @param object $wc_product Holds the WooCommerce product object.
 * @return string
 */
function wcwp_get_product_stock_status_message( $wc_product ) {
	$stock_status            = wcwp_get_product_stock_status( $wc_product );
	$stock_text              = ( ! empty( $stock_status['stock_text'] ) ) ? $stock_status['stock_text'] : '';
	$prod_stock_class        = ( ! empty( $stock_status['prod_stock_class'] ) ) ? $stock_status['prod_stock_class'] : '';
	$font_awesome_icon_class = ( ! empty( $stock_status['font_awesome_icon_class'] ) ) ? $stock_status['font_awesome_icon_class'] : '';
	$stock_status_message    = '<span class="' . $prod_stock_class . '"><i class="fa ' . $font_awesome_icon_class . '"></i>&nbsp;' . $stock_text . '</span>';

	/**
	 * Stock status message filter.
	 *
	 * This filter helps to modify the stock status message that comes with a smiley.
	 *
	 * @param string $stock_status_message Holds the stock status message.
	 * @param array  $stock_status Holds the stock status details.
	 * @param object $wc_product Holds the WooCommerce product object.
	 * @return string
	 */
	return apply_filters( 'wcwp_product_stock_status_message', $stock_status_message, $stock_status, $wc_product );
}
