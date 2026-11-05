/******/ (() => { // webpackBootstrap
/*!************************************************!*\
  !*** ./src/js/blocks/menu-card-slider/view.js ***!
  \************************************************/
(function ($) {
  'use strict';

  const customerStoriesWrp = [];
  $(document).ready(function () {
    // Check the screen size before initializing the slider
    if (window.innerWidth > 1150) {
      // functions calls
      SliderBlock();
    }

    // resize functions calls
    $(window).on('resize', function () {
      // Check the screen size on resize and then initialize the slider
      if (window.innerWidth > 1150) {
        SliderBlock();
      } else {
        $('.mega-menu-card-slider-main').slick('unslick');
      }
    });
  });
  function SliderBlock() {
    $('.mega-menu-card-slider').each(function (index) {
      const sliderMain = $(this).find('.mega-menu-card-slider-main');
      const autoplay = sliderMain.attr('data-block-auto-slide') || false;
      const arrows = sliderMain.attr('data-display-arrows') || false;
      const loopSlide = sliderMain.attr('data-slide-loop') || false;
      const dots = sliderMain.attr('data-loop-slide') || false;
      const sliderConfig = {
        autoplay: 'true' === autoplay ? true : false,
        autoplaySpeed: 3000,
        pauseOnHover: 'true' === autoplay ? true : false,
        dots: 'true' === dots ? true : false,
        infinite: 'true' === loopSlide ? true : false,
        arrows: 'true' === arrows ? true : false,
        slidesToShow: 4,
        slidesToScroll: 1,
        swipeToSlide: true,
        responsive: [{
          breakpoint: 767,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1
          }
        }]
      };
      if (customerStoriesWrp[index]) {
        customerStoriesWrp[index].slick('refresh');
      } else {
        customerStoriesWrp[index] = sliderMain.slick(sliderConfig);
      }
    });
  }
})(jQuery);
/******/ })()
;
//# sourceMappingURL=view.js.map