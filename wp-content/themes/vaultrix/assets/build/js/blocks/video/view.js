/******/ (() => { // webpackBootstrap
/*!*************************************!*\
  !*** ./src/js/blocks/video/view.js ***!
  \*************************************/
(function ($) {
  'use strict';

  $(document).on('click', '.dp-video .dp-video__thumbnail-wrapper', function () {
    $(this).parents('.dp-video').find('.dp-video__thumbnail-wrapper').css('display', 'none');
    const media = $(this).parents('.dp-video').find('video').get(0);
    if (media && media.currentSrc) {
      const playPromise = media.play();
      if (playPromise !== undefined) {
        playPromise.catch(() => {});
      }
    }
  });
})(jQuery);
function initDpVideoPlayers() {
  document.querySelectorAll('.dp-video .dp-video__wrapper').forEach(wrapper => {
    const video = wrapper.querySelector('video');
    const bigPlayButton = wrapper.querySelector('.pause-button');
    if (!video || !bigPlayButton) {
      return;
    }
    const playIcon = bigPlayButton.querySelector('i');
    if (!playIcon) {
      return;
    }
    function setPlayIcon() {
      playIcon.classList.remove('fa-play', 'fa-pause', 'fa-history', 'fa-replay');
      if (video.ended) {
        playIcon.classList.add('fa-history');
      } else if (video.paused) {
        playIcon.classList.add('fa-play');
      } else {
        playIcon.classList.add('fa-pause');
      }
    }
    function playPauseVideo(event) {
      if (event) {
        event.preventDefault();
        event.stopPropagation();
      }
      if (video.error || !video.currentSrc) {
        return;
      }
      if (!video.paused && !video.ended) {
        video.pause();
        return;
      }
      const playPromise = video.play();
      if (playPromise !== undefined) {
        playPromise.catch(() => {
          setPlayIcon();
        });
      }
    }
    let touchHandled = false;
    bigPlayButton.addEventListener('touchstart', function (event) {
      event.preventDefault();
      touchHandled = true;
      playPauseVideo(event);
    });
    bigPlayButton.addEventListener('click', function (event) {
      if (touchHandled) {
        touchHandled = false;
        return;
      }
      playPauseVideo(event);
    });
    video.addEventListener('play', setPlayIcon);
    video.addEventListener('pause', setPlayIcon);
    video.addEventListener('ended', setPlayIcon);
    setPlayIcon();
  });
}
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initDpVideoPlayers);
} else {
  initDpVideoPlayers();
}
/******/ })()
;
//# sourceMappingURL=view.js.map