/**
 * @file
 * Contains js for the CPMF Charts.
 */

(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.behaviors.cpmfTopZonesChart = {
        attach: function (context, settings) {

            drawChart();

            function drawChart() {
              $(context).find("#topZonesChart").once().each(function() {
                var topZones = [];
                var jsonUrlTopZones = drupalSettings.path.baseUrl + 'rest/zones' + '?_format=json';
                var jsonUrlPillars = drupalSettings.path.baseUrl + 'rest/pillars?_format=json';
                var PillarData = [];
                var PillarColors = [];
                var seriesData = [];
                $.getJSON(jsonUrlPillars, function (pillars) {
                    $.getJSON(jsonUrlTopZones, function (zones) {
                        $.each(pillars, function (i, pillar) {
                            PillarColors.push(pillar.field_color);
                            PillarData[i] = [];
                            seriesData.push(
                              {name: pillar.field_pillar_number + '. ' + pillar.title, color: pillar.field_color}
                          );
                            $.each(zones, function (j, zone) {
                                if (i === 0) {
                                    topZones.push(zone.title);
                                }
                                for (var key in zone) {
                                    if (zone.hasOwnProperty(key) && key.match("^field_")) {
                                        if (key === 'field_pillar_' + pillar.field_field_name_code) {
                                            PillarData[i].push(parseFloat(zone[key]));
                                        }
                                    }
                                }
                            });
                        });

                        for (var key in topZones) {
                            seriesData[key]['data'] = PillarData[key];
                        }
                        console.log(seriesData); console.log(PillarData);

                        Highcharts.chart('topZonesChart', {
                            chart: {
                                type: 'bar',
                                height: topZones.length * 20 + 120 // 20px per data item plus top and bottom margins
                            },
                            title: {
                                text: 'Top Zones Overall - ' + drupalSettings.activeYearName
                            },
                            xAxis: {
                                categories: topZones
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
                                        return (this.total / seriesData.length).toFixed(1);
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
              });
            }
        }
    }
}(jQuery, Drupal, drupalSettings));
