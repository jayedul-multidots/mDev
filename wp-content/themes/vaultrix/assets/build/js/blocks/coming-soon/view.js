/******/ (() => { // webpackBootstrap
/*!*******************************************!*\
  !*** ./src/js/blocks/coming-soon/view.js ***!
  \*******************************************/
(function ($) {
  'use strict';

  const customerStoriesWrp = [];
  $(document).ready(function () {
    // functions calls
    SliderBlock();

    // resize functions calls
    $(window).on('resize', function () {
      SliderBlock();
    });
  });
  function SliderBlock() {
    $('.coming-soon-block-slider').each(function (index) {
      const sliderMain = $(this).find('.coming-soon-block-slider-main');
      const sliderConfig = {
        autoplay: true,
        autoplaySpeed: 3000,
        dots: false,
        infinite: false,
        arrows: true,
        slidesToShow: 3,
        slidesToScroll: 1,
        swipeToSlide: true,
        responsive: [{
          breakpoint: 768,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1,
            dots: true
          }
        }, {
          breakpoint: 481,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true
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