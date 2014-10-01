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

function cnp_get_subdomain() {
	$http = $_SERVER['HTTP_HOST'];
	$domain_array = explode(".", $http);
	$subdomain = array_shift($domain_array);
	return $subdomain;
}

//-----------------------------------------------------------------------------
// STRINGS
//-----------------------------------------------------------------------------

/**
 * Take an icon name and return inline SVG for the icon.
 * @param  string $icon_name The ID of the icon in the defs list of the SVG file.
 * @param  string $viewbox   Optional viewbox size.
 * @param  string $echo      To output, or not to output. Set to 0 if using URL-query.
 */
function cnp_isvg($args) {

	$defaults = array(
		'icon-name'	=> ''
	,	'viewbox' 	=> '0 0 32 32'
	,	'echo'		=> true
	,	'path'      => cnp_theme_url('/img/icons.svg')
	);

	$vars = wp_parse_args( $args, $defaults );
	$icon = '<svg role="img" title="'. $vars['icon-name'] .'" class="icon '. $vars['icon-name'] .'" viewBox="'. $vars['viewbox'] .'"><use xlink:href="'. $vars['path'] .'#'. $vars['icon-name'] .'"></use></svg>';
	if ( $vars['echo'] == true ) {
		echo $icon;
	} else {
		return $icon;
	}
}

/**
 * Take a timestamp and turn it in to human timing.
 * @param  timestamp $time      To output, or not to output. Set to 0 if using URL-query.
 */
function cnp_human_timing ($time, $cutoff=2) {

	$current_time = current_time('timestamp');
    $time = $current_time - $time; // to get the time since that moment

    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
    }

}

/**
 * Get a file's extension
 * @param 	string 	$file	the filename, as a string
 */
function cnp_getExt($file) {
	if (is_string($file)) {
		$arr = explode('.',$file);
		end($arr);
		$ext = current($arr);
		return $ext;
	}
	return false;
}


//-----------------------------------------------------------------------------
// MENUS
//-----------------------------------------------------------------------------

/**
 * Convert <li><a></a></li> pattern to <a></a> pattern, transferring all <li> attributes to the <a>
 * @param  string $s String to manipulate. Duh.
 * @return string    Manipulated string
 */
function cnp_lia2a($string) {

	if (!is_string($string))
		return 'ERROR lia2a(): Not a string (CNP Core, functions/theme.php)';

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
	,	'echo'            => false // always false or else it'd echo on line 143.
	,	'echo_menu'       => true  // sometimes true, sometimes not. it depends.
	);
	$vars = wp_parse_args($args, $defaults);

	$menu = wp_nav_menu($vars);
	$menu = cnp_lia2a($menu);
	$menu = trim($menu);
	$menu = str_replace("\r", "", $menu);
	$menu = str_replace("\n", "", $menu);

	if ( $vars['echo_menu'] === true ) {
		echo $menu.PHP_EOL;
	}

	else {
		return $menu;
	}
}

/**
 * Display requested nav menu, and add menu icons via Font Awesome
 * @param  string $menu_name Same as 'menu' in wp_nav_menu arguments. Allows simple retrieval of menu with just the one argument
 */
function cnp_fa_nav_menu($menu_name, $args=array()) {

	$ancestor = highest_ancestor();

	$defaults = array(
		'menu'            => $menu_name
	,	'container'       => 'nav'
	,	'container_class' => sanitize_title($menu_name)
	,   'before_items'    => ''
	,	'echo'            => true
	);
	$vars = wp_parse_args($args, $defaults);

	$items = wp_get_nav_menu_items($vars['menu']);

	if ( !empty($items) ) {

		$output = '';

		($vars['container'] != '' ? $output .= '<'.$vars['container'].' class="'. $vars['container_class'] .'">' : '');

		(isset($vars['before_items']) ? $output .= $vars['before_items'] : '');

		foreach ($items as $key => $item) {
			$class = implode(' ', $item->classes);
			if ( isset($ancestor['id']) && $ancestor['id'] == $item->object_id )
				$class .= ' current-menu-item';

			$target = '';
			if ( !empty($item->target) )
				$target = 'target='.$item->target;

			$output .= '<a class="'. $class .'" href="'. $item->url .'" '. $target .'>';
			//$args = array('echo'=>false);
			//( !empty($item->classes) ? $output .= '<i class="'. implode(" ", $item->classes) .'"></i>' : '');
			$output .= '<span class="title">'. $item->title .'</span>';
			$output .= '</a>';
		}

		($vars['container'] != '' ? $output .= '</'.$vars['container'].'>' : '' );

		if ( $vars['echo'] ) {
			echo $output.PHP_EOL;
		} else {
			return $output;
		}

	}
}

