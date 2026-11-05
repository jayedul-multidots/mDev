/******/ (() => { // webpackBootstrap
/*!*********************************************!*\
  !*** ./src/js/blocks/video-gallery/view.js ***!
  \*********************************************/
/* eslint-disable no-console */
(function ($) {
  'use strict';

  $(document).ready(function () {
    $('.video__gallery-tab-content .video-gallery__item__wrapper .video-gallery__item:first-child').addClass('active');
    $(document).on('click', '.video-gallery__item', function () {
      $('.video__gallery-tab-content .video-gallery__item__wrapper .video-gallery__item').removeClass('active');
      // $( this ).parents( '.video-gallery__item' ).toggleClass( 'active' );
      $(this).addClass('active');
    });

    // Video Gallery Tabs
    $(document).on('click', '.video__gallery-tab a', function () {
      const _this = $(this);
      _this.parents('.video-gallery').find('.video__gallery-tab').removeClass('active');
      _this.parent().addClass('active');
      const tabId = _this.attr('href') || '';
      _this.parents('.video-gallery').find('.video__gallery-tab-content').removeClass('active');
      $(tabId).addClass('active');
      $(tabId).find('.video-gallery__item__wrapper .video-gallery__item').removeClass('active');
      $(tabId).find('.video-gallery__item__wrapper .video-gallery__item:first-child').trigger('click');
      $(tabId).find('.video-gallery__item__wrapper .video-gallery__item:first-child').addClass('active');
      return false;
    });

    // Video Gallery Popup
    $(document).on('click', '.video-gallery_main__item_video', function () {
      const _this = $(this);
      _this.parents('.video__gallery-tab-content').find('.video-popup').css('opacity', '1');
      _this.parents('.video__gallery-tab-content').find('.video-popup').addClass('active');
    });
    $(document).on('click', '.video-popup .close-btn', function () {
      $(this).parents('.video-popup').find('.video-popups-wrap').html(''); // phpcs:ignore
      $(this).parents('.video-popup').css('opacity', '0');
      $(this).parents('.video-popup').removeClass('active');
    });
    $(document).on('click', '.video-gallery__item', function () {
      const _this = $(this);
      $.ajax({
        type: 'GET',
        data: {
          action: 'video_main_item',
          post_id: _this.data('post-id')
        },
        url: siteConfig.ajaxUrl,
        // eslint-disable-line
        beforeSend() {
          // _this
          // 	.parents( '.video__gallery-tab-content' )
          // 	.find( '.video-gallery_main__item' )
          // 	.css( 'filter', 'blur(10px)' );
        },
        success(response) {
          const responseObj = $.parseJSON(response);
          _this.parents('.video__gallery-tab-content').find('.video-gallery_main__item').html(responseObj.ajax_response); // phpcs:ignore
          _this.parents('.video__gallery-tab-content').find('.video-gallery_main__item').css('filter', 'none');
        }
      });
    });
  });
})(jQuery);
/******/ })()
;
//# sourceMappingURL=view.js.map