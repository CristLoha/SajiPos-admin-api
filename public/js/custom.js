/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

$(function () {
  // Override Stisla's default sidebar toggle so we fully hide/show
  // the sidebar on desktop instead of just minimizing it.
  $("[data-toggle='sidebar']").off('click');

  $("[data-toggle='sidebar']").on('click', function () {
    var body = $('body'),
      w = $(window);

    if (w.outerWidth() <= 1024) {
      // Mobile / tablet: keep existing Stisla behaviour
      body.removeClass('search-show search-gone');
      if (body.hasClass('sidebar-gone')) {
        body.removeClass('sidebar-gone');
        body.addClass('sidebar-show');
      } else {
        body.addClass('sidebar-gone');
        body.removeClass('sidebar-show');
      }
    } else {
      // Desktop: toggle full hide/show
      body.removeClass('sidebar-mini'); // clear mini state if present
      body.toggleClass('sidebar-hidden');
    }

    return false;
  });
});
