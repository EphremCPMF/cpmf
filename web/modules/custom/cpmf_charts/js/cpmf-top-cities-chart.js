/**
 * @file
 * Contains js for the CPMF Charts.
 */

(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.behaviors.cpmfTopCitiesChart = {
        attach: function (context, settings) {

            drawChart();

            function drawChart() {
              $(context).find("#topCitiesChart").once().each(function() {
                var topCities = [];
                var jsonUrlTopCities = drupalSettings.path.baseUrl + 'rest/cities-overall?_format=json';
                var jsonUrlPillars = drupalSettings.path.baseUrl + 'rest/pillars?_format=json';
                var PillarData = [];
                var PillarColors = [];
                var seriesData = [];
                $.getJSON(jsonUrlPillars, function (pillars) {
                    $.getJSON(jsonUrlTopCities, function (cities) {
                        $.each(pillars, function (i, pillar) {
                            PillarColors.push(pillar.field_color);
                            PillarData[i] = [];
                            seriesData.push({name: pillar.field_pillar_number + '. ' + pillar.title});
                            $.each(cities, function (j, city) {
                                if (i === 0) {
                                    topCities.push(city.title);
                                }
                                for (var key in city.pillars) {
                                    if (city.pillars.hasOwnProperty(key) && key.match("^field_")) {
                                        if (key === 'field_pillar_' + pillar.field_field_name_code) {
                                            PillarData[i].push(parseFloat(city.pillars[key]));
                                        }
                                    }
                                }
                            });
                        });

                        for (var key in PillarData) {
                            seriesData[key]['data'] = PillarData[key];
                            seriesData[key]['color'] = PillarColors[key];
                        }

                        Highcharts.chart('topCitiesChart', {
                            chart: {
                                type: 'bar',
                                height: cities.length * 20 + 120 // 20px per data item plus top and bottom margins
                            },
                            title: {
                                text: 'Top Cities Overall - ' + drupalSettings.activeYearName
                            },
                            xAxis: {
                                categories: topCities
                            },
                            yAxis: {
                                min: 0,
                                title: {
                                    text: ''
                                },
                                labels: {
                                    enabled: false
                                },
                                reversedStacks: false,
                                stackLabels: {
                                    enabled: true,
                                    formatter: function() {
                                        return (this.total / seriesData.length).toFixed(0);
                                    }
                                }
                            },
                            legend: {
                                reversed: false
                            },
                            plotOptions: {
                                series: {
                                    stacking: 'normal'
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            series: seriesData
                        });
                    });


                });

                // No data
                var chart = Highcharts.chart('topCitiesChart', {
                  chart: {
                    height: 80
                  },
                  title: {
                    text: 'Top Cities Overall - ' + drupalSettings.activeYearName
                  },
                  credits: {
                    enabled: false
                  },
                  yAxis: {
                    title: {
                      text: ''
                    },
                    labels: {
                      enabled: false
                    }
                  },
                  legend: {
                    enabled: false
                  }
                });
                var msg = Drupal.t("No data to display");
                $('#topCitiesChart').append('<div class="no-data">' + msg + '</div>');

              });
            }
        }
    }
}(jQuery, Drupal, drupalSettings));
