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
                        <label>From:</label>
                        <input type="text" class="form-control" v-model="post.from">
                    </div>
                </div>
            </div><br />
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>To:</label>
                        <input type="text" class="form-control" v-model="post.to">
                    </div>
                </div>
            </div><br />
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Convert:</label>
                        <input type="text" class="form-control" v-model="post.convert">
                    </div>
                </div>
            </div><br />
            <select v-model="post.range">
                <option disabled value="">Date rang, please select one</option>
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
        </form>

        <div id="chart_view">
            <div ref="chart" class="chart"></div>
        </div>

        <div id="chart_value">
            <div ref="chart_value" class="chart"></div>
        </div>

        <div id="realtime">
            <div ref="chart_value" class="chart"></div>
        </div>
    </div>
</template>

<script>
    import ApexCharts from "apexcharts";

    var lastDate = null;
    var realtime_chart = null;
    var real_time_data = [];
    var realtime_response_data = '';

    function getNewSeries(axios_client){
        console.log(lastDate);
        if (lastDate == null){
            lastDate = new Date().setSeconds(0,0) / 1000;
        }
        let uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto_current" + "/" + lastDate + "/" + "kraken" + "/" + "usd";
        axios_client.get(uri).then(response => (realtime_response_data = response.data));
        var date = _.cloneDeep(lastDate);
        var result = {x: date * 1000 , y: realtime_response_data['data']};
        lastDate += 60;
        return result;
    }

    export default {
        data(){
            return {
                content:null,
                ohlc_chart:null,
                value_chart: null,
                post:{}
            }
        },
        mounted() {

           this.create_update_realtime_value();
        },
        methods: {
            addPost(){
                console.log(process.env.MIX_API_PORT);
                let uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto_historical/" + this.post.action + "/" + this.post.start + "/" + this.post.end + "/" + this.post.exchange + "/" + this.post.range + "/" + this.post.from + "/" + this.post.to + "/" + this.post.convert;
                if (this.post.action === "ohlc"){
                    this.axios.get(uri).then(response => (this.create_update_ohlc_chart(response.data)));
                }
                else{
                    this.axios.get(uri).then(response => (this.create_update_value_chart(response.data)));
                }
            },

            create_update_ohlc_chart(data){
                var new_data = data['data'].map(function (item) {
                    return {x:(item['x'] - 3600) * 1000, y:item["y"]}
                });

                if (this.ohlc_chart != null){
                    this.ohlc_chart.updateSeries([{data: new_data}]);
                    return
                }
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
                        type: 'category',
                        labels: {
                            formatter: function(val) {
                                return new Date(val).toLocaleString();
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                    },
                    yaxis: {
                        tooltip: {
                            enabled: true
                        }
                    },
                    series: [{
                        data: new_data
                    }],
                };

                if (this.$refs.chart) {
                    this.ohlc_chart = new ApexCharts(this.$refs.chart, chartOptions);
                    this.ohlc_chart.render();
                }
            },

            create_update_value_chart(data){
                var new_data = data['data'].map(function (item) {
                    return [new Date(item[0] * 1000 -  3600 * 1000), item[1]]
                });

                if (this.value_chart != null){
                    this.value_chart.updateSeries([{name: "Price", "data": new_data}]);
                    return
                }

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

                if (this.$refs.chart_value) {
                    this.value_chart = new ApexCharts(this.$refs.chart_value, options);
                    this.value_chart.render();
                }
            },

            create_update_realtime_value(){
                real_time_data.push(getNewSeries(this.axios));
                var options = {
                    series: [{
                        data: real_time_data
                    }],
                    chart: {
                        id: 'realtime',
                        height: 350,
                        type: 'line',
                        animations: {
                            enabled: true,
                            easing: 'linear',
                            dynamicAnimation: {
                                speed: 1000
                            }
                        },
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    title: {
                        text: 'Dynamic Updating Chart',
                        align: 'left'
                    },
                    markers: {
                        size: 0
                    },
                    xaxis: {
                        type: 'datetime',
                        range: 900000
                    },
                    yaxis: {
                        forceNiceScale: true
                    },
                    legend: {
                        show: false
                    },
                };

                realtime_chart = new ApexCharts(document.querySelector("#realtime"), options);
                realtime_chart.render();

                window.setInterval(function () {
                    real_time_data.push(getNewSeries(this.axios));

                    realtime_chart.updateSeries([{
                        data: real_time_data
                    }])
                }, 60 * 1000)
            },
        },
        filters: {
            pretty: function(value) {
                return JSON.stringify(value, null, 4);
            }
        }
    }
</script>s

