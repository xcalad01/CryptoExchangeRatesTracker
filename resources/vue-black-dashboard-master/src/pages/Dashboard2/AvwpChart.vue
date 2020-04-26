<template>
  <div>
    <div class="row">
      <div class="col-md-2" style="display:grid;justify-content: center">
        <label>Convert to:</label><br>
        <select v-model=post.to @change="onChangeTo()">
          <option v-for="item in to_available" :value="item.value">{{item.text}}</option>
        </select>
      </div>
      <div class="col-md-2" style="display:grid;justify-content: center">
        <label>Range:</label><br>
        <select v-model="post.range">
          <option>1d</option>
          <option>12h</option>
          <option>6h</option>
          <option>3h</option>
          <option>1h</option>
          <option>5m</option>
          <option>1m</option>

        </select>
      </div>
      <div class="col-md-2" style="display:grid;justify-content: center">
        <label style="display:grid;justify-content: center">Start:</label><br>
        <datetime type="date" v-model="post.start"></datetime>
      </div>
      <div class="col-md-2" style="display:grid;justify-content: center">
        <label style="display:grid;justify-content: center">End:</label><br>
        <datetime type="date" v-model="post.end"></datetime>
      </div>
      <div class="col-md-2" style="display:grid;justify-content: center">
        <form @submit.prevent="query_awvp_chart_data(false)">
          <button class="btn btn-primary">Query</button>
        </form>
      </div>
    </div>
    <br>
    <br>
    <div class="row">
      <div class="col-12">
        <card type="chart" id="chart_awvp_cart">
          <div class="chart-area">
            <div id="chart_view_awvp">
              <div ref="chart_awvp" class="chart"></div>
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
  import { Datetime } from 'vue-datetime';

  export default {
    components: {
      datetime: Datetime
    },

    data (){
      return {
        to_available: [
          {value: "usd", text: "USD"},
          {value: "eur", text: "EUR"}
        ],
        post: {},
        axios: axios,

        asset: null,

        awvp_chart_data: null,
        awvp_chart: null,

        title: null,

        old_to: null,
      }
    },

    mounted() {
      this.asset = this.$route.name;
      this.post.to = 'usd';
      this.old_to = this.post.to;
      this.title = this.$route.meta['title'];

      this.init_available();
      this.query_awvp_chart_data(true);
    },

    methods: {
      query_awvp_chart_data(init) {
        if (init) {
          this.post.start = new Date().setHours(0, 0, 0, 0) / 1000;
          this.post.end = new Date().setHours(0, 0, 0, 0) / 1000 + 86400;
          this.post.range = '1h';
        } else {
          this.post.start = Date.parse(this.post.start) / 1000;
          this.post.end = Date.parse(this.post.end) / 1000;
        }

        let url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/asset/value/" + this.asset + "/" + this.post.to + "/" + this.post.start + "/" + this.post.end + "/" + this.post.range;
        if (this.awvp_chart == null){
          this.init_awvp_chart();
        }
        this.awvp_chart.showLoading();
        this.axios.get(url).then(response => (this.update_awvp_chart(response.data)));
      },

      init_awvp_chart(){
        const card = document.getElementById('chart_awvp_cart');

        var options = {
          chart: {
            zoomType: 'x',
            renderTo: this.$refs.chart_awvp,
            height: card.offsetHeight,
            width: card.offsetWidth
          },

          rangeSelector: {
            selected: 1
          },

          title: {
            text: "Volume weighted average price"
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

        this.awvp_chart = new Highcharts.Chart(options);
      },

      update_awvp_chart(data){
        this.awvp_chart_data = data['data'].map(function (item) {
          return [(item[0]) * 1000, item[1]]
        });

        if (this.awvp_chart != null){
          this.awvp_chart.hideLoading();
          this.awvp_chart.series[0].setData(this.awvp_chart_data);
        }
      },

      finish_change_to(data){
        var new_fiat = data['data']['fiat'];
        var old_fiat = data['data']['old_fiat'];

        var new_data = this.awvp_chart_data.map(function (item) {
            return [item[0], item[1] / old_fiat * new_fiat];
        });

        this.awvp_chart.series[0].setData(new_data);
        this.awvp_chart_data = new_data;
      },

      onChangeTo(){
        var now = new Date().setSeconds(0, 0) / 1000;
        var url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/fiat/historical/" + now + "/" + this.post.to + "/" + this.old_to;
        this.old_to = this.post.to;
        this.axios.get(url).then(response => (this.finish_change_to(response.data)));
      },

      finish_init_avail(data){
        var formatted = data['data'].map(function (item) {
          return {value: item, text: item.toUpperCase()}
        });
        this.to_available = formatted;
      },

      init_available(){
        let uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/fiat/all_supported";
        this.axios.get(uri).then(response => (this.finish_init_avail(response.data)));
      },
    }
  }
</script>

<style>

</style>
