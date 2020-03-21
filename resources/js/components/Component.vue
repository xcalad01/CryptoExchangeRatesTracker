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
                let uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto_historical/" + this.post.action + "/" + this.post.start + "/" + this.post.end + "/" + this.post.exchange + "/" + this.post.convert;
                this.axios.get(uri).then(response => (this.create_update_chart(response.data)));
            },

            create_update_chart(data){
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
            }
        },
        filters: {
            pretty: function(value) {
                return JSON.stringify(value, null, 4);
            }
        }
    }
</script>s

