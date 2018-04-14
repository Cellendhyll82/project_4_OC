
/* -------------------------------- LOADING PAGE ------------------------------- */
$('body').css('overflow', 'hidden');
$('#loading-page-icon').css('height', $(window).height() + 'px');
$('#loading-page-icon').css('top', $(window).scrollTop() + 'px');

$(window).on('load', function() {
	$('body').css('overflow', 'initial');
	$('#loading-page-icon').css('display', 'none');
});

/* ----------------------------------- HEADER ---------------------------------- */
function showSmartNav() {
	$('#smart-nav').toggle();
}

$(window).resize(function() {
	if($(window).width() >= 750) {
		$('#smart-nav').css('display', 'none');
	}
});

/* ------------------------------ MENU ------------------------------- */
$('#smart-nav-icon').click(function() {
	if($('#smart-nav').css('display') == 'none') {
		$('#smart-nav').slideDown();
		$('header #smart-nav-icon').css('border', 'none');
		$('header #smart-nav-icon').css('box-shadow', 'none');
	} else {
		$('#smart-nav').slideUp();
		$('header #smart-nav-icon').css('border', '1px solid #424242');
		$('header #smart-nav-icon').css('border-right', 'none');
		$('header #smart-nav-icon').css('box-shadow', '0px 2px 2px rgba(0, 0, 0, 0.3)');
	}
});

$('#smart-nav li a').click(function() {
	$('#smart-nav').fadeOut(100);
	$('header #smart-nav-icon').css('border', '1px solid #424242');
	$('header #smart-nav-icon').css('border-right', 'none');
	$('header #smart-nav-icon').css('box-shadow', '0px 2px 2px rgba(0, 0, 0, 0.3)');
});

$(document).click(function(e) {
	if($('header #smart-nav').css('display') == 'block' && $(e.target).closest('header').length == 0) {
		$('#smart-nav').slideUp();
		$('header #smart-nav-icon').css('border', '1px solid #424242');
		$('header #smart-nav-icon').css('border-right', 'none');
		$('header #smart-nav-icon').css('box-shadow', '0px 2px 2px rgba(0, 0, 0, 0.3)');
	}
});

$('header nav ul li a').click(function(e) {
	$('html,body').animate({scrollTop: $($(this).attr('href')).offset().top}, 'swing');
});

$(window).on('load', function() {
	var offset = $(window).scrollTop() + $(window).height()/2;

    $.each($('section'), function() {
    	if($(this).offset().top < offset) {
    		$.each($('#dumb-nav li a'), function() {
    			$(this).removeClass('currently-hovered');
    		});
    		$('#dumb-nav li a[href="#' + $(this).prop('id') + '-anchor"]').addClass('currently-hovered');
    		return ;
    	}
    });
});

$(window).scroll(function() {
	var offset = $(window).scrollTop() + $(window).height()/2;

    $.each($('section'), function() {
    	if($(this).offset().top < offset) {
    		$.each($('#dumb-nav li a'), function() {
    			$(this).removeClass('currently-hovered');
    		});
    		$('#dumb-nav li a[href="#' + $(this).prop('id') + '-anchor"]').addClass('currently-hovered');
    		return ;
    	}
    });
});


/* ------------------------------- ACCUEIL -------------------------------------- */
$(document).ready(function() {
	if($('#accueil-wrapper').hasClass('background-image')) {
		var image = $('#accueil-wrapper').data('image');
		if(window.innerWidth < 500) {
			var bgUrl = 'www/images/small/' + image.split('.')[0] + '_sm.' + image.split('.')[1];
		} else if(window.innerWidth < 1000) {
			var bgUrl = 'www/images/mediumSmall/' + image.split('.')[0] + '_md_sm.' + image.split('.')[1];
		} else if(window.innerWidth < 1500) {
			var bgUrl = 'www/images/mediumLarge/' + image.split('.')[0] + '_md_lg.' + image.split('.')[1];
		} else {
			var bgUrl = 'www/images/large/' + image.split('.')[0] + '_lg.' + image.split('.')[1];
		}

		$('#accueil-wrapper').attr('style', 
			'background: url(' + bgUrl + ');'
			+ 'background-size:     cover;'
			+ 'background-repeat:   no-repeat;'
			+ 'background-position: center center;'
		);
	}
});


