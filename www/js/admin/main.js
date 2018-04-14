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

/* ------------------------------ POPUP ---------------------------------- */
$(document).on('click', '.open-popup-on-click', function () {
	setPopup($(this));
});

function setPopup(element)
{
	var popup = $('#' + element.data('popup-id') + '.popup');

	if($(element).prop('id')) {
		popup.attr('data-btn-clicked', $(element).prop('id'));
	}

	fillPopup(element.data('popup-id'), element.data(), function() {
		if(popup.find('img').length !== 0) {
			popup.find('img').on('load', function() {
				setPopupDiemensions(popup);	
			});
		}
		setPopupDiemensions(popup);
	});

	
	if(popup.find('.images-wrapper').length) {
		alignImages();
	}

	//close popup on escape key press
	$(document).on('keydown', function (event) {
		var keyCode = event.which;
		//esc key
		if(keyCode == 27) {
	    	popup.css("display", "none");
	    }
	});
}

function fillPopup(popupId, params, success)
{
	var popup = $('#'+popupId+'.popup');

	switch (popupId) {

		case 'showImage':
			popup.find('.popup-image').html('<img src="www/images/' + params['filename'] + '">');
			popup.find('.popup-image-filename').html(params['filename']);
			var date = new Date(params['insertionDate']);
			popup.find('.popup-image-insertion-date').html(date.toString('dd/MM/yyyy'));
			popup.find('.popup-delete-btn').attr('data-filename', params['filename']);
			popup.find('.popup-delete-btn').attr('data-delete-link', '?c=Image&action=delete&id=' + params['id']);
			popup.find('.popup-delete-btn').attr('data-popup-id', 'deleteConfirmation');
			popup.find('.popup-delete-btn').attr('data-to-delete', 'filename');

			$(popup.find('.popup-delete-btn')).click(function () {
				setPopup($(this));
			});
			success();
			break;

		case 'deleteConfirmation':
			if(params['toDelete'] == 'filename') {
				popup.find('.popup-text').html('Etes-vous certain de vouloir supprimer "<em>' + params['filename'] + '</em>"?'
				+ '<br />Cette action n\'est pas réversible');
			} else if(params['toDelete'] == 'comment') {
				popup.find('.popup-text').html('Etes-vous certain de vouloir supprimer le commentaire de "<em>' + params['username'] + '</em>"?'
				+ '<br />Cette action n\'est pas réversible');
			} else {
				popup.find('.popup-text').html('Etes-vous certain de vouloir supprimer "<em>' + params['title'] + '</em>"?'
				+ '<br />Cette action n\'est pas réversible');
			}
			
			popup.find('.popup-delete-btn').attr('href', params['deleteLink']);
			popup.find('.popup-cancel-btn').click(function () {
				$(this).parent().parent().css('display', 'none');
			});
			success();
			break;

		case 'previewEpisode':
			var imageId = $('input[name="episode-imageId"]').val();
			var imageFilename = $('#imageBrowser .image[data-id="' + imageId + '"]').data('filename');
			if(imageFilename) {
				popup.find('.episode-image').attr('style', 
					'background: url(www/images/' + imageFilename + ');'
					+ 'background-size: cover;'
	                + 'background-repeat: no-repeat;'
	                + 'background-position: center center;'
	            );
			} else {
				popup.find('.episode-image').css('display', 'none');
			}

			popup.find('h1').html($('input[name="episode-title"]').val());
			popup.find('.episode-author').html($('input[name="episode-author"]').val());
			popup.find('.episode-content').html(tinyMCE.get('episode-content').getContent());

			var date = $('input[name="episode-publicationDate"]').val();
			var day = date.split('-')[2];
			var month = date.split('-')[1];
			var year = date.split('-')[0];
			popup.find('.episode-publicationDate').html(day +'/' + month + '/' + year);

			success();
			break;

		default:
			success();
			break;
	}

}

function setPopupDiemensions(popup)
{
	popup.css('display', 'block');
	if(popup.height() < $(window).height()) {
		popup.css('top', ($(window).height() - popup.outerHeight())/2 + 'px');
		popup.css('left', 'calc(50% - ' + (popup.outerWidth())/2 + 'px)');
		popup.css('position', 'fixed');
	} else {
		popup.css('top', '30px');
		popup.css('left', 'calc(50% - ' + (popup.outerWidth())/2 + 'px)');
		popup.css('height', $(window).height() - 120 + 'px');
		popup.find('.popup-content-wrapper').css('height', popup.height() - 40 + 'px');
	}

	popup.find('.close-icon').click(function () {
		popup.css('display', 'none');
	});
}

//to align images in images-browser
function alignImages() 
{
	$('.image.hidden').remove();
	var nbImages = $('.image').length;
	var nbImagesPerRow = Math.trunc($('#images-wrapper').width()/146); //130 + 8 + 8 (width + margin-left + margin-right)
	var nbImagesToAdd = (nbImages % nbImagesPerRow == 0) ? 0 : nbImagesPerRow - nbImages % nbImagesPerRow;

	for(var i = 0; i < nbImagesToAdd; i++) {
		$('#images-wrapper').append('<div class="image hidden"></div>');
	}
}

$('iframe').on('load', function() {
  $(this).contents().find('body').append('<scr' + 'ipt type="text/javascript" src="src/lib/jquery-3.2.0.js"></scr' + 'ipt>');   
  $(this).contents().find('body').append('<scr' + 'ipt type="text/javascript" src="project 5/www/js/publicMain.js"></scr' + 'ipt>');   
  $(this).contents().find('body').append('<scr' + 'ipt type="text/javascript" src="project 5/www/js/custom_js.js"></scr' + 'ipt>');    
});


/* ------------------------------ FOOTER ---------------------------------- */
$.each($('.width-50-footer'), function() {
	$(this).children().css('position', 'absolute');
	$(this).find('input').css('bottom', '8px');
	$(this).find('span').css('left', '30px');
	$(this).find('span').css('bottom', '8px');
	$(this).css('height', $(this).next().height() + 'px');
});