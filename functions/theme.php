<?php

function cnp_theme_url($path) {
	$path = ltrim(trim($path), '/');
	static $base = trailingslashit(get_stylesheet_directory_uri());
	return $base.$path;
}
