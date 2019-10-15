/**
 * @file
 * Contains js for the CPMF Charts.
 */

(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.behaviors.cpmfProjectByIndicatorChart = {
        attach: function (context, settings) {
            var initialIndicator = $(".form-item-select-project-indicator .form-select").val();
            var initialIndicatorName = $(".form-item-select-project-indicator .form-select option:selected").text();

            drawChart(initialIndicator, initialIndicatorName);

            function drawChart(indicator, indicatorName) {
              $(context).find("#ProjectByIndicator").once(indicator).each(function() {
                var topCities = [];
                var jsonUrl = drupalSettings.path.baseUrl + 'rest/top-cities-by-indicator/' + indicator + '?_format=json';
                var seriesData = [{
                  name: 'Performance',
                  data: []
                },{
                  name: 'Baseline',
                  data: []
                },{
                  name: 'Value',
                  data: []
                },{
                  name: 'Target',
                  data: []
                }];
                var colors = [];
                $.getJSON(jsonUrl, function (indicators) {
                        $.each(indicators, function (i, indicator) {
                          if (drupalSettings.projectByIndicator.cities.indexOf(indicator.city_id) >= 0) {
                            topCities.push(indicator.title);
                            seriesData[0]['data'].push(parseFloat(indicator.indicator_performance));
                            seriesData[1]['data'].push(parseFloat(indicator.indicator_baseline));
                            seriesData[2]['data'].push(parseFloat(indicator.indicator_value));
                            seriesData[3]['data'].push(parseFloat(indicator.indicator_target));
                          }
                        });
                        Highcharts.chart('ProjectByIndicator', {
                            chart: {
                                type: 'bar',
                                height: topCities.length * 20 + 120 // 20px per data item plus top and bottom margins
                            },
                            title: {
                                text: 'Project Indicators - ' + drupalSettings.activeYearName
                            },
                            xAxis: {
                                categories: topCities,
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

                $('.form-item-select-project-indicator .form-select').once().on('change', function () {
                    var indicatorName = $(this).find("option:selected").text();
                    drawChart(this.value, indicatorName);
                });
              });
            }
        }
    }
}(jQuery, Drupal, drupalSettings));
