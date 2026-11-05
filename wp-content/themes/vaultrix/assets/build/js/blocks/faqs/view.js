/******/ (() => { // webpackBootstrap
/*!************************************!*\
  !*** ./src/js/blocks/faqs/view.js ***!
  \************************************/
(function ($) {
  'use strict';

  $(document).ready(function () {
    $(document).on('click', '.faq-listing__item-header', function () {
      $(this).siblings('.faq-listing__item-desc').slideToggle('slow');
      $(this).parent().toggleClass('active');
    });
  });
})(jQuery);
/******/ })()
;
//# sourceMappingURL=view.js.map