/**
 * Display requested nav menu, and add menu icons via inline svg
 * @param  string $menu_name Same as 'menu' in wp_nav_menu arguments. Allows simple retrieval of menu with just the one argument
 */
function cnp_svg_nav_menu($menu_name, $args=array()) {

	$ancestor = highest_ancestor();

	$defaults = array(
		'menu'            => $menu_name
	,	'container_class' => sanitize_title($menu_name)
	,   'before_items'    => ''
	);
	$vars = wp_parse_args($args, $defaults);

	$items = wp_get_nav_menu_items($vars['menu']);

	if ( !empty($items) ) {

		$output = '<nav class="'. $vars['container_class'] .'">';
		(isset($vars['before_items']) ? $output .= $vars['before_items'] : '');

		foreach ($items as $key => $item) {
			$class = implode(' ', $item->classes);
			if ( $ancestor['id'] == $item->object_id )
				$class .= ' current-menu-item';

			$target = '';
			if ( !empty($item->target) )
				$target = 'target='.$item->target;

			$output .= '<a class="'. $class .'" href="'. $item->url .'" '. $target .'>';
			$args = array('echo'=>false);
			( !empty($item->classes) ? $output .= cnp_isvg('icon-name='. $item->classes[0] .'&echo=0') : '');
			$output .= '<span class="title">'. $item->title .'</span>';
			$output .= '</a>';
		}

		$output .= '</nav>';

		echo $output.PHP_EOL;
	}
}


/**
 * Build contextually aware section navigation
 * @param  string $menu_name Same as 'menu' in wp_nav_menu arguments. Allows simple retrieval of menu with just the one argument
 * @param  array  $args      Passed directly to wp_nav_menu
 */
function cnp_subnav($options=array()) {

	if (is_search() || is_404())
		return false;

	$defaults = array(
		'header'	=> '<h2 class="title">In this Section</h2>'
	);

	$vars = wp_parse_args( $options, $defaults );

	$list_options = array(
		'title_li'         => 0
	,	'show_option_none' => 0
	,   'echo'             => 0
	);

	$before = '<nav class="section">'. $vars['header'] .'<ul>'.PHP_EOL;
	$after = '</ul></nav>'.PHP_EOL;
	$list = '';

	// Taxonomy archives
	// Includes categories, tags, custom taxonomies
	// Does not include date archives
	if (is_tax()) {

		$query_obj = get_queried_object();

		if (isset($options['list_options'][$query_obj->taxonomy]))
			$list_options = wp_parse_args($options['list_options'][$query_obj->taxonomy], $list_options);

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

				if (isset($options['list_options'][$post->post_type]))
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

	if ( is_front_page() ) :

		$ancestor = array(
			'id'   => (get_option("page_on_front") ? get_option("page_on_front") : 0)
		,	'slug' => 'home'
		,	'name' => 'Home'
		);

	elseif ( is_home() || is_singular('post') ) :

		$home = get_post(get_option("page_for_posts"));

		if (!isset($home)) {
			$ancestor = array(
				'id'   => 0
			,	'slug' => 'blog'
			,	'name' => 'Blog'
			);
		} else {
			$ancestor = array(
				'id'   => $home->ID
			,	'slug' => $home->post_name
			,	'name' => $home->post_title
			);
		}

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

function pagination($args=0) {

	global $wp_query;
	$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
	$defaults = array(
		'base'      => @add_query_arg('paged','%#%')
	,	'format'    => ''
	,	'total'     => $wp_query->max_num_pages
	,	'current'   => $current
	,	'end_size'  => 1
	,	'mid_size'  => 2
	,	'prev_text' => '&larr; Back'
	,	'next_text' => 'More &rarr;'
	,	'type'      => 'plain'
	);
	$pagination = wp_parse_args($args, $defaults);

	$links = paginate_links($pagination);
	echo $links
		? '<p class="pagination">'.$links.'</p>'
		: '';

}
