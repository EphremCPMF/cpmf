/**
 * @file
 * Contains js for the CPMF Data.
 */

(function ($, Drupal) {
    'use strict';

    Drupal.behaviors.cpmfDataCitySelect = {
        attach: function (context, settings) {
            $('#edit-select-city').change(function() {
                var cityId = $("#edit-select-city").val();
                var cityName = $("#edit-select-city option:selected").text();
                window.location = drupalSettings.path.baseUrl + 'city-data/' + cityId;
            });

            $(document).ready(function() {
                var currentPath = drupalSettings.path.currentPath;
                var args = currentPath.split('/');
                $("#edit-select-city").val(args[1]);
                var initialCityId = $("#edit-select-city").val();
                var initialCityName = $("#edit-select-city option:selected").text();
            });
        }
    }
}(jQuery, Drupal));
