(($) ->
	$.fn.cnp_freshness_widget = () ->
		
		data = { action: 'cnp_content_freshness' }

		$this = $ this
		
		$.post ajaxurl, data, (r) ->
			$this.find('#cnp-content-freshness-loading').slideUp 400, ->
				$this.find('#cnp-content-freshness-container').html(r).slideDown(500)

)(jQuery)
