<?php
/**
 * Plugin Name:       Kebbet plugins - Shortcode for footnotes
 * Plugin URI:        https://github.com/kebbet/kebbet-shortcode-footnotes
 * Description:       Adds a shortcode that creates footnotes in the content and a footnote list at the end of the_content.
 * Version:           20210921.1
 * Author:            Erik Betshammar
 * Author URI:        https://verkan.se
 * Text Domain        kebbet-shortcode-footnotes
 * Requires at least: 5.8
 *
 * @package kebbet-shortcode-footnotes
 * @author Erik Betshammar
 */

namespace kebbet\shortcode\footnotes;

defined( 'ABSPATH' ) or exit;

/**
 * Include init functions.
 */
require_once plugin_dir_path( __FILE__ ) . 'inc/init.php';

/**
 * Include helper functions.
 */
require_once plugin_dir_path( __FILE__ ) . 'inc/helpers.php';

/**
 * Include settings functions.
 */
require_once plugin_dir_path( __FILE__ ) . 'inc/settings.php';

/**
 * Include listing functions.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/listing.php';

/**
 * Include shortcode execution functions.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/shortcode.php';

/**
 * Hook into the `plugins_loaded` action
 */
function plugin_loaded() {
	register();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\plugin_loaded', 0 );

/**
 * Register the shortcode and filter the_content.
 */
function register() {
	add_shortcode( settings\shortcode(), __NAMESPACE__ . '\shortcode\replace_shortcode_with_sup' );
	add_filter( 'the_content', __NAMESPACE__ . '\listing\list_footnotes', 12 );
}
