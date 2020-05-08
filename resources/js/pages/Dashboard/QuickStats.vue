<template>
    <div class="row">
        <div class="cp-col-12 p-lg-4">
            <card class="card_quick_stats" type="chart">
                <div class="chart-area" id="volume_by_currency_card">
                    <div id="volume_by_currency">
                        <div ref="chart_volume_by_currency" class="chart"></div>
                    </div>
                </div>
            </card>
        </div>
        <div class="p-col-12 p-lg-4">
            <card class="card_quick_stats" type="chart">
                <div class="chart-area" id="volume_by_pair_card">
                    <div id="volume_by_pair">
                        <div ref="chart_volume_by_pair" class="chart"></div>
                    </div>
                </div>
            </card>
        </div>
    </div>
</template>

<script>
    import Highcharts from "highcharts";
    import {axios} from "../../plugins/axios";

    export default {
        data (){
            return {
                axios: axios,
                exchange: null
            }
        },

        methods: {
            volume_by_currency(data){
                var chart_data = Object.keys(data).map(function (item) {
                    return {name: item, y: data[item]}
                });
                const card = document.getElementById('volume_by_currency_card');

                console.log(chart_data);
                var options = {
                    chart: {
                        renderTo: this.$refs.chart_volume_by_currency,
                        height: card.offsetHeight,
                        width: card.offsetWidth,
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                        text: 'Volume by currency'
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    accessibility: {
                        point: {
                            valueSuffix: '%'
                        }
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                            }
                        }
                    },
                    series: [{
                        name: 'Currency',
                        colorByPoint: true,
                        data: chart_data
                    }]
                };

                this.volume_by_currency_chart = new Highcharts.Chart(options);
            },

            volume_by_pair(data){
                var chart_data = Object.keys(data).map(function (item) {
                    return {name: item, y: data[item]}
                });

                const card = document.getElementById('volume_by_pair_card');
                var options = {
                    chart: {
                        renderTo: this.$refs.chart_volume_by_pair,
                        height: card.offsetHeight,
                        width: card.offsetWidth,
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                        text: 'Volume by market pair'
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    accessibility: {
                        point: {
                            valueSuffix: '%'
                        }
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                            }
                        }
                    },
                    series: [{
                        name: 'Pair',
                        colorByPoint: true,
                        data: chart_data
                    }]
                };

                this.volume_by_currency_chart = new Highcharts.Chart(options);
            },

            create_quick_stats(){
                let uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/exchange/stats/" + this.exchange;
                this.axios.get(uri).then(response => (this.finish_quick_stats(response.data)));
            },

            finish_quick_stats(data){
                this.volume_by_currency(data['data']['volume_by_currency']);
                this.volume_by_pair(data['data']['volume_by_pair']);
            },
        },

        mounted() {
            this.exchange = this.$route.name;
            this.create_quick_stats();
        }
    }

</script>

<style>
    .card_quick_stats {
        background: #1e1e28;
        box-shadow: none;
    }

</style>
