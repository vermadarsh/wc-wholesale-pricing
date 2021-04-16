<?php
/**
 * Fired during plugin activation
 *
 * @link       https://codersux.com
 * @since      1.0.0
 *
 * @package    Wc_Wholesale_Pricing
 * @subpackage Wc_Wholesale_Pricing/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wc_Wholesale_Pricing
 * @subpackage Wc_Wholesale_Pricing/includes
 * @author     CodersUx <info@codersux.com>
 */
class Wc_Wholesale_Pricing_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Flush the rewrite rules.
		$current_permastruct = get_option( 'permalink_structure' );
		update_option( 'permalink_structure', $current_permastruct );
	}

}
