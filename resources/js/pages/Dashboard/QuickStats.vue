<template>
    <div>
        <div class="row quick_stats_margin">
            <div class="col-sm"><p class="title" style="text-align: center">Year established</p><br/><p style="text-align: center">{{year_established}}</p></div>
            <div class="col-sm"><p class="title" style="text-align: center">Country</p><br><p style="text-align: center">{{country}}</p></div>
            <div class="col-sm"><p class="title" style="text-align: center">Centralized</p><br><p style="text-align: center">{{centralized}}</p></div>
            <div class="col-sm"><p class="title" style="text-align: center">Accepted Payment Methods</p><br><p style="text-align: center">{{payment_methods}}</p></div>
        </div>
        <div class="row quick_stats_margin">
            <div class="col-sm"><p class="title" style="text-align: center">Url</p><br><p style="text-align: center">{{url}}</p></div>
            <div class="col-sm"><p class="title" style="text-align: center">Facebook</p><br><p style="text-align: center">{{facebook}}</p></div>
            <div class="col-sm"><p class="title" style="text-align: center">Redit</p><br><p style="text-align: center">{{reddit}}</p></div>
            <div class="col-sm"><p class="title" style="text-align: center">Twitter handle</p><br><p style="text-align: center">{{twitter}}</p></div>
        </div>
        <div class="row">
            <div class="col-sm">
                <card class="card_quick_stats" type="chart">
                    <div class="chart-area chart-area_quick_stats" id="volume_by_currency_card">
                        <div id="volume_by_currency">
                            <div ref="chart_volume_by_currency" class="chart"></div>
                        </div>
                    </div>
                </card>
            </div>
            <div class="col-sm">
                <card class="card_quick_stats" type="chart">
                    <div class="chart-area chart-area_quick_stats" id="volume_by_pair_card">
                        <div id="volume_by_pair">
                            <div ref="chart_volume_by_pair" class="chart"></div>
                        </div>
                    </div>
                </card>
            </div>
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
                exchange: null,
                year_established: null,
                country: null,
                centralized: null,
                payment_methods: null,
                url: null,
                facebook: null,
                reddit: null,
                twitter: null
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

            additional_info(data){
                this.year_established = data['Year'];
                this.country = data['Country'];
                this.centralized = data['Centralized'];
                this.payment_methods = data['Accepted_payment_methods'];
                this.url = data['Url'];
                this.facebook = data['Facebook'];
                this.reddit = data['Reddit'];
                this.twitter = data['Twitter'];
            },

            create_quick_stats(){
                let uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/exchange/stats/" + this.exchange;
                this.axios.get(uri).then(response => (this.finish_quick_stats(response.data)));
            },

            finish_quick_stats(data){
                this.additional_info(data['data']['additional']);
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

    .quick_stats_margin{
        margin-top: 30px;
    }
    .card_quick_stats {
        background: #1e1e28;
        box-shadow: none;
    }

    .chart-area_quick_stats {
        height: 100% !important;
        width: 100% !important;
    }

</style>
