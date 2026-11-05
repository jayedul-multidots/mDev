/******/ (() => { // webpackBootstrap
/*!*******************************************!*\
  !*** ./src/js/blocks/my-playlist/view.js ***!
  \*******************************************/
/* eslint-disable no-unused-vars */
/* eslint-disable no-else-return */
/* eslint-disable no-alert */
(function ($) {
  'use strict';

  $(document).ready(function () {
    $(document).on('click', '.recently-added .added-to-playlist', function () {
      if ($('.playlist-listing .playlist-listing__video-wrapper').hasClass('slick-initialized')) {
        $('.playlist-listing .playlist-listing__video-wrapper').slick('unslick');
      }
      const postID = $(this).parents('.playlist-popup-button').attr('data-post-id');
      const playlistSlug = $('.playlist-listing__playlist-tab p.active').attr('data-playlist-slug');
      $.ajax({
        type: 'GET',
        data: {
          action: 'dp_remove_recently_added_video',
          pid: postID,
          playlist_slug: playlistSlug
        },
        url: siteConfig.ajaxUrl,
        // eslint-disable-line
        beforeSend() {
          $('.playlist-listing .playlist-listing__video-wrapper').remove(); // phpcs:ignore
          $('.recently-added .recently-added__videos').empty();
          $('.recently-added .recently-added__videos').append('<div class="playlist-listing__loader"><span class="fa fa-spinner fa-pulse"></span></div>');
          $('.playlist-listing__loader').remove();
          $('.playlist-listing .playlist-listing__video-section').empty().append('<div class="playlist-listing__loader"><span class="fa fa-spinner fa-pulse"></span></div>');
        },
        success(response) {
          $('.playlist-listing__loader').remove();
          const responseArray = $.parseJSON(response);
          $('.playlist-listing .playlist-listing__video-section').append(responseArray.playlist_video_html);
          if (responseArray.recently_added_video_html) {
            $('.recently-added .recently-added__videos').html(responseArray.recently_added_video_html);
          } else {
            $('.recently-added').remove();
          }
          videoSliderFunc();
        }
      });
      // return false;
    });
  });
  const videoSliderWrp = [];
  const seriesSliderWrp = [];
  const episodeSliderWrap = [];
  $(document).ready(function () {
    $('.playlist-listing .playlist-listing__playlist-tab:first-child p').addClass('active');
    seriesSliderFunc();
    episodeSliderFunc();
    videoSliderFunc();
    $(window).on('resize', function () {
      seriesSliderFunc();
      episodeSliderFunc();
      videoSliderFunc();
    });
    tabSliderFunc();
    $('.playlist-listing__series-episodes-wrapper').on('setPosition', function (event, slick) {
      // Remove the class from all slides
      $('.playlist-listing__series-episodes-wrapper .slick-slide').removeClass('slider-active-last');

      // Calculate the index of the 6th visible slide based on window width and add class accordingly
      let indexToAddClass;
      const windowWidth = $(window).width();
      if (windowWidth < 481) {
        indexToAddClass = 1;
      } else if (windowWidth < 576) {
        indexToAddClass = 2;
      } else if (windowWidth < 768) {
        indexToAddClass = 3;
      } else if (windowWidth < 1025) {
        indexToAddClass = 4;
      } else {
        indexToAddClass = 5;
      }
      if (indexToAddClass) {
        $('.playlist-listing__series-episodes-wrapper .slick-active').eq(indexToAddClass).addClass('slider-active-last');
      }
    });
    $('.playlist-listing__video-wrapper').on('setPosition', function (event, slick) {
      // Remove the class from all slides
      $('.playlist-listing__video-wrapper .slick-slide').removeClass('slider-active-last');

      // Calculate the index of the 6th visible slide based on window width and add class accordingly
      let indexToAddClass;
      const windowWidth = $(window).width();
      if (windowWidth < 481) {
        indexToAddClass = 1;
      } else if (windowWidth < 576) {
        indexToAddClass = 2;
      } else if (windowWidth < 768) {
        indexToAddClass = 3;
      } else if (windowWidth < 1025) {
        indexToAddClass = 4;
      } else {
        indexToAddClass = 5;
      }
      if (indexToAddClass) {
        $('.playlist-listing__video-wrapper .slick-active').eq(indexToAddClass).addClass('slider-active-last');
      }
    });
  });
  function seriesSliderFunc() {
    $('.playlist-listing').each(function (index) {
      const seriesSliderMain = $(this).find('.playlist-listing__series-wrapper');
      const seriesSliderConfig = {
        slidesToShow: 4,
        slidesToScroll: 1,
        autoplay: true,
        arrows: true,
        dots: false,
        infinite: false,
        responsive: [{
          breakpoint: 1025,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 1
          }
        }, {
          breakpoint: 768,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1,
            arrows: false,
            dots: true
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
      if (seriesSliderWrp[index]) {
        seriesSliderWrp[index].slick('refresh');
      } else {
        seriesSliderWrp[index] = seriesSliderMain.slick(seriesSliderConfig);
      }
      if (!seriesSliderMain.hasClass('slick-initialized')) {
        // If not initialized, initialize Slick slider
        seriesSliderMain.slick(seriesSliderConfig);
      }
      if (seriesSliderMain.find('.slick-dots li').length === 1) {
        seriesSliderMain.find('.slick-dots').css('display', 'none');
      }
    });
  }
  function episodeSliderFunc() {
    $('.playlist-listing').each(function (index) {
      const epSliderMain = $(this).find('.playlist-listing__series-episodes-wrapper');
      const epSliderConfig = {
        slidesToShow: 6,
        slidesToScroll: 1,
        autoplay: true,
        arrows: true,
        dots: false,
        infinite: false,
        responsive: [{
          breakpoint: 1025,
          settings: {
            slidesToShow: 5,
            slidesToScroll: 1
          }
        }, {
          breakpoint: 768,
          settings: {
            slidesToShow: 4,
            slidesToScroll: 1,
            arrows: false,
            dots: true
          }
        }, {
          breakpoint: 576,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 1,
            arrows: false,
            dots: true
          }
        }, {
          breakpoint: 481,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1,
            arrows: false,
            dots: true
          }
        }]
      };
      if (episodeSliderWrap[index]) {
        episodeSliderWrap[index].slick('refresh');
      } else {
        episodeSliderWrap[index] = epSliderMain.slick(epSliderConfig);
      }
      if (!epSliderMain.hasClass('slick-initialized')) {
        // If not initialized, initialize Slick slider
        epSliderMain.slick(epSliderConfig);
      }
      if (epSliderMain.find('.slick-dots li').length === 1) {
        epSliderMain.find('.slick-dots').css('display', 'none');
      }
    });
  }
  function videoSliderFunc() {
    $('.playlist-listing').each(function (index) {
      const sliderMain = $(this).find('.playlist-listing__video-wrapper');
      const sliderConfig = {
        slidesToShow: 6,
        slidesToScroll: 1,
        autoplay: false,
        arrows: true,
        dots: false,
        infinite: false,
        responsive: [{
          breakpoint: 1025,
          settings: {
            slidesToShow: 5,
            slidesToScroll: 1
          }
        }, {
          breakpoint: 768,
          settings: {
            slidesToShow: 4,
            slidesToScroll: 1,
            arrows: false,
            dots: true
          }
        }, {
          breakpoint: 576,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 1,
            arrows: false,
            dots: true
          }
        }, {
          breakpoint: 481,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1,
            arrows: false,
            dots: true
          }
        }]
      };
      if (videoSliderWrp[index]) {
        videoSliderWrp[index].slick('refresh');
      } else {
        videoSliderWrp[index] = sliderMain.slick(sliderConfig);
      }
      if (!sliderMain.hasClass('slick-initialized')) {
        // If not initialized, initialize Slick slider
        sliderMain.slick(sliderConfig);
      }
      if (sliderMain.find('.slick-dots li').length === 1) {
        sliderMain.find('.slick-dots').css('display', 'none');
      }
    });
  }
  function tabSliderFunc() {
    const tabSliderMain = $('.playlist-listing .playlist-listing__playlist-tab-wrapper');
    const sliderConfig = {
      infinite: false,
      slidesToShow: 6,
      slidesToScroll: 1,
      dots: false,
      arrows: true,
      autoplay: false,
      variableWidth: true,
      responsive: [{
        breakpoint: 768,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 1
        }
      }, {
        breakpoint: 481,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1
        }
      }]
    };
    tabSliderMain.slick(sliderConfig);
    if (!tabSliderMain.hasClass('slick-initialized')) {
      // If not initialized, initialize Slick slider
      tabSliderMain.slick(sliderConfig);
    }
  }
  $(document).on('click', '.playlist-listing .playlist-listing__playlist-tab p', function () {
    const selectedPlaylist = $(this).attr('data-playlist-slug');
    $('.playlist-listing .playlist-listing__playlist-tab p').removeClass('active');
    $(this).addClass('active');
    $.ajax({
      type: 'GET',
      data: {
        action: 'dp_selected_playlist_listing_filter',
        selected_playlist: selectedPlaylist
      },
      url: siteConfig.ajaxUrl,
      // eslint-disable-line
      success(response) {
        $('.playlist-listing .playlist-listing__tab-content').html(response); // phpcs:ignore
        seriesSliderFunc();
        episodeSliderFunc();
        videoSliderFunc();
      }
    });
    return false;
  });
  $(document).on('click', '.playlist-listing .playlist-listing__series', function () {
    const selectedSeries = $(this).attr('data-series-id');
    $.ajax({
      type: 'GET',
      data: {
        action: 'dp_selected_series_listing_filter',
        selected_series: selectedSeries
      },
      url: siteConfig.ajaxUrl,
      // eslint-disable-line
      success(response) {
        $('.playlist-listing .playlist-listing__series-episodes').html(response); // phpcs:ignore
        episodeSliderFunc();
      }
    });
    return false;
  });
  $(document).on('click', '.playlist-listing .added-to-playlist', function () {
    if ($('.playlist-listing .playlist-listing__video-wrapper').hasClass('slick-initialized')) {
      $('.playlist-listing .playlist-listing__video-wrapper').slick('unslick');
    }
    const postId = $(this).parents('.playlist-popup-button').attr('data-post-id');
    const playlistSlug = $('.playlist-listing__playlist-tab p.active').attr('data-playlist-slug');
    $.ajax({
      type: 'GET',
      data: {
        action: 'dp_remove_playlist_listed_video',
        post_id: postId,
        playlist_slug: playlistSlug
      },
      url: siteConfig.ajaxUrl,
      // eslint-disable-line
      beforeSend() {
        $('.playlist-listing .playlist-listing__video-wrapper').remove(); // phpcs:ignore
        $('.recently-added .recently-added__videos').empty();
        $('.playlist-listing__loader').remove();
        $('.playlist-listing .playlist-listing__video-section').empty().append('<div class="playlist-listing__loader"><span class="fa fa-spinner fa-pulse"></span></div>');
      },
      success(response) {
        $('.playlist-listing__loader').remove();
        const responseArray = $.parseJSON(response);
        $('.playlist-listing .playlist-listing__video-section').append(responseArray.playlist_video_html);
        if (responseArray.recently_added_video_html) {
          $('.recently-added .recently-added__videos').html(responseArray.recently_added_video_html);
        } else {
          $('.recently-added').remove();
        }
        videoSliderFunc();
      }
    });
    // return false;
  });
  $(document).on('click', '.playlist-listing .playlist-listing__remove-series', function () {
    if (confirm('Are you sure to remove Series from the playlist?')) {
      if ($('.playlist-listing .playlist-listing__series-wrapper').hasClass('slick-initialized')) {
        $('.playlist-listing .playlist-listing__series-wrapper').slick('unslick');
      }
      const seriesId = $(this).attr('data-series-id');
      const playlistSlug = $('.playlist-listing__playlist-tab p.active').attr('data-playlist-slug');
      $.ajax({
        type: 'GET',
        data: {
          action: 'dp_remove_playlist_listed_series',
          series_id: seriesId,
          playlist_slug: playlistSlug
        },
        url: siteConfig.ajaxUrl,
        // eslint-disable-line
        beforeSend() {
          $('.playlist-listing .playlist-listing__series-section-wrapper').remove(); // phpcs:ignore
          $('.playlist-listing .playlist-listing__series-episodes').remove(); // phpcs:ignore
          $('.playlist-listing .playlist-listing__series-section').empty(); // phpcs:ignore

          $('.playlist-listing__loader').remove();
          $('.playlist-listing .playlist-listing__series-section').append('<div class="playlist-listing__loader"><span class="fa fa-spinner fa-pulse"></span></div>');
        },
        success(response) {
          $('.playlist-listing__loader').remove();
          $('.playlist-listing .playlist-listing__series-section').append(response);
          // setTimeout( () => {
          // }, 500 );

          seriesSliderFunc();
          episodeSliderFunc();
        }
      });
    }
    return false;
  });
})(jQuery);
/******/ })()
;
//# sourceMappingURL=view.js.map