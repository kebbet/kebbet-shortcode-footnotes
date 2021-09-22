<?php
/**
 * Activation and deactivation function for the plugin.
 *
 * @since 20210921.1
 * @package kebbet-shortcode-footnotes
 * @author Erik Betshammar
 */

namespace kebbet\footnotes\install;

defined( 'ABSPATH' ) or exit;

/**
 * On plugin activation.
 *
 * @since 20210921.1
 */
function plugin_activation() {
	load_textdomain();
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\plugin_activation' );

/**
 * Load language files.
 */
function load_textdomain() {
	load_plugin_textdomain( 'kebbet-shortcode-footnotes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
