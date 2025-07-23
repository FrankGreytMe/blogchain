(function($){
	"use strict";

	/*****************************
	 :: Exists function.
	 *****************************/
	if ( ! $.fn.exists ) {
		$.fn.exists = function() {
			return this.length > 0;
		};
	}

	// Initialize when document is ready
	$(document).ready(function() {

		wcr_check_user_status();

		// Login form submission.
		$(document).on('submit', '#wcr-login-form', function(e) {
			e.preventDefault();

			var login_form     = $(e.target);
			var submit_btn     = login_form.find('button[type="submit"]');
			var original_label = submit_btn.text();

			// Show loading state
			submit_btn.text('Logging in...').prop('disabled', true);

			$.post(
				wcr_front_obj.ajax_url,
				{
					action: 'wcr_login',
					email: login_form.find('input[name="email"]').val(),
					password: login_form.find('input[name="password"]').val(),
					nonce: login_form.find('input[name="nonce"]').val()
				},
				function(response) {
					if (response.success) {
						alert('Login successful!');
						location.reload();
					} else {
						alert('Login failed: ' + response.data);
					}
				}
			)
			.done((response) => {
				if (response.success) {
					wcr_show_message('Login successful!', 'success');
					wcr_display_user_data(response.data.user_data);
					login_form.hide();
				} else {
					wcr_show_message('Login failed: ' + response.data, 'error');
				}
			})
			.fail(() => {
				wcr_show_message('Network error. Please try again.', 'error');
			})
			.always(() => {
				submit_btn.text(original_label).prop('disabled', false);
			});
		});

		// Logout button.
		$(document).on('click', '.wcr-logout-btn', function(e) {
			e.preventDefault();

			var $logout_btn = $( this ),
				$logout_wrapper = $logout_btn.closest('.wcr-logout-fields-wrapper');
			console.log( $logout_btn );
			console.log( $logout_wrapper );

			var formData = {
				action: 'wcr_logout',
				nonce: $logout_wrapper.find('input[name="nonce"]').val()
			};
			console.log( formData );

			$.post(
				wcr_front_obj.ajax_url,
				formData
			)
			.done((response) => {
				if (response.success) {
					wcr_show_message('Logged out successfully!', 'success');
					location.reload();
				} else {
					wcr_show_message('Logout failed: ' + response.data, 'error');
				}
			});
		});

		// Auto-refresh user data periodically
		setInterval(() => {
			wcr_refresh_user_data();
		}, 300000); // 5 minutes
	});

	function wcr_show_message(message, type = 'info') {
		var messageClass = type === 'error' ? 'wcr-error' : 'wcr-success';
		var message_html = `<div class="wcr-message ${messageClass}">${message}</div>`;

		// Remove existing messages
		$('.wcr-message').remove();

		// Add new message
		$('#wcr-user-container').prepend(message_html);

		// Auto-remove after 5 seconds
		setTimeout(() => {
			$('.wcr-message').fadeOut();
		}, 5000);
	}

	function wcr_refresh_user_data() {
		if ($('.wcr-user-data').length > 0) {
			wcr_check_user_status();
		}
	}

	function wcr_check_user_status() {
		var formData = {
			action: 'wcr_get_user_data'
		};

		$.post(
			wcr_front_obj.ajax_url,
			formData
		)
		.done((response) => {
			if (response.success) {
				wcr_display_user_data(response.data);
				$('#wcr-login-form').hide();
			}
		});
	}

	function wcr_display_user_data(userData) {
		var container = $('#wcr-user-container');
		if (container.length === 0) return;

		let html = '<div class="wcr-user-data">';
		html += '<h3>Welcome, ' + wcr_escape_html(userData.name || userData.email) + '</h3>';
		html += '<div class="wcr-user-info">';

		if (userData.email) {
			html += '<p><strong>Email:</strong> ' + wcr_escape_html(userData.email) + '</p>';
		}

		if (userData.profile_picture) {
			html += '<p><img src="' + wcr_escape_html(userData.profile_picture) + '" alt="Profile" style="max-width: 100px; border-radius: 50%;"></p>';
		}

		// Add more fields as needed based on xyz.com response
		if (userData.company) {
			html += '<p><strong>Company:</strong> ' + wcr_escape_html(userData.company) + '</p>';
		}

		if (userData.role) {
			html += '<p><strong>Role:</strong> ' + wcr_escape_html(userData.role) + '</p>';
		}

		html += '</div>';
		html += '<button class="wcr-logout-btn" style="margin-top: 10px;">Logout</button>';
		html += '</div>';

		container.html(html);
	}

	function wcr_escape_html(text) {
		var div = document.createElement('div');
		div.textContent = text;
		return div.innerHTML;
	}

})(jQuery);
