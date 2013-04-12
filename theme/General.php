<?php

/**
 * Describes action/filter hooks for theme related functions
 */
final class CNP_Theme_General {

//-----------------------------------------------------------------------------
// THEME-RELATED HOOK OVERRIDES
//-----------------------------------------------------------------------------

	/**
	 * Creates a nicely formatted and more specific title element text
	 * for output in head of document, based on current view.
	 * @access public
	 * @param  string $title Default title text for current view
	 * @param  string $sep   Optional separator
	 * @return string        Filtered title
	 */
	public static function wp_title($title, $sep) {
		global $paged, $page;

		//ignore feed pages
		if (is_feed()) return $title;

		//add site name
		$title .= get_bloginfo('name');

		//add site description for the home/front page
		$desc = get_bloginfo('description', 'display');
		if ($desc && (is_home() || is_front_page()))
			$title = "$title $sep $desc";

		//add a page number if necessary
		if ($paged >= 2 || $page >= 2)
			$title = "$title $sep Page ".max($paged, $page);

		return $title;
	}

	/**
	 * Gets the description for the current page, either the excerpt of
	 * a singular item, or the site/blog description otherwise.
	 * @access public
	 */
	public static function description($description) {
		return esc_attr(is_singular()
			? the_excerpt()
			: get_bloginfo('description', 'display')
		);
	}

//-----------------------------------------------------------------------------
// INITIALIZATION
//-----------------------------------------------------------------------------

	/**
	 * Adds all filter & action hooks to WP to handle when necessary
	 * @access public
	 */
	public static function initialize() {
		$cls = __CLASS__;
		add_filter('wp_title',        array($cls, 'wp_title'), 10, 2);
		add_filter('cnp_description', array($cls, 'description'));
	}

}
