/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery'
], function ($) {
    'use strict';

    $('.cms-block__frequently-asked-questions-title .mobile-title').click(function () {
        $(this).toggleClass('expanded');
        $(this).parent().next('.cms-block__frequently-asked-questions-content').slideToggle(200);
    });

    $('.page-footer .footer-col h4').click(function () {
        $(this).toggleClass('active');
        $(this).next('ul').slideToggle();
    });

    $('.faq-collapse h4').click(function () {
        $(this).toggleClass('expanded');
        $(this).parent('div[data-role="title"]').next('div[data-role="content"]').slideToggle(200);
    });

    $('.sidebar-main-top .block.filter .block-title').click(function () {
        $(this).toggleClass('expanded');
        $(this).next('.block-content').children('.filter-options').slideToggle(200);
    });

    $(".person-description .showmore").click(function () {
        $(this).parents('.person-description').find('.person-more').slideDown(300);
        $(this).hide();
    })
});
