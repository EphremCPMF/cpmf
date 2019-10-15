/**
 * @file
 * Contains js for the CPMF Charts.
 */

(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.behaviors.cpmfTopZonesByPillarChart = {
        attach: function (context, settings) {

            var initialPillar = $("#zones-by-pillar .form-select").val();
            var initialPillarName = $("#zones-by-pillar .form-select option:selected").text();

            drawChart(initialPillar, initialPillarName);

            function drawChart(pillar, pillarName) {
              $(context).find("#topZonesByPillar").once(pillar).each(function() {
                var topZones = [];
                var jsonUrl = drupalSettings.path.baseUrl + 'rest/zones-by-pillar/' + pillar + '?_format=json';
                var seriesData = [{
                    name: pillarName,
                    data: []
                }];
                var colors = [];
                $.getJSON(jsonUrl, function (pillars) {
                        $.each(pillars, function (i, pillar) {
                            topZones.push(pillar.title);
                            seriesData[0]['data'].push(parseFloat(pillar.pillar_value));
                            colors[0] = pillar.pillar_color;
                        });

                        Highcharts.chart('topZonesByPillar', {
                            chart: {
                                type: 'bar',
                                height: topZones.length * 20 + 120 // 20px per data item plus top and bottom margins
                            },
                            title: {
                                text: 'Top Zones by Pillar - ' + drupalSettings.activeYearName
                            },
                            xAxis: {
                                categories: topZones,
                                title: {
                                    text: ''
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

                $('#zones-by-pillar .form-select').once().on('change', function () {
                    var pillarName = $(this).find("option:selected").text();
                    drawChart(this.value, pillarName);
                });
              });
            }
        }
    }
}(jQuery, Drupal, drupalSettings));
