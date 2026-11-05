/******/ (() => { // webpackBootstrap
/*!****************************************************!*\
  !*** ./src/js/blocks/video-listing-filter/view.js ***!
  \****************************************************/
(function ($) {
  'use strict';

  $(document).ready(function () {
    tabSliderFunc();
    $(window).on('resize', function () {
      tabSliderFunc();
    });
  });
  function tabSliderFunc() {
    jQuery(`.video-listing-filter .video-listing-filter__category-tab-wrapper`).not('.slick-initialized').slick({
      infinite: false,
      slidesToShow: 6,
      slidesToScroll: 1,
      dots: false,
      arrows: true,
      autoplay: false,
      variableWidth: true,
      responsive: [{
        breakpoint: 911,
        settings: {
          slidesToShow: 4.5,
          slidesToScroll: 1
        }
      }, {
        breakpoint: 767,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1
        }
      }]
    });
  }
})(jQuery);
/******/ })()
;
//# sourceMappingURL=view.js.map