/**
 * @file
 * Contains js for the CPMF Charts.
 */

(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.behaviors.cpmfCitiesIndicatorLineChart = {
        attach: function (context, settings) {
            var initialIndicator = $("#cities-by-indicator .form-select").val();
            var initialIndicatorName = $("#cities-by-indicator .form-select option:selected").text();

            drawChart(initialIndicator, initialIndicatorName);

            function drawChart(indicator, indicatorName) {
              $(context).find("#citiesIndicatorLineChart").once(indicator).each(function() {
                var jsonUrlTopCities = drupalSettings.path.baseUrl + 'rest/cities-indicator-line/' + indicator + '?_format=json';
                var seriesData = [];
                var yearNames = [];
                    $.getJSON(jsonUrlTopCities, function (cities) {
                        $.each(cities, function (j, city) {
                            seriesData.push({'name': city.name, 'data': city.data});
                              if (j === 0) {
                                $.each(city.years, function (index, value) {
                                  yearNames.push(value);
                                });
                              }
                        });
                        Highcharts.chart('citiesIndicatorLineChart', {
                            chart: {
                                type: 'line'
                            },
                            title: {
                                text: indicatorName
                            },
                            yAxis: {
                                title: {
                                    text: 'Indicator Score'
                                }
                            },
                            xAxis: {
                              categories: yearNames
                            },
                            legend: {
                                layout: 'vertical',
                                align: 'right',
                                verticalAlign: 'middle'
                            },
                            credits: {
                                enabled: false
                            },
                            series: seriesData
                        });
                    });

                $('#cities-by-indicator .form-select').once().on('change', function () {
                    var indicatorName = $(this).find("option:selected").text();
                    drawChart(this.value, indicatorName);
                });

              });
            }
        }
    }
}(jQuery, Drupal, drupalSettings));
