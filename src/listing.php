<?php
/**
 * Listing of footnotes.
 *
 * @since 20210921.1
 * @package kebbet-shortcode-footnotes
 * @author Erik Betshammar
 */

namespace kebbet\shortcode\footnotes\listing;

defined( 'ABSPATH' ) or exit;

/**
 * Display the footnote list. Return source content or modified content.
 *
 * @param string $content The original content of a post.
 * @return string
 */
function list_footnotes( $content ) {

	$notes_list = '';
	$title      = apply_filters( 'kebbet_shortcode_footnote_list_title', __( 'Footnotes', 'kebbet-shortcode-footnotes' ) );
	$title_tag  = apply_filters( 'kebbet_shortcode_footnote_list_title_tag', 'h3' );
	$wrap_class = apply_filters( 'kebbet_shortcode_footnote_list_wrap_class', 'footnotes-wrap' );

	if ( is_admin() ) {
		return $content;
	}

	$notes_content = get_post_footnotes();
	if ( ! $notes_content ) {
		return $content;
	}

	foreach ( $notes_content as $note_number => $footnote_content ) {
		$footnote_content = \kebbet\shortcode\footnotes\helpers\strip_paragraph( $footnote_content );
		$reference        =\kebbet\shortcode\footnotes\ helpers\link_id( $note_number, true, false );

		if ( true === \kebbet\shortcode\footnotes\settings\back_link() ) {
			$source_link      = \kebbet\shortcode\footnotes\helpers\link_id( $note_number, false, true );
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

	// Append the list.
	$content .= wp_kses_post( $list_content );

	return $content;
}

/**
 * Get the footnotes and return them in an array with the note number as key.
 * - False if no footnotes in the content.
 *
 * @global $post
 *
 * @source https://stackoverflow.com/a/32525101
 *
 * @return false|array
 */
function get_post_footnotes() {

	// Use $post since the_content is modified and the shortcodes are removed.
	global $post;

	$code  = \kebbet\shortcode\footnotes\settings\shortcode();
	$notes = array();

	if ( ! has_shortcode( $post->post_content, $code ) ) {
		return false;
	}

	$pattern = get_shortcode_regex( array( $code ) );

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
