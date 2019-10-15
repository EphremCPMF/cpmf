/**
 * @file
 * Contains js for the CPMF Charts.
 */

(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.behaviors.cpmfTopCitiesByPillarChart = {
        attach: function (context, settings) {
            var initialPillar = $("#cities-by-pillar .form-select").val();
            var initialPillarName = $("#cities-by-pillar .form-select option:selected").text();

            drawChart(initialPillar, initialPillarName);

            function drawChart(pillar, pillarName) {
              $(context).find("#topCitiesByPillar").once(pillar).each(function() {
                var topCities = [];
                var jsonUrl = drupalSettings.path.baseUrl + 'rest/cities-by-pillar/' + pillar + '?_format=json';
                var seriesData = [{
                    name: pillarName,
                    data: []
                }];
                var colors = [];
                $.getJSON(jsonUrl, function (pillars) {
                        $.each(pillars, function (i, pillar) {
                            topCities.push(pillar.title);
                            seriesData[0]['data'].push(parseFloat(pillar.pillar_value));
                            colors[0] = pillar.pillar_color;
                        });

                        Highcharts.chart('topCitiesByPillar', {
                            chart: {
                                type: 'bar',
                                height: topCities.length * 20 + 120 // 20px per data item plus top and bottom margins
                            },
                            title: {
                                text: 'Top Cities by Pillar - ' + drupalSettings.activeYearName
                            },
                            xAxis: {
                                categories: topCities,
                                title: {
                                    text: 'City'
                                },
                                style: {
                                    color: '#FFFFFF'
                                }
                            },
                            yAxis: {
                                min: 0,
                                max: 100,
                                title: {
                                    text: ''
                                }
                            },
                            plotOptions: {
                                bar: {
                                    dataLabels: {
                                        enabled: true
                                    }
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            colors: colors,
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
