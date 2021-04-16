<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://codersux.com
 * @since      1.0.0
 *
 * @package    Wc_Wholesale_Pricing
 * @subpackage Wc_Wholesale_Pricing/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wc_Wholesale_Pricing
 * @subpackage Wc_Wholesale_Pricing/public
 * @author     CodersUx <info@codersux.com>
 */
class Wc_Wholesale_Pricing_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function wcwp_wp_enqueue_scripts_callback() {
		// Enqueue font awesome.
		wp_enqueue_style(
			$this->plugin_name . '-font-awesome',
			WCWP_PLUGIN_URL . 'public/css/font-awesome.min.css',
			array(),
			filemtime( WCWP_PLUGIN_PATH . 'public/css/font-awesome.min.css' ),
			'all'
		);

		// Enqueue style.
		wp_enqueue_style(
			$this->plugin_name,
			WCWP_PLUGIN_URL . 'public/css/wc-wholesale-pricing-public.css',
			array(),
			filemtime( WCWP_PLUGIN_PATH . 'public/css/wc-wholesale-pricing-public.css' )
		);

		// Enqueue script.
		wp_enqueue_script(
			$this->plugin_name,
			WCWP_PLUGIN_URL . 'public/js/wc-wholesale-pricing-public.js',
			array( 'jquery' ),
			filemtime( WCWP_PLUGIN_PATH . 'public/js/wc-wholesale-pricing-public.js' ),
			true
		);

