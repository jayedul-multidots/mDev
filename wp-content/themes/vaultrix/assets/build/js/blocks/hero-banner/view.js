/******/ (() => { // webpackBootstrap
/*!*******************************************!*\
  !*** ./src/js/blocks/hero-banner/view.js ***!
  \*******************************************/
(function ($) {
  'use strict';

  // Wait for the document to be ready
  $(document).ready(function () {
    // Get all the elements with the class "hero-banner__highlighted-text"
    const highlightedTextElements = $('.hero-banner__highlighted-text');

    // Get the "highlighted-text" span element
    const highlightedSpan = $('.hero-banner__wrapper .highlighted-text');

    // Initialize an index to keep track of the current element
    let currentIndex = 0;

    // Function to loop and print the highlighted text with a fade-in/fade-out effect
    function loopHighlightedText() {
      if (currentIndex < highlightedTextElements.length) {
        // Fade out the current text
        highlightedSpan.fadeOut(500, function () {
          // Update the text and fade it back in
          highlightedSpan.text(highlightedTextElements.eq(currentIndex).text()).fadeIn(500);
          currentIndex++;
          // Call the function recursively after a delay
          setTimeout(loopHighlightedText, 2000);
        });
      } else {
        // Reset the index to loop back to the beginning
        currentIndex = 0;
        // Call the function recursively after a delay
        setTimeout(loopHighlightedText, 2000);
      }
    }

    // Start the loop
    loopHighlightedText();
  });
})(jQuery);
/******/ })()
;
//# sourceMappingURL=view.js.map