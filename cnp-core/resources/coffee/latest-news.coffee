(($) ->
	$.fn.cnp_latest_news = () ->
		
		data = { action: 'cnp_latest_news' }

		$this = $ this
		
		$.post ajaxurl, data, (r) ->
			$this.find('#cnp-latest-news-loading').slideUp 400, ->
				$this.find('#cnp-latest-news-container').html(r).slideDown(500)

)(jQuery)
