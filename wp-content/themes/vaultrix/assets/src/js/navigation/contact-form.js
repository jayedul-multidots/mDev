/* eslint-disable no-undef */
/* eslint-disable object-shorthand */
/* eslint-disable no-console */
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
    const formId = $('#vaultrix-contact-form');
    let data = '';
    $(document).on("click", "#dp-contact-form-submit", function () {
        $('.dp-ajax-success').remove();
        $('.dp-ajax-error').remove();
        $(formId).validate({
            ignore: [],
            errorElement: 'span',
            rules: {
                name: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                },
            },
            messages: {
                name: {
                    required: "Please fill out this field.",
                },
                email: {
                    required: "Please fill out this field.",
                    email: "Please enter valid email address.",
                },
            },
            highlight: function (element) {
                $(element).parent().addClass('mdinc-valid-err');
            },
            unhighlight: function (element) {
                $(element).parent().removeClass('mdinc-valid-err');
            },

            submitHandler: function () {
                const form = $(formId)[0];
                $('.form-submit').addClass('has-loader');
                const recaptchaSecretKey = $('#dp-contact-form-recaptcha-secret-key').val();
                grecaptcha.ready(function () {
                    grecaptcha.execute(recaptchaSecretKey).then(function (token) {
                        $('#dp-contact-form-token').val(token);
                        data = new FormData(form);
                        data.append('action', 'dp_contact_form_submission');

                        // our AJAX identifier.
                        $.ajax({
                            type: 'POST',
                            url: siteConfig.ajaxUrl,
                            data: data,
                            contentType: false,
                            processData: false,

                            success: function (response) {
                                $('.form-submit').removeClass('has-loader');
                                const result = $.parseJSON(response);
                                if (1 === result.success) {
                                    $(formId).append(result.message);
                                    $(formId)[0].reset();
                                } else {
                                    $(formId).append(result.message);
                                }
                            }
                        });
                    });
                });
            }
        });
    });
})(jQuery);