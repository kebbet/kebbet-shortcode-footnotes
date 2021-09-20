<?php
/**
 * Helpers for the plugin.
 *
 * @since 20210920.1
 * @package kebbet-shortcode-footnotes
 * @author Erik Betshammar
 */

namespace kebbet\shortcode\footnotes\helpers;

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
		$string = $slug . '-' . esc_attr( $num );
	} else {
		$string = $slug . '-id-' . esc_attr( $num );
	}

	if ( true === $target ) {
		$string = '#' . $string;
	}

	return $string;
}

/**
 * Remove `<p>`-elements and replace with a double `<br />`.
 *
 * @since 20210920.1
 *
 * @param string $string The string to modify
 * @return string
 */
function strip_paragraph( $string ) {
	$string = mb_replace( '<p>', '', $string ); // Remove paragraphs in title and footnote list.
	$string = mb_replace( '</p>', '<br/><br/>', $string ); // Remove paragraphs in title and footnote list.

	return $string;
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
 * @param string $search Expression to search for.
 * @param string $replace Expression to replace with.
 * @param string $subject The string to search in.
 * @param int    $count
 * @return false|string
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
