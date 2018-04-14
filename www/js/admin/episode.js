$('#imageBrowser .image').click(function () {
	$('#imageBrowser').css('display', 'none');

	if($(this).parent().parent().data('btn-clicked') &&  $(this).parent().parent().data('btn-clicked') !== 'episode-image') {
		var btnClicked = $(this).parent().parent().data('btn-clicked');
		var imageName = $(this).data('filename');

		$('#' + btnClicked).prev().val(imageName);
	}
	else {
		if($('#no-image').length) {
			$('<div class="object-image-wrapper">'
				+ '<img src="www/images/website/close_icon.png" class="delete-object-image">'
				+ '<img src="www/images/' +  $(this).data('filename') + '" id="episode-image" class="open-popup-on-click monitor-change object-image" data-popup-id="imageBrowser" data-image-load="2" title="cliquez pour modifier">'
				+ '<input type="hidden" name="episode-imageId" value="' + $(this).data('id') + '">'
			+ '</div>').insertAfter($('#no-image'));
			$('#no-image').css('display', 'none');
			$('#no-image').prop('id', 'is-image');
			$('.open-popup-on-click').click( function () {
				setPopup($(this));
			});
			$('img.monitor-change').on('load', function () {
				setObjectImageDeleteIcon();
			});
		} else {
			$('#episode-image').attr('data-image-load', parseInt($('#episode-image').attr('data-image-load')) + 1);
			$('#episode-image').attr('src', 'www/images/' + $(this).data('filename'));
			$('input[name="episode-imageId"').val($(this).data('id'));
			$('img.monitor-change').on('load', function () {
				setObjectImageDeleteIcon();
			});
		}
	}
});

$(document).on('click', '.delete-object-image', function() {
	displayNoImageDiv($(this).parent().prev());
	$(this).parent().remove();
});