/******/ (() => { // webpackBootstrap
/*!************************************************************!*\
  !*** ./src/js/blocks/recently-added-video-listing/view.js ***!
  \************************************************************/
/* eslint-disable no-unused-vars */
(function ($) {
  'use strict';

  const videoSliderWrp = [];
  function buildTrailerPopupHtml(url, type) {
    const typeAttr = type ? ` type="${type}"` : '';
    return `
			<div class="watch-video-loader" id="watch-video-loader"></div>
			<div class="watch-video-content watch-trailer-content" id="watch-video-content">
				<div class="watch-trailer-back-button"></div>
				<video controls playsinline preload="metadata">
					<source src="${url}"${typeAttr}>
				</video>
			</div>
		`;
  }
  document.addEventListener('click', function (event) {
    const button = event.target.closest('.recently-added-video-listing .play-from-beginning[data-trailer-url]');
    if (!button) {
      return;
    }
    event.preventDefault();
    event.stopImmediatePropagation();
    const trailerUrl = button.getAttribute('data-trailer-url');
    const trailerType = button.getAttribute('data-trailer-type') || '';
    if (!trailerUrl) {
      return;
    }
    const wrapper = button.closest('.video-popup-wrapper');
    if (wrapper) {
      wrapper.classList.remove('video-mini-popup-show');
      const hoverVideo = wrapper.querySelector('.video-hover-popup video');
      if (hoverVideo) {
        hoverVideo.pause();
      }
    }
    $('html').removeClass('html-video-mini-popup-show');
    $('.video-popup-wrapper-with-slider').removeClass('video-popup-open');
    $('html').addClass('video-video-popup-show');
    $('.video-watch-popup').addClass('open');
    $('.video-watch-popup').empty().html(buildTrailerPopupHtml(trailerUrl, trailerType));
    setTimeout(() => {
      $('.video-watch-popup .watch-video-loader').remove();
      const video = $('.video-watch-popup video').get(0);
      if (video) {
        const playPromise = video.play();
        if (playPromise !== undefined) {
          playPromise.catch(() => {});
        }
      }
    }, 500);
  }, true);
  $(document).ready(function () {
    videoSliderFunc();
    $(window).on('resize', function () {
      videoSliderFunc();
    });
    $('.recently-added-video-listing__boxes').on('setPosition', function (event, slick) {
      // Remove the class from all slides
      $('.recently-added-video-listing__boxes .slick-slide').removeClass('slider-active-last');

      // Calculate the index of the 6th visible slide based on window width and add class accordingly
      let indexToAddClass;
      const windowWidth = $(window).width();
      if (windowWidth < 480) {
        indexToAddClass = 1;
      } else if (windowWidth < 576) {
        indexToAddClass = 2;
      } else {
        indexToAddClass = 3;
      }
      if (indexToAddClass) {
        $('.recently-added-video-listing__boxes .slick-active').eq(indexToAddClass).addClass('slider-active-last');
      }
    });
  });
  function videoSliderFunc() {
    $('.recently-added-video-listing').each(function (index) {
      const sliderMain = $(this).find('.recently-added-video-listing__boxes');
      const autoplay = $(this).attr('data-block-auto-slide'),
        arrows = $(this).attr('data-display-arrows');
      const sliderConfig = {
        slidesToShow: 4,
        slidesToScroll: 1,
        autoplay: 'true' === autoplay ? true : false,
        arrows: 'true' === arrows ? true : false,
        dots: false,
        infinite: false,
        responsive: [{
          breakpoint: 576,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 1
          }
        }, {
          breakpoint: 480,
          settings: {
            slidesToShow: 2.2,
            slidesToScroll: 1
          }
        }]
      };
      // Check if the slider section is in the header and media width is less than 910
      const isHeaderSlider = $(this).closest('.vaultrix-header').length > 0;
      const isMediaWidthLessThan910 = $(window).width() < 1025;
      if (isHeaderSlider && isMediaWidthLessThan910) {
        // Don't initialize the slick slider
        if (videoSliderWrp[index]) {
          videoSliderWrp[index].slick('unslick');
          videoSliderWrp[index] = null;
        }
      } else {
        // Initialize the slick slider
        if (videoSliderWrp[index]) {
          videoSliderWrp[index].slick('refresh');
        } else {
          videoSliderWrp[index] = sliderMain.slick(sliderConfig);
        }
      }
    });
  }
})(jQuery);
/******/ })()
;
//# sourceMappingURL=view.js.map