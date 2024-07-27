/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 *
 * Magenest_Ticket extension
 * NOTICE OF LICENSE
 *
 * @category  Magenest
 * @package   Magenest_Ticket
 * @author ThaoPV <thaopw@gmail.com>
 */
/*jshint browser:true*/
/*global alert:true*/
define([
    'jquery',
    'mage/template',
    'Magento_Ui/js/modal/alert',
    'jquery/ui',
    'useDefault',
    'collapsable',
    'mage/translate',
    'mage/backend/validation'
], function ($, mageTemplate) {
    'use strict';
    var rowId=1;
    $.widget('mage.customPDF', {
        options: {
            selectionItemCount: {},
            pdfTemplate: '',
            rowPattern: '',
            itemCount : ''
        },

        _create: function () {
            this.rowTmpl = mageTemplate('#custom-pdf-option-row-template');
            this._initOptionBoxes();
        },

        _initOptionBoxes: function () {
            this._on({
                /**
                 * Minimize custom option block
                 */
                'click [data-ui-id="pdf-event-ticket-settings-add-row-button"]': function (event) {
                    this.addOption(event);
                },

                'click button[id=delete-row]': function (event) {
                    var element = $(event.target).closest('[data-role="pdf-setting-container"] > div.fieldset-wrapper,tr');
                    if (element.length) {
                        element.remove();
                    }
                },

                /**
                 * Change custom option type
                 */
                'change select[id^=pdf_template_][id$=_info]': function (event, data) {
                    console.log(event.currentTarget);
                }
            });
        },

        /**
         * Add selection value for 'select' type of custom option
         */
        addOption: function (event) {
            var data = {},
                element = event.target || event.srcElement || event.currentTarget,
                rowTmpl;
            if (typeof element !== 'undefined') {
                data.id = rowId;
            } else {
                data = event;
                data.id = rowId;
            }
            rowTmpl = this.rowTmpl({
                data: data
            });
            $(rowTmpl).appendTo($('[data-role="pdf-setting-container"]'));
            rowId++;
        }
    });
});
