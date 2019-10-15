/**
 * @file
 * Contains js for the CPMF Charts.
 */

(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.behaviors.cpmfTopRegionsByPillarChart = {
        attach: function (context, settings) {

            var initialPillar = $("#regions-by-pillar .form-select").val();
            var initialPillarName = $("#regions-by-pillar .form-select option:selected").text();

            drawChart(initialPillar, initialPillarName);

            function drawChart(pillar, pillarName) {
              $(context).find("#topRegionsByPillar").once(pillar).each(function() {
                var topRegions = [];
                var jsonUrl = drupalSettings.path.baseUrl + 'rest/regions-by-pillar/' + pillar + '?_format=json';
                var seriesData = [{
                    name: pillarName,
                    data: []
                }];
                var colors = [];
                $.getJSON(jsonUrl, function (pillars) {
                        $.each(pillars, function (i, pillar) {
                            topRegions.push(pillar.title);
                            seriesData[0]['data'].push(parseFloat(pillar.pillar_value));
                            colors[0] = pillar.pillar_color;
                        });

                        Highcharts.chart('topRegionsByPillar', {
                            chart: {
                                type: 'bar',
                                height: topRegions.length * 20 + 120 // 20px per data item plus top and bottom margins
                            },
                            title: {
                                text: 'Top Regions by Pillar - ' + drupalSettings.activeYearName
                            },
                            xAxis: {
                                categories: topRegions,
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

                $('#regions-by-pillar .form-select').once().on('change', function () {
                    var pillarName = $(this).find("option:selected").text();
                    drawChart(this.value, pillarName);
                });
              });
            }
        }
    }
}(jQuery, Drupal, drupalSettings));
