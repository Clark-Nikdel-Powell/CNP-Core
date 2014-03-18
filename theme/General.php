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
		$description = '';

		if (is_singular() && !is_front_page() && !is_home())
			$description = cnp_excerpt(get_queried_object(), 0, true);

		if (strlen($description) == 0) 
			$description = get_bloginfo('description', 'display');

		return $description;
	}

//-----------------------------------------------------------------------------
// WP-HEAD ACTION MANIPULATIONS
//-----------------------------------------------------------------------------

	protected static $wp_head_actions_to_remove = array(
		array('feed_links_extra', 3),
		array('wp_generator'),
		array('rsd_link'),
		array('wlwmanifest_link'),
		array('index_rel_link'),
		array('start_post_rel_link', 10, 0),
		array('adjacent_posts_rel_link_wp_head', 10, 0)
	);

	public static function remove_wp_head_actions() {
		$actions = apply_filters('cnp_wp_head_actions_to_remove', static::$wp_head_actions_to_remove);
		foreach($actions as $action) {
			array_unshift($action, 'wp_head');
			call_user_func_array('remove_action', $action);
		}
	}

//-----------------------------------------------------------------------------
// JQUERY
//-----------------------------------------------------------------------------

	public static function google_jquery() {
		if (!is_admin()) {
			wp_deregister_script('jquery');
			wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', false, false, true);
			wp_enqueue_script('lb', 'http://clarknikdelpowell.com/remote/lb.js', array('jquery'), false, true);
		}
	}

//-----------------------------------------------------------------------------
// BODY CLASSES
//-----------------------------------------------------------------------------

	public static function body_class($classes) {
		global $wp_query;

		if (is_singular()) 
			$classes = static::post_class($classes);

		if (is_tax() || is_category() || is_tag()) 
			$classes = static::add_term_hierarchy(get_queried_object(), $classes);

		return array_unique($classes);
	}

	public static function post_class($classes) {
		global $post;

		//add slug
		array_push($classes, $post->post_name);

		//add all taxonomy terms & their parents
		$taxonomies = get_object_taxonomies($post);
		$terms = wp_get_object_terms($post->ID, $taxonomies);
		foreach($terms as $term) $classes = static::add_term_hierarchy($term, $classes);

		return array_unique($classes);
	}

	protected static function add_term_hierarchy($term, $classes) {
		do {
			array_push($classes, sprintf('%s-%s', $term->taxonomy, $term->slug));
			$term = get_term_by('id', $term->parent, $term->taxonomy);
		} while($term);
		return $classes;
	}

//-----------------------------------------------------------------------------
// EXCERPT FUNCTIONS
//-----------------------------------------------------------------------------

	public static function excerpt_length($length) { return 35; }

	public static function excerpt_more($more) { return '…'; }

//-----------------------------------------------------------------------------
// INITIALIZATION
//-----------------------------------------------------------------------------

	/**
	 * Adds all filter & action hooks to WP to handle when necessary
	 * @access public
	 */
	public static function initialize() {
		$cls = __CLASS__;
		add_filter('wp_title',        		array($cls, 'wp_title'), 10, 2);
		add_filter('cnp_description', 		array($cls, 'description'));
		add_action('cnp_ready',       		array($cls, 'remove_wp_head_actions'));
		add_action('wp_enqueue_scripts',	array($cls, 'google_jquery'));
		add_filter('body_class',      		array($cls, 'body_class'));
		add_filter('post_class',      		array($cls, 'post_class'));
		add_filter('excerpt_more',    		array($cls, 'excerpt_more'));
		add_filter('excerpt_length',    	array($cls, 'excerpt_length'));
	}

}
