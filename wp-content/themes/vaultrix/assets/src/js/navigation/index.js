/* eslint-disable no-undef */
/* eslint-disable jsdoc/require-param */
/* eslint-disable prettier/prettier */
/* eslint-disable eqeqeq */
/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
(function ($) {
	'use strict';

	$('.menu-toggle').click(function () {
		$('header .main-navigation').toggleClass('toggled');
		$('html').toggleClass('mobile-menu-open');
	});

	// responsive menu css
	$('.main-navigation ul.menu>li.menu-item-has-children').on(
		'click',
		function () {
			const windowWidth = $(window).width();
			if (767 > windowWidth) {
				$(this).find('.sub-menu,.children').slideToggle();
				$(this).toggleClass('active-sub');
			}
		}
	);

	// Add class to mega-menu
	$(
		'.main-navigation ul li.menu-item-has-children > a, .main-navigation ul li.menu-item-has-megamenu > a'
	).append('<i class="fas fa-chevron-down"></i>');
	$(document).on(
		'click',
		'.main-navigation ul li.menu-item-has-megamenu a',
		function () {
			const windowWidth = $(window).width();
			const parentLi = $(this).closest('li');
			if (1151 > windowWidth) {
				parentLi.toggleClass('active-list');
				$(this)
					.closest('li.menu-item-has-megamenu')
					.find('.megamenu-wrap')
					.slideToggle();
			}
		}
	);

	// Sticky Header
	$(window).scroll(function () {
		if ($(window).scrollTop() >= 67) {
			$('.vaultrix-header').addClass('fixed-header');
		} else {
			$('.vaultrix-header').removeClass('fixed-header');
		}
	});

	// search js
	$('header.site-header .header-serch-icon').click(function () {
		$('body').addClass('search-form-visible');
	});
	$('.close-btn').click(function () {
		$('body').removeClass('search-form-visible');
	});

	// Pricing-popup JS
	const popup = $('header .popup');
	const pricingButton = $('header .pricing');
	const signUpButton = $('header .signup-button');
	const loginButton = $('.login-button');
	const nonmemberLogedInUserButton = $('.member-warning-message span');

	$(pricingButton).click(function (e) {
		e.preventDefault();
		$('html').addClass('show-popup');
		$(popup).css('display', 'block');
		$(this).parents('ul').find('li').removeClass('current-menu-item');
		$(this).addClass('current-menu-item');
		$('html').css('height', '100%');
		$('header .signup-popup').css('display', 'none');
		$('header .pricing-popup').css('display', 'block');
		$('header .login-popup').css('display', 'none');
		$('header .forgot-popup').css('display	', 'none');
		if ($('#site-navigation').hasClass('toggled')) {
			$('#site-navigation').removeClass('toggled');
		}
	});

	$(signUpButton).add(nonmemberLogedInUserButton).click(function (e) {
		e.preventDefault();
		$('html').addClass('show-popup');
		$(popup).css('display', 'block');
		$('html').css('height', '100%');
		$('header .pricing-popup').css('display', 'none');
		$('header .signup-popup').css('display', 'block');
		$('header .login-popup').css('display', 'none');
		$('header .forgot-popup').css('display', 'none');
		if ($(pricingButton).hasClass('current-menu-item')) {
			$(pricingButton).removeClass('current-menu-item');
		}
		if ($('#site-navigation').hasClass('toggled')) {
			$('#site-navigation').removeClass('toggled');
		}
	});

	$(document).on('click', '.login-button, .login-button a', function (e) {
		e.preventDefault();
		$('html').addClass('show-popup');
		$(popup).css('display', 'block');
		$('html').css('height', '100%');
		$('header .pricing-popup').css('display', 'none');
		$('header .signup-popup').css('display', 'none');
		$('header .login-popup').css('display', 'block');
		$('header .forgot-popup').css('display', 'none');
		if ($(pricingButton).hasClass('current-menu-item')) {
			$(pricingButton).removeClass('current-menu-item');
		}
		if ($(loginButton).hasClass('current-menu-item')) {
			$(loginButton).removeClass('current-menu-item');
		}
		if ($('#site-navigation').hasClass('toggled')) {
			$('#site-navigation').removeClass('toggled');
		}
		// Remove previous error message
		$('#forgot-error-message').html('');
		$('#forgot-error-message').removeClass('pmpro_message pmpro_error');

		$('#login-error-message').html('');
		$('#login-error-message').removeClass('pmpro_message pmpro_error');
	});

	$(document).on("click", 'header .forgot-button', function (e) {
		e.preventDefault();
		$('html').addClass('show-popup');
		$(popup).css('display', 'block');
		$('html').css('height', '100%');
		$('header .pricing-popup').css('display', 'none');
		$('header .signup-popup').css('display', 'none');
		$('header .login-popup').css('display', 'none');
		$('header .forgot-popup').css('display', 'block');
		if ($(loginButton).hasClass('current-menu-item')) {
			$(loginButton).removeClass('current-menu-item');
		}
		if ($('#site-navigation').hasClass('toggled')) {
			$('#site-navigation').removeClass('toggled');
		}
		// Remove previous error message
		$('#forgot-error-message').html('');
		$('#forgot-error-message').removeClass('pmpro_message pmpro_error');

		$('#login-error-message').html('');
		$('#login-error-message').removeClass('pmpro_message pmpro_error');
	});

	$('.popup-close').click(function (e) {
		e.preventDefault();
		$('html').removeClass('show-popup');
		$('html').removeClass('mobile-menu-open');
		$(popup).css('display', 'none');
		$(this).addClass('current-menu-item');
		$('html').css('height', '');
		$('header .pricing-popup').css('display', 'none');
		$('header .signup-popup').css('display', 'none');
		$('header .login-popup').css('display', 'none');
		$('header .forgot-popup').css('display', 'none');
		if ($(pricingButton).hasClass('current-menu-item')) {
			$(pricingButton).removeClass('current-menu-item');
		}
		// Remove previous error message
		$('#forgot-error-message').html('');
		$('#forgot-error-message').removeClass('pmpro_message pmpro_error');

		$('#login-error-message').html('');
		$('#login-error-message').removeClass('pmpro_message pmpro_error');
	});

	$(document).on("click", '.change-password-button', function (e) {
		e.preventDefault();
		$('html').addClass('show-popup');
		$(popup).css('display', 'block');
		$(this).addClass('current-menu-item');
		$('html').css('height', '100%');
		$('header .change-password-popup').css('display', 'block');
	});

	$(document).keydown(function (event) {
		if ($('html').hasClass('show-popup')) {
			if (event.keyCode == 27) {
				$('html').removeClass('show-popup');
				$(popup).css('display', 'none');
				$(this).addClass('current-menu-item');
				$('html').css('height', '');
				$('header .pricing-popup').css('display', 'none');
				$('header .signup-popup').css('display', 'none');
				$('header .login-popup').css('display', 'none');
				$('header .forgot-popup').css('display', 'none');
				if ($(pricingButton).hasClass('current-menu-item')) {
					$(pricingButton).removeClass('current-menu-item');
				}
			}
		}
	});

	$(document).on("click", '.change-billing-button', function (e) {
		e.preventDefault();
		// $('html').addClass('show-popup');
		$('html').css('height', '100%');
		$('html').css('overflow', 'hidden');
	});

	// close the welcome popup js.
	$(document).on("click", '.welcome-popup .welcome-popup-close', function () {
		$('.welcome-popup').css('display', 'none');
		$('html').css({
			'height': '',
			'overflow': ''
		});

		const url = new URL(window.location.href);
		url.searchParams.delete('welcome');
		window.history.replaceState({}, '', url);
	});

	if ($('body .welcome-popup').length > 0) {
		// Add height: 100% and overflow: hidden to the html tag
		$('html').css({
			'height': '100%',
			'overflow': 'hidden'
		});
	}


	$(document).ready(function () {
		// Password view and hide
		$(document).on("click", '.show-password', function () {
			// toggle the type attribute
			const password = $(this).siblings('input');
			const type = password.attr('type') === 'password' ? 'text' : 'password';
			password.attr('type', type);
			// toggle the eye slash icon
			$(this).toggleClass('fa-eye-slash');
		});

		// Delete user
		$(document).on("click", '.dp-delete-button', function () {
			const text = siteConfig.deleteAccountText;
			// eslint-disable-next-line no-alert
			if (confirm(text) === true) {
				// Trigger AJAX request to server
				$.ajax({
					type: 'POST',
					url: siteConfig.ajaxUrl,
					data: {
						action: 'delete_user'
					},
					success(response) {
						response = JSON.parse(response);
						if (response.success) {
							window.location.replace(response.data.redirecturl);
						} else {
							// eslint-disable-next-line no-alert
							alert(response.data.message);
						}
					}
				});
			}
		});

		// Histrory back button
		$(document).on("click", '.pmpro_history_back', function (e) {
			e.preventDefault();
			window.history.back();
		});

		$('#avatar-upload-form').submit(function (e) {
			e.preventDefault();
			$('.profile-submit').addClass('loader-show');
			const formData = new FormData(this);
			formData.append('action', 'handle_avatar_upload');
			formData.append('nonce', siteConfig.ajax_nonce);
			$.ajax({
				type: 'POST',
				url: siteConfig.ajaxUrl,
				data: formData,
				processData: false, // Important! To send file data properly
        		contentType: false, 
				success(response) {
					if(response){
						setTimeout(() => {
							location.replace(response);
						}, 800);
					}
				}
			});
		});
	});

	//Window height related js
	window.addEventListener('DOMContentLoaded', () => {
		adjustFooterPosition();
	});

	window.addEventListener('resize', () => {
		adjustFooterPosition();
	});

	function adjustFooterPosition() {
		const content = document.querySelector('.site-main');
		const footer = document.querySelector('.site-footer');

		const contentHeight = content.offsetHeight;
		const footerHeight = footer.offsetHeight;

		const windowHeight = window.innerHeight;
		const bodyHeight = document.body.offsetHeight;

		if (bodyHeight < windowHeight) {
			const remainingHeight = windowHeight - (contentHeight + footerHeight);
			if (remainingHeight > 0) {
				content.style.minHeight = `${contentHeight + remainingHeight}px`;
				// content.style.display = "flex";
				// content.style.alignItems = "center";
				// content.style.justifyContent = "center";
			}
		}
	}

})(jQuery);