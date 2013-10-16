(($) ->
	$.fn.cnp_right_now = () ->
		
		data = { action: 'cnp_right_now' }
		
		$this = $ this

		$.post ajaxurl, data, (r) ->
			$this.find('#cnp-right-now-loading').slideUp 400, ->
				$this.find('#cnp-right-now-container').html(r).slideDown(500)

)(jQuery)
