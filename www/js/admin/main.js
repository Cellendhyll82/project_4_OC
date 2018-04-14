//--------------------------- NAVIGATION ---------------------------------------//
$(window).on('load', function() {
	if($('#navigation-wrapper').height() < $(document).height()) {
		$('#navigation-wrapper').css('height', $(document).height() - 40 + 'px');
	}
});

$(window).resize(function () {
	$(document).ready(function() {
		if($('#navigation-wrapper').height() < $(document).height()) {
			$('#navigation-wrapper').css('height', $(document).height() - 40 + 'px');
		}
	});
});
