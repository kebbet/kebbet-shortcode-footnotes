<?php
/**
 * Plugin Name:       Kebbet plugins - Shortcode for footnotes
 * Plugin URI:        https://github.com/kebbet/kebbet-shortcode-footnotes
 * Description:       Adds a shortcode that creates footnotes in the content and a footnote list at the end of the_content.
 * Version:           20210920.2
 * Author:            Erik Betshammar
 * Author URI:        https://verkan.se
 * Requires at least: 5.8
 *
 * @package kebbet-shortcode-footnotes
 * @author Erik Betshammar
 */

namespace kebbet\shortcode\footnotes;

const SHORTCODE = 'fn'; // apply_filters( 'kebbet_shortcode_footnote_name', 'fn' );
const TITLEATTR = true;
const BACKLINK  = false;

/**
 * Include helper functions.
 */
require_once plugin_dir_path( __FILE__ ) . 'inc/helpers.php';

/**
 * Hook into the `init` action
 */
function init() {
	load_textdomain();
	register();
}
add_action( 'init', __NAMESPACE__ . '\init', 0 );

/**
 * Load language files.
 */
function load_textdomain() {
	load_plugin_textdomain( 'kebbet-shortcode-footnotes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Register the shortcode and filter the_content.
 */
function register() {
	add_shortcode( SHORTCODE, __NAMESPACE__ . '\replace_shortcode_with_sup' );
	add_filter( 'the_content', __NAMESPACE__ . '\list_footnotes', 12 );
}

/**
 * Display the footnote list.
 *
 * @param string $content The original content of a post.
 * @return string The modified content.
 */
function list_footnotes( $content ) {

	$notes_list = '';
	$title      = apply_filters( 'kebbet_shortcode_footnote_list_title', __( 'Footnotes', 'kebbet-shortcode-footnotes' ) );
	$title_tag  = apply_filters( 'kebbet_shortcode_footnote_list_title_tag','h3' );
	$wrap_class = apply_filters( 'kebbet_shortcode_footnote_list_wrap_class','footnotes-wrap' );

	if ( is_admin() ) {
		return $content;
	}

	$notes_content = get_post_footnotes();
	if ( ! $notes_content ) {
		return $content;
	}

	foreach ( $notes_content as $note_number => $footnote_content ) {
		$footnote_content = helpers\strip_paragraph( $footnote_content );
		$reference        = helpers\link_id( $note_number, true, false );

		if ( true === BACKLINK ) {
			$source_link      = helpers\link_id( $note_number, false, true );
			$footnote_content = '<a href="' . esc_url( $source_link ) . '">&#8593;</a> ' . $footnote_content;
		}

		$notes_list .= '<li id="' . esc_attr( $reference ) . '"><span>' . $footnote_content . '</span></li>';
	}

	$list_content  = '<div class="' . esc_attr( $wrap_class ) . '">';
	$list_content .= '<' . $title_tag . '>' . $title . '</' . $title_tag . '>';
	$list_content .= '<ol class="footnotes-list">';
	$list_content .= $notes_list;
	$list_content .= '</ol>';
	$list_content .= '</div>';
	$content      .= wp_kses_post( $list_content );

	return $content;
}

/**
 * Get the footnotes and return them in an array with the note number as key.
 *
 * @global $post
 *
 * @return false|array
 */
function get_post_footnotes() {

	// Use $post since the_content is modified and the shortcodes are removed.
	global $post;

	if ( ! has_shortcode( $post->post_content, SHORTCODE ) ) {
		return false;
	}

	/**
	 * @source https://stackoverflow.com/a/32525101
	 */
	$pattern = get_shortcode_regex( array( SHORTCODE ) );
	$notes   = array();

	if ( preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches ) ) {
		if ( ! $matches ) {
			return false;
		}

		foreach ( $matches[5] as $key => $value ) {
			$num         = $key + 1;
			$notes[$num] = $value;
		}
	}
	return $notes;
}

/**
 * Replace content between the shortcode and count number of footnotes.
 *
 * @param array  $attributes The attributes.
 * @param string $content    The content of each shortcode.
 * @return string
 */
function replace_shortcode_with_sup( $attributes, $content ) {

	global $footnote_count;

	$attributes  = '';
	$target_post = helpers\get_post_scope_id();
	$content     = do_shortcode( $content ); // Render out any shortcode within the contents.
	$first_item  = ! isset( $footnote_count[$target_post] );
	$note_number = intval( 1 );

	// Which footnote is this?
	if ( ! $first_item ) {
		$find_max    = max( $footnote_count[$target_post]['ref'] );
		$note_number = $find_max + intval( 1 );
	}

	$footnote_count[$target_post]['ref'][] = $note_number;

	// Add optional title attr to link element.
	if ( true === TITLEATTR ) {
		$content    = helpers\strip_paragraph( $content );
		$title      = str_replace( '"', '&quot;', strip_tags( $content ) );
		$attributes = ' title="' . esc_attr( $title ) . '"';
	}

	$sup_class    = apply_filters( 'kebbet_shortcode_footnote_note_class', 'footnotes-footnote' );
	$note_link    = helpers\link_id( $note_number, true, true );
	$source_id    = helpers\link_id( $note_number, false, false );
	$sup_content  = '<sup id="' . esc_attr( $source_id ) . '" class="' . esc_attr( $sup_class ) . '">';
	$sup_content .= '<a href="' . esc_url( $note_link ) . '"' . $attributes . '>' . esc_attr( $note_number ) . '</a>';
	$sup_content .= '</sup>';

	// Replace the content between shortcode with the new <sup>-number.
	$content = $sup_content;

	return $content;
}
