<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://codersux.com
 * @since      1.0.0
 *
 * @package    Wc_Wholesale_Pricing
 * @subpackage Wc_Wholesale_Pricing/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wc_Wholesale_Pricing
 * @subpackage Wc_Wholesale_Pricing/admin
 * @author     CodersUx <info@codersux.com>
 */
class Wc_Wholesale_Pricing_Admin {

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
	 * @param string $plugin_name       The name of this plugin.
	 * @param string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function wcwp_admin_enqueue_scripts_callback() {
		global $post;

		if ( empty( $post->ID ) || 'product' !== get_post_type( $post->ID ) ) {
			return;
		}

		// Enqueue style.
		wp_enqueue_style(
			$this->plugin_name,
			WCWP_PLUGIN_URL . 'admin/css/wc-wholesale-pricing-admin.css',
			array(),
			filemtime( WCWP_PLUGIN_PATH . 'admin/css/wc-wholesale-pricing-admin.css' )
		);

		// Enqueue script.
		wp_enqueue_script(
			$this->plugin_name,
			WCWP_PLUGIN_URL . 'admin/js/wc-wholesale-pricing-admin.js',
			array( 'jquery' ),
			filemtime( WCWP_PLUGIN_PATH . 'admin/js/wc-wholesale-pricing-admin.js' ),
			true
		);

		// Localize jquery scripts variables.
		wp_localize_script(
			$this->plugin_name,
			'WCWP_Admin_JS_Obj',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/**
	 * Admin settings for wholesale pricing.
	 *
	 * @param array $sections Array of WC products tab sections.
	 */
	public function wcwp_woocommerce_get_sections_products_callback( $sections ) {
		$sections['wc-wholesale-pricing'] = __( 'Wholesale pricing', 'wc-wholesale-pricing' );

		return $sections;
	}

	/**
	 * Add custom section to WooCommerce settings products tab.
	 *
	 * @param array $settings Holds the woocommerce settings fields array.
	 * @param array $current_section Holds the wcbogo settings fields array.
	 * @return array
	 */
	public function wcwp_woocommerce_get_settings_products_callback( $settings, $current_section ) {
		// Check the current section is what we want.
		if ( 'wc-wholesale-pricing' === $current_section ) {
			return $this->wcwp_plugin_settings_fields();
		} else {
			return $settings;
		}
	}

	/**
	 * Return the fields for plugin settings.
	 *
	 * @return array
	 */
	private function wcwp_plugin_settings_fields() {
		return apply_filters(
			'woocommerce_wcwp_plugin_settings',
			array(
				array(
					'title' => __( 'General', 'wc-wholesale-pricing' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'wcwp_plugin_settings_title',
				),
				array(
					'name'     => __( 'Allowed User Roles', 'wc-wholesale-pricing' ),
					'type'     => 'multiselect',
					'options'  => wcwp_get_user_roles(),
					'class'    => 'wc-enhanced-select',
					'desc'     => __( 'This holds the wholesale pricing to the currently loggedin user role. Leave blank to allow for all roles.', 'wc-wholesale-pricing' ),
					'desc_tip' => true,
					'default'  => '',
					'id'       => 'wcwp_wholesale_price_allowed_user_roles',
				),
				array(
					'name'              => __( 'Product\'s Page Notice', 'wc-wholesale-pricing' ),
					'type'              => 'textarea',
					'class'             => 'wcwp-product-details-notice',
					'desc'              => __( 'This holds the wholesale notice being displayed on product details page.', 'wc-wholesale-pricing' ),
					'desc_tip'          => true,
					'id'                => 'wcwp_wholesale_notice_on_product_page',
					'placeholder'       => __( 'Use constant [min_quantity] and [price] for setting the message.', 'wc-wholesale-pricing' ),
					'custom_attributes' => array(
						'rows' => 5,
					),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'wcwp_plugin_settings_end',
				),
				array(
					'title' => __( 'WC Cart', 'wc-wholesale-pricing' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'wcwp_wc_cart_title',
				),
				array(
					'name' => __( 'Enable Cart Notice', 'wc-wholesale-pricing' ),
					'desc' => __( 'This enables showing the wholesale notice for the items in cart.', 'wc-wholesale-pricing' ),
					'id'   => 'wcwp_show_wholesale_notice_cart_item',
					'type' => 'checkbox',
				),
				array(
					'name'        => __( 'Cart Item Notice Title', 'wc-wholesale-pricing' ),
					'type'        => 'text',
					'class'       => 'wcwp-cart-item-notice-title',
					'desc'        => __( 'This holds the wholesale notice title for cart item.', 'wc-wholesale-pricing' ),
					'desc_tip'    => true,
					'id'          => 'wcwp_cart_item_notice_title',
					'placeholder' => __( 'Default: Wholesale Notice', 'wc-wholesale-pricing' ),
				),
				array(
					'name'              => __( 'Cart Item Notice Message', 'wc-wholesale-pricing' ),
					'type'              => 'textarea',
					'class'             => 'wcwp-cart-item-notice-message',
					'desc'              => __( 'This holds the wholesale notice message for cart item.', 'wc-wholesale-pricing' ),
					'desc_tip'          => true,
					'id'                => 'wcwp_cart_item_notice_message',
					'placeholder'       => __( 'Default: Add [quantity] unit(s) more of this product to avail the wholesale price of [price].', 'wc-wholesale-pricing' ),
					'custom_attributes' => array(
						'rows' => 5,
					),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'wcwp_wc_cart_end',
				),
				array(
					'title' => __( 'Dokan Support', 'wc-wholesale-pricing' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'wcwp_dokan_support_title',
				),
				array(
					'name' => __( 'Show Wholesale Products Tab on Store Single Page', 'wc-wholesale-pricing' ),
					'desc' => __( 'This enables showing the wholesale products tab on the store single page.', 'wc-wholesale-pricing' ),
					'id'   => 'wcwp_show_wholesale_tab_store_single',
					'type' => 'checkbox',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'wcwp_dokan_support_end',
				),
			)
		);
	}

	/**
	 * Add HTML markup for wholesale prices for simple product.
	 */
	public function wcwp_woocommerce_product_options_general_product_data_callback() {
		global $post;

		if ( empty( $post->ID ) ) {
			return;
		}

		// Get the product data.
		$wc_product = wc_get_product( $post->ID );

		if ( false === $wc_product ) {
			return;
		}

		if ( ! $wc_product->is_type( 'simple' ) ) {
			return;
		}

		echo wp_kses(
			'<div class="options_group wholesale-pricing show_if_simple">',
			array(
				'div' => array(
					'class' => array(),
				),
			)
		);
		echo wp_kses( wcwp_get_wholesale_minimum_quantity_field( $post->ID ), wcwp_wp_kses_number_ignore_fields() );
		echo wp_kses( wcwp_get_wholesale_price_field( $post->ID ), wcwp_wp_kses_number_ignore_fields() );

		/**
		 * Action to add custom fields to wholesale pricing block.
		 *
		 * @param object $wc_product Holds the WooCommerce product object.
		 */
		do_action( 'wxwp_woocommerce_product_options_wholesale_pricing', $wc_product );
		echo wp_kses_post( '</div>' );
	}

	/**
	 * Save the wholesale pricing data.
	 *
	 * @since    1.0.0
	 * @param int $product_id Holds the product ID.
	 */
	public function wcwp_woocommerce_process_product_meta_callback( $product_id ) {
		$this->wcwp_update_product_meta( $product_id );
	}

	/**
	 * Update product meta for free product data.
	 *
	 * @param int $product_id Holds the product ID.
	 * @param int $loop Holds the loop index for variations listing.
	 */
	private function wcwp_update_product_meta( $product_id, $loop = -1 ) {

		if ( -1 !== $loop ) {
			$min_qty = ( ! empty( $_POST['_wholesale_minimum_quantity'][ $loop ] ) ) ? wp_unslash( $_POST['_wholesale_minimum_quantity'][ $loop ] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Cannot veryfy WooCommerce variation update nonce.
			$price   = ( ! empty( $_POST['_wholesale_price'][ $loop ] ) ) ? wp_unslash( $_POST['_wholesale_price'][ $loop ] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Cannot veryfy WooCommerce variation update nonce.
		} else {
			$min_qty = filter_input( INPUT_POST, '_wholesale_minimum_quantity', FILTER_SANITIZE_NUMBER_INT );
			$price   = filter_input( INPUT_POST, '_wholesale_price', FILTER_SANITIZE_NUMBER_INT );
		}

		// Update the database now.
		if ( ! empty( $min_qty ) ) {
			update_post_meta( $product_id, 'wcwp_wholesale_minimum_quantity', $min_qty );
		}

		if ( ! empty( $price ) ) {
			update_post_meta( $product_id, 'wcwp_wholesale_price', $price );
		}
	}

	/**
	 * HTML markup to select the free product for variations.
	 *
	 * @param int    $loop Holds the loop index value.
	 * @param array  $variation_data Holds the variation basic data.
	 * @param object $variation Holds the variation post object data.
	 */
	public function wcwp_woocommerce_variation_options_pricing_callback( $loop, $variation_data, $variation ) {
		echo wp_kses( wcwp_get_wholesale_minimum_quantity_field( $variation->ID, $loop ), wcwp_wp_kses_number_ignore_fields() );
		echo wp_kses( wcwp_get_wholesale_price_field( $variation->ID, $loop ), wcwp_wp_kses_number_ignore_fields() );
	}

	/**
	 * Save the free product/variation.
	 *
	 * @param int $variation_id Holds the variation ID.
	 * @param int $loop Holds the loop index for variations listing.
	 */
	public function wcwp_woocommerce_save_product_variation_callback( $variation_id, $loop ) {
		$this->wcwp_update_product_meta( $variation_id, $loop );
	}

	/**
	 * Add fields to quick edit WooCommerce product.
	 */
	public function wcwp_woocommerce_product_quick_edit_end_callback() {
		ob_start();
		?>
		<br class="clear" />
		<h4 class="wcwp_wholesale_price_fields_title"><?php esc_html_e( 'Wholesale Pricing Options', 'wc-wholesale-pricing' ); ?></h4>
		<div class="wcwp_wholesale_price_fields">
			<label>
				<span class="title"><?php esc_html_e( 'Min. Qty', 'wc-wholesale-pricing' ); ?></span>
				<span class="input-text-wrap">
					<input type="number" min="1" step="1" name="_wholesale_min_qty" class="text wc_input_price wholesale_min_qty" placeholder="0">
				</span>
			</label>
			<br class="clear" />
			<label>
				<span class="title"><?php esc_html_e( 'Price', 'wc-wholesale-pricing' ); ?></span>
				<span class="input-text-wrap">
					<input type="number" min="0.01" step="0.01" name="_wholesale_price" class="text wc_input_price wholesale_price" placeholder="0.00">
				</span>
			</label>
			<br class="clear" />
		</div>
		<?php
		echo ob_get_clean();
	}

	/**
	 * Update the wholesale pricing options on quick edit save.
	 *
	 * @param object $product Holds the WooCommerce product object.
	 */
	public function wcwp_woocommerce_product_quick_edit_save_callback( $product ) {

		// Return if the product is not simple.
		if ( ! $product->is_type( 'simple' ) ) {
			return;
		}

		$product_id        = $product->get_id();
		$wholesale_min_qty = (int) filter_input( INPUT_POST, '_wholesale_min_qty', FILTER_SANITIZE_NUMBER_INT );
		$wholesale_price   = (float) filter_input( INPUT_POST, '_wholesale_price', FILTER_SANITIZE_NUMBER_FLOAT );

		// Update wholesale min. quantity.
		if ( ! empty( $wholesale_min_qty ) ) {
			update_post_meta( $product_id, 'wcwp_wholesale_minimum_quantity', $wholesale_min_qty );
		} else {
			// Delete the meta if the wholesale prices are empty.
			delete_post_meta( $product_id, 'wcwp_wholesale_minimum_quantity' );
		}

		// Update wholesale price.
		if ( ! empty( $wholesale_price ) ) {
			update_post_meta( $product_id, 'wcwp_wholesale_price', $wholesale_price );
		} else {
			// Delete the meta if the wholesale prices are empty.
			delete_post_meta( $product_id, 'wcwp_wholesale_price' );
		}
	}

	/**
	 * Add hidden fields to render wholesale price data in the name column.
	 *
	 * @param string $column Holds the column name.
	 * @param int    $post_id Holds the post ID.
	 */
	public function wcwp_manage_product_posts_custom_column_callback( $column, $post_id ) {
		switch ( $column ) {
			case 'name':
				$min_qty       = get_post_meta( $post_id, 'wcwp_wholesale_minimum_quantity', true );
				$price         = get_post_meta( $post_id, 'wcwp_wholesale_price', true );
				$product_types = get_the_terms( $post_id, 'product_type' );
				$product_type  = ( ! empty( $product_types[0]->slug ) ) ? $product_types[0]->slug : '';
				ob_start();
				?>
				<input type="hidden" id="_wholesale_min_qty_<?php echo esc_attr( $post_id ); ?>" value="<?php echo esc_html( $min_qty ); ?>" />
				<input type="hidden" id="_wholesale_price_<?php echo esc_attr( $post_id ); ?>" value="<?php echo esc_html( $price ); ?>" />
				<input type="hidden" id="_wcwp_product_type_<?php echo esc_attr( $post_id ); ?>" value="<?php echo esc_html( $product_type ); ?>" />
				<?php
				echo wp_kses(
					ob_get_clean(),
					array(
						'input' => array(
							'type'  => array(),
							'id'    => array(),
							'value' => array(),
						),
					)
				);
				break;

			default:
				break;
		}
	}

	/**
	 * Wholesale ordering form.
	 *
	 * @param array $args Holds the shortcode arguments array.
	 * @return string
	 */
	public function wcwp_wholesale_ordering_callback( $args = array() ) {

		return $this->wcwp_wholesale_ordering_form_shortcode_markup( $args );
	}

	/**
	 * Wholesale ordering shortcode template.
	 *
	 * @param array $args Holds the shortcode arguments array.
	 * @return string
	 */
	private function wcwp_wholesale_ordering_form_shortcode_markup( $args ) {
		$posts_per_page = get_option( 'posts_per_page' );
		$author         = ( ! empty( $args['author'] ) ) ? $args['author'] : false;
		$product_query  = wcwp_get_wholesale_products_list( $posts_per_page, $author );
		$products       = $product_query->posts;
		ob_start();
		?>
		<div class="woocommerce wcwp-wholesale-ordering-container">
			<?php
			if ( empty( $products ) ) {
				echo wp_kses_post( wcwp_get_no_wholesale_products_html() );
			} else {
				echo wcwp_get_wholesale_ordering_form_html( $products );
			}

			// Display the pagination if the max number of pages increase by 1.
			if ( 1 < $product_query->max_num_pages ) {
				?>
				<div class="wcwp-wholesale-products-pagination pagination">
					<?php
					echo wp_kses_post(
						paginate_links(
							array(
								'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
								'total'        => $product_query->max_num_pages,
								'current'      => max( 1, get_query_var( 'paged' ) ),
								'format'       => '?paged=%#%',
								'show_all'     => false,
								'type'         => 'plain',
								'end_size'     => 2,
								'mid_size'     => 1,
								'prev_next'    => false,
								'add_args'     => false,
								'add_fragment' => '',
							)
						)
					);
					?>
				</div>
				<?php
			}
			?>
		</div>
		<?php
		return ob_get_clean();
	}
}
