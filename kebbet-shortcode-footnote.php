<?php
/**
 * Plugin Name:       Kebbet plugins - Shortcode for footnotes
 * Plugin URI:        https://github.com/kebbet/kebbet-shortcode-footnotes
 * Description:       Adds a shortcode that creates footnotes in the content and a footnote list at the end of the_content.
 * Version:           20210919.2
 * Author:            Erik Betshammar
 * Author URI:        https://verkan.se
 * Requires at least: 5.8
 *
 * @package kebbet-shortcode-footnotes
 * @author Erik Betshammar
 */

namespace kebbet\shortcode\footnotes;

const SHORTCODE = 'fn';
const TITLEATTR = true;
const BACKLINK  = true;

/**
 * Hook into the 'init' action
 */
function init() {
	load_textdomain();
	register();
}
add_action( 'init', __NAMESPACE__ . '\init', 0 );

function load_textdomain() {
	load_plugin_textdomain( 'kebbet-shortcode-footnotes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Register the shortcode.
 */
function register() {
	add_shortcode( SHORTCODE, __NAMESPACE__ . '\shortcode_extract' );
	add_filter( 'the_content', __NAMESPACE__ . '\list_footnotes', 12 );
}

/**
 * Return the translatable link slug.
 *
 * @return string
 */
function link_slug() {
	$string = _x( 'footnote', 'Slug for the anchor links.', 'kebbet-shortcode-footnotes' );
	$string = apply_filters( 'kebbet_shortcode_footnote_slug', $string );
	return $string;
}

/**
 * One place to set link ids and matching targets.
 *
 * @param int  $num The footnote number.
 * @param bool $target Wether the string is used as a target or not.
 * @return string
 */
function link_id( $num, $target = false ) {

	if ( ! $num ) {
		return false;
	}

	$string = link_slug() . '-id-' . esc_attr( $num );
	if ( true === $target ) {
		$string = '#' . $string;
	}

	return $string;
}

/**
 * Display the footnote list.
 *
 * @param string $content The real content.
 * @return string The modified content.
 */
function list_footnotes( $content ) {

	$notes_list = '';
	$title      = apply_filters( 'kebbet_shortcode_footnote_list_title', __( 'Footnotes', 'kebbet-shortcode-footnotes' ) );

	if ( is_admin() ) {
		return $content;
	}

	$notes_content = get_post_footnotes();
	if ( ! $notes_content ) {
		return $content;
	}

	foreach ( $notes_content as $note_number => $footnote_content ) {
		$footnote_content = mb_replace( '<p>', '', $footnote_content ); // Remove paragraphs in title and footnote list.
		$footnote_content = mb_replace( '</p>', '<br/><br/>', $footnote_content ); // Remove paragraphs in title and footnote list.

		if ( true === BACKLINK ) {
			$source_link      = link_id( $note_number, true );
			$footnote_content = '<a href="' . esc_url( $source_link ) . '">&#8593;</a> ' . $footnote_content;
		}

		$notes_list .= '<li id="' . esc_attr( link_slug() . '-' . $note_number ) . '">' . $footnote_content . '</li>';
	}

	$list_content  = '<div class="footnotes-wrap">';
	$list_content .= '<h3>' . $title . '</h3>';
	$list_content .= '<ol class="footnotes-list">';
	$list_content .= $notes_list;
	$list_content .= '</ol>';
	$list_content .= '</div>';
	$content      .= wp_kses_post( $list_content );

	return $content;
}

/**
 * Get the footnotes and store them in an array with the note number as key.
 *
 * @global $post
 *
 * @return array
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
			return $content;
		}

		foreach ( $matches[5] as $key => $value) {
			$num         = $key +1;
			$notes[$num] = $value;
		}
	}
	return $notes;
}

/**
 * Replace content between shortcodes and count number of footnotes.
 *
 * @param array  $atts The attributes.
 * @param string $content The content of each shortcode.
 */
function shortcode_extract( $atts, $content ) {

	global $footnote_count;

	$attributes  = '';
	$target_post = get_post_scope_id();
	$content     = do_shortcode( $content ); // Render out any shortcode within the contents.
	$first_item  = ! isset( $footnote_count[$target_post] );
	$note_number = intval( 1 );

	// Which footnote is this=
	if ( ! $first_item ) {
		$find_max    = max( $footnote_count[$target_post]['used_ref_num'] );
		$note_number = $find_max + intval( 1 );
	}

	$footnote_count[$target_post]['used_ref_num'][] = $note_number;

	// Add title attr to link element.
	if ( true === TITLEATTR ) {
		$content     = mb_replace( '<p>', '', $content ); // Remove paragraphs in title and footnote list.
		$content     = mb_replace( '</p>', '<br /><br />', $content ); // Remove paragraphs in title and footnote list.
		$title       = str_replace( '"', '&quot;', strip_tags( $content ) );
		$attributes .= ' title="' . esc_attr( $title ) . '" ';
	}

	$sup_class    = 'footnotes-footnote';
	$note_link    = '#' . link_slug() . '-' . esc_attr( $note_number );
	$source_id    = link_id( $note_number );
	$sup_content  = '<sup id="' . esc_attr( $source_id ) . '" class="' . esc_attr( $sup_class ) . '">';
	$sup_content .= '<a href="' . esc_url( $note_link ) . '"' . $attributes . '>' . esc_attr( $note_number ) . '</a>';
	$sup_content .= '</sup>';

	// Replace the content between shortcode with the new <sup>-number.
	$content = $sup_content;

	return $content;
}

/**
 * Get the ID for the scope, and hash it.
 *
 * @return string
 */
function get_post_scope_id() {

	$id   = get_the_ID();
	$type = get_post_type( $id );
	return $type . '_' . $id;
}

/**
 * Multibyte version of str_replace
 *
 * @source https://stackoverflow.com/a/3786018
 *
 * @param string $search Expression to search for.
 * @param string $replace Expression to replace with.
 * @param string $subject The string to search in.
 * @param int    $count
 * @return string
 */
function mb_replace( $search, $replace, $subject, &$count=0 ) {
	if (!is_array($search) && is_array($replace)) {
		return false;
	}
	if (is_array($subject)) {
		// call mb_replace for each single string in $subject
		foreach ($subject as &$string) {
			$string = &mb_replace($search, $replace, $string, $c);
			$count += $c;
		}
	} elseif (is_array($search)) {
		if (!is_array($replace)) {
			foreach ($search as &$string) {
				$subject = mb_replace($string, $replace, $subject, $c);
				$count += $c;
			}
		} else {
			$n = max(count($search), count($replace));
			while ($n--) {
				$subject = mb_replace(current($search), current($replace), $subject, $c);
				$count += $c;
				next($search);
				next($replace);
			}
		}
	} else {
		$parts   = mb_split(preg_quote($search), $subject);
		$count   = count($parts)-1;
		$subject = implode($replace, $parts);
	}
	return $subject;
}
