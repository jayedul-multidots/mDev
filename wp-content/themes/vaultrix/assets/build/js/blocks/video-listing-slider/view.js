/******/ (() => { // webpackBootstrap
/*!****************************************************!*\
  !*** ./src/js/blocks/video-listing-slider/view.js ***!
  \****************************************************/
(function ($) {
  'use strict';

  const videoSliderWrp = [];
  $(document).ready(function () {
    videoSliderFunc();
    $(window).on('resize', function () {
      videoSliderFunc();
    });
    $('.video-listing-slider').each(function () {
      $(this).on('setPosition', function (event, slick) {
        // Remove the class from all slides
        $(this).find('.slick-slide').removeClass('slider-active-last');

        // Calculate the index of the 6th visible slide based on window width and add class accordingly
        let indexToAddClass;
        const windowWidth = $(window).width();
        if (windowWidth < 481) {
          indexToAddClass = 1;
        } else if (windowWidth < 576) {
          indexToAddClass = 2;
        } else if (windowWidth < 768) {
          indexToAddClass = 3;
        } else if (windowWidth < 991) {
          indexToAddClass = 4;
        } else {
          indexToAddClass = 5;
        }

        // Add the class to the calculated index
        $(this).find('.slick-active').eq(indexToAddClass).addClass('slider-active-last');
      });
    });
  });
  function videoSliderFunc() {
    $('.video-listing-slider').each(function (index) {
      const sliderMain = $(this).find('.video-listing-slider__boxes');
      const autoplay = $(this).attr('data-block-auto-slide'),
        arrows = $(this).attr('data-display-arrows');
      const sliderConfig = {
        slidesToShow: 6,
        slidesToScroll: 1,
        autoplay: 'true' === autoplay ? true : false,
        arrows: 'true' === arrows ? true : false,
        dots: false,
        infinite: false,
        responsive: [{
          breakpoint: 991,
          settings: {
            slidesToShow: 5.2,
            slidesToScroll: 1
          }
        }, {
          breakpoint: 768,
          settings: {
            slidesToShow: 4,
            slidesToScroll: 1
          }
        }, {
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
      if (videoSliderWrp[index]) {
        videoSliderWrp[index].slick('refresh');
      } else {
        videoSliderWrp[index] = sliderMain.slick(sliderConfig);
      }
    });
  }
})(jQuery);
/******/ })()
;
//# sourceMappingURL=view.js.map