/* ------------------------------- EPISODES -------------------------------------- */
$('.episode-excerpt-header').click(function() {
	$('.episode-excerpt-header').next('.episode-excerpt-content').slideUp();
	
	if($(this).next('.episode-excerpt-content').css('display') == 'none') {
		$(this).next('.episode-excerpt-content').slideDown();
	}
	
});

/* EPISODE PAGE */
$(document).ready(function() {
	if($('.episode-image').length != 0) {
		var image = $('.episode-image').data('image');
		if($('.episode-image').width() < 500) {
			var bgUrl = 'www/images/small/' + image.split('.')[0] + '_sm.' + image.split('.')[1];
		} else if($('.episode-image').width() < 1000) {
			var bgUrl = 'www/images/mediumSmall/' + image.split('.')[0] + '_md_sm.' + image.split('.')[1];
		} else if($('.episode-image').width() < 1500) {
			var bgUrl = 'www/images/mediumLarge/' + image.split('.')[0] + '_md_lg.' + image.split('.')[1];
		} else {
			var bgUrl = 'www/images/large/' + image.split('.')[0] + '_lg.' + image.split('.')[1];
		}

		$('.episode-image').attr('style', 
			'background: url(' + bgUrl + ');'
			+ 'background-size:     cover;'
			+ 'background-repeat:   no-repeat;'
			+ 'background-position: center center;'
		);
	}
});


/* ------------------------- CONTACT --------------------------- */
$('input[name="send-email"]').click(function(e) {
	e.preventDefault();

	$('.contact-input').removeClass('error');
	$('.contact-input-error').css('display', 'none');

	if($('input[name="contactForm-name"]').val() == '') {
		$('input[name="contactForm-name"]').addClass('error');
		$('#contact-input-error-missing-name').css('display', 'block');
	} else if($('input[name="contactForm-email"]').val() == '') {
		$('input[name="contactForm-email"]').addClass('error');
		$('#contact-input-error-missing-email').css('display', 'block');
	} else if($('input[name="contactForm-subject"]').val() == '') {
		$('input[name="contactForm-subject"]').addClass('error');
		$('#contact-input-error-missing-subject').css('display', 'block');
	} else if($('textarea[name="contactForm-message"]').val() == '') {
		$('textarea[name="contactForm-message"]').addClass('error');
		$('#contact-input-error-missing-message').css('display', 'block');
	} else if(!validateEmail($('input[name="contactForm-email"]').val())) {
		$('input[name="contactForm-email"]').addClass('error');
		$('#contact-input-error-invalid-email').css('display', 'block');
	} else {
		$('form').submit();
	}

});

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}


/* ------------------------------- FOOTER -------------------------------------- */
$('html').css('min-height', 'calc(100% - ' + $('header').height() + 'px - ' + $('footer').height() + 'px)');
$('html').css('padding-top', $('header').height() + 'px');
$('html').css('padding-bottom', $('footer').height() + 'px');

if(window.innerWidth > 850) {
	$('footer #credit').css('top', ($('footer').height() - $('footer #credit').height())/2 + 'px');
} else {
	$('footer #credit').css('top', '100px');
}

$(window).resize(function() {
	$('html').css('min-height', 'calc(100% - ' + $('header').height() + 'px - ' + $('footer').height() + 'px)');
	$('html').css('padding-top', $('header').height() + 'px');
	$('html').css('padding-bottom', $('footer').height() + 'px');
	
	if(window.innerWidth > 850) {
		$('footer #credit').css('top', ($('footer').height() - $('footer #credit').height())/2 + 'px');
	} else {
		$('footer #credit').css('top', '100px');
		$('#contact').css('padding-bottom', $('footer').height() + 80 + 'px');
	}
});