		// Localize jquery scripts variables.
		wp_localize_script(
			$this->plugin_name,
			'WCWP_Public_JS_Obj',
			array(
				'ajaxurl'                     => admin_url( 'admin-ajax.php' ),
				'invalid_product_id'          => apply_filters( 'wcwp_invalid_product_id_error', __( 'Invalid product ID.', 'wc-wholesale-pricing' ) ),
				'invalid_product_quantity'    => apply_filters( 'wcwp_invalid_product_quantity_error', __( 'Invalid product quantity.', 'wc-wholesale-pricing' ) ),
				'wcwp_ajax_nonce'             => wp_create_nonce( 'wcwp-ajax-nonce' ),
				'processing_btn_txt'          => apply_filters( 'wcwp_processing_button_text', __( 'Processing...', 'wc-wholesale-pricing' ) ),
				'notification_success_header' => apply_filters( 'wcwp_notification_success_header', __( 'Success', 'wc-wholesale-pricing' ) ),
				'notification_error_header'   => apply_filters( 'wcwp_notification_error_header', __( 'Error', 'wc-wholesale-pricing' ) ),
				'ajax_nonce_failure'          => apply_filters( 'wcwp_ajax_nonce_failure_error', __( 'Action couldn\'t be taken due to security failure. Please try again later.', 'wc-wholesale-pricing' ) ),
			)
		);
	}

	/**
	 * Add custom data to the cart item when free product is added to the cart.
	 *
	 * @param array $cart_item_data Holds the cart item data.
	 * @param int   $product_id Holds the product ID.
	 * @param int   $variation_id Holds the variation ID.
	 * @return array
	 */
	public function wcwp_woocommerce_add_cart_item_data_callback( $cart_item_data, $product_id, $variation_id ) {
		// Check if wholesale prices are available for this user.
		$is_wholesale_available = wcwp_is_wholesale_available();

		// Return if the wholesale prices is not available.
		if ( ! $is_wholesale_available ) {
			return;
		}

		// Process further to get the wholesale data of the product.
		$prod_id         = wcwp_product_id( $product_id, $variation_id );
		$wholesale_price = wcwp_get_wholesale_price( $prod_id );

		if ( false === $wholesale_price ) {
			return $cart_item_data;
		}

		$cart_item_data['wcwp_has_wholesale_price'] = true;

		return $cart_item_data;
	}

	/**
	 * Update the cart totals when the product is added to the cart.
	 *
	 * @param object $cart_obj Holds the complete cart object.
	 */
	public function wcwp_woocommerce_before_calculate_totals_callback( $cart_obj ) {

		// Return if in case the request goes the wrong way.
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		// Check if wholesale prices are available for this user.
		$is_wholesale_available = wcwp_is_wholesale_available();

		// Return if the wholesale prices is not available.
		if ( ! $is_wholesale_available ) {
			return;
		}

		// Process further to set the wholesale prices.
		foreach ( $cart_obj->get_cart() as $cart_item ) {
			$prod_id         = wcwp_product_id( $cart_item['product_id'], $cart_item['variation_id'] );
			$wholesale_price = wcwp_get_wholesale_price( $prod_id );

			if ( true === $cart_item['wcwp_has_wholesale_price'] && $cart_item['quantity'] >= $wholesale_price['min_qty'] ) {
				// Set the price in cart.
				$cart_item['data']->set_price( $wholesale_price['price'] );
			}
		}
	}

	/**
	 * Show the offer message on product details page.
	 *
	 * @return void
	 */
	public function wcwp_woocommerce_before_add_to_cart_form_callback() {
		$product_id = get_the_ID();

		// Check if wholesale prices are available for this user.
		$is_wholesale_available = wcwp_is_wholesale_available();

		// Return if the wholesale prices is not available.
		if ( ! $is_wholesale_available ) {
			return;
		}

		// Get the wholesale price.
		$wholesale_price = wcwp_get_wholesale_price( $product_id );

		// Return, if the wholesale price is not available on this product.
		if ( false === $wholesale_price ) {
			return;
		}

		// Show the notice now.
		$show_wholesale_notice = apply_filters( 'wcwp_show_wholesale_notice_simple_product_details_page', true, $product_id, $wholesale_price );

		if ( $show_wholesale_notice ) {
			echo wp_kses_post( wcwp_get_product_wholesale_notice_html( $wholesale_price ) );
		}
	}

	/**
	 * Return the variation description adding the offer html.
	 *
	 * @param array  $data Holds the variation data array.
	 * @param object $variable_product Holds the variable product object.
	 * @param object $variation Holds the variation object.
	 * @return array
	 */
	public function wcwp_woocommerce_available_variation_callback( $data, $variable_product, $variation ) {
		$variation_id = $variation->get_id();

		// Check if wholesale prices are available for this user.
		$is_wholesale_available = wcwp_is_wholesale_available();

		// Return if the wholesale prices is not available.
		if ( ! $is_wholesale_available ) {
			return;
		}

		// Get the wholesale price.
		$wholesale_price = wcwp_get_wholesale_price( $variation_id );

		// Return, if the wholesale price is not available on this product.
		if ( false === $wholesale_price ) {
			return;
		}

		// Show the notice now.
		$show_wholesale_notice = apply_filters( 'wcwp_show_wholesale_notice_variable_product_details_page', true, $variation_id, $wholesale_price );

		if ( $show_wholesale_notice ) {
			$wholesale_notice_html          = wcwp_get_product_wholesale_notice_html( $wholesale_price );
			$data['variation_description'] .= $wholesale_notice_html;
		}

		return $data;
	}

	/**
	 * Add wholesale pricing options when editing products from dokan dashboard.
	 *
	 * @param object $post Holds the post object.
	 */
	public function wcwp_dokan_product_edit_after_pricing_callback( $post ) {
		echo wp_kses(
			'<div class="show_if_simple dokan-clearfix wcwp-wholesale-pricing-options">',
			array(
				'div' => array(
					'class' => array(),
				),
			)
		);
		echo $this->wcwp_dokan_wholesale_pricing_options( $post->ID );
		echo wp_kses_post( '</div>' );
	}

	/**
	 * Add wholesale pricing options when a new product is added from dokan dashboard.
	 */
	public function wcwp_dokan_new_product_after_product_tags_callback() {
		echo $this->wcwp_dokan_wholesale_pricing_options();
	}

	/**
	 * Render the HTML markup for dokan wholesale pricing options.
	 *
	 * @param int $post_id Holds the post ID.
	 * @return string
	 */
	private function wcwp_dokan_wholesale_pricing_options( $post_id = -1 ) {
		$wholesale_min_qty = (int) filter_input( INPUT_POST, '_wholesale_min_qty', FILTER_SANITIZE_NUMBER_INT );
		$wholesale_price   = (float) filter_input( INPUT_POST, '_wholesale_price', FILTER_SANITIZE_NUMBER_FLOAT );

		// Get min. quantity.
		if ( empty( $wholesale_min_qty ) && -1 !== $post_id ) {
			$wholesale_min_qty = get_post_meta( $post_id, 'wcwp_wholesale_minimum_quantity', true );
		}

		// Get wholesale price.
		if ( empty( $wholesale_price ) && -1 !== $post_id ) {
			$wholesale_price = get_post_meta( $post_id, 'wcwp_wholesale_price', true );
		}
		ob_start();
		?>
		<div class="dokan-form-group dokan-clearfix dokan-wholesale-price-container">
			<div class="content-half-part wholesale-min-quantity">
				<label for="_wholesale_min_qty" class="form-label"><?php esc_html_e( 'Wholesale Min. Quantity', 'wc-wholesale-pricing' ); ?></label>
				<div class="dokan-input-group">
					<span class="dokan-input-group-addon"><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></span>
					<?php
					if ( function_exists( 'dokan_post_input_box' ) ) {
						dokan_post_input_box(
							$post_id,
							'_wholesale_min_qty',
							array(
								'class'       => 'dokan-product-wholesale-min-qty',
								'placeholder' => 0,
								'value'       => $wholesale_min_qty,
								'min'         => 1,
								'step'        => 1,
							),
							'number'
						);
					}
					?>
				</div>
			</div>
			<div class="content-half-part wholesale-price">
				<label for="_wholesale_price" class="form-label">
					<?php esc_html_e( 'Wholesale Price', 'wc-wholesale-pricing' ); ?>
				</label>
				<div class="dokan-input-group">
					<span class="dokan-input-group-addon"><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></span>
					<?php
					if ( function_exists( 'dokan_post_input_box' ) ) {
						dokan_post_input_box(
							$post_id,
							'_wholesale_price',
							array(
								'class'       => 'dokan-product-wholesale-price',
								'placeholder' => '0.00',
								'value'       => $wholesale_price,
								'min'         => 0.01,
								'step'        => 0.01,
							),
							'number'
						);
					}
					?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Save wholesale price options when any vendor adds product to the store.
	 *
	 * @param int $product_id Holds the product ID.
	 */
	public function wcwp_dokan_new_product_added_callback( $product_id ) {
		$this->wcwp_update_dokan_wholesale_pricing_options( $product_id, 'add' );
	}

	/**
	 * Save wholesale price options when any vendor updates product to the store.
	 *
	 * @param int $product_id Holds the product ID.
	 */
	public function wcwp_dokan_product_updated_callback( $product_id ) {
		$this->wcwp_update_dokan_wholesale_pricing_options( $product_id, 'update' );
	}

	/**
	 * Update the wholesale price options when any product is added/updated at store from dokan dashboard.
	 *
	 * @param int    $product_id Holds the product ID.
	 * @param string $action Holds the action string.
	 */
	private function wcwp_update_dokan_wholesale_pricing_options( $product_id, $action ) {
		$wholesale_min_qty = (int) filter_input( INPUT_POST, '_wholesale_min_qty', FILTER_SANITIZE_NUMBER_INT );
		$wholesale_price   = (float) filter_input( INPUT_POST, '_wholesale_price', FILTER_SANITIZE_NUMBER_FLOAT );

		// Update wholesale min. quantity.
		if ( ! empty( $wholesale_min_qty ) ) {
			update_post_meta( $product_id, 'wcwp_wholesale_minimum_quantity', $wholesale_min_qty );
		} else {
			// Delete the meta if the wholesale prices are empty.
			if ( 'update' === $action ) {
				delete_post_meta( $product_id, 'wcwp_wholesale_minimum_quantity' );
			}
		}

		// Update wholesale price.
		if ( ! empty( $wholesale_price ) ) {
			update_post_meta( $product_id, 'wcwp_wholesale_price', $wholesale_price );
		} else {
			// Delete the meta if the wholesale prices are empty.
			if ( 'update' === $action ) {
				delete_post_meta( $product_id, 'wcwp_wholesale_price' );
			}
		}
	}

	/**
	 * Add custom data to the cart item data.
	 *
	 * @param array $item_data Holds the item data.
	 * @param array $cart_item_data Holds the cart item data.
	 * @return array
	 */
	public function wcwp_woocommerce_get_item_data_callback( $item_data, $cart_item_data ) {
		// Check if the cart notice is enabled.
		$show_notice = wcwp_show_wholesale_notice_cart_item();

		if ( false === $show_notice ) {
			return $item_data;
		}

		// Get the item wholesale data.
		$product_id      = $cart_item_data['product_id'];
		$variation_id    = $cart_item_data['variation_id'];
		$cart_quantity   = $cart_item_data['quantity'];
		$prod_id         = wcwp_product_id( $product_id, $variation_id );
		$wholesale_price = wcwp_get_wholesale_price( $prod_id );

		if ( $cart_quantity < $wholesale_price['min_qty'] ) {
			$quantity_needed_more = $wholesale_price['min_qty'] - $cart_quantity;

			// Notice info parts.
			$notice_title   = wcwp_get_cart_item_notice_title();
			$notice_message = wcwp_get_cart_item_notice_message();
			$notice_message = str_replace( '[quantity]', $quantity_needed_more, $notice_message );
			$notice_message = str_replace( '[price]', wc_price( $wholesale_price['price'] ), $notice_message );
			$item_data[]    = array(
				'key'   => $notice_title,
				'value' => $notice_message,
			);

			/**
			 * Cart item wholesale data filter.
			 *
			 * This filter helps to modify the cart item notice for the products that can help the customer to avail wholesale pricing.
			 *
			 * @param array $item_data Holds the wholesale item data.
			 * @param array $cart_item_data Holds the cart item data.
			 * @return array
			 */
			$item_data = apply_filters( 'wcwp_wholesale_item_data', $item_data, $cart_item_data );
		}

		return $item_data;
	}

	/**
	 * Add some footer content.
	 */
	public function wcwp_wp_footer_callback() {
		ob_start();
		?>
		<div class="wcwp_notification_popup">
			<span class="wcwp_notification_close"></span>
			<div class="wcwp_notification_icon"><i class="fa" aria-hidden="true"></i></div>
			<div class="wcwp_notification_message">
				<h3 class="title"></h3>
				<p class="message"></p>
			</div>
		</div>
		<?php
		echo wp_kses_post( ob_get_clean() );
	}

	/**
	 * AJAX served to add item to cart.
	 */
	public function wcwp_add_to_cart_callback() {
		$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );

		if ( empty( $action ) || 'add_to_cart' !== $action ) {
			echo 0;
			wp_die();
		}

		// Check ajax nonce.
		$ajax_nonce = filter_input( INPUT_POST, 'wcwp_ajax_nonce', FILTER_SANITIZE_STRING );

		if ( ! wp_verify_nonce( $ajax_nonce, 'wcwp-ajax-nonce' ) ) {
			echo -1;
			wp_die();
		}

		// Get the posted data.
		$product_id = (int) filter_input( INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT );
		$quantity   = (int) filter_input( INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT );

		if ( empty( $product_id ) || empty( $quantity ) || ! is_int( $product_id ) || ! is_int( $quantity ) ) {
			echo 0;
			wp_die();
		}

		/**
		 * Before adding product to cart.
		 *
		 * This hook fires right before adding product to the cart.
		 *
		 * @param int $product_id Holds the product ID.
		 * @param int $quantity Holds the product quantity.
		 */
		do_action( 'wcwp_before_add_to_cart', $product_id, $quantity );

		// Add the product to cart now.
		WC()->cart->add_to_cart( $product_id, $quantity );

		/**
		 * After adding product to cart.
		 *
		 * This hook fires right after adding product to the cart.
		 *
		 * @param int $product_id Holds the product ID.
		 * @param int $quantity Holds the product quantity.
		 */
		do_action( 'wcwp_after_add_to_cart', $product_id, $quantity );

		wp_send_json_success(
			array(
				'code'                 => 'wcwp-product-added-to-cart',
				/* translators: 1: %s: product title, 2: %d: product quantity */
				'notification_message' => sprintf( __( '%1$s has been added to cart with quantity %2$d.', 'wc-wholesale-pricing' ), get_the_title( $product_id ), $quantity ),
			)
		);
		wp_die();
	}

	/**
	 * AJAX served to update mini cart.
	 */
	public function wcwp_update_mini_cart_callback() {
		$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );

		if ( empty( $action ) || 'update_mini_cart' !== $action ) {
			echo 0;
			wp_die();
		}

		// Check ajax nonce.
		$ajax_nonce = filter_input( INPUT_POST, 'wcwp_ajax_nonce', FILTER_SANITIZE_STRING );

		if ( ! wp_verify_nonce( $ajax_nonce, 'wcwp-ajax-nonce' ) ) {
			echo -1;
			wp_die();
		}

		echo wp_kses_post( woocommerce_mini_cart() );
		wp_die();
	}

	/**
	 * Show wholesale products tab on store single page.
	 *
	 * @param array $tabs Holds the tabs array.
	 * @param int   $store_id Holds the store ID.
	 * @return array
	 */
	public function wcwp_dokan_store_tabs_callback( $tabs, $store_id ) {
		// Check if the wholesale tab is to be shown on store single page.
		$show_tab = wcwp_show_wholesale_tab_store_single();

		if ( false === $show_tab ) {
			return $tabs;
		}

		$tab_title = __( 'Wholesale Products', 'wc-wholesale-pricing' );
		/**
		 * Wholesale tab title filter.
		 *
		 * This filter helps to modify the tab title for the wholesale products on vendor's page.
		 *
		 * @param string $tab_title Holds the tab title.
		 * @param int    $store_id Holds the store ID.
		 * @return string
		 */
		$tab_title = apply_filters( 'wcwp_wholesale_tab_title_vendor_page', __( 'Wholesale Products', 'wc-wholesale-pricing' ), $store_id );

		// Add wholesale tab.
		$tabs['wcwp_wholesale_products'] = array(
			'title' => $tab_title,
			'url'   => dokan_get_store_url( $store_id ) . 'wholesale/',
		);

		return $tabs;
	}

	/**
	 * Add rewrite rule for wholesale tab.
	 *
	 * @param string $store_url Holds the store URL.
	 */
	public function wcwp_dokan_rewrite_rules_loaded_callback( $store_url ) {
		// Check if the wholesale tab is to be shown on store single page.
		$show_tab = wcwp_show_wholesale_tab_store_single();

		if ( false === $show_tab ) {
			return;
		}

		add_rewrite_rule( $store_url . '/([^/]+)/wholesale?$', 'index.php?' . $store_url . '=$matches[1]&wholesale=true', 'top' );
	}

	/**
	 * Add custom query var to dokan.
	 *
	 * @param array $query_vars Holds the array of query vars.
	 * @return array
	 */
	public function wcwp_dokan_query_var_filter_callback( $query_vars = array() ) {
		// Check if the wholesale tab is to be shown on store single page.
		$show_tab = wcwp_show_wholesale_tab_store_single();

		if ( false === $show_tab ) {
			return $query_vars;
		}

		// Add query var now.
		$query_vars[] = 'wholesale';

		return $query_vars;
	}

	/**
	 * Include the wholesale products template.
	 *
	 * @param string $template Holds the template file location.
	 * @return string
	 */
	public function wcwp_template_include_callback( $template ) {
		// Return if WC function doesn't exist.
		if ( ! function_exists( 'WC' ) ) {
			return $template;
		}

		// Check if the wholesale tab is to be shown on store single page.
		$show_tab = wcwp_show_wholesale_tab_store_single();

		if ( false === $show_tab ) {
			return $template;
		}

		if ( get_query_var( 'wholesale' ) ) {
			return WCWP_PLUGIN_PATH . 'public/templates/store-wholesale-products.php';
		}

		return $template;
	}

	/**
	 * Add wholesale notice to grouped child product's label.
	 *
	 * @param string $label Holds the product label.
	 * @param object $product Holds the WooCommerce product object.
	 * @return string
	 */
	public function wcwp_woocommerce_grouped_product_list_column_label_callback( $label, $product ) {
		// Check if wholesale prices are available for this user.
		$is_wholesale_available = wcwp_is_wholesale_available();

		// Return if the wholesale prices is not available.
		if ( ! $is_wholesale_available ) {
			return $label;
		}

		$product_id      = $product->get_id();
		$wholesale_price = wcwp_get_wholesale_price( $product_id );

		// Return the product title if the wholesale costing is not available.
		if ( false === $wholesale_price ) {
			return $label;
		}

		$label .= wp_kses_post( wcwp_get_product_wholesale_notice_html( $wholesale_price ) );

		return $label;
	}

	/**
	 * Add wholesale flash for the shop loop items.
	 */
	public function wcwp_woocommerce_before_shop_loop_item_title_callback() {
		$product_id      = get_the_ID();
		$wholesale_price = wcwp_get_wholesale_price( $product_id );

		// Return, if the wholesale costing is not available.
		if ( false === $wholesale_price ) {
			return;
		}

		$wholesale_flash = '<span class="wcwp-wholesale-flash">' . __( 'Wholesale!', 'wc-wholesale-pricing' ) . '</span>';

		/**
		 * Wholesale flash filter.
		 *
		 * This filter helps modifying the wholesale flash on shop loop item.
		 *
		 * @param string $wholesale_flash Holds the wholesale flash message.
		 * @param int    $product_id Holds the product ID.
		 * @param array  $wholesale_price Holds the wholesale price options.
		 * @return string
		 */
		echo apply_filters( 'wcwp_shop_loop_wholesale_flash', $wholesale_flash, $product_id, $wholesale_price );
	}

	/**
	 * Modify the product price in the mini cart.
	 *
	 * @param string $price Holds the product price.
	 * @param array  $cart_item Holds the cart item data.
	 * @return string
	 */
	public function wcwp_woocommerce_cart_item_price_callback( $price, $cart_item ) {
		$prod_id         = wcwp_product_id( $cart_item['product_id'], $cart_item['variation_id'] );
		$wholesale_price = wcwp_get_wholesale_price( $prod_id );

		if ( true === $cart_item['wcwp_has_wholesale_price'] && $cart_item['quantity'] >= $wholesale_price['min_qty'] ) {
			// Set the price in cart.
			$price = wc_price( $wholesale_price['price'] );
		}

		return $price;
	}
}
