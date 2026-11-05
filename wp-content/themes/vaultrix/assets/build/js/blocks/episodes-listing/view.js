/******/ (() => { // webpackBootstrap
/*!************************************************!*\
  !*** ./src/js/blocks/episodes-listing/view.js ***!
  \************************************************/
(function ($) {
  'use strict';

  const episodeSliderWrp = [];
  $(document).ready(function () {
    episodeSliderFunc();
    $(window).on('resize', function () {
      episodeSliderFunc();
    });
  });
  function episodeSliderFunc() {
    $('.episodes-listing').each(function (index) {
      const sliderMain = $(this).find('.episodes-listing__boxes');
      const sliderConfig = {
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: false,
        arrows: true,
        dots: false,
        infinite: false,
        responsive: [{
          breakpoint: 768,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1
          }
        }, {
          breakpoint: 481,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            dots: true
          }
        }]
      };
      if (episodeSliderWrp[index]) {
        episodeSliderWrp[index].slick('refresh');
      } else {
        episodeSliderWrp[index] = sliderMain.slick(sliderConfig);
      }
    });
  }
})(jQuery);
/******/ })()
;
//# sourceMappingURL=view.js.map