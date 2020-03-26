<template>
    <div id="chart_view">
        <div ref="chart" class="chart"></div>
    </div>
</template>


<script>
    import ApexCharts from "apexcharts";

    export default {
        name: "App",
        mounted() {
            // code from Example: https://apexcharts.com/javascript-chart-demos/area-charts/spline/
            var options = {
                chart: {
                    height: 350,
                    type: "area"
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: "smooth"
                },
                series: [
                    {
                        name: "series1",
                        data: [31, 40, 28, 51, 42, 109, 100]
                    },
                    {
                        name: "series2",
                        data: [11, 32, 45, 32, 34, 52, 41]
                    }
                ],

                xaxis: {
                    type: "datetime",
                    categories: [
                        "2018-09-19T00:00:00",
                        "2018-09-19T01:30:00",
                        "2018-09-19T02:30:00",
                        "2018-09-19T03:30:00",
                        "2018-09-19T04:30:00",
                        "2018-09-19T05:30:00",
                        "2018-09-19T06:30:00"
                    ]
                },
                tooltip: {
                    x: {
                        format: "dd/MM/yy HH:mm"
                    }
                }
            };

            if (this.$refs.chart) {
                // HTML element exists
                var chart = new ApexCharts(this.$refs.chart, options);
                chart.render();
            }
        }
    };
</script>

<style>
    #chart_view {
        font-family: "Avenir", Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        color: #2c3e50;
        margin-top: 60px;
    }

    .chart {
        width: 100%;
    }
</style>

