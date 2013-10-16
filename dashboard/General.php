<?php

class CNP_Dashboard_General {

	protected static $widgets_to_remove = array(
	  'dashboard_incoming_links' => array(
	    'page'    => 'dashboard',
	    'context' => 'normal'
	  ),
	  'dashboard_right_now' => array(
	    'page'    => 'dashboard',
	    'context' => 'normal'
	  ),
	  'dashboard_recent_drafts' => array(
	    'page'    => 'dashboard',
	    'context' => 'side'
	  ),
	  'dashboard_quick_press' => array(
	    'page'    => 'dashboard',
	    'context' => 'side'
	  ),
	  'dashboard_plugins' => array(
	    'page'    => 'dashboard',
	    'context' => 'normal'
	  ),
	  'dashboard_primary' => array(
	    'page'    => 'dashboard',
	    'context' => 'side'
	  ),
	  'dashboard_secondary' => array(
	    'page'    => 'dashboard',
	    'context' => 'side'
	  ),
	  'dashboard_recent_comments' => array(
	    'page'    => 'dashboard',
	    'context' => 'normal'
	  )
	);

	public static function remove_widgets() {
		$widgets_to_remove = apply_filters('cnp_remove_dashboard_widgets', static::$widgets_to_remove);
		foreach($widgets_to_remove as $widget_id => $options)
			remove_meta_box($widget_id, $options['page'], $options['context']);
	}

	public static function initialize() {
		remove_action('welcome_panel', 'wp_welcome_panel');
		add_action('wp_dashboard_setup', array(get_called_class(), 'remove_widgets'), 11);
	}

}
