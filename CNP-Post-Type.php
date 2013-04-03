<?php

/**
 * Helper class for defining and managing functionality for a custom post type
 * @see http://codex.wordpress.org/Function_Reference/register_post_type
 */
abstract class CNP_Post_Type {

//-----------------------------------------------------------------------------
// REGISTRATION
//-----------------------------------------------------------------------------

	/**
	 * The name of the post type used in registering and accessing this CPT.
	 * Maximum 20 charaacters, can not contain capital letters or spaces.
	 * 
	 * @var string
	 */
	protected static $name = null;

	/**
	 * Registration arguments for this post type. The values set here will override
	 * any other registration args defined in this class
	 * @var array
	 */
	protected static $args = null;

	/**
	 * An array of labels for this post type.
	 * @var array
	 */
	protected static $labels = null;

	/**
	 * An array of features that the post type should support
	 * @var array
	 */
	protected static $supports = null;

	/**
	 * An array of capabilities for this post type
	 * @var array
	 */
	protected static $capabilities = null;

	/**
	 * An array of taxonomies that should be applied to this post type
	 * @var array
	 */
	protected static $taxonomies = null;

	/**
	 * Default registration arguments that should be applied to all post types
	 * unless overwritten
	 */
	protected final static function default_args() {
		return array(
			'menu_position' => 20,
			'supports' => array(
				'title',
				'editor',
				'thumbnail',
				'excerpt',
				'revisions'
			),
			'has_archive' => true,
			'rewrite' => array('with_front' => false)
		);
	}

	/**
	 * Registers the post type with WordPress. This method
	 * should not be called directly. 
	 */
	public final static function register() {
		$args = static::default_args();
		
		if (is_array(static::$labels))       $args['labels']       = static::$labels;
		if (is_array(static::$supports))     $args['supports']     = static::$supports;
		if (is_array(static::$capabilities)) $args['capabilities'] = static::$capabilities;
		if (is_array(static::$taxonomies))   $args['taxonomies']   = static::$taxonomies;

		if (is_array(static::$args))
			$args = wp_parse_args(static::$args, $args);

		register_post_type(static::$name, $args);
	}

//-----------------------------------------------------------------------------
// ADMIN ARCHIVE VIEW CLEANUP
//-----------------------------------------------------------------------------

	/**
	 * Column IDs to remove from the admin archive page for this post type
	 * @var array
	 */
	protected static $columns_to_remove = array('comments');

	/**
	 * Column IDs => titles to add to the archive page for this post type
	 * @var array
	 */
	protected static $columns_to_add = array();

	/**
	 * Column IDs to move to the end of the table.
	 * @var array
	 */
	protected static $columns_to_end = array('author', 'date');

	/**
	 * Removes, adds and manipulates columns
	 * @access public
	 * @param  array $cols Column ids and titles
	 * @return array       The manipulated input array
	 */
	public static function manage_columns($cols) {

		//remove specified columns
		if (is_array(static::$columns_to_remove))
			foreach(static::$columns_to_remove as $col)
				unset($cols[$col]);

		//add columns
		if (is_array(static::$columns_to_add))
			foreach(static::$columns_to_add as $col => $name)
				$cols[$col] = $name;

		//move columns to end
		if (is_array(static::$columns_to_end)) 
			foreach ($static::$columns_to_end as $col) {
				if (!array_key_exists($col, $cols)) continue;
				$val = $cols[$col];
				unset($cols[$col]);
				$cols[$col] = $val;
			}

		return $cols;
	}

	/**
	 * Set the value of a particular column for a particular post_id
	 * for this post type.
	 * 
	 * @access public
	 */
	public static function column_values($col, $post_id) {
		//NOOP
		//Needs to be overridden in child class to manipulate
		//column values for this archive view
	}

//-----------------------------------------------------------------------------
// ADMIN EDIT SCREEN VIEW CLEANUP
//-----------------------------------------------------------------------------

	/**
	 * Override the default placeholder for the title on the post edit screen
	 * @var string
	 */
	protected static $title_prompt = false;

