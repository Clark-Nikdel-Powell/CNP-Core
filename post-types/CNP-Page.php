<?php

class CNP_Page_Post_Type extends CNP_Post_Type {

	protected static $name = 'page';

	protected static $remove_supports = array(
		'author',
		'trackbacks',
		'comments'
	);

	public static function register() { /* NOOP */ }

}
