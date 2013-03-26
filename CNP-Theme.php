<?

class CNP_Theme {

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

	public static function initialize() {

		add_filter('wp_title', array(__CLASS__, 'wp_title'), 10, 2);

	}

}
