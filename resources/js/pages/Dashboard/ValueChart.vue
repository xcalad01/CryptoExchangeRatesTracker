<template>
  <div>
    <div class="row">
      <div class="col-sm" style="display:grid;justify-content: center">
        <div class="form-group">
          <label class="label">From:</label>
          <select class="select-css" v-model=post.from @change="onChangeFrom()">
            <option v-for="item in from_available" :value="item">{{item.toUpperCase()}}</option>
          </select>
        </div>
      </div>
      <div class="col-sm" style="display:grid;justify-content: center">
        <div class="form-group">
          <label class="label">To:</label>
          <select class="select-css" v-model=post.to @change="onChangeTo()">
            <option v-for="item in to_available" :value="item">{{item.toUpperCase()}}</option>
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
        <form @submit.prevent="value_chart(false)">
          <div class="form-group">
            <button class="btn btn-primary">Query Value</button>
          </div>
        </form>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <card type="chart">
          <div class="chart-area" id="chart_value_cart">
            <div id="chart_view_value">
              <div ref="chart_value" class="chart"></div>
            </div>
          </div>
        </card>
      </div>
    </div>
  </div>
</template>

<script>
  import {Datetime} from "vue-datetime";
  import {axios} from "../../plugins/axios";
  import Highcharts from "highcharts";

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
          all_available: null
      }
    },

    methods: {
      value_chart(init){
        if (init){
          this.post.start = new Date().setSeconds(0, 0) / 1000 - 86400;
          this.post.end = new Date().setSeconds(0, 0) / 1000;
          this.post.range = '1h';
        }
        else{
          this.post.start = Date.parse(this.post.start) / 1000;
          this.post.end = Date.parse(this.post.end) / 1000;
        }

        let url_volume = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/" + "value" + "/" + this.post.start + "/" + this.post.end + "/" + this.exchange + "/" + this.post.range + "/" + this.post.from + "/" + this.post.to;
        if (this.chart == null){
          this.init_chart();
        }
        this.chart.showLoading();
        this.axios.get(url_volume).then(response => (this.update_chart(response.data)));
      },

      init_chart(){
        const card = document.getElementById('chart_value_cart');

        var options = {
          chart: {
            zoomType: 'x',
            renderTo: this.$refs.chart_value,
            height: card.offsetHeight,
            width: card.offsetWidth
          },

          rangeSelector: {
            selected: 1
          },

          title: {
            text: "Open/High/Close average price"
          },

          xAxis: {
            type: 'datetime'
          },

          series: [{
            name: 'Price',
            data: null,
            marker: {
              enabled: true,
              radius: 3
            },
            shadow: true,
            tooltip: {
              valueDecimals: 2
            }
          }]};

        this.chart = new Highcharts.Chart(options);
      },

      update_chart(data){
        this.chart_data = data['data'].map(function (item) {
          return [(item[0] + 2 * 3600) * 1000, item[1]]
        });

        if (this.chart != null){
          this.chart.hideLoading();
          this.chart.series[0].setData(this.chart_data);
        }
      },

      finish_init_avail(data){
          this.all_available = data['data'];
          this.from_available = Object.keys(this.all_available);
          this.to_available = this.all_available[this.from_available[0]];
      },

      init_available(){
        let uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/pairs/" + this.exchange;
        this.axios.get(uri).then(response => (this.finish_init_avail(response.data)));
      },

      onChangeFrom(){
          this.to_available = this.all_available[this.post.from];
          this.post.to = this.to_available[0];
      },

      finish_change_to(data){
        var new_fiat = data['data']['fiat'];
        var old_fiat = data['data']['old_fiat'];


        var new_data = this.chart_data.map(function (item) {
          return [item[0], item[1] / old_fiat * new_fiat]
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

      this.value_chart(true);
    }
  }
</script>

<style>
</style>
