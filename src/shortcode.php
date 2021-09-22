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
 * Return markup for the `sup`-element.
 *
 * @since 20210922.1
 *
 * @param int    $number The number that replaces the shortcode content.
 * @param string $content The content between shortcode-tags that can be places in a title-attribute.
 * @return string
 */
function display( $number, $content ) {
	$link_title  = '';
	$note_link   = \kebbet\shortcode\footnotes\helpers\link_id( $number, true, true );
	$sup_class   = apply_filters( 'kebbet_shortcode_footnote_note_class', 'footnotes-footnote' );
	$sup_id      = '';

	// Add optional title attribute to link element.
	if ( true === \kebbet\shortcode\footnotes\settings\title_attributes() ) {
		$content    = do_shortcode( $content ); // Render out any shortcode within the contents.
		$content    = \kebbet\shortcode\footnotes\helpers\strip_paragraph( $content );
		$content    = str_replace( '"', '&quot;', strip_tags( $content ) );
		$link_title = ' title="' . esc_attr( $content ) . '"';
	}

	// Add back links if enabled.
	if ( true === \kebbet\shortcode\footnotes\settings\back_link() ) {
		$source_id = \kebbet\shortcode\footnotes\helpers\link_id( $number, false, false );
		$sup_id    = ' id="' . esc_attr( $source_id ) . '"';
	}

	// Build the `sup`-element.
	$sup_content  = '<sup' . $sup_id . ' class="' . esc_attr( $sup_class ) . '">';
	$sup_content .= '<a href="' . esc_url( $note_link ) . '"' . $link_title . '>' . esc_attr( $number ) . '</a>';
	$sup_content .= '</sup>';

	return $sup_content;
}

/**
 * Count number of footnotes and replace content between the shortcode tags.
 *
 * @param array  $attributes The attributes.
 * @param string $content    The content of each shortcode.
 * @return string
 */
function replace_content_with_sup( $attributes, $content ) {

	// Count number of notes in each post.
	global $counter;

	$note_number = intval( 1 );
	$target      = \kebbet\shortcode\footnotes\helpers\get_post_scope_id();
	$first_item  = ! isset( $counter[$target] );

	// Which footnote is this?
	if ( ! $first_item ) {
		$find_max    = max( $counter[$target] );
		$note_number = $find_max + intval( 1 );
	}

	// Update counter.
	$counter[$target][] = $note_number;

	// Markup for the sup-element.
	$sup_content = display( $note_number, $content );

	return $sup_content;
}
