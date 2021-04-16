<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://codersux.com
 * @since      1.0.0
 *
 * @package    Wc_Wholesale_Pricing
 * @subpackage Wc_Wholesale_Pricing/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wc_Wholesale_Pricing
 * @subpackage Wc_Wholesale_Pricing/includes
 * @author     CodersUx <info@codersux.com>
 */
class Wc_Wholesale_Pricing {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wc_Wholesale_Pricing_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WC_WHOLESALE_PRICING_VERSION' ) ) {
			$this->version = WC_WHOLESALE_PRICING_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wc-wholesale-pricing';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wc_Wholesale_Pricing_Loader. Orchestrates the hooks of the plugin.
	 * - Wc_Wholesale_Pricing_I18n. Defines internationalization functionality.
	 * - Wc_Wholesale_Pricing_Admin. Defines all hooks for the admin area.
	 * - Wc_Wholesale_Pricing_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		// The class responsible for orchestrating the actions and filters of the core plugin.
		require_once WCWP_PLUGIN_PATH . 'includes/class-wc-wholesale-pricing-loader.php';

		// The class responsible for defining internationalization functionality of the plugin.
		require_once WCWP_PLUGIN_PATH . 'includes/class-wc-wholesale-pricing-i18n.php';

		// The class responsible for defining all the custom functions.
		require_once WCWP_PLUGIN_PATH . 'includes/wc-wholesale-pricing-functions.php';

		// The class responsible for defining all actions that occur in the admin area.
		require_once WCWP_PLUGIN_PATH . 'admin/class-wc-wholesale-pricing-admin.php';

		// The class responsible for defining all actions that occur in the public-facing side of the site.
		require_once WCWP_PLUGIN_PATH . 'public/class-wc-wholesale-pricing-public.php';

		$this->loader = new Wc_Wholesale_Pricing_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wc_Wholesale_Pricing_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Wc_Wholesale_Pricing_I18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Wc_Wholesale_Pricing_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'wcwp_admin_enqueue_scripts_callback' );
		$this->loader->add_filter( 'woocommerce_get_sections_products', $plugin_admin, 'wcwp_woocommerce_get_sections_products_callback' );
		$this->loader->add_filter( 'woocommerce_get_settings_products', $plugin_admin, 'wcwp_woocommerce_get_settings_products_callback', 10, 2 );
		$this->loader->add_action( 'woocommerce_product_options_general_product_data', $plugin_admin, 'wcwp_woocommerce_product_options_general_product_data_callback' );
		$this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin, 'wcwp_woocommerce_process_product_meta_callback' );
		$this->loader->add_action( 'woocommerce_variation_options_pricing', $plugin_admin, 'wcwp_woocommerce_variation_options_pricing_callback', 10, 3 );
		$this->loader->add_action( 'woocommerce_save_product_variation', $plugin_admin, 'wcwp_woocommerce_save_product_variation_callback', 10, 2 );
		$this->loader->add_action( 'woocommerce_product_quick_edit_end', $plugin_admin, 'wcwp_woocommerce_product_quick_edit_end_callback' );
		$this->loader->add_action( 'woocommerce_product_quick_edit_save', $plugin_admin, 'wcwp_woocommerce_product_quick_edit_save_callback' );
		$this->loader->add_action( 'manage_product_posts_custom_column', $plugin_admin, 'wcwp_manage_product_posts_custom_column_callback', 99, 2 );
		$this->loader->add_shortcode( 'wcwp_wholesale_ordering', $plugin_admin, 'wcwp_wholesale_ordering_callback' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Wc_Wholesale_Pricing_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'wcwp_wp_enqueue_scripts_callback' );
		$this->loader->add_filter( 'woocommerce_add_cart_item_data', $plugin_public, 'wcwp_woocommerce_add_cart_item_data_callback', 20, 3 );
		$this->loader->add_action( 'woocommerce_before_calculate_totals', $plugin_public, 'wcwp_woocommerce_before_calculate_totals_callback', 5 );
		$this->loader->add_action( 'woocommerce_before_add_to_cart_form', $plugin_public, 'wcwp_woocommerce_before_add_to_cart_form_callback' );
		$this->loader->add_filter( 'woocommerce_available_variation', $plugin_public, 'wcwp_woocommerce_available_variation_callback', 10, 3 );
		$this->loader->add_action( 'dokan_product_edit_after_pricing', $plugin_public, 'wcwp_dokan_product_edit_after_pricing_callback' );
		$this->loader->add_action( 'dokan_new_product_after_product_tags', $plugin_public, 'wcwp_dokan_new_product_after_product_tags_callback' );
		$this->loader->add_action( 'dokan_new_product_added', $plugin_public, 'wcwp_dokan_new_product_added_callback', 20 );
		$this->loader->add_action( 'dokan_product_updated', $plugin_public, 'wcwp_dokan_product_updated_callback', 20 );
		$this->loader->add_filter( 'woocommerce_get_item_data', $plugin_public, 'wcwp_woocommerce_get_item_data_callback', 20, 2 );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'wcwp_wp_footer_callback' );
		$this->loader->add_action( 'wp_ajax_add_to_cart', $plugin_public, 'wcwp_add_to_cart_callback' );
		$this->loader->add_action( 'wp_ajax_nopriv_add_to_cart', $plugin_public, 'wcwp_add_to_cart_callback' );
		$this->loader->add_action( 'wp_ajax_update_mini_cart', $plugin_public, 'wcwp_update_mini_cart_callback' );
		$this->loader->add_action( 'wp_ajax_nopriv_update_mini_cart', $plugin_public, 'wcwp_update_mini_cart_callback' );
		$this->loader->add_filter( 'dokan_store_tabs', $plugin_public, 'wcwp_dokan_store_tabs_callback', 10, 2 );
		$this->loader->add_action( 'dokan_rewrite_rules_loaded', $plugin_public, 'wcwp_dokan_rewrite_rules_loaded_callback' );
		$this->loader->add_filter( 'dokan_query_var_filter', $plugin_public, 'wcwp_dokan_query_var_filter_callback', 20 );
		$this->loader->add_filter( 'template_include', $plugin_public, 'wcwp_template_include_callback', 99 );
		$this->loader->add_filter( 'woocommerce_grouped_product_list_column_label', $plugin_public, 'wcwp_woocommerce_grouped_product_list_column_label_callback', 20, 2 );
		$this->loader->add_action( 'woocommerce_before_shop_loop_item_title', $plugin_public, 'wcwp_woocommerce_before_shop_loop_item_title_callback' );
		$this->loader->add_filter( 'woocommerce_cart_item_price', $plugin_public, 'wcwp_woocommerce_cart_item_price_callback', 10, 2 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wc_Wholesale_Pricing_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
