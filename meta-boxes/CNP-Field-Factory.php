<?

class CNP_Meta_Box_Field_Factory {

//-----------------------------------------------------------------------------
// SETTINGS
//-----------------------------------------------------------------------------

	protected static $shared_field_defaults = array(
		'label'       => '',
		'desc'        => '',
		'id'          => '',
		'class'       => '',
		'default'     => '',
		'hidden'      => false
	);

	protected static $option_default = array(
		'label' => '',
		'value' => ''
	);

	protected static $field_defaults = array(

		//ERROR - DEFAULT IF SOMETHING WENT WRONG
		'error' => array(),

		//TEXT INPUT
		'text' => array(
			'attr' => array()
		),

		//TEXTAREA INPUT
		'textarea' => array(
			'attr' => array()
		),

		//CHECKBOX INPUT
		'checkbox' => array(
			'default' => false
		),

		//SELECT DROPDOWN BOX
		'select' => array(
			'options' => array(),
			'attr'    => array()
		),

		//RADIO OPTIONS
		'radio' => array(
			'options' => array()
		),

		//CHECKBOX GROUP
		'checkbox_group' => array(
			'options' => array()
		),

		//TAXONOMY SELECT
		'taxonomy' => array(
			'taxonomy' => '',
			'attr'     => array(),
		),

		//POST SELECTOR
		'post' => array(
			'query' => array(),
			'attr'  => array()
		),

		//DATE PICKER
		'date' => array(
			'attr' => array()
		),

		//RANGE SLIDER
		'slider' => array(
			'min'  => 0,
			'max'  => 100,
			'step' => 0.1
		),

		//MEDIA
		'media' => array(
			'file_type' => 'image'
		)
	);

//-----------------------------------------------------------------------------
// NORMALIZATION
//-----------------------------------------------------------------------------

	protected static function normalize_fields($fields) {
		$new_fields = array();
		if (!is_array($fields)) return $new_fields;

		foreach($fields as $field) 
			$new_fields[] = static::normalize_field($field);

		return $new_fields;
	}

	protected static function normalize_field($field) {
		if (!is_array($field)) $field = array();

		if (!isset($field['type']) || !array_key_exists($field['type'], static::$field_defaults))
			$field['type'] = 'error';

		$field = wp_parse_args($field, static::$shared_field_defaults);
		$field = wp_parse_args($field, static::$field_defaults[$field['type']]);

		if (isset($field['options'])) $field['options'] = static::normalize_options($field['options']);

		return $field;
	}

	protected static function normalize_options($options) {
		if (!is_array($options)) $options = array();
		$new_options = array();

		foreach($options as $option) {
			if (!is_array($option)) $option = array('value' => $option);

			$option = wp_parse_args($option, static::$option_default);

			if (!$option['label']) $option['label'] = $option['value'];
			if (!$option['value']) $option['value'] = $option['label'];

			$new_options[] = $option;
		}

		return $new_options;
	}

//-----------------------------------------------------------------------------
// VALUE RETRIEVAL
//-----------------------------------------------------------------------------

	protected static function apply_field_value($field, $post_id = -1) {

		switch($field['type']) {

			//TAXONOMY
			case 'taxonomy':
				$selected = wp_get_object_terms($post_id, $field['taxonomy'], array('fields' => 'slugs')); 
				$selected = is_array($selected) && count($selected)
					? $selected = array_shift($selected)
					: '';
				$field['value'] = $post_id > -1
					? $selected
					: get_option($field['id'], '');
				if ('' === $field['value']) $field['value'] = $field['default'];
			break;

			//JUST GET POST META OR BLOG OPTION
			default:
				$field['value'] = $post_id > -1
					? get_post_meta($post_id, $field['id'], true)
					: get_option($field['id'], '');

				if ('' === $field['value']) $field['value'] = $field['default'];
		}

			return $field;
	}

	protected static function apply_field_values($fields, $post_id = -1) {
		$new_fields = array();
		if (!is_array($fields)) return $new_fields;

		foreach($fields as $field) 
			$new_fields[] = static::apply_field_value($field, $post_id);

		return $new_fields;
	}

//-----------------------------------------------------------------------------
// VALUE SANITIZATION
//-----------------------------------------------------------------------------

	protected static function sanitize_field_value($field) {
		$value = isset($_POST[$field['id']]) ? $_POST[$field['id']] : '';
		$field['value'] = $value;
		return $field;
	}

	protected static function sanitize_field_values($fields) {
		$new_fields = array();
		if (!is_array($fields)) return $new_fields;

		foreach($fields as $field) 
			$new_fields[] = static::sanitize_field_value($field);

		return $new_fields;
	}

//-----------------------------------------------------------------------------
// DISPLAY
//-----------------------------------------------------------------------------

