(function($){
	"use strict";

	/*****************************
	 :: Exists function.
	 *****************************/
	if (!$.fn.exists) {
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

		if ( $('.single-post-style').exists() ) {
			single_post_menu();
		}
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

	function single_post_menu() {
		var post_wrap        = $('.single-post-style'),
			sticky_menu_wrap = post_wrap.find('.post-sticky-menu-wrap'),
			position_top     = 122,
			position_right   = 355;

		var post_wrap_top   = get_post_wrap_top(post_wrap),
			post_wrap_right = get_post_wrap_right(post_wrap);

		sticky_menu_wrap.css({
			'--wcr_post_sticky_menu_top': post_wrap_top + 'px',
			'--wcr_post_sticky_menu_right': post_wrap_right + 'px',
		});

		/*
		sticky_menu_wrap.stop(true, true).animate({
			opacity: 1
		}, 200);
		sticky_menu_wrap.stop(true, true).queue(function(next) {
			// show_loader();
			sticky_menu_wrap.css({
				// '--wcr_post_sticky_menu_opacity': 1,
			});

			// Move to the next item in the queue (if any)
			next();
		});
		*/

		$(window).off('scroll.psmi').on('scroll.psmi', function(){
			sticky_menu_wrap.addClass('show');
		});

		if ($('.post-sticky-menu-item.psmi-play-video').exists() && $('.psmi-video').exists()) {
			var psmi_video_btn       = sticky_menu_wrap.find('.post-sticky-menu-item.psmi-play-video'),
				psmi_video           = sticky_menu_wrap.find('.psmi-video'),
				psmi_video_close_btn = psmi_video.find('.psmi-video-close-btn'),
				video_iframe         = psmi_video.find('iframe'),
				video_src            = video_iframe.attr('src'); // store original src

			// FIX: remove old click bindings before adding new
			psmi_video_btn.off('click').on('click', function(e) {
				e.preventDefault();
				// console.log( $(this) );
				if ($(this).hasClass('active')) {
					psmi_close_video_popup();
				} else {
					psmi_video_btn.addClass('active');
					psmi_video.addClass('active');
					// $('body').addClass('no-scroll'); // disable scroll
					// video_iframe.attr('src', video_src); // restore video src when opening
					// video_iframe.attr('src', get_embed_src(video_src));
					setTimeout(function() {
						$(window).trigger('resize');
					}, 300);
				}
				console.log($(this));
			});

			psmi_video_close_btn.off('click').on('click', function() {
				psmi_close_video_popup();
			});

			// Click outside popup closes it
			$(document).off('click.psmiOutside').on('click.psmiOutside', function(e) {
				if (
					psmi_video.hasClass('active') &&
					!$(e.target).closest('.psmi-video, .psmi-play-video').length
				) {
					psmi_close_video_popup();
				}
			});

			// Clicking other sticky menu buttons also closes video popup
			sticky_menu_wrap.find('.post-sticky-menu-item').not('.psmi-play-video')
				.off('click.psmiOther')
				.on('click.psmiOther', function() {
					if (psmi_video.hasClass('active')) {
						psmi_close_video_popup();
					}
				});

			// ESC key closes popup
			$(document).off('keydown.psmiEsc').on('keydown.psmiEsc', function(e) {
				if (e.key === "Escape" && psmi_video.hasClass('active')) {
					psmi_close_video_popup();
				}
			});

			function psmi_close_video_popup() {
				psmi_video_btn.removeClass('active');
				psmi_video.removeClass('active');
				// $('body').removeClass('no-scroll'); // re-enable scroll
				// video_iframe.attr('src', ''); // stop YouTube video
			}
		}
	}

	function get_embed_src(original_src) {
		if (!original_src) return '';

		let video_id = '';

		// Extract from watch?v=
		if (original_src.includes('watch?v=')) {
			video_id = original_src.split('watch?v=')[1].split('&')[0];
		}
		// Extract from youtu.be short link
		else if (original_src.includes('youtu.be/')) {
			video_id = original_src.split('youtu.be/')[1].split('?')[0];
		}
		// Extract from shorts
		else if (original_src.includes('/shorts/')) {
			video_id = original_src.split('/shorts/')[1].split('?')[0];
		}
		// Already embed format
		else if (original_src.includes('/embed/')) {
			video_id = original_src.split('/embed/')[1].split('?')[0];
		}

		if (!video_id) return original_src; // fallback

		// Return safe embed format
		return `https://www.youtube.com/embed/${video_id}?rel=0&autoplay=1&modestbranding=1&playsinline=1`;
	}


	$(window).on('resize orientationchange', function() {
		single_post_menu();
	});

	function get_post_wrap_top() {
		var content_top = parseInt($('#content').css('padding-top'), 10),
			primary_top = parseInt($('#primary').css('margin-top'), 10);
		return content_top + primary_top;
	}

	function get_post_wrap_right(post_wrap) {
		var offset          = post_wrap.offset(),
			width           = post_wrap.outerWidth(),
			post_wrap_right = offset.left + width,
			viewport_width  = $(window).width();

		return viewport_width - post_wrap_right;
	}
})(jQuery);
