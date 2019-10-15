/**
 * @file
 * Contains js for the CPMF Charts.
 */

(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.behaviors.cpmfCityPolarChart = {
        attach: function (context, settings) {

            Chart.defaults.global.defaultFontColor = '#89949B';
            Chart.defaults.global.title.fontFamily = "'Lato', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
            Chart.defaults.global.title.fontSize = 20;

            Chart.plugins.register({
                beforeDraw: function(chartInstance) {
                    var ctx = chartInstance.chart.ctx;
                    ctx.fillStyle = "white";
                    ctx.fillRect(0, 0, chartInstance.chart.width, chartInstance.chart.height);
                }
            });

            // Draw initial polar chart
            var initialCityId = $(".form-item-select-city .form-select").val();
            var initialCityName = $(".form-item-select-city .form-select option:selected").text();
            if ($(".block-entity-fieldnodenid").length) {
                initialCityId = $(".block-entity-fieldnodenid .field--name-nid").text();
            }
            if ($(".block-entity-fieldnodetitle").length) {
                initialCityName = $(".block-entity-fieldnodetitle span").text();
            }

            drawPolarChart(initialCityId, initialCityName);

            function drawPolarChart(cityId, cityName) {
              $(context).find("#polarChartWithLegend").once(cityId).each(function() {
                var pillarLabels = [];
                var backgroundColors = [];
                var cityData = [];
                var jsonUrlPillars = drupalSettings.path.baseUrl + 'rest/pillars?_format=json';
                var jsonUrlCityData = drupalSettings.path.baseUrl + 'rest/city-pillar-data/' + cityId + '?_format=json';
                $.getJSON(jsonUrlPillars, function (pillars) {
                    $.getJSON(jsonUrlCityData, function (city_data) {
                        $.each(pillars, function (i, item) {
                            pillarLabels[i] = item.field_pillar_number + '. ' + item.title;
                            backgroundColors[i] = item.field_color;
                        });

                        $.each(city_data[0], function (field_name, value) {
                            cityData.push(value);
                        });

                        var cityPolarData = {
                            datasets: [{
                                data: cityData,
                                backgroundColor: backgroundColors
                            }],
                            labels: pillarLabels
                        };

                        var ctxCityPolar = document.getElementById("cityPolarChart").getContext("2d");
                        var cityPolarChart = new Chart(ctxCityPolar, {
                            type: 'polarArea',
                            data: cityPolarData,
                            options: {
                                title: {
                                    display: true,
                                    text: [cityName, drupalSettings.activeYearName]
                                },
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12
                                    },
                                    display: true
                                },
                                animation: {
                                    onComplete: done()
                                }
                            }
                        });
                        document.getElementById("cityPolarChartLegend").innerHTML = cityPolarChart.generateLegend();
                        document.getElementById("print-city-polar-chart").addEventListener("click",function(){
                            $('#print-city-polar-chart').attr({
                                'href': cityPolarChart.toBase64Image(),
                                'target': '_blank'
                            });
                        });
                    });
                });

                $('.form-item-select-city .form-select').once().on('change', function () {
                    var cityName = $(this).find("option:selected").text();
                    drawPolarChart(this.value, cityName);
                });

                function done() {

                }
              });
            }
        }
    }
}(jQuery, Drupal, drupalSettings));