	/**
	 * IDs of the meta boxes to remove from the post edit screen
	 * @var array
	 */
	protected static $meta_boxes_to_remove = array();

	/**
	 * Whether or not to move the revisions meta box to the sidebar
	 * @var boolean
	 */
	protected static $move_revisions_meta_box = true;


	/**
	 * Whether or not to move the author meta box to the sidebar
	 * @var boolean
	 */
	protected static $move_author_meta_box = true;

	/**
	 * Whether or not to move the slug meta box to the sidebar
	 * @var boolean
	 */
	protected static $move_slug_meta_box = true;

	/**
	 * Changes the title placeholder to the override specified
	 * @access public
	 * @param  string $prompt The current prompt value
	 * @return string         The updated prompt value
	 */
	public final static function enter_title_here($prompt) {
		$new = trim(static::$title_prompt);
		return '' === $new || !static::is_this_cpt()
			? $prompt
			: $new;
	}

	/**
	 * Remove meta boxes from the post edit screen
	 * @access public
	 */
	public final static function remove_meta_boxes() {
		if (!static::is_this_cpt()) return;
		if (is_array(static::$meta_boxes_to_remove))
			foreach($meta_boxes_to_remove as $box) {
				remove_meta_box($box, static::$name, 'normal');
				remove_meta_box($box, static::$name, 'side');
			}
	}

	/**
	 * Move meta boxes on the pot edit screen
	 * @access public
	 */
	public final static function move_meta_boxes() {
		if (!static::is_this_cpt()) return;

		$relocations = array(
			'revisions' => array(
				'revisionsdiv',
				'Revisions',
				'post_revisions_meta_box',
				static::$name,
				'side',
				'low'
			),
			'author' => array(
				'authordiv',
				'Author',
				'post_author_meta_box',
				static::$name,
				'side',
				'high'
			),
			'slug' => array(
				'slugdiv',
				'Slug',
				'post_slug_meta_box',
				static::$name,
				'side',
				'high'
			)
		);

		if (!static::$move_revisions_meta_box) unset($relocations['revisions']);
		if (!static::$move_author_meta_box) unset($relocations['author']);
		if (!static::$move_slug_meta_box) unset($relocations['slug']);

		foreach($relocations as $support => $args) {
			if ('slug' !== $support && !post_type_supports(static::$name, $support)) continue;
			remove_meta_box($args[0], static::$name, 'normal');
			call_user_func_array('add_meta_box', $args);
		}
	}

//-----------------------------------------------------------------------------
// MAIN QUERY CHANGES
//-----------------------------------------------------------------------------

	/**
	 * Overrides the number of posts displayed on an archive page
	 * @var int
	 */
	protected static $archive_quantity = false;

	/**
	 * Sets the number of posts displayed on an archive page
	 * @access public
	 */
	public static function change_quantity($query) {
		if (is_admin()) return;
		if (!$query->is_main_query()) return;
		if (!$query->is_archive()) return;
		if ($query->get('post_type') != static::$name) return;
		if (false === static::$archive_quantity) return;
		$query->set('posts_per_page', (int)static::$archive_quantity);
	}

//-----------------------------------------------------------------------------
// UTILITIES
//-----------------------------------------------------------------------------

	/**
	 * Checks whether or not the current screen is for this post type
	 * @access public
	 * @return boolean [description]
	 */
	protected static function is_this_cpt() {
		global $post_type;
		return $post_type == static::$name;
	}

//-----------------------------------------------------------------------------
// INITIALIZATION
//-----------------------------------------------------------------------------

	public static function initialize() {
		$cls = get_called_class();
		$name = static::$name;

		add_action('init', array($cls, 'register'));

		add_filter("manage_edit-{$name}_columns", array($cls, 'manage_columns'));
		add_action("manage_{$name}_posts_custom_column", array($cls, 'column_values'), 10, 2);

		add_filter('enter_title_here', array($cls, 'enter_title_here'));

		add_action('do_meta_boxes', array($cls, 'move_meta_boxes'));
		add_action('do_meta_boxes', array($cls, 'remove_meta_boxes'));
	}

}
