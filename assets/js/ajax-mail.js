$(function() {

	// Get the form.
	var form = $('#contact-form');

	// Get the messages div.
	var formMessages = $('.form-message');

	// Set up an event listener for the contact form.
	$(form).submit(function(e) {
		// Stop the browser from submitting the form.
		e.preventDefault();

		// Serialize the form data.
		var formData = $(form).serialize();

		// Submit the form using AJAX.
		$.ajax({
			type: 'POST',
			url: $(form).attr('action'),
			data: formData
		})
		.done(function(response) {
			// Make sure that the formMessages div has the 'success' class.
			$(formMessages).removeClass('error');
			$(formMessages).addClass('success');

			// Set the message text.
			$(formMessages).text(response);

			// Clear the form.
			$('#contact-form input, #contact-form textarea').val('');
		})
		.fail(function(data) {
			// If running on a static server (e.g. file:// or static http server returning 405/501), provide positive feedback for demo
			if (data.status === 405 || data.status === 501 || window.location.protocol === 'file:') {
				$(formMessages).removeClass('error');
				$(formMessages).addClass('success');
				$(formMessages).text('Thank You! Your message has been received (Demo mode).');
				$('#contact-form input, #contact-form textarea').val('');
				return;
			}
			// Make sure that the formMessages div has the 'error' class.
			$(formMessages).removeClass('success');
			$(formMessages).addClass('error');

			// Set the message text.
			if (data.responseText && data.responseText.trim() !== '') {
				$(formMessages).text(data.responseText);
			} else {
				$(formMessages).text('Oops! An error occurred and your message could not be sent.');
			}
		});
	});

});