<?php
/**
 * Shortcode execution.
 *
 * @since 20210921.1
 * @package kebbet-shortcode-footnotes
 * @author Erik Betshammar
 */

namespace kebbet\shortcode\footnotes\shortcode;

defined( 'ABSPATH' ) or exit;

/**
 * Replace content between the shortcode and count number of footnotes.
 *
 * @param array  $attributes The attributes.
 * @param string $content    The content of each shortcode.
 * @return string
 */
function replace_content_with_sup( $attributes, $content ) {

	// Count number of notes in each post.
	global $footnote_count;

	$attributes  = '';
	$sup_id      = '';
	$target_post = \kebbet\shortcode\footnotes\helpers\get_post_scope_id();
	$content     = do_shortcode( $content ); // Render out any shortcode within the contents.
	$first_item  = ! isset( $footnote_count[$target_post] );
	$note_number = intval( 1 );

	// Which footnote is this?
	if ( ! $first_item ) {
		$find_max    = max( $footnote_count[$target_post]['ref'] );
		$note_number = $find_max + intval( 1 );
	}

	$footnote_count[$target_post]['ref'][] = $note_number;

	// Add optional title attribute to link element.
	if ( true === \kebbet\shortcode\footnotes\settings\title_attributes() ) {
		$content    = \kebbet\shortcode\footnotes\helpers\strip_paragraph( $content );
		$title      = str_replace( '"', '&quot;', strip_tags( $content ) );
		$attributes = ' title="' . esc_attr( $title ) . '"';
	}

	// Add back links if enabled.
	if ( true === \kebbet\shortcode\footnotes\settings\back_link() ) {
		$source_id = \kebbet\shortcode\footnotes\helpers\link_id( $note_number, false, false );
		$sup_id    = ' id="' . esc_attr( $source_id ) . '"';
	}

	$sup_class = apply_filters( 'kebbet_shortcode_footnote_note_class', 'footnotes-footnote' );
	$note_link = \kebbet\shortcode\footnotes\helpers\link_id( $note_number, true, true );

	// Build the `sup`-element.
	$sup_content  = '<sup' . $sup_id . ' class="' . esc_attr( $sup_class ) . '">';
	$sup_content .= '<a href="' . esc_url( $note_link ) . '"' . $attributes . '>' . esc_attr( $note_number ) . '</a>';
	$sup_content .= '</sup>';

	// Replace the content between shortcode with the new <sup>-number.
	$content = $sup_content;

	return $content;
}
