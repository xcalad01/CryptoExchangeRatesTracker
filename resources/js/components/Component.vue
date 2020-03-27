import ApexCharts from "apexcharts";
<template>
    <div>
        <h1>Crypto Hist Value/Ohlc</h1>
        <form @submit.prevent="addPost">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Start:</label>
                        <input type="text" class="form-control" v-model="post.start">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>End:</label>
                        <input type="text" class="form-control" v-model="post.end">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Exchange:</label>
                        <input type="text" class="form-control" v-model="post.exchange">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Convert:</label>
                        <input type="text" class="form-control" v-model="post.convert">
                    </div>
                </div>
            </div><br />
            <select v-model="post.range">
                <option disabled value="">Date rang, please select ne</option>
                <option>1d</option>
                <option>1h</option>
            </select><br />
            <select v-model="post.action">
                <option disabled value="">Please select one</option>
                <option>value</option>
                <option>ohlc</option>
            </select>

            <div class="form-group">
                <button class="btn btn-primary">Query</button>
            </div>
            <pre>{{ content | pretty }}</pre>
        </form>

        <div id="chart_view">
            <div ref="chart" class="chart"></div>
        </div>

        <div id="chart_value">
            <div ref="chart_value" class="chart"></div>
        </div>
    </div>
</template>

<script>
    import ApexCharts from "apexcharts";

    export default {
        data(){
            return {
                content:null,
                post:{}
            }
        },
        mounted() {

        },
        methods: {
            addPost(){
                console.log(process.env.MIX_API_PORT);
                let uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto_historical/" + this.post.action + "/" + this.post.start + "/" + this.post.end + "/" + this.post.exchange + "/" + this.post.range + "/" + this.post.convert;
                if (this.post.action === "ohlc"){
                    this.axios.get(uri).then(response => (this.create_update_ohlc_chart(response.data)));
                }
                else{
                    this.axios.get(uri).then(response => (this.create_update_value_chart(response.data)));
                }
            },

            create_update_ohlc_chart(data){
                console.log(data);
                // code from Example: https://apexcharts.com/javascript-chart-demos/area-charts/spline/
                var chartOptions = {
                    chart: {
                        type: 'candlestick',
                        height: 350
                    },
                    title: {
                        text: 'CandleStick Chart',
                        align: 'left'
                    },
                    xaxis: {
                        type: 'datetime'
                    },
                    yaxis: {
                        tooltip: {
                            enabled: true
                        }
                    },
                    series: [{
                        data: data['data']
                    }],
                };

                if (this.$refs.chart) {
                    // HTML element exists
                    var chart = new ApexCharts(this.$refs.chart, chartOptions);
                    chart.render();
                }
            },

            create_update_value_chart(data){
                var new_data = data['data'].map(function (item) {
                    console.log(item[0]);
                    return [new Date(item[0]), item[1]]
                });
                console.log(new_data);
                var options = {
                    series: [{
                        name: 'Price',
                        data: new_data
                    }],
                    chart: {
                        type: 'area',
                        stacked: false,
                        height: 350,
                        zoom: {
                            type: 'x',
                            enabled: true,
                            autoScaleYaxis: true
                        },
                        toolbar: {
                            autoSelected: 'zoom'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    markers: {
                        size: 0,
                    },
                    title: {
                        text: 'Stock Price Movement',
                        align: 'left'
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            inverseColors: false,
                            opacityFrom: 0.5,
                            opacityTo: 0,
                            stops: [0, 90, 100]
                        },
                    },
                    yaxis: {
                        labels: {
                            formatter: function (val) {
                                return (val / 1).toFixed(0);
                            },
                        },
                        title: {
                            text: 'Price'
                        },
                    },
                    xaxis: {
                        type: 'datetime',
                    },
                    tooltip: {
                        shared: false,
                        y: {
                            formatter: function (val) {
                                return (val / 1).toFixed(0)
                            }
                        }
                    }
                };

                if (this.$refs.chart) {
                    var chart = new ApexCharts(this.$refs.chart_value, options);
                    chart.render();
                }
                // var chart = new ApexCharts(document.querySelector("#chart"), options);
            }
        },
        filters: {
            pretty: function(value) {
                return JSON.stringify(value, null, 4);
            }
        }
    }
</script>s

