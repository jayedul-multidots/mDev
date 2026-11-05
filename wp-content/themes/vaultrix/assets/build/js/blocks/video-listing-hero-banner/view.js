/******/ (() => { // webpackBootstrap
/*!*********************************************************!*\
  !*** ./src/js/blocks/video-listing-hero-banner/view.js ***!
  \*********************************************************/
/* eslint-disable no-var */
(function ($) {
  'use strict';

  const videoSliderWrp = [];
  $(document).ready(function () {
    videoSliderFunc();
    $(window).on('resize', function () {
      videoSliderFunc();
    });
  });
  function videoSliderFunc() {
    $('.video-listing-hero-banner').each(function (index) {
      const sliderMain = $(this).find('.video-listing-hero-banner__boxes');
      const arrows = sliderMain.attr('data-display-arrows') || false;
      const dots = sliderMain.attr('data-block-dots') || false;
      const sliderConfig = {
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        arrows: 'true' === arrows ? true : false,
        dots: 'true' === dots ? true : false,
        appendDots: $(this).find('.video-listing-hero-banner__navigation'),
        infinite: false,
        responsive: [{
          breakpoint: 576,
          settings: {
            arrows: false
          }
        }]
      };
      if (videoSliderWrp[index]) {
        videoSliderWrp[index].slick('refresh');
      } else {
        videoSliderWrp[index] = sliderMain.slick(sliderConfig);
      }
      sliderMain.on('beforeChange', function () {
        const playlistPopup = $('.video-listing-hero-banner #playlist-popup');
        if (playlistPopup) {
          playlistPopup.remove();
          $('.video-listing-hero-banner .pl-icon').css('pointer-events', 'auto');
          $('.video-listing-hero-banner__navigation').show();
          var slider = $('.video-listing-hero-banner__boxes');
          slider.slick('slickPlay');
          slider.slick('slickSetOption', 'autoplay', true);
          $('.video-listing-hero-banner__pause-btn').removeClass('paused');
        }
      });
    });
  }
  // Toggle Autoplay on button click
  $('.video-listing-hero-banner .video-listing-hero-banner__pause-btn').on('click', function () {
    var slider = $('.video-listing-hero-banner__boxes');

    // Check if currently autoplaying
    var isAutoplaying = slider.slick('slickGetOption', 'autoplay');
    if (isAutoplaying) {
      slider.slick('slickPause');
      slider.slick('slickSetOption', 'autoplay', false);
      $('.video-listing-hero-banner__pause-btn').addClass('paused');
    } else {
      slider.slick('slickPlay');
      slider.slick('slickSetOption', 'autoplay', true);
      $('.video-listing-hero-banner__pause-btn').removeClass('paused');
    }
  });
  $(document).on('click', '.video-listing-hero-banner .playlist-popup-button .not-added-to-playlist', function (e) {
    e.preventDefault();
    $('.video-listing-hero-banner__navigation').hide();
    var slider = $('.video-listing-hero-banner__boxes');

    // Check if currently autoplaying
    var isAutoplaying = slider.slick('slickGetOption', 'autoplay');
    if (isAutoplaying) {
      slider.slick('slickPause');
      slider.slick('slickSetOption', 'autoplay', false);
      $('.video-listing-hero-banner__pause-btn').addClass('paused');
    }
  });
  $(document).on('click', '.video-listing-hero-banner .playlist-popup-button .playlist-popup-close', function (e) {
    e.preventDefault();
    $('.video-listing-hero-banner__navigation').show();
    $('.video-listing-hero-banner #playlist-popup').remove();
    $('.pl-icon').css('pointer-events', 'auto');
    var slider = $('.video-listing-hero-banner__boxes');
    slider.slick('slickPlay');
    slider.slick('slickSetOption', 'autoplay', true);
    $('.video-listing-hero-banner__pause-btn').removeClass('paused');
  });
})(jQuery);
/******/ })()
;
//# sourceMappingURL=view.js.map