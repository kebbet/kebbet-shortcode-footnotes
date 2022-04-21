<?php
/**
 * Settings for the plugin.
 *
 * @since 20210920.3
 * @package kebbet-shortcode-footnotes
 * @author Erik Betshammar
 */

namespace kebbet\footnotes\settings;

defined( 'ABSPATH' ) or exit;

/**
 * Return the shortcode name.
 *
 * @since 20210920.3
 *
 * @return string
 */
function shortcode() {
	$default_code = 'fn';
	$return_value = apply_filters( 'kebbet_shortcode_footnote_name', $default_code );
	return $return_value;
}

/**
 * Return setting for wether or not to add title attribute to links.
 *
 * @since 20210920.3
 *
 * @return bool
 */
function title_attributes() {
	$default_value = true;
	$return_value  = apply_filters( 'kebbet_shortcode_footnote_link_title', $default_value );
	return boolval( $return_value );
}

/**
 * Return setting for wether or not to allow and display `back`-links to source note.
 *
 * @since 20210920.3
 *
 * @return bool
 */
function back_link() {
	$default_value = false;
	$return_value  = apply_filters( 'kebbet_shortcode_footnote_list_back_link', $default_value );
	return boolval( $return_value );
}
