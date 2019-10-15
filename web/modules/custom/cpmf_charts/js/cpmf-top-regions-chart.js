/**
 * @file
 * Contains js for the CPMF Charts.
 */

(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.behaviors.cpmfTopRegionsChart = {
        attach: function (context, settings) {

            drawChart();

            function drawChart() {
              $(context).find("#topRegionsChart").once().each(function() {
                var topRegions = [];
                var jsonUrlTopRegions = drupalSettings.path.baseUrl + 'rest/regions' + '?_format=json';
                var jsonUrlPillars = drupalSettings.path.baseUrl + 'rest/pillars?_format=json';
                var PillarData = [];
                var PillarColors = [];
                var seriesData = [];
                $.getJSON(jsonUrlPillars, function (pillars) {
                    $.getJSON(jsonUrlTopRegions, function (regions) {
                        $.each(pillars, function (i, pillar) {
                            PillarColors.push(pillar.field_color);
                            PillarData[i] = [];
                            seriesData.push(
                                {name: pillar.field_pillar_number + '. ' + pillar.title, color: pillar.field_color}
                            );
                            $.each(regions, function (j, region) {
                                if (i === 0) {
                                    topRegions.push(region.title);
                                }
                                for (var key in region) {
                                    if (region.hasOwnProperty(key) && key.match("^field_")) {
                                        if (key === 'field_pillar_' + pillar.field_field_name_code) {
                                            PillarData[i].push(parseFloat(region[key]));
                                        }
                                    }
                                }
                            });
                        });

                        for (var pkey in PillarData) {
                            seriesData[pkey]['data'] = PillarData[pkey];
                        }
                        console.log(seriesData);

                        Highcharts.chart('topRegionsChart', {
                            chart: {
                                type: 'bar',
                                height: topRegions.length * 20 + 120 // 20px per data item plus top and bottom margins
                            },
                            title: {
                                text: 'Top Regions Overall - ' + drupalSettings.activeYearName
                            },
                            xAxis: {
                                categories: topRegions
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
              });
            }
        }
    }
}(jQuery, Drupal, drupalSettings));
