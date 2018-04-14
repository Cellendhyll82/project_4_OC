$('.comment-episodes-wrapper .comment-episode').click(function(){
    $(this).parent().find('.comment-episode').removeClass('selected');
    $(this).addClass('selected');
    var val = $(this).attr('data-value');
    $(this).parent().find('input').val(val);
});

$(document).ready(function() {
	var id = $('input[name="comment-episodeId"]').val();
	$('#comment-episode-' + id).addClass('selected');
});