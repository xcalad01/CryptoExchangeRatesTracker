<template>
  <div>
    <div class="row">
      <div class="col-md-2">
        <label>Convert to:</label><br>
        <select v-model=post.to @change="onChangeFrom()">
          <option v-for="item in to_available" :value="item.value">{{item.text}}</option>
        </select>
      </div>
      <div class="col-md-2">
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
      <div class="col-md-2">
        <label>Start:</label>
        <datetime type="date" v-model="post.start"></datetime>
      </div>
      <div class="col-md-2">
        <label>End:</label>
        <datetime type="date" v-model="post.end"></datetime>
      </div>
      <div class="col-md-2">
        <form @submit.prevent="awvp_chart(false)">
          <div class="form-group">
            <button class="btn btn-primary">Query</button>
          </div>
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
  import { Datetime } from 'vue-datetime';
  import Highcharts from 'highcharts';
  import Highstock from 'highcharts/highstock';

  import { axios } from '../plugins/axios';

  export default {
    components: {
      datetime: Datetime
    },

    data() {
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

      }
    },

    mounted() {
      this.asset = this.$route.path;
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

        let url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/asset/value/" + "btc" + "/" + "usd" + "/" + this.post.start + "/" + this.post.end + "/" + this.post.range;
        console.log(url);
        this.axios.get(url).then(response => (this.create_update_awvp_chart(response.data)));
      },

      create_update_awvp_chart(data){
        this.awvp_chart_data = data['data'].map(function (item) {
          return [(item[0]) * 1000, item[1]]
        });

        if (this.awvp_chart != null){
          this.awvp_chart.series[0].setData(this.awvp_chart_data);
          return
        }

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
            text: this.post.from + " vs. " + this.post.to
          },

          xAxis: {
            type: 'datetime'
          },

          series: [{
            name: 'Price',
            data: this.awvp_chart_data,
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
      }
    }
  }
</script>

<style>

</style>

