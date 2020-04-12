<template>
  <div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label>From:</label>
          <select v-model=post.from @change="onChangeFrom()">
            <option v-for="item in from_available" :value="item.value">{{item.text}}</option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>To:</label>
          <select v-model=post.to @change="onChangeTo()">
            <option v-for="item in to_available" :value="item.value">{{item.text}}</option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>Range:</label>
          <select v-model="post.range">
            <option disabled value="">Date rang, please select one</option>
            <option>1d</option>
            <option>12h</option>
            <option>6h</option>
            <option>3h</option>
            <option>1h</option>
            <option>5min</option>
            <option>1min</option>

          </select>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <label>Start:</label>
          <datetime type="datetime" v-model="post.start"></datetime>
        </div>
        <div class="col-md-6">
          <label>End:</label>
          <datetime type="datetime" v-model="post.end"></datetime>
        </div>
      </div>

      <form @submit.prevent="ohlc_value_chart(false)">
        <div class="form-group">
          <button class="btn btn-primary">Query</button>
        </div>
      </form>
    </div>
    <div class="row">
      <div class="col-12">
        <card type="chart">
          <div class="chart-area">
            <div id="chart_view_ohlc">
              <div ref="chart_ohlc" class="chart"></div>
            </div>
          </div>
        </card>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <card type="chart">
          <template slot="header">
            <div class="row">
              <div class="row">
                <div class="col-sm-6" :class="isRTL ? 'text-right' : 'text-left'">
<!--                  <h3 class="card-title">Value</h3>-->
                </div>
              </div>
            </div>
          </template>
          <div class="chart-area">
            <div id="chart_view_value">
              <div ref="chart_value" class="chart"></div>
            </div>
          </div>
        </card>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-4" :class="{'text-right': isRTL}">
        <card type="chart">
          <template slot="header">
            <h5 class="card-category">Realtime Value</h5>
            <h3 class="card-title"><i class="tim-icons icon-bell-55 text-primary "></i>{{last_realtime_value}} {{post.to}}</h3>
          </template>
          <div class="chart-area">
            <div id="realtime">
              <div ref="chart_realtime" class="chart"></div>
            </div>
          </div>
        </card>
      </div>
      <div class="col-lg-4" :class="{'text-right': isRTL}">
        <card type="chart">
          <template slot="header">
            <h5 class="card-category">Realtime Volume</h5>
            <h3 class="card-title"><i class="tim-icons icon-delivery-fast text-info "></i>{{last_realtime_volume}} {{post.to}}</h3>
          </template>
          <div class="chart-area">
            <div id="volume">
              <div ref="chart_volume" class="chart"></div>
            </div>
          </div>
        </card>
      </div>
    </div>
  </div>
