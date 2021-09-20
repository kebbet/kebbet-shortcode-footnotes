<?php
/**
 * Settings for the plugin.
 *
 * @since 20210920.3
 * @package kebbet-shortcode-footnotes
 * @author Erik Betshammar
 */

namespace kebbet\shortcode\footnotes\settings;

/**
 * Return the shortcode name.
 *
 * @return string
 */
function shortcode() {
	$default = 'fn';
	$value   = apply_filters( 'kebbet_shortcode_footnote_name', $default );
	return $value;
}

/**
 * Return setting for wether or not to add title attribute to links.
 *
 * @return bool
 */
function title_attributes() {
	$default = true;
	$value   = apply_filters( 'kebbet_shortcode_footnote_link_title', $default );
	return boolval( $value );
}

/**
 * Return setting for wether or not to allow and display `back`-links to source note.
 *
 * @return bool
 */
function back_link() {
	$default = false;
	$value   = apply_filters( 'kebbet_shortcode_footnote_list_back_link', $default );
	return boolval( $value );
}