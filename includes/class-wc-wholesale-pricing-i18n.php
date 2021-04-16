<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://codersux.com
 * @since      1.0.0
 *
 * @package    Wc_Wholesale_Pricing
 * @subpackage Wc_Wholesale_Pricing/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wc_Wholesale_Pricing
 * @subpackage Wc_Wholesale_Pricing/includes
 * @author     CodersUx <info@codersux.com>
 */
class Wc_Wholesale_Pricing_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wc-wholesale-pricing',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