	public static function display_post_fields($fields, $post_id) {
		$fields = static::normalize_fields($fields);
		$fields = static::apply_field_values($fields, $post_id);
		static::display_fields($fields);
	}

	public static function display_dashboard_fields($fields) {
		$fields = static::normalize_fields($fields);
		$fields = static::apply_field_values($fields, -1);
		static::display_fields($fields);
	}

	protected static function display_fields($fields) {
		$hidden_fields = array();
		
		echo '<table class="form-table cnp-fields">';
		foreach($fields as $field) {
			if ($field['hidden']) {
				$hidden_fields[] = $field;
				continue;
			}
			$class = static::field_class($field);
			echo "<tr class=\"$class\"><th><label for=\"{$field['id']}\">{$field['label']}</label></th><td>";
			static::display_field($field);
			echo "</td></tr>";
		}
		echo '</table>';

		foreach($hidden_fields as $field) static::display_field($field);
	}

	protected static function display_field($field) {
		switch($field['type']) {

			//TEXT INPUT
			case 'text':
				printf(
					'<input type="text" name="%1$s" id="%1$s" value="%2$s" %3$s/><br/><span class="description">%4$s</span>',
					$field['id'],
					$field['value'],
					static::field_attributes($field),
					$field['desc']
				);
			break;

			//TEXT AREA INPUT
			case 'textarea':
				printf(
					'<textarea name="%1$s" id="%1$s" %2$s>%3$s</textarea><br/><span class="description">%4$s</span>',
					$field['id'],
					static::field_attributes($field),
					$field['value'],
					$field['desc']					
				);
			break;

			//CHECKBOX
			case 'checkbox':
				printf(
					'<input type="checkbox" name="%1$s" id="%1$s" %2$s /><label for="%1$s"> %3$s</label>',
					$field['id'],
					$field['value'] ? 'checked="checked"' : '',
					$field['desc']
				);
			break;

			//SELECT DROP DOWN LIST
			case 'select':
				$options = implode('', array_map(
					function($o) use ($field) { return sprintf(
						'<option value="%s" %s>%s</option>',
						$o['value'],
						$field['value'] === $o['value'] ? 'selected="selected"' : '',
						$o['label']
					);},
					$field['options']
				));
				printf(
					'<select name="%1$s" id="%1$s" %2$s>%3$s</select><br/><span class="description">%4$s</span>',
					$field['id'],
					static::field_attributes($field),
					$options,
					$field['desc']
				);
			break;

			//RADIO BUTTON GROUP
			case 'radio':
				$options = implode('', array_map(
					function($o) use ($field) { return sprintf(
						'<input type="radio" name="%1$s" id="%1$s-%2$s" value="%2$s" %3$s /><label for="%1$s-%2$s"> %4$s</label><br/>',
						$field['id'],
						$o['value'],
						$field['value'] === $o['value'] ? 'checked="checked"' : '',
						$o['label']
					);},
					$field['options']
				));
				printf(
					'%s<span class="description">%s</span>',
					$options,
					$field['desc']
				);
			break;

			//CHECKBOX GROUP
			case 'checkbox_group':
				$options = implode('', array_map(
					function($o) use ($field) { return sprintf(
						'<input type="checkbox" name="%1$s[]" id="%1$s-%2$s" value="%2$s" %3$s /><label for="%1$s-%2$s"> %4$s</label><br/>',
						$field['id'],
						$o['value'],
						is_array($field['value']) && in_array($o['value'], $field['value']) ? 'checked="checked"' : '',
						$o['label']
					);},
					$field['options']
				));
				printf(
					'%s<span class="description">%s</span>',
					$options,
					$field['desc']					
				);
			break;

			//TAXONOMY SELECT
			case 'taxonomy':
				$terms = get_terms($field['taxonomy'], array('get' => 'all'));
				$options = '<option value="">Select One</option>';
				$options .= implode('', array_map(
					function($o) use ($field) { return sprintf(
						'<option value="%s" %s>%s</option>',
						$o->slug,
						$o->slug === $field['value'] ? 'selected="selected"' : '',
						$o->name
					);},
					$terms
				));
				printf(
					'<select name="%1$s" id="%1$s" %2$s>%3$s</select><br/><span class="description">%4$s</span>',
					$field['id'],
					static::field_attributes($field),
					$options,
					$field['desc']
				);
			break;

			//POST SELECT
			case 'post':
				$options = '<option value="">Select One</option>';
				$options .= implode('', array_map(
					function($p) use ($field) { 
						$pt = get_post_type_object($p->post_type);
						return sprintf(
							'<option value="%d" %s>%s</option>',
							$p->ID,
							$p->ID == $field['value'] ? 'selected="selected"' : '',
							sprintf('%s: %s', $pt->labels->singular_name, $p->post_title)
						);
					},
					get_posts($field['query'])
				));
				printf(
					'<select name="%1$s" id="%1$s" %2$s>%3$s</select><br/><span class="description">%4$s</span>',
					$field['id'],
					static::field_attributes($field),
					$options,
					$field['desc']
				);
			break;

			case 'date':
				printf(
					'<input type="text" name="%1$s" id="%1$s" value="%2$s" %3$s/><br/><span class="description">%4$s</span><script>%5$s</script>',
					$field['id'],
					$field['value'],
					static::field_attributes($field),
					$field['desc'],
					";jQuery(function($) { $('#{$field['id']}').datepicker(); });"
				);
			break;

			case 'slider':
				$value = (float)$field['value'];
				$onchange = "function(e,ui) { $('#{$field['id']}').val(ui.value); $('#cnp-slider-label-{$field['id']}').text(ui.value); }";
				$options = "{ min: {$field['min']}, max: {$field['max']}, step: {$field['step']}, value: {$value}, slide: {$onchange} }";
				printf(
					'<div id="cnp-slider-%1$s"></div><input type="hidden" name="%1$s" id="%1$s" value="%2$s"/><span class="cnp-slider-label" id="cnp-slider-label-%1$s">%2$s</span><span class="description">%3$s</span><script>%4$s</script>',
					$field['id'],
					$value,
					$field['desc'],
					";jQuery(function($) { $('#cnp-slider-{$field['id']}').slider($options); });"
				);
			break;

			case 'media':
				$image_src = wp_get_attachment_thumb_url((int)$field['value']);
				$js = ";jQuery(function($) {
					var img = $('#cnp-media-img-{$field['id']}');
					var btn = $('#cnp-media-button-{$field['id']}');
					var rmv = $('#cnp-media-remove-{$field['id']}');
					var val = $('#{$field['id']}');
					btn.cnp_media_uploader({ select: function(a) {
						a = a[0];
						val.val(a.id);
						img.attr('src', a.sizes.thumbnail.url);
					} });
					rmv.click(function(e) {
						e.preventDefault();
						val.val('');
						img.attr('src', 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==');
					});
				});";
				printf('<img src="%4$s" width="150" height="150" id="cnp-media-img-%1$s" /><div><input type="hidden" name="%1$s" id="%1$s" value="%2$s"/>
					<input class="cnp-media-button button" id="cnp-media-button-%1$s" type="button" value="Choose Image" />
					<small> <a href="#" class="cnp-media-remove" id="cnp-media-remove-%1$s">Remove</a>
					<br><span class="description">%3$s</span></div><script>%5$s</script>',
					$field['id'],
					$field['value'],
					$field['desc'],
					$image_src,
					$js
				);
			break;

			//ERROR DISPLAY
			case 'error': /* fall through */
			default:
				echo '<span class="description">ERROR: The field could not be rendered.</span>';
		}
	}

	protected static function field_class($field) {
		$classes = explode(' ', $field['class']);
		$classes[] = 'cnp-field';
		$classes[] = "cnp-field-type-{$field['type']}";
		$classes[] = "cnp-field-id-{$field['id']}";
		return implode(' ', $classes);
	}

	protected static function field_attributes($field) {
		$attr = isset($field['attr']) ? $field['attr'] : '';
		if (!is_array($attr)) return $attr;
		$output = '';
		foreach($attr as $key => $val)
			$output .= sprintf(' %s="%s"', $key, $val);
		return $output;
	}


//-----------------------------------------------------------------------------
// SAVE
//-----------------------------------------------------------------------------

	public static function save_post_fields($fields, $post_id) {
		$fields = static::normalize_fields($fields);
		$fields = static::sanitize_field_values($fields);
		foreach($fields as $field)
			switch($field['type']) {
				case 'taxonomy':
					wp_set_object_terms($post_id, $field['value'], $field['taxonomy']);  
				break;

				default:
					update_post_meta($post_id, $field['id'], $field['value']);
			}	
	}

	public static function save_dashboard_fields($fields) {
		$fields = static::normalize_fields($fields);
		$fields = static::sanitize_field_values($fields);
		foreach($fields as $field)
			switch($field['type']) {
				default:
					update_option($field['id'], $field['value']);
			}
	}
	
}
