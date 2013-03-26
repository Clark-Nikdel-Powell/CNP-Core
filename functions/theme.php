<?php

/**
 * Link to files in the theme safely (will account for HTTP/HTTPS)
 * @access public
 * @param  string $path Path relative to the theme to a file/directory
 * @return string       Absolute path to theme resource
 */
function cnp_theme_url($path) {
	$path = ltrim(trim($path), '/');
	static $base = trailingslashit(get_stylesheet_directory_uri());
	return $base.$path;
}
