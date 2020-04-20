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
        <card type="chart" id="chart_ohlc_cart">
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
        <card type="chart" id="chart_value_cart">
          <div class="chart-area">
            <div id="chart_view_value">
              <div ref="chart_value" class="chart"></div>
            </div>
          </div>
        </card>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-4">
        <card type="chart">
          <template slot="header">
            <h5 class="card-category">Realtime Value</h5>
            <h3 class="card-title"><i class="tim-icons icon-bell-55 text-primary "></i>{{last_realtime_value}} {{post.to}}</h3>
          </template>
          <div class="chart-area" id="realtime_value_cart">
            <div id="realtime">
              <div ref="chart_realtime" class="chart"></div>
            </div>
          </div>
        </card>
      </div>
      <div class="col-lg-4">
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
      <div class="col-lg-4" :class="{'text-right': isRTL}">
        <div id="highchart"></div>
      </div>
    </div>
  </div>
</template>
<script>
  import LineChart from '../components/Charts/LineChart';
  import BarChart from '../components/Charts/BarChart';
  import Ohlcchart from '../components/Charts/Ohlc';
  import TaskList from './Dashboard/TaskList';
  import UserTable from './Dashboard/UserTable';

  import { axios } from '../plugins/axios';
  import { Datetime } from 'vue-datetime';
  import Highcharts from 'highcharts';
  import Highstock from 'highcharts/highstock'
  var global_component_instance = null;

  Highcharts.createElement('link', {
    href: 'https://fonts.googleapis.com/css?family=Unica+One',
    rel: 'stylesheet',
    type: 'text/css'
  }, null, document.getElementsByTagName('head')[0]);
  Highcharts.theme = {
    colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
      '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
    chart: {
      backgroundColor: {
        linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
        stops: [
          [0, '#2a2a2b'],
          [1, '#3e3e40']
        ]
      },
      style: {
        fontFamily: '\'Unica One\', sans-serif'
      },
      plotBorderColor: '#606063'
    },
    title: {
      style: {
        color: '#E0E0E3',
        textTransform: 'uppercase',
        fontSize: '20px'
      }
    },
    subtitle: {
      style: {
        color: '#E0E0E3',
        textTransform: 'uppercase'
      }
    },
    xAxis: {
      gridLineColor: '#707073',
      labels: {
        style: {
          color: '#E0E0E3'
        }
      },
      lineColor: '#707073',
      minorGridLineColor: '#505053',
      tickColor: '#707073',
      title: {
        style: {
          color: '#A0A0A3'
        }
      }
    },
    yAxis: {
      gridLineColor: '#707073',
      labels: {
        style: {
          color: '#E0E0E3'
        }
      },
      lineColor: '#707073',
      minorGridLineColor: '#505053',
      tickColor: '#707073',
      tickWidth: 1,
      title: {
        style: {
          color: '#A0A0A3'
        }
      }
    },
    tooltip: {
      backgroundColor: 'rgba(0, 0, 0, 0.85)',
      style: {
        color: '#F0F0F0'
      }
    },
    plotOptions: {
      series: {
        dataLabels: {
          color: '#F0F0F3',
          style: {
            fontSize: '13px'
          }
        },
        marker: {
          lineColor: '#333'
        }
      },
      boxplot: {
        fillColor: '#505053'
      },
      candlestick: {
        lineColor: 'white'
      },
      errorbar: {
        color: 'white'
      }
    },
    legend: {
      backgroundColor: 'rgba(0, 0, 0, 0.5)',
      itemStyle: {
        color: '#E0E0E3'
      },
      itemHoverStyle: {
        color: '#FFF'
      },
      itemHiddenStyle: {
        color: '#606063'
      },
      title: {
        style: {
          color: '#C0C0C0'
        }
      }
    },
    credits: {
      style: {
        color: '#666'
      }
    },
    labels: {
      style: {
        color: '#707073'
      }
    },
    drilldown: {
      activeAxisLabelStyle: {
        color: '#F0F0F3'
      },
      activeDataLabelStyle: {
        color: '#F0F0F3'
      }
    },
    navigation: {
      buttonOptions: {
        symbolStroke: '#DDDDDD',
        theme: {
          fill: '#505053'
        }
      }
    },
    // scroll charts
    rangeSelector: {
      buttonTheme: {
        fill: '#505053',
        stroke: '#000000',
        style: {
          color: '#CCC'
        },
        states: {
          hover: {
            fill: '#707073',
            stroke: '#000000',
            style: {
              color: 'white'
            }
          },
          select: {
            fill: '#000003',
            stroke: '#000000',
            style: {
              color: 'white'
            }
          }
        }
      },
      inputBoxBorderColor: '#505053',
      inputStyle: {
        backgroundColor: '#333',
        color: 'silver'
      },
      labelStyle: {
        color: 'silver'
      }
    },
    navigator: {
      handles: {
        backgroundColor: '#666',
        borderColor: '#AAA'
      },
      outlineColor: '#CCC',
      maskFill: 'rgba(255,255,255,0.1)',
      series: {
        color: '#7798BF',
        lineColor: '#A6C7ED'
      },
      xAxis: {
        gridLineColor: '#505053'
      }
    },
    scrollbar: {
      barBackgroundColor: '#808083',
      barBorderColor: '#808083',
      buttonArrowColor: '#CCC',
      buttonBackgroundColor: '#606063',
      buttonBorderColor: '#606063',
      rifleColor: '#FFF',
      trackBackgroundColor: '#404043',
      trackBorderColor: '#404043'
    }
  };
  // Apply the theme
  Highcharts.setOptions(Highcharts.theme);
  Highstock.setOptions(Highcharts.theme);


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

    methods: {
      ohlc_value_chart(init){
        if (init){
          this.post.start = new Date().setSeconds(0, 0) / 1000 - 86400;
          this.post.end = new Date().setSeconds(0, 0) / 1000;
          this.post.range = '1h';
        }
        else{
          this.post.start = Date.parse(this.post.start) / 1000;
          this.post.end = Date.parse(this.post.end) / 1000;
        }

        let url_value = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto_historical/" + "ohlc_v2" + "/" + this.post.start + "/" + this.post.end + "/" + this.exchange + "/" + this.post.range + "/" + this.post.from + "/" + this.post.to;
        this.axios.get(url_value).then(response => (this.create_update_ohlc_chart(response.data)));

        let url_volume = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto_historical/" + "value_v2" + "/" + this.post.start + "/" + this.post.end + "/" + this.exchange + "/" + this.post.range + "/" + this.post.from + "/" + this.post.to;
        this.axios.get(url_volume).then(response => (this.create_update_value_chart(response.data)));
      },

      create_update_ohlc_chart(data){
        this.ohlc_chart_data = data['data'].map(function (item) {
          return [(item['x']) * 1000].concat(item["y"])
        });

        if (this.ohlc_chart != null){
          this.ohlc_chart.series[0].setData(this.ohlc_chart_data);
          return
        }

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
            text: this.post.from + " vs. " + this.post.to
          },

          xAxis: {
            type: 'datetime'
          },

          series: [{
            type: 'candlestick',
            name: 'Price',
            data: this.ohlc_chart_data,
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

        this.ohlc_chart = new Highstock.Chart(options);
      },

      create_update_value_chart(data){
        this.value_chart_data = data['data'].map(function (item) {
          return [(item[0] + 2 * 3600) * 1000, item[1]]
        });

        if (this.value_chart != null){
          this.value_chart.series[0].setData(this.value_chart_data);
          return
        }

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
            text: this.post.from + " vs. " + this.post.to
          },

          xAxis: {
            type: 'datetime'
          },

          series: [{
            name: 'Price',
            data: this.value_chart_data,
            marker: {
              enabled: true,
              radius: 3
            },
            shadow: true,
            tooltip: {
              valueDecimals: 2
            }
          }]};

        this.value_chart = new Highcharts.Chart(options);

      },

      create_update_realtime_value(){
        const card = document.getElementById('realtime_value_cart');

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
            name: 'Volume',
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
        if (init){
          this.real_time_data = data['data'].map(function (item) {
            return [item[0] * 1000, item[1]]
          });
          this.last_realtime_value = this.real_time_data.slice(-1)[0]['y'];
          this.realtime_chart.series[0].setData(this.real_time_data);
        }
        else{
          this.real_time_data.push([date * 1000, data['data']]);
          this.realtime_chart.series[0].addPoint(this.real_time_data[this.real_time_data.length - 1]);
          if (data['data']){
            this.last_realtime_value = data['data'];
          }

          this.real_time_value_interval = 60;
        }
      },

      save_realtime_response_data_volume(data, init, lastDateVolume){
        var last_value = this.last_realtime_volume;
        this.real_time_volume_data.push(...data['data'].map(function (item) {
          if(item['y']){
            last_value = item['y'];
          }
          return [item['x'] * 1000, item['y']]
        }));

        if (init){
          this.volume_chart.series[0].setData(this.real_time_volume_data)
        }
        else{
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
          if (item[1] != null) {
            return [item[0], item[1] / old_fiat * new_fiat]
          }
        });
        this.realtime_chart.series[0].setData(new_data);
        this.real_time_data= new_data;
        this.last_realtime_value = new_data.slice(-1)[0]['y'];

        new_data = this.real_time_volume_data.map(function (item) {
          if (item[1] != null){
            return [item[0], item[1] / old_fiat * new_fiat]
          }
        });
        this.volume_chart.series[0].setData(new_data);
        this.real_time_volume_data = new_data;
        this.last_realtime_volume = this.last_realtime_volume / old_fiat * new_fiat;

        new_data = this.ohlc_chart_data.map(function (item) {
          return [
            item[0],
            item[1] / old_fiat * new_fiat,
            item[2] / old_fiat * new_fiat,
            item[3] / old_fiat * new_fiat,
            item[4] / old_fiat * new_fiat
          ]
        });
        this.ohlc_chart.series[0].setData(new_data);
        this.ohlc_chart_data = new_data;

        new_data = this.value_chart_data.map(function (item) {
          return [item[0], item[1] / old_fiat * new_fiat]
        });
        this.value_chart.series[0].setData(new_data);
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
