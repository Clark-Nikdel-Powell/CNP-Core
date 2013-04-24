(($) ->
	$.fn.cnp_media_uploader = (opts) ->
		defaults =
			title:    'Upload/Select Image'
			button:   'Select Image'
			type:     'image'
			multiple: false
			select:   (attachment) ->

		options = $.extend defaults, opts || {}

		$this = $ this
		frame = undefined

		$this.on 'click', (e) ->
			e.preventDefault()
			
			if frame? then frame.close()
			frame = wp.media.frames.customHeader = wp.media
				title: options.title
				library:
					type: options.type
				button:
					text: options.button
				multiple: false

			frame.on 'select', ->
				options.select frame.state().get('selection').map (a) -> a.toJSON()

			frame.open()
)(jQuery)
