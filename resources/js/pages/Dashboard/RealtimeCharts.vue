<template>
    <div class="row realtime_margin">
        <div class="col-lg-4">
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label class="label">From:</label><br>
                        <select class="select-css" v-model=post.value.from @change="onChangeFromValue()">
                            <option v-for="item in from_value_available" :value="item">{{item.toUpperCase()}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label class="label">To:</label><br>
                        <select class="select-css" v-model=post.value.to @change="onChangeToValue()">
                            <option v-for="item in to_value_available" :value="item">{{item.toUpperCase()}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <card type="chart">
                <template slot="header">
                    <div class="row">
                        <h5 class="card-category">Realtime Value</h5>
                    </div>
                    <div class="row">
                        <h3 class="card-title">
                            {{currency_symbol_value}} {{last_realtime_value}}
                        </h3>
                    </div>
                </template>
                <div class="chart-area" id="realtime_value_cart">
                    <div id="realtime">
                        <div ref="chart_realtime" class="chart"></div>
                    </div>
                </div>
            </card>
        </div>
        <div class="col-lg-4">
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label class="label">From:</label><br>
                        <select class="select-css" v-model=post.volume.from @change="onChangeFromVolume()">
                            <option v-for="item in from_volume_available" :value="item">{{item.toUpperCase()}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label class="label">To:</label><br>
                        <select class="select-css" v-model=post.volume.to @change="onChangeToVolume()">
                            <option v-for="item in to_volume_available" :value="item">{{item.toUpperCase()}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <card type="chart">
                <template slot="header">
                    <div class="row">
                        <h5 class="card-category">Realtime Volume</h5>
                    </div>
                    <div class="row">
                        <h3 class="card-title">
                            {{currency_symbol_volume}} {{last_realtime_volume}}
                        </h3>
                    </div>
                </template>
                <div class="chart-area">
                    <div id="volume">
                        <div ref="chart_volume" class="chart"></div>
                    </div>
                </div>
            </card>
        </div>
    </div>
</template>

<script>
  import Highcharts from "highcharts";
  import {axios} from "../../plugins/axios";
  import getSymbolFromCurrency from "currency-symbol-map";
  import Humanize from 'humanize-plus';

  var global_component_instance = null;

  export default {
    data (){
      return  {
        post: {
          value: {},
          volume: {}
        },

        realtime_chart:null,
        volume_chart:null,


        axios: axios,
        hist_available: [],

          from_value_available: null,
          from_volume_available: null,

          to_value_available: null,
          to_volume_available: null,

          all_available: null,

        last_realtime_value:null,
        last_realtime_volume:null,

        real_time_value_interval:null,
        real_time_volume_interval:null,

        lastDateValue: null,
        lastDateVolume: null,

        real_time_data: [],
        real_time_volume_data: [],
        value_chart_data: [],
        ohlc_chart_data: [],

        interval_id_value: null,
        interval_id_volume: null,

        timeout_id_value: null,
        timeout_id_volume: null,

        old_to_value: null,
        old_to_volume: null,

        exchange: null,

        currency_symbol_value: null,
        currency_symbol_volume: null,

        render_realtime: true,

          volume_by_currency_chart: null,
      }
    },

    methods: {
      create_update_realtime_value(){
        const card = document.getElementById('realtime_value_cart');

        if (this.realtime_chart){
            this.real_time_data = null;
            this.realtime_chart.series[0].setData([]);
        }

        var options = {
          chart: {
            renderTo: this.$refs.chart_realtime,
            height: card.offsetHeight,
            width: card.offsetWidth
          },

          time: {
            useUTC: false
          },

          title: {
            text: 'Realtime 1m value'
          },

          xAxis: {
            type: 'datetime'
          },

          exporting: {
            enabled: false
          },

          series: [{
            name: 'Price',
            data: []
          }]
        };

        this.realtime_chart = new Highcharts.Chart(options);

        this.getNewSeriesValue(true);

        this.timeout_id_value = setTimeout(function () {
          global_component_instance.interval_id_value = global_component_instance.getNewSeriesValue(false);
          global_component_instance.interval_id_value = setInterval(function () {
            global_component_instance.interval_id_value = global_component_instance.getNewSeriesValue(false);
          }, 60 * 1000)
        }, global_component_instance.real_time_value_interval * 1000)
      },

      create_update_realtime_volume(){
        const card = document.getElementById('realtime_value_cart');

          if (this.volume_chart){
              this.real_time_volume_data = null;
              this.volume_chart.series[0].setData([]);
          }

        var options = {
          chart: {
            renderTo: this.$refs.chart_volume,
            height: card.offsetHeight,
            width: card.offsetWidth
          },

          time: {
            useUTC: false
          },

          title: {
            text: 'Realtime 1m volume'
          },

          xAxis: {
            type: 'datetime'
          },

          exporting: {
            enabled: false
          },

          series: [{
            type: 'column',
            name: 'Price',
            data: []
          }]
        };

        this.volume_chart = new Highcharts.Chart(options);
        this.getNewSeriesVolume(true);

        this.timeout_id_volume = setTimeout(function () {
          global_component_instance.interval_id_volume = global_component_instance.getNewSeriesVolume(false);
          global_component_instance.interval_id_volume = setInterval(function () {
            global_component_instance.interval_id_volume = global_component_instance.getNewSeriesVolume(false);
          }, 60 * 1000)
        }, global_component_instance.real_time_volume_interval * 1000)
      },

      save_realtime_response_data_value(data, init, date){
          this.realtime_chart.hideLoading();
        if (init){
          this.real_time_data = data['data'].map(function (item) {
            return [item[0] * 1000, item[1]]
          });
          this.last_realtime_value = Humanize.formatNumber((this.real_time_data[this.real_time_data.length - 1][1]), 3);
          this.realtime_chart.series[0].setData(this.real_time_data);
        }
        else{
          this.real_time_data.push([date * 1000, data['data'][0][1]]);
          this.realtime_chart.series[0].addPoint(this.real_time_data[this.real_time_data.length - 1]);
          if (data['data']){
            this.last_realtime_value = Humanize.formatNumber(data['data'][0][1], 3);
          }

          this.real_time_value_interval = 60;
        }
      },

      save_realtime_response_data_volume(data, init, lastDateVolume){
          this.volume_chart.hideLoading();
          if (init){
              this.real_time_volume_data = data['data'].map(function (item) {
                  if(item['y']){
                      last_value = Humanize.formatNumber(item['y'], 3);
                  }
                  return [item['x'] * 1000, item['y']]
              });
              this.volume_chart.series[0].setData(this.real_time_volume_data)
          }
          else{
              var last_value = this.last_realtime_volume;
              this.real_time_volume_data.push(...data['data'].map(function (item) {
                  if(item['y']){
                      last_value = Humanize.formatNumber(item['y']);
                  }
                  return [item['x'] * 1000, item['y']]
              }));
              this.volume_chart.series[0].addPoint(this.real_time_volume_data[this.real_time_volume_data.length - 1], true, true)
          }

        this.last_realtime_volume = last_value;
      },

      getNewSeriesValue(init){
        if (this.lastDateValue == null) {
          this.lastDateValue = new Date().setSeconds(0, 0) / 1000;
        }
        if (init){
          var now = new Date() / 1000;
          this.real_time_value_interval = (this.lastDateValue + 60 + 50) - now;
          var start = this.lastDateValue-1080;
          var end = this.lastDateValue;
        }
        else{
            var start = this.lastDateValue-60;
            var end = this.lastDateValue;
        }

        this.realtime_chart.showLoading();
        let value_uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/value/" + start + "/" + end + "/" + this.exchange + "/" + "1m" + "/" + this.post.value.from + "/" + this.post.value.to;
        axios.get(value_uri).then(response => (global_component_instance.save_realtime_response_data_value(response.data, init, this.lastDateValue)));
        this.lastDateValue += 60;
      },

      getNewSeriesVolume(init){
        if (this.lastDateVolume == null) {
          this.lastDateVolume = new Date().setSeconds(0, 0) / 1000;
        }

        let volume_uri;
        if (init){
          var now = new Date() / 1000;
          this.real_time_volume_interval = (this.lastDateVolume + 60 + 50) - now;
          volume_uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/volume" + "/" + (this.lastDateVolume-1080) + "/" + (this.lastDateVolume+60) + "/" + this.exchange + "/" + "1m" + "/" + this.post.volume.from + "/" + this.post.volume.to;
        }
        else{
          volume_uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/volume" + "/" + (this.lastDateVolume-60) + "/" + (this.lastDateVolume) + "/" + this.exchange + "/" + "1m" + "/" + this.post.volume.from + "/" + this.post.volume.to;
        }

        this.volume_chart.showLoading();
        axios.get(volume_uri).then(response => (global_component_instance.save_realtime_response_data_volume(response.data, init, this.lastDateVolume)));
        this.lastDateVolume += 60;
      },

      finish_init_avail(data){
          this.all_available = data['data'];

          this.from_value_available = Object.keys(this.all_available);
          this.to_value_available = this.all_available[this.from_value_available[0]];

          this.from_volume_available = Object.keys(this.all_available);
          this.to_volume_available = this.all_available[this.from_volume_available[0]];

          this.post.value.from = this.from_value_available[0];
          this.post.volume.from = this.from_volume_available[0];
          this.post.value.to = this.to_value_available[0];
          this.post.volume.to = this.to_volume_available[0];

          this.old_to_value = this.post.value.to;
          this.old_to_volume = this.post.volume.to;

          this.create_update_realtime_value();
          this.create_update_realtime_volume();
      },

      init_available(){
        let uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/pairs/" + this.exchange;
        this.axios.get(uri).then(response => (this.finish_init_avail(response.data)));
      },

      onChangeFromValue(){
          this.to_value_available = this.all_available[this.post.value.from];
          this.old_to_value = this.post.value.to;
          this.post.value.to = this.to_value_available[0];
          this.old_to_value = this.post.value.to;
          this.lastDateValue = null;
          this.clear_value_realtime_timeouts_intervals();
          this.create_update_realtime_value();
      },

      onChangeFromVolume(){
          this.to_volume_available = this.all_available[this.post.volume.from];
          this.old_to_volume = this.post.volume.to;
          this.post.volume.to = this.to_volume_available[0];
          this.old_to_volume = this.post.volume.to;
          this.lastDateVolume = null;
          this.clear_volume_realtime_timeouts_intervals();
          this.create_update_realtime_volume();
      },

      finish_change_to_value(data){
        var new_fiat = data['data']['fiat'];
        var old_fiat = data['data']['old_fiat'];

        var new_data = this.real_time_data.map(function (item) {
          if (item[1] != null) {
            return [item[0], item[1] / old_fiat * new_fiat]
          }
        });
        this.realtime_chart.series[0].setData(new_data);
        this.real_time_data = new_data;
        this.last_realtime_value = Humanize.formatNumber(new_data[new_data.length - 1][1], 3);
      },

      finish_change_to_volume(data){
        var new_fiat = data['data']['fiat'];
        var old_fiat = data['data']['old_fiat'];

        var new_data = this.real_time_volume_data.map(function (item) {
          if (item[1] != null){
            return [item[0], item[1] / old_fiat * new_fiat]
          }
        });
        this.volume_chart.series[0].setData(new_data);
        this.real_time_volume_data = new_data;
        this.last_realtime_volume = Humanize.formatNumber(new_data[new_data.length - 1][1], 3);

      },

      onChangeToValue(){
        var now = new Date().setSeconds(0, 0) / 1000;
        var url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/fiat/historical/" + now + "/" + this.post.value.to + "/" + this.old_to_value;
        this.old_to_value = this.post.value.to;
        this.axios.get(url).then(response => (this.finish_change_to_value(response.data)));
      },

      onChangeToVolume(){
        var now = new Date().setSeconds(0, 0) / 1000;
        var url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/fiat/historical/" + now + "/" + this.post.volume.to + "/" + this.old_to_volume;
        this.old_to_volume = this.post.volume.to;
        this.axios.get(url).then(response => (this.finish_change_to_volume(response.data)));
      },

      clear_value_realtime_timeouts_intervals(){
        clearTimeout(this.timeout_id_value);
        clearInterval(this.interval_id_value);
      },

      clear_volume_realtime_timeouts_intervals(){
        clearTimeout(this.timeout_id_volume);
        clearInterval(this.interval_id_volume);
      },

      clear_all_timeouts_intervals(){
        clearTimeout(this.timeout_id_value);
        clearTimeout(this.timeout_id_volume);
        clearInterval(this.interval_id_value);
        clearInterval(this.interval_id_volume);
      }
    },

    mounted() {
      this.exchange = this.$route.name;
      global_component_instance = this;
      this.init_available();
    },

    watch: {
      old_to_value: function (){
        if (this.post.value.to){
          this.currency_symbol_value = getSymbolFromCurrency(this.post.value.to.toUpperCase());
        }
      },
      old_to_volume: function (){
        if (this.post.volume.to){
          this.currency_symbol_volume = getSymbolFromCurrency(this.post.volume.to.toUpperCase());
        }
      }
    },

    beforeDestroy() {
      this.clear_all_timeouts_intervals();

    }
  }
</script>

<style>
    .realtime_margin{
        margin-top: 30px;
    }
</style>
