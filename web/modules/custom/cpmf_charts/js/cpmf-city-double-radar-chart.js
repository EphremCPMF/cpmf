/**
 * @file
 * Contains js for the CPMF City Double Radar Chart.
 */

(function ($, Drupal, drupalSettings) {
    'use strict';

    Drupal.behaviors.cpmfCityDoubleRadarChart = {
        attach: function (context, settings) {

            Chart.defaults.global.defaultFontColor = '#89949B';
            Chart.defaults.global.title.fontFamily = "'Lato', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
            Chart.defaults.global.title.fontSize = 20;

            // Draw initial radar chart
            var initialCityId1 = $("#edit-select-city-one").val();
            var initialCityName1 = $("#edit-select-city-one option:selected").text();
            var initialCityId2 = $("#edit-select-city-two").val();
            var initialCityName2 = $("#edit-select-city-two option:selected").text();

            drawCityDoubleChart(initialCityId1, initialCityName1, initialCityId2, initialCityName2);

            function drawCityDoubleChart(cityId1, cityName1, cityId2, cityName2) {
              $(context).find("#cityDoubleRadarChart").once(cityId1 + '-' + cityId2).each(function() {
                var pillarLabels = [];
                var backgroundColors = [];
                var cityData1 = [];
                var cityData2 = [];
                var jsonUrlPillars = drupalSettings.path.baseUrl + 'rest/pillars?_format=json';
                var jsonUrlCityData1 = drupalSettings.path.baseUrl + 'rest/city-pillar-data/' + cityId1 + '?_format=json';
                var jsonUrlCityData2 = drupalSettings.path.baseUrl + 'rest/city-pillar-data/' + cityId2 + '?_format=json';
                $.getJSON(jsonUrlPillars, function (pillars) {
                    $.getJSON(jsonUrlCityData1, function (city_data1) {
                        $.getJSON(jsonUrlCityData2, function (city_data2) {
                            $.each(pillars, function (i, item) {
                                pillarLabels[i] = item.field_pillar_number + '. ' + item.title;
                                backgroundColors[i] = item.field_color;
                            });

                            $.each(city_data1[0], function (field_name, value) {
                                cityData1.push(value);
                            });
                            $.each(city_data2[0], function (field_name, value) {
                                cityData2.push(value);
                            });

                            var cityDoubleRadarData = {
                                labels: pillarLabels,
                                datasets: [
                                    {
                                        label: cityName1,
                                        data: cityData1,
                                        backgroundColor: "rgba(179,181,198,0.2)",
                                        borderColor: "rgba(179,181,198,1)",
                                        pointBackgroundColor: "rgba(179,181,198,1)",
                                        pointBorderColor: "#fff",
                                        pointHoverBackgroundColor: "#fff",
                                        pointHoverBorderColor: "rgba(179,181,198,1)"
                                    },
                                    {
                                        label: cityName2,
                                        data: cityData2,
                                        backgroundColor: "rgba(255,99,132,0.2)",
                                        borderColor: "rgba(255,99,132,1)",
                                        pointBackgroundColor: "rgba(255,99,132,1)",
                                        pointBorderColor: "#fff",
                                        pointHoverBackgroundColor: "#fff",
                                        pointHoverBorderColor: "rgba(255,99,132,1)",
                                    }
                                ]
                            };

                            var ctxCityDoubleRadar = document.getElementById("cityDoubleRadarChart");
                            var cityDoubleRadarChart = new Chart(ctxCityDoubleRadar, {
                                type: 'radar',
                                data: cityDoubleRadarData,
                                options: {
                                  title: {
                                    display: true,
                                    text: drupalSettings.activeYearName
                                  },
                                    legend: {
                                        labels: {
                                            fontSize: 20
                                        }
                                    }
                                }
                            });
                            document.getElementById("print-city-radar-chart").addEventListener("click",function(){
                                $('#print-city-radar-chart').attr({
                                    'href':cityDoubleRadarChart.toBase64Image(),
                                    'target':'_blank'
                                });
                            });
                        });
                    });
                });

                $('.select-cities').once().on('change', function () {
                    var initialCityId1 = $("#edit-select-city-one").val();
                    var initialCityName1 = $("#edit-select-city-one option:selected").text();
                    var initialCityId2 = $("#edit-select-city-two").val();
                    var initialCityName2 = $("#edit-select-city-two option:selected").text();

                    drawCityDoubleChart(initialCityId1, initialCityName1, initialCityId2, initialCityName2);
                });
              });
            }
        }
    }
}(jQuery, Drupal, drupalSettings));
