<template>
  <div>
    <div class="row">
      <div class="col-lg-8 ml-auto mr-auto">
        <div class="row">
          <div class="col-md-3" style="display:grid;justify-content: center">
            <label style="display:grid;justify-content: center">Day:</label><br>
            <datetime type="date" v-model="post.start"></datetime>
          </div>
          <div class="col-md-3" style="display:grid;justify-content: center">
            <label>Convert to:</label><br>
            <select v-model=post.to @change="onChangeFrom()">
              <option v-for="item in to_available" :value="item.value">{{item.text}}</option>
            </select>
          </div>
          <div class="col-md-4" style="display:grid;justify-content: center">
            <form @submit.prevent="asset_value(false)">
              <div class="form-group">
                <button class="btn btn-primary">Query Day Value</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-8 ml-auto mr-auto">
        <div class="row">
          <span style="width: 100%;text-align: center;font-size: 250%">{{currency_day_price}}</span>
        </div>
      </div>
    </div>
    <br>
    <br>
    <div class="row">
      <div class="col-md-2" style="display:grid;justify-content: center">
        <label>Convert to:</label><br>
        <select v-model=post.to @change="onChangeFrom()">
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
  import { Datetime } from 'vue-datetime';
  import Highcharts from 'highcharts';
  import { axios } from '../plugins/axios';
  import getSymbolFromCurrency from 'currency-symbol-map'

  export default {
    components: {
      datetime: Datetime,
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

        day_price: null,

        title: null

      }
    },

    computed: {
      currency_day_price(){
        if (this.day_price == null){
          return null;
        }

        return getSymbolFromCurrency(this.post.to.toUpperCase()) + " " + this.day_price;
      }
    },

    mounted() {
      this.asset = this.$route.name;
      this.post.to = 'usd';
      this.title = this.$route.meta['title'];

      this.asset_value(true);
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

      asset_value(init){
        if (init){
          this.post.start = (new Date().setHours(0,0,0,0) + new Date().getTimezoneOffset() * - 1 * 60 * 1000) / 1000;
          this.post.end = (new Date().setHours(0,0,0,0) + new Date().getTimezoneOffset() * - 1 * 60 * 1000 + 86400000) / 1000
        }
        else{
          this.post.start = Date.parse(this.post.start) / 1000;
          this.post.end = this.post.start + 86400;
        }

        let url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/asset/value/" + this.asset + "/" + this.post.to + "/" + this.post.start + "/" + this.post.end;
        this.axios.get(url).then(response => (this.update_value(response.data)));
      },

      update_value(data){
        this.day_price = data['data'][0][1].toFixed(3);
      }
    }
  }
</script>

<style>

</style>

