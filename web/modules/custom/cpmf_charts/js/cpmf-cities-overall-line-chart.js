/**
 * @file
 * Contains js for the CPMF Charts.
 */

(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.behaviors.cpmfCitiesOverallLineChart = {
        attach: function (context, settings) {

            drawChart();

            function drawChart() {
              $(context).find("#citiesOverallLineChart").once().each(function() {
                var jsonUrlTopCities = drupalSettings.path.baseUrl + 'rest/cities-overall-line?_format=json';
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

                        Highcharts.chart('citiesOverallLineChart', {
                            chart: {
                                type: 'line'
                            },
                            title: {
                                text: 'Cities Overall'
                            },
                            yAxis: {
                                title: {
                                    text: 'Overall Score'
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

              });
            }
        }
    }
}(jQuery, Drupal, drupalSettings));
