<?php

class CNP_Admin_TinyMCE {

	protected static $editor_stylesheet = 'css/tinymce.css';

	protected static $first_row_buttons = array(
		"bold",
		"italic",
		"strikethrough",
		"bullist",
		"numlist",
		"outdent",
		"indent",
		"justifyleft",
		"justifycenter",
		"justifyright",
		"link",
		"unlink",
		"formatselect",
		"wp_adv"
	);

	protected static $second_row_buttons = array(
		"undo",
		"redo",
		"pastetext",
		"pasteword", 
		"removeformat",
		"charmap", 
		"blockquote",
		"wp_more"
	);

	public static function mce_buttons_first_row($buttons) {
		return apply_filters('cnp_tinymce_first_row_buttons', static::$first_row_buttons);
	}

	public static function mce_buttons_second_row($buttons) {
		return apply_filters('cnp_tinymce_second_row_buttons', static::$second_row_buttons);
	}

	public static function add_editor_style() { 
		add_editor_style(static::$editor_stylesheet); 
	}

	public static function initialize() {
		add_filter('mce_buttons', array(get_called_class(), 'mce_buttons_first_row'));
		add_filter('mce_buttons_2', array(get_called_class(), 'mce_buttons_second_row'));
		add_action('init', array(get_called_class(), 'add_editor_style'));	
	}

}
