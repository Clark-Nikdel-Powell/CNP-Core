<?php

/**
 * Link to files in the theme safely (will account for HTTP/HTTPS)
 * @access public
 * @param  string $path Path relative to the theme to a file/directory
 * @return string       Absolute path to theme resource
 */
function cnp_theme_url($path) {
	$path = ltrim(trim($path), '/');
	$base = trailingslashit(get_stylesheet_directory_uri());
	return $base.$path;
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
