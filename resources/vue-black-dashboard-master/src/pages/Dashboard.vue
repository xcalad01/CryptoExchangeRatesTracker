import ApexCharts from "apexcharts";
<template>
  <div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label>From:</label>
          <select v-model=post.from>
            <option v-for="item in from_available" :value="item.value">{{item.text}}</option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label>To:</label>
          <select v-model=post.to>
            <option v-for="item in to_available" :value="item.value">{{item.text}}</option>
          </select>
        </div>
      </div>
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
    </div>
    <div class="row">
      <div class="col-12">
        <card type="chart">
          <template slot="header">
            <div class="row">
              <div class="col-sm-6" :class="isRTL ? 'text-right' : 'text-left'">
                <h3 class="card-title">Candlestick chart</h3>
              </div>
            </div>
          </template>
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
            <h3 class="card-title"><i class="tim-icons icon-bell-55 text-primary "></i>{{last_realtime_value}}</h3>
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
            <h3 class="card-title"><i class="tim-icons icon-delivery-fast text-info "></i>{{last_realtime_volume}}</h3>
          </template>
          <div class="chart-area">
            <div id="volume">
              <div ref="chart_volume" class="chart"></div>
            </div>
          </div>
        </card>
      </div>
      <div class="col-lg-4" :class="{'text-right': isRTL}">
        <card type="chart">
          <template slot="header">
            <h5 class="card-category">{{$t('dashboard.completedTasks')}}</h5>
            <h3 class="card-title"><i class="tim-icons icon-send text-success "></i> 12,100K</h3>
          </template>
          <div class="chart-area">
            <line-chart style="height: 100%"
                        chart-id="green-line-chart"
                        :chart-data="greenLineChart.chartData"
                        :gradient-stops="greenLineChart.gradientStops"
                        :extra-options="greenLineChart.extraOptions">
            </line-chart>
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

  var global_component_instance = null;

  export default {
    components: {
      LineChart,
      BarChart,
      Ohlcchart,
      TaskList,
      UserTable
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
        last_realtime_value: null
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
        }
        this.$refs.bigChart.updateGradients(chartData);
        this.bigLineChart.chartData = chartData;
        this.bigLineChart.activeIndex = index;
      },

      addPost(){
        console.log(process.env.MIX_API_PORT);
        let uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto_historical/" + this.post.action + "/" + this.post.start + "/" + this.post.end + "/" + this.post.exchange + "/" + this.post.range + "/" + this.post.from + "/" + this.post.to;
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
            height: 250
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

        if (this.$refs.chart_ohlc) {
          this.ohlc_chart = new ApexCharts(this.$refs.chart_ohlc, chartOptions);
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
            height: 200,
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
        var options = {
          series: [{
            data: this.real_time_data
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
        if (this.$refs.chart_realtime) {
          this.realtime_chart = new ApexCharts(this.$refs.chart_realtime, options);
          this.realtime_chart.render();
        }

        this.getNewSeriesValue(true);

        window.setInterval(function () {
          global_component_instance.getNewSeriesValue(false);
        }, this.real_time_value_interval * 1000)
      },

      create_update_realtime_volume(){
        var options = {
          series: [{
            data: this.real_time_data
          }],
          chart: {
            id: 'realtime',
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


        window.setInterval(function () {
          global_component_instance.getNewSeriesVolume(false);
        }, this.real_time_volume_interval * 1000)
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
          var now = new Date() / 1000;
          this.real_time_value_interval = (this.lastDateValue + 60 + 10) - now;
        }
        else{
          this.real_time_data.push({x: date * 1000, y: data['data']});
          this.realtime_chart.updateSeries([{
            data: this.real_time_data
          }]);
          this.last_realtime_value = data['data'];
          this.real_time_value_interval = 60;
        }

        this.lastDateValue += 60;
      },

      save_realtime_response_data_volume(data){
        this.real_time_volume_data.push(...data['data'].map(function (item) {
          return {x:item['x'] * 1000, y: item['y']}
        }));

        this.volume_chart.updateSeries([{
          data: this.real_time_volume_data
        }]);
        this.last_realtime_volume = this.real_time_volume_data.slice(-1)[0]['y'];

        if (this.real_time_volume_interval === null){
          var now = new Date() / 1000;
          this.real_time_volume_interval = (this.lastDateVolume + 60 + 10) - now;
        }
        else{
          this.real_time_volume_interval = 60;
        }

        this.lastDateVolume += 60;
      },

      getNewSeriesValue(init){
        if (this.lastDateValue == null) {
          this.lastDateValue = new Date().setSeconds(0, 0) / 1000;
        }

        let value_uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto_current" + "/" + (this.lastDateValue-60) + "/" + "gdax" + "/" + "btc" + "/" + "usd/" + init;
        axios.get(value_uri).then(response => (global_component_instance.save_realtime_response_data_value(response.data, init, global_component_instance.lastDateValue)));
      },

      getNewSeriesVolume(init){
        if (this.lastDateVolume == null) {
          this.lastDateVolume = new Date().setSeconds(0, 0) / 1000;
        }

        let volume_uri;
        if (init){
          volume_uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto_historical/volume" + "/" + (this.lastDateVolume-1080) + "/" + (this.lastDateVolume+60) + "/" + "gdax" + "/" + "1m" + "/" + "btc" + "/" + "usd/";
        }
        else{
          volume_uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto_historical/volume" + "/" + (this.lastDateVolume-60) + "/" + (this.lastDateVolume) + "/" + "gdax" + "/" + "1m" + "/" + "btc" + "/" + "usd/";
        }
        axios.get(volume_uri).then(response => (global_component_instance.save_realtime_response_data_volume(response.data, init, global_component_instance.lastDateVolume)));
      },

      finish_init_avail(data){
        this.from_available = data['from'];
        this.to_available = data['to'];
      },

      init_available(){
        let uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/all_hist_avail/" + 'bitfinex';
        this.axios.get(uri).then(response => (this.finish_init_avail(response.data)));
      }

    },
    mounted() {
      global_component_instance = this;
      this.create_update_realtime_value();
      this.create_update_realtime_volume();
      this.init_available();

      this.i18n = this.$i18n;
      if (this.enableRTL) {
        this.i18n.locale = 'ar';
        this.$rtl.enableRTL();
      }
      // this.initBigChart(0);
    },
    beforeDestroy() {
      if (this.$rtl.isRTL) {
        this.i18n.locale = 'en';
        this.$rtl.disableRTL();
      }
    }
  };
</script>
<style>
</style>
