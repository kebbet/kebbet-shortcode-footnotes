=== Kebbet plugins - Shortcode for footnotes ===
Contributors: kebbet
Tags: footnote,footnotes
Requires at least: 5.8
Tested up to: 5.8.1
Requires PHP: 7.0
Stable tag: 20210920.3
License: ?

Adds a shortcode that creates footnotes in the_content and a footnote list at the end of the_content.

== Description ==
Adds a footnote shortcode without a GUI for the user to adjust the behavior.

## Usage
Use like this `[fn]The footnote content[/fn]`. Automatic numbering without any options. One set of notes per post.

## Filters
* `kebbet_shortcode_footnote_name` Allow for changing the shortcode.
* `kebbet_shortcode_footnote_note_class` Change the sup-note class.
* `kebbet_shortcode_footnote_link_title` Enable or disable link title.
* `kebbet_shortcode_footnote_slug` Modify the slug in the links.
* `kebbet_shortcode_footnote_list_title` Modify the list header.
* `kebbet_shortcode_footnote_list_title_tag` Change the title tag (header level) for the list.
* `kebbet_shortcode_footnote_list_back_link` Enable or disable link back to source.
* `kebbet_shortcode_footnote_list_wrap_class` Modify the wrapper class for the list section.

== Changelog ==
= 20210920.3 =
* Allow for filtering of settings.

= 20210920.2 =
* Extend the `link_id`-function to use up or down direction. Remove `link_slug` function, move use case to `link_id`.

= 20210920.1 =
* Typos and separation of helper functions to their own file.

= 20210919.1 =
* Initial release.
