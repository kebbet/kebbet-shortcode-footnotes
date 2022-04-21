<?php
/**
 * Helpers for the plugin.
 *
 * @since 20210920.1
 * @package kebbet-shortcode-footnotes
 * @author Erik Betshammar
 */

namespace kebbet\footnotes\helpers;

defined( 'ABSPATH' ) or exit;

/**
 * One place to set link ids and matching targets.
 *
 * @param int  $num The footnote number.
 * @param bool $up Wether the string is used as the up reference or not.
 * @param bool $target Wether the string is used as a target or not.
 * @return false|string
 */
function link_id( $num, $up = false, $target = false ) {

	if ( ! $num ) {
		return false;
	}

	$slug = _x( 'footnote', 'Slug for the anchor links.', 'kebbet-shortcode-footnotes' );
	$slug = apply_filters( 'kebbet_shortcode_footnote_slug', $slug );

	if ( true === $up ) {
		$output = $slug . '-' . esc_attr( $num );
	} else {
		$output = $slug . '-id-' . esc_attr( $num );
	}

	if ( true === $target ) {
		$output = '#' . $output;
	}

	return $output;
}

/**
 * Remove `<p>`-elements and replace with a double `<br />`.
 *
 * @since 20210920.1
 *
 * @param string $content The string to modify
 * @return string
 */
function strip_paragraph( $content ) {
	$content = mb_replace( '<p>', '', $content ); // Remove paragraphs in title and footnote list.
	$content = mb_replace( '</p>', '<br/><br/>', $content ); // Remove paragraphs in title and footnote list.

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
 * Multi byte version of str_replace
 *
 * @source https://stackoverflow.com/a/3786018
 *
 * @param string $search_for Expression to search for.
 * @param string $replace    Expression to replace with.
 * @param string $subject    The string to search in.
 * @param int    $count
 * @return false|string
 */
function mb_replace( $search_for, $replace, $subject, &$count=0 ) {
	if ( ! is_array( $search_for ) && is_array( $replace ) ) {
		return false;
	}
	if ( is_array( $subject ) ) {
		// call mb_replace for each single string in $subject
		foreach ( $subject as &$content ) {
			$content  = &mb_replace( $search_for, $replace, $content, $c );
			$count   += $c;
		}
	} elseif ( is_array( $search_for ) ) {
		if ( ! is_array( $replace ) ) {
			foreach ( $search_for as &$content ) {
				$subject  = mb_replace( $content, $replace, $subject, $c );
				$count   += $c;
			}
		} else {
			$n = max( count( $search_for ), count( $replace ) );
			while ( $n-- ) {
				$subject = mb_replace( current( $search_for ), current( $replace ), $subject, $c );
				$count += $c;
				next( $search_for );
				next( $replace );
			}
		}
	} else {
		$parts   = mb_split( preg_quote( $search_for ), $subject );
		$count   = count( $parts ) - 1;
		$subject = implode( $replace, $parts );
	}
	return $subject;
}