</template>
<script>
  import LineChart from '../components/Charts/LineChart';
  import BarChart from '../components/Charts/BarChart';
  import Ohlcchart from '../components/Charts/Ohlc';
  import * as chartConfigs from '../components/Charts/config';
  import TaskList from './Dashboard/TaskList';
  import UserTable from './Dashboard/UserTable';
  import config from '../config';

  import { axios } from '../plugins/axios';
  import ApexCharts from "apexcharts";
  import { Datetime } from 'vue-datetime';

  var global_component_instance = null;

  export default {
    components: {
      LineChart,
      BarChart,
      Ohlcchart,
      TaskList,
      UserTable,
      datetime: Datetime
    },
    data() {
      return {
        bigLineChart: {
          allData: [],
          activeIndex: 0,
          chartData: null,
          extraOptions: chartConfigs.purpleChartOptions,
          gradientColors: config.colors.primaryGradient,
          gradientStops: [1, 0.4, 0],
          categories: [],
        },
        purpleLineChart: {
          extraOptions: chartConfigs.purpleChartOptions,
          chartData: {
            labels: ['JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],
            datasets: [{
              label: "Data",
              fill: true,
              borderColor: config.colors.primary,
              borderWidth: 2,
              borderDash: [],
              borderDashOffset: 0.0,
              pointBackgroundColor: config.colors.primary,
              pointBorderColor: 'rgba(255,255,255,0)',
              pointHoverBackgroundColor: config.colors.primary,
              pointBorderWidth: 20,
              pointHoverRadius: 4,
              pointHoverBorderWidth: 15,
              pointRadius: 4,
              data: [80, 100, 70, 80, 120, 80],
            }]
          },
          gradientColors: config.colors.primaryGradient,
          gradientStops: [1, 0.2, 0],
        },
        greenLineChart: {
          extraOptions: chartConfigs.greenChartOptions,
          chartData: {
            labels: ['JUL', 'AUG', 'SEP', 'OCT', 'NOV'],
            datasets: [{
              label: "My First dataset",
              fill: true,
              borderColor: config.colors.danger,
              borderWidth: 2,
              borderDash: [],
              borderDashOffset: 0.0,
              pointBackgroundColor: config.colors.danger,
              pointBorderColor: 'rgba(255,255,255,0)',
              pointHoverBackgroundColor: config.colors.danger,
              pointBorderWidth: 20,
              pointHoverRadius: 4,
              pointHoverBorderWidth: 15,
              pointRadius: 4,
              data: [90, 27, 60, 12, 80],
            }]
          },
          gradientColors: ['rgba(66,134,121,0.15)', 'rgba(66,134,121,0.0)', 'rgba(66,134,121,0)'],
          gradientStops: [1, 0.4, 0],
        },
        blueBarChart: {
          extraOptions: chartConfigs.barChartOptions,
          chartData: {
            labels: ['USA', 'GER', 'AUS', 'UK', 'RO', 'BR'],
            datasets: [{
              label: "Countries",
              fill: true,
              borderColor: config.colors.info,
              borderWidth: 2,
              borderDash: [],
              borderDashOffset: 0.0,
              data: [53, 20, 10, 80, 100, 45],
            }]
          },
          gradientColors: config.colors.primaryGradient,
          gradientStops: [1, 0.4, 0],
        },
        ohlc_chart:null,
        value_chart: null,
        realtime_chart:null,
        volume_chart:null,
        post:{},
        axios: axios,
        hist_available: [],

        from_available: null,

        to_available: null,

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

        old_to: null,

        exchange: null,

        start_date: null,
        end_date: null
      }
    },
    computed: {
      enableRTL() {
        return this.$route.query.enableRTL;
      },
      isRTL() {
        return this.$rtl.isRTL;
      },
      bigLineChartCategories() {
        return this.$t('dashboard.chartCategories');
      }
    },
    methods: {
      initBigChart(index) {
        let chartData = {
          datasets: [{
            fill: true,
            borderColor: config.colors.primary,
            borderWidth: 2,
            borderDash: [],
            borderDashOffset: 0.0,
            pointBackgroundColor: config.colors.primary,
            pointBorderColor: 'rgba(255,255,255,0)',
            pointHoverBackgroundColor: config.colors.primary,
            pointBorderWidth: 20,
            pointHoverRadius: 4,
            pointHoverBorderWidth: 15,
            pointRadius: 4,
            data: this.bigLineChart.allData[index]
          }],
          labels: ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],
        };
        this.$refs.bigChart.updateGradients(chartData);
        this.bigLineChart.chartData = chartData;
        this.bigLineChart.activeIndex = index;
      },

      ohlc_value_chart(init){
        if (init){
          this.post.start = new Date() / 1000 - 86400;
          this.post.end = new Date() / 1000;
          this.post.range = '1h';
        }
        else{
          this.post.start = Date.parse(this.post.start) / 1000;
          this.post.end = Date.parse(this.post.end) / 1000;
        }

        let url_value = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto_historical/" + "ohlc" + "/" + this.post.start + "/" + this.post.end + "/" + this.exchange + "/" + this.post.range + "/" + this.post.from + "/" + this.post.to;
        this.axios.get(url_value).then(response => (this.create_update_ohlc_chart(response.data)));

        let url_volume = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto_historical/" + "value" + "/" + this.post.start + "/" + this.post.end + "/" + this.exchange + "/" + this.post.range + "/" + this.post.from + "/" + this.post.to;
        this.axios.get(url_volume).then(response => (this.create_update_value_chart(response.data)));
      },

      create_update_ohlc_chart(data){
        this.ohlc_chart_data = data['data'].map(function (item) {
          return {x:(item['x'] - 3600) * 1000, y:item["y"]}
        });

        if (this.ohlc_chart != null){
          this.ohlc_chart.updateSeries([{data: this.ohlc_chart_data}]);
          return
        }
        var chartOptions = {
          chart: {
            type: 'candlestick',
            height: 250,
            foreColor: '#ffffff'
          },
          title: {
            text: 'CandleStick Chart',
            align: 'left'
          },
          xaxis: {
            type: 'category',
            labels: {
              formatter: function(val) {
                var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                var a = new Date(val);
                return a.getDate() + " " + months[a.getMonth()] + " " + a.getFullYear() + " " + a.getHours() + ":" + a.getMinutes();
              }
            }
          },
          tooltip: {
            enabled: true,
          },
          yaxis: {
            labels: {
              formatter: function (val) {
                return (val / 1).toFixed(2);
              },
            },
            title: {
              text: 'Ohlc'
            },
          },
          series: [{
            data: this.ohlc_chart_data
          }],
        };

        if (this.$refs.chart_ohlc) {
          this.ohlc_chart = new ApexCharts(this.$refs.chart_ohlc, chartOptions);
          this.ohlc_chart.render();
        }
      },

      create_update_value_chart(data){
        this.value_chart_data = data['data'].map(function (item) {
          return [new Date(item[0] * 1000 -  3600 * 1000), item[1]]
        });

        if (this.value_chart != null){
          this.value_chart.updateSeries([{name: "Price", "data": this.value_chart_data}]);
          return
        }

        var options = {
          series: [{
            name: 'Price',
            data: this.value_chart_data,
          }],
          chart: {
            type: 'area',
            stacked: false,
            height: 200,
            zoom: {
              type: 'x',
              enabled: true,
              autoScaleYaxis: true
            },
            toolbar: {
              autoSelected: 'zoom'
            },
            foreColor: '#ffffff'
          },
          dataLabels: {
            enabled: false
          },
          markers: {
            size: 0,
          },
          title: {
            text: 'Price Movement',
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
                return (val / 1).toFixed(2);
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
        var options = {
          series: [{
            data: this.real_time_data,
          }],
          chart: {
            id: 'realtime',
            height: 180,
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
            },
            foreColor: '#ffffff'
          },
          dataLabels: {
            enabled: false
          },
          stroke: {
            curve: 'smooth'
          },
          markers: {
            size: 0
          },
          xaxis: {
            type: 'datetime',
            range: 900000,
          },
          yaxis: {
            forceNiceScale: true,
          },
          legend: {
            show: false
          },
        };
        if (this.$refs.chart_realtime) {
          this.realtime_chart = new ApexCharts(this.$refs.chart_realtime, options);
          this.realtime_chart.render();
        }

        this.getNewSeriesValue(true);

        this.timeout_id_value = setTimeout(function () {
          global_component_instance.interval_id_value = global_component_instance.getNewSeriesValue(false);
          global_component_instance.interval_id_value = setInterval(function () {
            global_component_instance.interval_id_value = global_component_instance.getNewSeriesValue(false);
          }, 60 * 1000)
        }, global_component_instance.real_time_value_interval * 1000)
      },

      create_update_realtime_volume(){
        var options = {
          series: [{
            data: this.real_time_data
          }],
          chart: {
            id: 'realtime',
            foreColor: '#ffffff',
            height: 180,
            type: 'bar',
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
          markers: {
            size: 0
          },
          xaxis: {
            type: 'datetime',
            range: 900000,
          },
          yaxis: {
            forceNiceScale: true,
          },
          legend: {
            show: false
          },
        };
        if (this.$refs.chart_volume) {
          this.volume_chart = new ApexCharts(this.$refs.chart_volume, options);
          this.volume_chart.render();
        }

        this.getNewSeriesVolume(true);

        this.timeout_id_volume = setTimeout(function () {
          global_component_instance.interval_id_volume = global_component_instance.getNewSeriesVolume(false);
          global_component_instance.interval_id_volume = setInterval(function () {
            global_component_instance.interval_id_volume = global_component_instance.getNewSeriesVolume(false);
          }, 60 * 1000)
        }, global_component_instance.real_time_volume_interval * 1000)
      },

      save_realtime_response_data_value(data, init, date){
        if (init){
          this.real_time_data = data['data'].map(function (item) {
            return {x:item[0] * 1000, y: item[1]}
          });
          this.last_realtime_value = this.real_time_data.slice(-1)[0]['y'];
          this.realtime_chart.updateSeries([{
            data: this.real_time_data
          }]);
        }
        else{
          this.real_time_data.push({x: date * 1000, y: data['data']});
          this.realtime_chart.updateSeries([{
            data: this.real_time_data
          }]);
          if (data['data']){
            this.last_realtime_value = data['data'];
          }

          this.real_time_value_interval = 60;
        }
      },

      save_realtime_response_data_volume(data){
        var last_value = this.last_realtime_volume;
        this.real_time_volume_data.push(...data['data'].map(function (item) {
          if(item['y']){
            last_value = item['y'];
          }
          return {x:item['x'] * 1000, y: item['y']}
        }));

        this.volume_chart.updateSeries([{
          data: this.real_time_volume_data
        }]);

        this.last_realtime_volume = last_value;
      },

      getNewSeriesValue(init){
        if (this.lastDateValue == null) {
          this.lastDateValue = new Date().setSeconds(0, 0) / 1000;
        }
        if (init){
          var now = new Date() / 1000;
          this.real_time_value_interval = (this.lastDateValue + 60 + 50) - now;
        }

        let value_uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto_current" + "/" + (this.lastDateValue-60) + "/" + this.exchange + "/" + this.post.from + "/" + this.post.to + "/" + init;
        axios.get(value_uri).then(response => (global_component_instance.save_realtime_response_data_value(response.data, init, this.lastDateValue)));
        console.log(this.exchange);
        console.log("Value");
        console.log("Old: " + this.lastDateValue);
        this.lastDateValue += 60;
        console.log("New: " + this.lastDateValue);
        console.log(new Date());
      },

      getNewSeriesVolume(init){
        if (this.lastDateVolume == null) {
          this.lastDateVolume = new Date().setSeconds(0, 0) / 1000;
        }

        let volume_uri;
        if (init){
          var now = new Date() / 1000;
          this.real_time_volume_interval = (this.lastDateVolume + 60 + 50) - now;
          volume_uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto_historical/volume" + "/" + (this.lastDateVolume-1080) + "/" + (this.lastDateVolume+60) + "/" + this.exchange + "/" + "1m" + "/" + this.post.from + "/" + this.post.to;
        }
        else{
          volume_uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto_historical/volume" + "/" + (this.lastDateVolume-60) + "/" + (this.lastDateVolume) + "/" + this.exchange + "/" + "1m" + "/" + this.post.from + "/" + this.post.to;
        }
        axios.get(volume_uri).then(response => (global_component_instance.save_realtime_response_data_volume(response.data, init, this.lastDateVolume)));
        console.log("Volume");
        console.log("Old: " + this.lastDateVolume);
        this.lastDateVolume += 60;
        console.log("New: " + this.lastDateVolume);
        console.log(new Date());
      },

      finish_init_avail(data){
        this.from_available = data['from'];
        this.to_available = data['to'];
      },

      init_available(){
        let uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/all_hist_avail/" + this.exchange;
        this.axios.get(uri).then(response => (this.finish_init_avail(response.data)));
      },

      onChangeFrom(){
        this.clear_all_timeouts_intervals();
        this.create_update_realtime_value();
        this.create_update_realtime_volume();
        this.ohlc_value_chart(true);
      },

      finish_change_to(data){
        var new_fiat = data['data']['fiat'];
        var old_fiat = data['data']['old_fiat'];

        var new_data = this.real_time_data.map(function (item) {
          if (item["y"] != null) {
            return {x:item['x'], y:item["y"] / old_fiat * new_fiat}
          }
        });
        this.realtime_chart.updateSeries([{
          data: new_data
        }]);
        this.real_time_data= new_data;
        this.last_realtime_value = new_data.slice(-1)[0]['y'];

        new_data = this.real_time_volume_data.map(function (item) {
          if (item["y"] != null){
            return {x:item['x'], y:item["y"] / old_fiat * new_fiat}
          }
        });
        this.volume_chart.updateSeries([{
          data: new_data
        }]);
        this.real_time_volume_data = new_data;
        this.last_realtime_volume = this.last_realtime_volume / old_fiat * new_fiat;

        new_data = this.ohlc_chart_data.map(function (item) {
          return {x:item['x'], y:item["y"].map(function (item_y) {
            return item_y / old_fiat * new_fiat
            })}
        });
        this.ohlc_chart.updateSeries([{
          data: new_data
        }]);
        this.ohlc_chart_data = new_data;

        new_data = this.value_chart_data.map(function (item) {
          return [item[0], item[1] / old_fiat * new_fiat]
        });
        this.value_chart.updateSeries([{
          data: new_data
        }]);
        this.value_chart_data = new_data;

      },

      onChangeTo(){
        var now = new Date().setSeconds(0, 0) / 1000;
        var url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/fiat/historical/" + now + "/" + this.post.to + "/" + this.old_to;
        this.old_to = this.post.to;
        this.axios.get(url).then(response => (this.finish_change_to(response.data)));
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
      this.post.from = 'btc';
      this.post.to = 'usd';
      this.old_to ='usd';
      this.init_available();

      global_component_instance = this;
      this.ohlc_value_chart(true);
      this.create_update_realtime_value();
      this.create_update_realtime_volume();
    },
    beforeDestroy() {
      console.log(this.exchange);
      console.log(this.timeout_id_value);
      console.log(this.timeout_id_volume);
      console.log(this.interval_id_value);
      console.log(this.interval_id_volume);
      console.log("destroy");
      this.clear_all_timeouts_intervals();

    }
  };
</script>
<style>
</style>
