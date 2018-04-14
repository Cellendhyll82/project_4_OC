function alignImages() 
{
	$('.image.hidden').remove();
	var nbImages = $('.image').length;
	var nbImagesPerRow = Math.trunc($('#images-wrapper').width()/146);
	var nbImagesToAdd = (nbImages % nbImagesPerRow == 0) ? 0 : nbImagesPerRow - nbImages % nbImagesPerRow;

	for(var i = 0; i < nbImagesToAdd; i++) {
		$('#images-wrapper').append('<div class="image hidden"></div>');
	}
}

$(window).ready(alignImages());

/* ----------------------------- ADD IMAGE ------------------------- */
$('#add-btn').click(function () {
	$('#add-new-image').slideToggle();
});

$('#add-new-image #add-image-arrow').click(function () {
	$('#add-new-image').slideUp();
});