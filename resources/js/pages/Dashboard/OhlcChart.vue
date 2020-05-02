<template>
  <div>
    <div class="row">
      <div class="col-sm" style="display:grid;justify-content: center">
        <div class="form-group">
          <label class="label">From:</label>
          <select class="select-css" v-model=post.from @change="onChangeFrom()">
            <option v-for="item in from_available" :value="item.value">{{item.text}}</option>
          </select>
        </div>
      </div>
      <div class="col-sm" style="display:grid;justify-content: center">
        <div class="form-group">
          <label class="label">To:</label>
          <select class="select-css" v-model=post.to @change="onChangeTo()">
            <option v-for="item in to_available" :value="item.value">{{item.text}}</option>
          </select>
        </div>
      </div>
      <div class="col-sm" style="display:grid;justify-content: center">
        <div class="form-group">
          <label class="label">Range:</label>
          <select class="select-css" v-model="post.range">
            <option>1d</option>
            <option>12h</option>
            <option>6h</option>
            <option>3h</option>
            <option>1h</option>
            <option>5m</option>
            <option>1m</option>
          </select>
        </div>
      </div>
      <div class="col-sm" style="display:grid;justify-content: center">
          <div class="form-group">
              <label class="label">Start:</label>
              <datetime type="datetime" v-model="post.start"></datetime>
          </div>
      </div>
      <div class="col-sm" style="display:grid;justify-content: center">
          <div class="form-group">
              <label class="label">End:</label>
              <datetime type="datetime" v-model="post.end"></datetime>
          </div>
      </div>
      <div class="col-sm"  style="display:grid;justify-content: center">
        <form @submit.prevent="ohlc_chart(false)">
          <div class="form-group">
            <button class="btn btn-primary">Query OHLC</button>
          </div>
        </form>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <card type="chart" id="chart_ohlc_cart">
          <div class="chart-area">
            <div id="chart_view_ohlc">
              <div ref="chart_ohlc" class="chart"></div>
            </div>
          </div>
        </card>
      </div>
    </div>
  </div>
</template>

<script>
  import { axios } from '../../plugins/axios';
  import { Datetime } from 'vue-datetime';
  import Highstock from 'highcharts/highstock';
  var global_component_instance = null;

  export default {
    components: {
      datetime: Datetime,
    },

    data (){
      return {
        post: {},
        chart: null,
        chart_data: null,
        axios: axios,
        exchange: null,
        old_to: null,
        from_available: null,
        to_available: null,
      }
    },

    methods: {
      ohlc_chart(init){
        if (init){
          this.post.start = new Date().setSeconds(0, 0) / 1000 - 86400;
          this.post.end = new Date().setSeconds(0, 0) / 1000;
          this.post.range = '1h';
        }
        else{
          this.post.start = Date.parse(this.post.start) / 1000;
          this.post.end = Date.parse(this.post.end) / 1000;
        }

        let url_value = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/" + "ohlc" + "/" + this.post.start + "/" + this.post.end + "/" + this.exchange + "/" + this.post.range + "/" + this.post.from + "/" + this.post.to;
        if (this.chart == null){
          this.init_chart();
        }
        this.chart.showLoading();
        this.axios.get(url_value).then(response => (this.update_chart(response.data)));
      },

      init_chart(){
        const card = document.getElementById('chart_ohlc_cart');

        var options = {
          chart: {
            zoomType: 'x',
            renderTo: this.$refs.chart_ohlc,
            height: card.offsetHeight,
            width: card.offsetWidth
          },

          rangeSelector: {
            selected: 1
          },

          title: {
            text: "Candlestick chart"
          },

          xAxis: {
            type: 'datetime'
          },

          series: [{
            type: 'candlestick',
            name: 'Price',
            data: null,
            dataGrouping: {
              units: [
                [
                  'week', // unit name
                  [1] // allowed multiples
                ], [
                  'month',
                  [1, 2, 3, 4, 6]
                ]
              ]
            }
          }]};

        this.chart = new Highstock.Chart(options);
      },

      update_chart(data){
        this.chart_data = data['data'].map(function (item) {
          return [(item['x']) * 1000].concat(item["y"])
        });

        if (this.chart != null){
          this.chart.hideLoading();
          this.chart.series[0].setData(this.chart_data);
        }
      },

      finish_init_avail(data){
        this.from_available = data['from'];
        this.to_available = data['to'];
      },

      init_available(){
        let uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/pairs/" + this.exchange;
        this.axios.get(uri).then(response => (this.finish_init_avail(response.data)));
      },

      clear_all_timeouts_intervals(){
        clearTimeout(this.timeout_id_value);
        clearTimeout(this.timeout_id_volume);
        clearInterval(this.interval_id_value);
        clearInterval(this.interval_id_volume);
      },

      onChangeFrom(){
        this.ohlc_chart(true);
      },

      finish_change_to(data){
        var new_fiat = data['data']['fiat'];
        var old_fiat = data['data']['old_fiat'];


        var new_data = this.chart_data.map(function (item) {
          return [
            item[0],
            item[1] / old_fiat * new_fiat,
            item[2] / old_fiat * new_fiat,
            item[3] / old_fiat * new_fiat,
            item[4] / old_fiat * new_fiat
          ]
        });
        this.chart.series[0].setData(new_data);
        this.chart_data = new_data;
      },

      onChangeTo(){
        var now = new Date().setSeconds(0, 0) / 1000;
        var url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/fiat/historical/" + now + "/" + this.post.to + "/" + this.old_to;
        this.old_to = this.post.to;
        this.axios.get(url).then(response => (this.finish_change_to(response.data)));
      },

    },



    mounted() {
      const usd_exchanges = ["kraken", "gdax", "bitfinex", "gemini", "bitstamp", "bitbay", "okcoin"];
      this.exchange = this.$route.name;
      this.post.from = 'btc';
      this.post.to = usd_exchanges.includes(this.exchange) ? 'usd' : 'usdt';
      this.old_to = this.post.to;

      this.init_available();

      this.ohlc_chart(true);
    },

    beforeDestroy() {
      this.clear_all_timeouts_intervals();

    }
  }
</script>

<style>

</style>
