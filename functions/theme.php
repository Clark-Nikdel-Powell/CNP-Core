<?php

/**
 * Link to files in the theme safely (will account for HTTP/HTTPS)
 * @param  string $path Path relative to the theme to a file/directory
 * @return string       Absolute path to theme resource
 */
function cnp_theme_url($path) {
	$path = ltrim(trim($path), '/');
	$base = trailingslashit(get_stylesheet_directory_uri());
	return $base.$path;
}

/**
 * Get path to files in the theme safely
 * @param  string $path Path relative to the theme to a file/directory
 * @return string       Absolute path to theme resource
 */
function cnp_theme_path($path) {
	$path = ltrim(trim($path), '/');
	$base = trailingslashit(get_stylesheet_directory());
	return $base.$path;
}

//-----------------------------------------------------------------------------
// MENUS
//-----------------------------------------------------------------------------

/**
 * Convert <li><a></a></li> pattern to <a></a> pattern, transferring all <li> attributes to the <a>
 * @param  string $s String to manipulate. Duh.
 * @return string    Manipulated string
 */
function lia2a($string) {

	if (!is_string($string))
		return 'ERROR lia2a(): Not a string (CNP Core, functions/theme.php line 31)';

	$find = array('><a','</a>','<li','</li');
	$replace = array('','','<a','</a');
	$return = str_replace($find, $replace, $string);

	return $return;

}

/**
 * Display requested nav menu, but strip out <ul>s and <li>s
 * @param  string $menu_name Same as 'menu' in wp_nav_menu arguments. Allows simple retrieval of menu with just the one argument
 * @param  array  $args      Passed directly to wp_nav_menu
 */
function cnp_nav_menu($menu_name='', $args=array()) {

	$defaults = array(
		'menu'            => $menu_name
	,	'container'       => 'nav'
	,	'container_class' => sanitize_title($menu_name)
	,	'depth'           => 1
	,	'fallback_cb'     => false
	,	'items_wrap'      => PHP_EOL.'%3$s'
	,	'echo'            => false
	);
	$vars = wp_parse_args($args, $defaults);

	$menu = wp_nav_menu($vars);
	$menu = lia2a($menu);

	echo $menu.PHP_EOL;

}

/**
 * Build contextually aware section navigation
 * @param  string $menu_name Same as 'menu' in wp_nav_menu arguments. Allows simple retrieval of menu with just the one argument
 * @param  array  $args      Passed directly to wp_nav_menu
 */
function cnp_subnav($options=array()) {

	if (is_search() || is_404())
		return false;

	$list_options = array(
		'title_li'         => 0
	,	'show_option_none' => 0
	, 'echo'             => 0
	);

	$before = '<nav class="section"><h2>In This Section</h2><ul>'.PHP_EOL;
	$after = '</ul></nav>'.PHP_EOL;
	$list = '';

	// Taxonomy archives
	// Includes categories, tags, custom taxonomies
	// Does not include date archives
	if (is_tax()) {

		$query_obj = get_queried_object();
		$list_options['taxonomy'] = $query_obj->taxonomy;
		$list = wp_list_categories($list_options);

	}

	// Post types
	else {
		global $post;

		// Only if we have a post
		if ($post) {

			// Hierarchical post types show sub post lists
			if (is_post_type_hierarchical($post->post_type)) {

				$list_options['post_type'] = $post->post_type;
				if ($post->post_type == 'page') {
					$ancestor = highest_ancestor();
					$list_options['child_of'] = $ancestor['id'];
				}
				$list = wp_list_pages($list_options);

			}

			// Non-hierarchical post types show specified taxonomy lists
			else {

				if (isset($options['list_options']))
					$list_options = wp_parse_args($options['list_options'][$post->post_type], $list_options);

				$list = wp_list_categories($list_options);

			}

		}
	}

	// If there's no text inside the tags, the list is empty
	if (!strip_tags($list))
		return false;

	return $before.$list.$after;

}

//-----------------------------------------------------------------------------
// DESCRIPTION / EXCERPT FUNCTIONS
//-----------------------------------------------------------------------------

/**
 * Returns an appropriate description for the current page. Can be modified
 * using the cnp_description filter.
 *
 * @access public
 */
function cnp_description() {
	return apply_filters('cnp_description', '');
}

function cnp_excerpt($post, $max_words = false, $truncate = false) {

	$max_words = $max_words ? $max_words : apply_filters('excerpt_length', 35);

	$excerpt = $post->post_excerpt;
	if (!$excerpt) $excerpt = strip_tags(strip_shortcodes($post->post_content));

	if (!$truncate) return $excerpt;

	$words = explode(' ', $excerpt);
	if (count($words) > $max_words) {
		array_splice($words, $max_words);
		$excerpt = implode(' ', $words).apply_filters('excerpt_more', '&hellip');
	}

	return $excerpt;
}

//-----------------------------------------------------------------------------
// SCHEMA.ORG HELPER FUNCTIONS
//-----------------------------------------------------------------------------

/**
 * Defines a schema item type
 * @access public
 * @param  string $type Upper CamelCase type name
 */
