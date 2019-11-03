
jQuery(document).ready(function($) {
	// buttons
	$('.form.button').click(function() {
		window.location = "?gp=form";
	});
	$('.infos.button').click(function() {
		window.location = "?gp=infos";
	});
	$('.good-bye.button').click(function() {
		window.location = "?gp=good-bye";
	});

	// form
    $el = $('.select2.krankenkasse');
	$el.select2({
		placeholder: ""
	}).on('select2:select', function (e) {
		if(e.params.data.id == 'other') {
			$('.other.fields').slideDown("slow");
		} else {
			$('.other.fields').slideUp("slow");			
		}		
	});
	
	// result
    $('.submit-mail').click(function() {
        window.open("mailto:" + $('.mail-to').text() + "?subject=Datenschutzauskunft&body=" + encodeURIComponent($('.mail-text').text()));
	});
});