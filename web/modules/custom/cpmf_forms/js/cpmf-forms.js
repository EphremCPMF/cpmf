/**
 * @file
 * Contains js for the CPMF Forms.
 */

(function ($) {
    'use strict';

    Drupal.behaviors.cpmfForms = {
        attach: function (context, settings) {

            $('.views-exposed-form .form-item-status .form-select').on('change', function () {
            });

        }
    }
}(jQuery));
