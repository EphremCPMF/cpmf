/**
 * @file
 * Contains js for the CPMF Charts.
 */

(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.behaviors.cpmfTopRegionsByIndicatorChart = {
        attach: function (context, settings) {

            var initialIndicator = $("#regions-by-indicator .form-select").val();
            var initialIndicatorName = $("#regions-by-indicator .form-select option:selected").text();

            drawChart(initialIndicator, initialIndicatorName);

            function drawChart(indicator, indicatorName) {
              $(context).find("#topRegionsByIndicator").once(indicator).each(function() {
                var topRegions = [];
                var jsonUrl = drupalSettings.path.baseUrl + 'rest/regions-by-indicator/' + indicator + '?_format=json';
                var seriesData = [{
                    name: indicatorName,
                    data: []
                }];
                var colors = [];
                $.getJSON(jsonUrl, function (indicators) {
                        $.each(indicators, function (i, indicator) {
                            topRegions.push(indicator.title);
                            seriesData[0]['data'].push(parseFloat(indicator.indicator_value));
                            // colors[0] = indicator.indicator_color;
                        });

                        Highcharts.chart('topRegionsByIndicator', {
                            chart: {
                                type: 'bar',
                                height: topRegions.length * 20 + 120 // 20px per data item plus top and bottom margins
                            },
                            title: {
                                text: 'Top Regions by Indicator - ' + drupalSettings.activeYearName
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
                            // colors: colors,
                            series: seriesData
                    });

                });

                $('#regions-by-indicator .form-select').once().on('change', function () {
                    var indicatorName = $(this).find("option:selected").text();
                    drawChart(this.value, indicatorName);
                });
              });
            }
        }
    }
}(jQuery, Drupal, drupalSettings));
