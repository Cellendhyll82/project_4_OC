$('#imageBrowser .image').click(function () {
	$('#imageBrowser').css('display', 'none');

	if($(this).parent().parent().data('btn-clicked') &&  $(this).parent().parent().data('btn-clicked') !== 'accueil-image') {
		var btnClicked = $(this).parent().parent().data('btn-clicked');
		var imageName = $(this).data('filename');

		$('#' + btnClicked).prev().val(imageName);
	}
	else {
		if($('#no-image').length) {
			$('<div class="object-image-wrapper">'
				+ '<img src="www/images/website/close_icon.png" class="delete-object-image">'
				+ '<img src="www/images/' +  $(this).data('filename') + '" id="accueil-image" class="open-popup-on-click monitor-change object-image" data-popup-id="imageBrowser" data-image-load="2" title="cliquez pour modifier">'
				+ '<input type="hidden" name="accueil-imageId" value="' + $(this).data('id') + '">'
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
			$('#accueil-image').attr('data-image-load', parseInt($('#accueil-image').attr('data-image-load')) + 1);
			$('#accueil-image').attr('src', 'www/images/' + $(this).data('filename'));
			$('input[name="accueil-imageId"').val($(this).data('id'));
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