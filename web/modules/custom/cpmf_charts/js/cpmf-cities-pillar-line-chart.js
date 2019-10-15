/**
 * @file
 * Contains js for the CPMF Charts.
 */

(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.behaviors.cpmfCitiesPillarLineChart = {
        attach: function (context, settings) {
            var initialPillar = $("#cities-by-pillar .form-select").val();
            var initialPillarName = $("#cities-by-pillar .form-select option:selected").text();

            drawChart(initialPillar, initialPillarName);

            function drawChart(pillar, pillarName) {
              $(context).find("#citiesPillarLineChart").once(pillar).each(function() {
                var jsonUrlTopCities = drupalSettings.path.baseUrl + 'rest/cities-pillar-line/' + pillar + '?_format=json';
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

                        Highcharts.chart('citiesPillarLineChart', {
                            chart: {
                                type: 'line'
                            },
                            title: {
                                text: pillarName
                            },
                            yAxis: {
                                title: {
                                    text: 'Pillar Score'
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

                $('#cities-by-pillar .form-select').once().on('change', function () {
                    var pillarName = $(this).find("option:selected").text();
                    drawChart(this.value, pillarName);
                });

              });
            }
        }
    }
}(jQuery, Drupal, drupalSettings));