function cnp_schema_type($type) {
	printf(
		'itemscope itemtype="http://schema.org/%s"',
		trim($type)
	);
}

/**
 * Defines a schema item property
 * @access public
 * @param  string $prop Upper CamelCase property name
 */
function cnp_schema_prop($prop) {
	printf(
		'itemprop="%s"',
		trim($prop)
	);
}

/**
 * Defines a schema property that has no corresponding element on the page
 * as a meta element
 *
 * @access public
 * @param  string $prop    Upper CamelCase property name
 * @param  string $content Value of the property
 */
function cnp_schema_meta($prop, $content) {
	printf(
		'<meta itemprop="%s" content="%s" />',
		trim($prop),
		trim($content)
	);
}

//-----------------------------------------------------------------------------
// HIGHEST ANCESTOR
//-----------------------------------------------------------------------------

function cnp_highest_ancestor() {

	$ancestor = array(
		'id' => 0,
		'name' => '',
		'title' => 'Unknown',
		'context' => null
	);

	if (is_home() || is_front_page())
		$ancestor['title'] = 'Home';

	if (is_404()) {
		$ancestor['name'] = '404';
		$ancestor['title'] = 'Page Not Found';
	}

	if (is_search()) {
		$ancestor['slug'] = 'search';
		$ancestor['title'] = 'Search Results';
	}

	if (is_year()) {

	}

	if (is_month()) {

	}

	if (is_day()) {

	}

	if (is_author()) {

	}

	if (is_post_type_archive()) {

	}

	if (is_tax() || is_category() || is_tag()) {

	}

	if (is_attachment()) {

	}

	if (is_singular()) {

	}

	return $ancestor;
}

function highest_ancestor($args=0) {

	// merge passed arguments and defaults
	$defaults = array(
		'print'  => 0
	,	'return' => 0
	,	'stopat' => 0
	);
	$vars = wp_parse_args($args, $defaults);
	$posttype = get_post_type();

	if ( is_home() ) :

		$ancestor = array(
			'id'   => 0
		,	'slug' => 'home'
		,	'name' => 'Home'
		);

	elseif ( is_tax() ) :

		global $wp_query;
		$tax = $wp_query->get_queried_object();
		$ancestor = array(
			'id'   => $tax->term_id
		,	'slug' => $tax->slug
		,	'name' => $tax->name
		);

	elseif ( is_archive() || is_single() ) :

		if ( $posttype && $posttype!='post' && $posttype!='page' ) :
			global $wp_query;
			$archive = $wp_query->get_queried_object();
			if (is_singular()) {$archive = get_post_type_object($archive->post_type);}
			$ancestor = array(
				'slug'      => $archive->rewrite['slug']
			,	'name'      => $archive->labels->name
			,	'query_var' => $archive->query_var
			);
		else :
			global $post;
			if ($post) :
				$archive = get_the_category($post->ID);
				$archive = $archive[0];
			else :
				global $wp_query;
				$archive = $wp_query->get_queried_object();
			endif;

			while ($archive->parent != 0) :
				$archive = get_category($archive->parent);
			endwhile;

			$ancestor = array(
				'id'   => $archive->cat_ID
			,	'slug' => $archive->slug
			,	'name' => $archive->name
			,	'count' => $archive->count
			);
		endif;

	elseif ( is_search() ) :

		$ancestor = array(
			'id'   => 0
		,	'slug' => 'search'
		,	'name' => 'Search Results'
		);

	elseif ( is_page() ) :

		global $post;
		$page = $post;

		while ($page->post_parent > 0 && $page->post_parent != $vars['stopat']) :
			$page = get_post($page->post_parent);
		endwhile;

		$ancestor = array(
			'id'   => $page->ID
		,	'slug' => $page->post_name
		,	'name' => $page->post_title
		);

	elseif ($posttype && $posttype!='post' && $posttype!='page' ) :

		$posttype = get_post_type_object($posttype);
		$ancestor = array(
			'slug' => sanitize_html_class(strtolower($posttype->labels->name))
		,	'name' => $posttype->labels->name
		);

	else :

		$ancestor = array(
			'id'   => 0
		,	'slug' => '404'
		,	'name' => 'Page Not Found'
		);

	endif;

	if     ($vars['print'])  : print $ancestor[$vars['print']];
	elseif ($vars['return']) : return $ancestor[$vars['return']];
	else : return $ancestor;
	endif;

}

//-----------------------------------------------------------------------------
// PAGINATION
//-----------------------------------------------------------------------------

function pagination($prev='&larr; Previous', $next='Next &rarr;') {

	global $wp_query, $wp_rewrite;
	$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
	$pagination = array(
		'base'      => @add_query_arg('paged','%#%'),
		'format'    => '',
		'total'     => $wp_query->max_num_pages,
		'current'   => $current,
		'prev_text' => __($prev),
		'next_text' => __($next),
		'type'      => 'plain'
	);

	$links = paginate_links($pagination);
	echo $links
		? '<p class="pagination">'.$links.'</p>'
		: '';

}
