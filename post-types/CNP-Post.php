<?php

class CNP_Post_Post_Type extends CNP_Post_Type {

	protected static $name = 'post';

	protected static $remove_supports = array(
		'trackbacks',
		'comments'
	);

	protected static $columns_to_remove = array('comments', 'tags');

	public static function register() { /* NOOP */ }

}
