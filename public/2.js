(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[2],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/OhlcChart.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard/OhlcChart.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _plugins_axios__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../plugins/axios */ "./resources/js/plugins/axios.js");
/* harmony import */ var vue_datetime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-datetime */ "./node_modules/vue-datetime/dist/vue-datetime.js");
/* harmony import */ var vue_datetime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(vue_datetime__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var highcharts_highstock__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! highcharts/highstock */ "./node_modules/highcharts/highstock.js");
/* harmony import */ var highcharts_highstock__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(highcharts_highstock__WEBPACK_IMPORTED_MODULE_2__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



var global_component_instance = null;
/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    datetime: vue_datetime__WEBPACK_IMPORTED_MODULE_1__["Datetime"]
  },
  data: function data() {
    return {
      post: {},
      chart: null,
      chart_data: null,
      axios: _plugins_axios__WEBPACK_IMPORTED_MODULE_0__["axios"],
      exchange: null,
      old_to: null,
      from_available: null,
      to_available: null
    };
  },
  methods: {
    ohlc_chart: function ohlc_chart(init) {
      var _this = this;

      if (init) {
        this.post.start = new Date().setSeconds(0, 0) / 1000 - 86400;
        this.post.end = new Date().setSeconds(0, 0) / 1000;
        this.post.range = '1h';
      } else {
        this.post.start = Date.parse(this.post.start) / 1000;
        this.post.end = Date.parse(this.post.end) / 1000;
      }

      var url_value = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/" + "ohlc" + "/" + this.post.start + "/" + this.post.end + "/" + this.exchange + "/" + this.post.range + "/" + this.post.from + "/" + this.post.to;

      if (this.chart == null) {
        this.init_chart();
      }

      this.chart.showLoading();
      this.axios.get(url_value).then(function (response) {
        return _this.update_chart(response.data);
      });
    },
    init_chart: function init_chart() {
      var card = document.getElementById('chart_ohlc_cart');
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
            units: [['week', // unit name
            [1] // allowed multiples
            ], ['month', [1, 2, 3, 4, 6]]]
          },
          tooltip: {
            valueDecimals: 4
          }
        }]
      };
      this.chart = new highcharts_highstock__WEBPACK_IMPORTED_MODULE_2___default.a.Chart(options);
    },
    update_chart: function update_chart(data) {
      this.chart_data = data['data'].map(function (item) {
        return [item['x'] * 1000].concat(item["y"]);
      });

      if (this.chart != null) {
        this.chart.hideLoading();
        this.chart.series[0].setData(this.chart_data);
      }
    },
    finish_init_avail: function finish_init_avail(data) {
      this.all_available = data['data'];
      this.from_available = Object.keys(this.all_available);
      this.to_available = this.all_available[this.from_available[0]];
      this.post.from = this.from_available[0];
      this.post.to = this.to_available[0];
      this.ohlc_chart(true);
    },
    init_available: function init_available() {
      var _this2 = this;

      var uri = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/pairs/" + this.exchange;
      this.axios.get(uri).then(function (response) {
        return _this2.finish_init_avail(response.data);
      });
      this.old_to = this.post.to;
    },
    clear_all_timeouts_intervals: function clear_all_timeouts_intervals() {
      clearTimeout(this.timeout_id_value);
      clearTimeout(this.timeout_id_volume);
      clearInterval(this.interval_id_value);
      clearInterval(this.interval_id_volume);
    },
    onChangeFrom: function onChangeFrom() {
      this.to_available = this.all_available[this.post.from];
      this.post.to = this.to_available[0];
    },
    finish_change_to: function finish_change_to(data) {
      var new_fiat = data['data']['fiat'];
      var old_fiat = data['data']['old_fiat'];
      var new_data = this.chart_data.map(function (item) {
        return [item[0], item[1] / old_fiat * new_fiat, item[2] / old_fiat * new_fiat, item[3] / old_fiat * new_fiat, item[4] / old_fiat * new_fiat];
      });
      this.chart.series[0].setData(new_data);
      this.chart_data = new_data;
    },
    onChangeTo: function onChangeTo() {
      var _this3 = this;

      var now = new Date().setSeconds(0, 0) / 1000;
      var url = "http://" + "167.86.75.179" + ":" + "8001" + "/api/fiat/historical/" + now + "/" + this.post.to + "/" + this.old_to;
      this.old_to = this.post.to;
      this.axios.get(url).then(function (response) {
        return _this3.finish_change_to(response.data);
      });
    }
  },
  mounted: function mounted() {
    this.exchange = this.$route.name;
    this.init_available();
  },
  beforeDestroy: function beforeDestroy() {
    this.clear_all_timeouts_intervals();
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/QuickStats.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard/QuickStats.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var highcharts__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! highcharts */ "./node_modules/highcharts/highcharts.js");
/* harmony import */ var highcharts__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(highcharts__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _plugins_axios__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../plugins/axios */ "./resources/js/plugins/axios.js");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {
      axios: _plugins_axios__WEBPACK_IMPORTED_MODULE_1__["axios"],
      exchange: null,
      year_established: null,
      country: null,
      centralized: null,
      payment_methods: null,
      url: null,
      facebook: null,
      reddit: null,
      twitter: null
    };
  },
  methods: {
    volume_by_currency: function volume_by_currency(data) {
      var chart_data = Object.keys(data).map(function (item) {
        return {
          name: item,
          y: data[item]
        };
      });
      var card = document.getElementById('volume_by_currency_card');
      console.log(chart_data);
      var options = {
        chart: {
          renderTo: this.$refs.chart_volume_by_currency,
          height: card.offsetHeight,
          width: card.offsetWidth,
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie'
        },
        title: {
          text: 'Volume by currency'
        },
        tooltip: {
          pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
          point: {
            valueSuffix: '%'
          }
        },
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
              enabled: true,
              format: '<b>{point.name}</b>: {point.percentage:.1f} %'
            }
          }
        },
        series: [{
          name: 'Currency',
          colorByPoint: true,
          data: chart_data
        }]
      };
      this.volume_by_currency_chart = new highcharts__WEBPACK_IMPORTED_MODULE_0___default.a.Chart(options);
    },
    volume_by_pair: function volume_by_pair(data) {
      var chart_data = Object.keys(data).map(function (item) {
        return {
          name: item,
          y: data[item]
        };
      });
      var card = document.getElementById('volume_by_pair_card');
      var options = {
        chart: {
          renderTo: this.$refs.chart_volume_by_pair,
          height: card.offsetHeight,
          width: card.offsetWidth,
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie'
        },
        title: {
          text: 'Volume by market pair'
        },
        tooltip: {
          pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
          point: {
            valueSuffix: '%'
          }
        },
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
              enabled: true,
              format: '<b>{point.name}</b>: {point.percentage:.1f} %'
            }
          }
        },
        series: [{
          name: 'Pair',
          colorByPoint: true,
          data: chart_data
        }]
      };
      this.volume_by_currency_chart = new highcharts__WEBPACK_IMPORTED_MODULE_0___default.a.Chart(options);
    },
    additional_info: function additional_info(data) {
      this.year_established = data['Year'];
      this.country = data['Country'];
      this.centralized = data['Centralized'];
      this.payment_methods = data['Accepted_payment_methods'];
      this.url = data['Url'];
      this.facebook = data['Facebook'];
      this.reddit = data['Reddit'];
      this.twitter = data['Twitter'];
    },
    create_quick_stats: function create_quick_stats() {
      var _this = this;

      var uri = "http://" + "167.86.75.179" + ":" + "8001" + "/api/exchange/stats/" + this.exchange;
      this.axios.get(uri).then(function (response) {
        return _this.finish_quick_stats(response.data);
      });
    },
    finish_quick_stats: function finish_quick_stats(data) {
      this.additional_info(data['data']['additional']);
      this.volume_by_currency(data['data']['volume_by_currency']);
      this.volume_by_pair(data['data']['volume_by_pair']);
    }
  },
  mounted: function mounted() {
    this.exchange = this.$route.name;
    this.create_quick_stats();
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var highcharts__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! highcharts */ "./node_modules/highcharts/highcharts.js");
/* harmony import */ var highcharts__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(highcharts__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _plugins_axios__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../plugins/axios */ "./resources/js/plugins/axios.js");
/* harmony import */ var currency_symbol_map__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! currency-symbol-map */ "./node_modules/currency-symbol-map/currency-symbol-map.js");
/* harmony import */ var currency_symbol_map__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(currency_symbol_map__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var humanize_plus__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! humanize-plus */ "./node_modules/humanize-plus/dist/humanize.js");
/* harmony import */ var humanize_plus__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(humanize_plus__WEBPACK_IMPORTED_MODULE_3__);
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//




var global_component_instance = null;
/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {
      post: {
        value: {},
        volume: {}
      },
      realtime_chart: null,
      volume_chart: null,
      axios: _plugins_axios__WEBPACK_IMPORTED_MODULE_1__["axios"],
      hist_available: [],
      from_value_available: null,
      from_volume_available: null,
      to_value_available: null,
      to_volume_available: null,
      all_available: null,
      last_realtime_value: null,
      last_realtime_volume: null,
      real_time_value_interval: null,
      real_time_volume_interval: null,
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
      volume_by_currency_chart: null
    };
  },
  methods: {
    create_update_realtime_value: function create_update_realtime_value() {
      var card = document.getElementById('realtime_value_cart');

      if (this.realtime_chart) {
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

      if (this.timeout_id_value) {
        this.clear_value_realtime_timeouts_intervals();
      }

      this.realtime_chart = new highcharts__WEBPACK_IMPORTED_MODULE_0___default.a.Chart(options);
      this.lastDateValue = null;
      this.getNewSeriesValue(true);
      this.timeout_id_value = setTimeout(function () {
        global_component_instance.interval_id_value = global_component_instance.getNewSeriesValue(false);
        global_component_instance.interval_id_value = setInterval(function () {
          global_component_instance.interval_id_value = global_component_instance.getNewSeriesValue(false);
        }, 60 * 1000);
      }, global_component_instance.real_time_value_interval * 1000);
    },
    create_update_realtime_volume: function create_update_realtime_volume() {
      var card = document.getElementById('realtime_value_cart');

      if (this.volume_chart) {
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

      if (this.timeout_id_volume) {
        this.clear_volume_realtime_timeouts_intervals();
      }

      this.volume_chart = new highcharts__WEBPACK_IMPORTED_MODULE_0___default.a.Chart(options);
      this.lastDateVolume = null;
      this.getNewSeriesVolume(true);
      this.timeout_id_volume = setTimeout(function () {
        global_component_instance.interval_id_volume = global_component_instance.getNewSeriesVolume(false);
        global_component_instance.interval_id_volume = setInterval(function () {
          global_component_instance.interval_id_volume = global_component_instance.getNewSeriesVolume(false);
        }, 60 * 1000);
      }, global_component_instance.real_time_volume_interval * 1000);
    },
    save_realtime_response_data_value: function save_realtime_response_data_value(data, init, date) {
      this.realtime_chart.hideLoading();

      if (init) {
        this.real_time_data = data['data'].map(function (item) {
          return [item[0] * 1000, item[1]];
        });
        this.last_realtime_value = humanize_plus__WEBPACK_IMPORTED_MODULE_3___default.a.formatNumber(this.real_time_data[this.real_time_data.length - 1][1], 3);
        this.realtime_chart.series[0].setData(this.real_time_data);
      } else {
        this.real_time_data.push([data['data'][0][0] * 1000, data['data'][0][1]]);
        this.realtime_chart.series[0].addPoint(this.real_time_data[this.real_time_data.length - 1], true, true);

        if (data['data']) {
          this.last_realtime_value = humanize_plus__WEBPACK_IMPORTED_MODULE_3___default.a.formatNumber(data['data'][0][1], 3);
        }

        this.real_time_value_interval = 60;
      }
    },
    save_realtime_response_data_volume: function save_realtime_response_data_volume(data, init, lastDateVolume) {
      this.volume_chart.hideLoading();

      if (init) {
        this.real_time_volume_data = data['data'].map(function (item) {
          if (item['y']) {
            last_value = humanize_plus__WEBPACK_IMPORTED_MODULE_3___default.a.formatNumber(item['y'], 3);
          }

          return [item['x'] * 1000, item['y']];
        });
        this.volume_chart.series[0].setData(this.real_time_volume_data);
      } else {
        var _this$real_time_volum;

        var last_value = this.last_realtime_volume;

        (_this$real_time_volum = this.real_time_volume_data).push.apply(_this$real_time_volum, _toConsumableArray(data['data'].map(function (item) {
          if (item['y']) {
            last_value = humanize_plus__WEBPACK_IMPORTED_MODULE_3___default.a.formatNumber(item['y']);
          }

          return [item['x'] * 1000, item['y']];
        })));

        this.volume_chart.series[0].addPoint(this.real_time_volume_data[this.real_time_volume_data.length - 1], true, true);
      }

      this.last_realtime_volume = last_value;
    },
    getNewSeriesValue: function getNewSeriesValue(init) {
      var _this = this;

      if (!this.lastDateValue) {
        this.lastDateValue = new Date().setSeconds(0, 0) / 1000;
      }

      if (init) {
        var now = new Date() / 1000;
        this.real_time_value_interval = this.lastDateValue + 60 + 50 - now;
        var start = this.lastDateValue - 1080;
        var end = this.lastDateValue;
      } else {
        var start = this.lastDateValue - 60;
        var end = this.lastDateValue;
      }

      this.realtime_chart.showLoading();
      var value_uri = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/value/" + start + "/" + end + "/" + this.exchange + "/" + "1m" + "/" + this.post.value.from + "/" + this.post.value.to;
      _plugins_axios__WEBPACK_IMPORTED_MODULE_1__["axios"].get(value_uri).then(function (response) {
        return global_component_instance.save_realtime_response_data_value(response.data, init, _this.lastDateValue);
      });
      this.lastDateValue += 60;
    },
    getNewSeriesVolume: function getNewSeriesVolume(init) {
      var _this2 = this;

      if (!this.lastDateVolume) {
        this.lastDateVolume = new Date().setSeconds(0, 0) / 1000;
      }

      var volume_uri;

      if (init) {
        var now = new Date() / 1000;
        this.real_time_volume_interval = this.lastDateVolume + 60 + 50 - now;
        volume_uri = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/volume" + "/" + (this.lastDateVolume - 1080) + "/" + (this.lastDateVolume + 60) + "/" + this.exchange + "/" + "1m" + "/" + this.post.volume.from + "/" + this.post.volume.to;
      } else {
        volume_uri = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/volume" + "/" + (this.lastDateVolume - 60) + "/" + this.lastDateVolume + "/" + this.exchange + "/" + "1m" + "/" + this.post.volume.from + "/" + this.post.volume.to;
      }

      this.volume_chart.showLoading();
      _plugins_axios__WEBPACK_IMPORTED_MODULE_1__["axios"].get(volume_uri).then(function (response) {
        return global_component_instance.save_realtime_response_data_volume(response.data, init, _this2.lastDateVolume);
      });
      this.lastDateVolume += 60;
    },
    finish_init_avail: function finish_init_avail(data) {
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
    init_available: function init_available() {
      var _this3 = this;

      var uri = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/pairs/" + this.exchange;
      this.axios.get(uri).then(function (response) {
        return _this3.finish_init_avail(response.data);
      });
    },
    onChangeFromValue: function onChangeFromValue() {
      this.to_value_available = this.all_available[this.post.value.from];
      this.old_to_value = this.post.value.to;
      this.post.value.to = this.to_value_available[0];
      this.old_to_value = this.post.value.to;
      this.lastDateValue = null;
      this.clear_value_realtime_timeouts_intervals();
      this.create_update_realtime_value();
    },
    onChangeFromVolume: function onChangeFromVolume() {
      this.to_volume_available = this.all_available[this.post.volume.from];
      this.old_to_volume = this.post.volume.to;
      this.post.volume.to = this.to_volume_available[0];
      this.old_to_volume = this.post.volume.to;
      this.lastDateVolume = null;
      this.clear_volume_realtime_timeouts_intervals();
      this.create_update_realtime_volume();
    },
    finish_change_to_value: function finish_change_to_value(data) {
      var new_fiat = data['data']['fiat'];
      var old_fiat = data['data']['old_fiat'];
      var new_data = this.real_time_data.map(function (item) {
        if (item[1] != null) {
          return [item[0], item[1] / old_fiat * new_fiat];
        }
      });
      this.realtime_chart.series[0].setData(new_data);
      this.real_time_data = new_data;
      this.last_realtime_value = humanize_plus__WEBPACK_IMPORTED_MODULE_3___default.a.formatNumber(new_data[new_data.length - 1][1], 3);
    },
    finish_change_to_volume: function finish_change_to_volume(data) {
      var new_fiat = data['data']['fiat'];
      var old_fiat = data['data']['old_fiat'];
      var new_data = this.real_time_volume_data.map(function (item) {
        if (item[1] != null) {
          return [item[0], item[1] / old_fiat * new_fiat];
        }
      });
      this.volume_chart.series[0].setData(new_data);
      this.real_time_volume_data = new_data;
      this.last_realtime_volume = humanize_plus__WEBPACK_IMPORTED_MODULE_3___default.a.formatNumber(new_data[new_data.length - 1][1], 3);
    },
    onChangeToValue: function onChangeToValue() {
      var _this4 = this;

      var now = new Date().setSeconds(0, 0) / 1000;
      var url = "http://" + "167.86.75.179" + ":" + "8001" + "/api/fiat/historical/" + now + "/" + this.post.value.to + "/" + this.old_to_value;
      this.old_to_value = this.post.value.to;
      this.axios.get(url).then(function (response) {
        return _this4.finish_change_to_value(response.data);
      });
    },
    onChangeToVolume: function onChangeToVolume() {
      var _this5 = this;

      var now = new Date().setSeconds(0, 0) / 1000;
      var url = "http://" + "167.86.75.179" + ":" + "8001" + "/api/fiat/historical/" + now + "/" + this.post.volume.to + "/" + this.old_to_volume;
      this.old_to_volume = this.post.volume.to;
      this.axios.get(url).then(function (response) {
        return _this5.finish_change_to_volume(response.data);
      });
    },
    clear_value_realtime_timeouts_intervals: function clear_value_realtime_timeouts_intervals() {
      clearTimeout(this.timeout_id_value);
      clearInterval(this.interval_id_value);
    },
    clear_volume_realtime_timeouts_intervals: function clear_volume_realtime_timeouts_intervals() {
      clearTimeout(this.timeout_id_volume);
      clearInterval(this.interval_id_volume);
    },
    clear_all_timeouts_intervals: function clear_all_timeouts_intervals() {
      clearTimeout(this.timeout_id_value);
      clearTimeout(this.timeout_id_volume);
      clearInterval(this.interval_id_value);
      clearInterval(this.interval_id_volume);
    }
  },
  mounted: function mounted() {
    this.exchange = this.$route.name;
    global_component_instance = this;
    this.init_available();
  },
  watch: {
    old_to_value: function old_to_value() {
      if (this.post.value.to) {
        this.currency_symbol_value = currency_symbol_map__WEBPACK_IMPORTED_MODULE_2___default()(this.post.value.to.toUpperCase());
      }
    },
    old_to_volume: function old_to_volume() {
      if (this.post.volume.to) {
        this.currency_symbol_volume = currency_symbol_map__WEBPACK_IMPORTED_MODULE_2___default()(this.post.volume.to.toUpperCase());
      }
    }
  },
  beforeDestroy: function beforeDestroy() {
    this.clear_all_timeouts_intervals();
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/ValueChart.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard/ValueChart.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue_datetime__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue-datetime */ "./node_modules/vue-datetime/dist/vue-datetime.js");
/* harmony import */ var vue_datetime__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue_datetime__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _plugins_axios__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../plugins/axios */ "./resources/js/plugins/axios.js");
/* harmony import */ var highcharts__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! highcharts */ "./node_modules/highcharts/highcharts.js");
/* harmony import */ var highcharts__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(highcharts__WEBPACK_IMPORTED_MODULE_2__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    datetime: vue_datetime__WEBPACK_IMPORTED_MODULE_0__["Datetime"]
  },
  data: function data() {
    return {
      post: {},
      chart: null,
      chart_data: null,
      axios: _plugins_axios__WEBPACK_IMPORTED_MODULE_1__["axios"],
      exchange: null,
      old_to: null,
      from_available: null,
      to_available: null,
      all_available: null
    };
  },
  methods: {
    value_chart: function value_chart(init) {
      var _this = this;

      if (init) {
        this.post.start = new Date().setSeconds(0, 0) / 1000 - 86400;
        this.post.end = new Date().setSeconds(0, 0) / 1000;
        this.post.range = '1h';
      } else {
        this.post.start = Date.parse(this.post.start) / 1000;
        this.post.end = Date.parse(this.post.end) / 1000;
      }

      var url_volume = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/" + "value" + "/" + this.post.start + "/" + this.post.end + "/" + this.exchange + "/" + this.post.range + "/" + this.post.from + "/" + this.post.to;

      if (this.chart == null) {
        this.init_chart();
      }

      this.chart.showLoading();
      this.axios.get(url_volume).then(function (response) {
        return _this.update_chart(response.data);
      });
    },
    init_chart: function init_chart() {
      var card = document.getElementById('chart_value_cart');
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
            valueDecimals: 4
          }
        }]
      };
      this.chart = new highcharts__WEBPACK_IMPORTED_MODULE_2___default.a.Chart(options);
    },
    update_chart: function update_chart(data) {
      this.chart_data = data['data'].map(function (item) {
        return [item[0] * 1000, item[1]];
      });

      if (this.chart != null) {
        this.chart.hideLoading();
        this.chart.series[0].setData(this.chart_data);
      }
    },
    finish_init_avail: function finish_init_avail(data) {
      this.all_available = data['data'];
      this.from_available = Object.keys(this.all_available);
      this.to_available = this.all_available[this.from_available[0]];
      this.post.from = this.from_available[0];
      this.post.to = this.to_available[0];
      this.old_to = this.post.to;
      this.value_chart(true);
    },
    init_available: function init_available() {
      var _this2 = this;

      var uri = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/pairs/" + this.exchange;
      this.axios.get(uri).then(function (response) {
        return _this2.finish_init_avail(response.data);
      });
    },
    onChangeFrom: function onChangeFrom() {
      this.to_available = this.all_available[this.post.from];
      this.post.to = this.to_available[0];
    },
    finish_change_to: function finish_change_to(data) {
      var new_fiat = data['data']['fiat'];
      var old_fiat = data['data']['old_fiat'];
      var new_data = this.chart_data.map(function (item) {
        return [item[0], item[1] / old_fiat * new_fiat];
      });
      this.chart.series[0].setData(new_data);
      this.chart_data = new_data;
    },
    onChangeTo: function onChangeTo() {
      var _this3 = this;

      var now = new Date().setSeconds(0, 0) / 1000;
      var url = "http://" + "167.86.75.179" + ":" + "8001" + "/api/fiat/historical/" + now + "/" + this.post.to + "/" + this.old_to;
      this.old_to = this.post.to;
      this.axios.get(url).then(function (response) {
        return _this3.finish_change_to(response.data);
      });
    }
  },
  mounted: function mounted() {
    this.exchange = this.$route.name;
    this.init_available();
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Exchange_dashboard.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Exchange_dashboard.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Dashboard_OhlcChart__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Dashboard/OhlcChart */ "./resources/js/pages/Dashboard/OhlcChart.vue");
/* harmony import */ var _Dashboard_ValueChart__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Dashboard/ValueChart */ "./resources/js/pages/Dashboard/ValueChart.vue");
/* harmony import */ var _Dashboard_RealtimeCharts__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Dashboard/RealtimeCharts */ "./resources/js/pages/Dashboard/RealtimeCharts.vue");
/* harmony import */ var _Dashboard_QuickStats__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./Dashboard/QuickStats */ "./resources/js/pages/Dashboard/QuickStats.vue");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//




/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    ohlc_chart: _Dashboard_OhlcChart__WEBPACK_IMPORTED_MODULE_0__["default"],
    value_chart: _Dashboard_ValueChart__WEBPACK_IMPORTED_MODULE_1__["default"],
    realtime_charts: _Dashboard_RealtimeCharts__WEBPACK_IMPORTED_MODULE_2__["default"],
    quick_stats: _Dashboard_QuickStats__WEBPACK_IMPORTED_MODULE_3__["default"]
  },
  data: function data() {
    return {
      render_realtime: true
    };
  },
  methods: {
    realtime_click: function realtime_click() {
      this.render_realtime = true;
    },
    quick_stats_click: function quick_stats_click() {
      this.render_realtime = false;
    }
  },
  mounted: function mounted() {}
});

/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/QuickStats.vue?vue&type=style&index=0&lang=css&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard/QuickStats.vue?vue&type=style&index=0&lang=css& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.quick_stats_margin{\n    margin-top: 30px;\n}\n.card_quick_stats {\n    background: #1e1e2e;\n    box-shadow: none;\n}\n.chart-area_quick_stats {\n    height: 100% !important;\n    width: 100% !important;\n}\n.allign_links {\n    text-align: center;\n    margin-left: auto;\n    margin-right: auto;\n    display: block;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=style&index=0&lang=css&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=style&index=0&lang=css& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.realtime_margin{\n    margin-top: 30px;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Exchange_dashboard.vue?vue&type=style&index=0&lang=css&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Exchange_dashboard.vue?vue&type=style&index=0&lang=css& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.button_row {\n    display: -webkit-box;\n    display: flex;\n    flex-wrap: wrap;\n    margin-left: -15px;\n    -webkit-box-pack: center;\n    justify-content: right;\n    margin-right: 10px;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/QuickStats.vue?vue&type=style&index=0&lang=css&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard/QuickStats.vue?vue&type=style&index=0&lang=css& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../node_modules/css-loader??ref--6-1!../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../node_modules/postcss-loader/src??ref--6-2!../../../../node_modules/vue-loader/lib??vue-loader-options!./QuickStats.vue?vue&type=style&index=0&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/QuickStats.vue?vue&type=style&index=0&lang=css&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=style&index=0&lang=css&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=style&index=0&lang=css& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../node_modules/css-loader??ref--6-1!../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../node_modules/postcss-loader/src??ref--6-2!../../../../node_modules/vue-loader/lib??vue-loader-options!./RealtimeCharts.vue?vue&type=style&index=0&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=style&index=0&lang=css&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Exchange_dashboard.vue?vue&type=style&index=0&lang=css&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Exchange_dashboard.vue?vue&type=style&index=0&lang=css& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../node_modules/css-loader??ref--6-1!../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../node_modules/postcss-loader/src??ref--6-2!../../../node_modules/vue-loader/lib??vue-loader-options!./Exchange_dashboard.vue?vue&type=style&index=0&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Exchange_dashboard.vue?vue&type=style&index=0&lang=css&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/OhlcChart.vue?vue&type=template&id=f9f8914c&":
/*!*****************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard/OhlcChart.vue?vue&type=template&id=f9f8914c& ***!
  \*****************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("div", { staticClass: "row" }, [
      _c(
        "div",
        {
          staticClass: "col-sm",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c("div", { staticClass: "form-group" }, [
            _c("label", { staticClass: "label" }, [_vm._v("From:")]),
            _vm._v(" "),
            _c(
              "select",
              {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.post.from,
                    expression: "post.from"
                  }
                ],
                staticClass: "select-css",
                on: {
                  change: [
                    function($event) {
                      var $$selectedVal = Array.prototype.filter
                        .call($event.target.options, function(o) {
                          return o.selected
                        })
                        .map(function(o) {
                          var val = "_value" in o ? o._value : o.value
                          return val
                        })
                      _vm.$set(
                        _vm.post,
                        "from",
                        $event.target.multiple
                          ? $$selectedVal
                          : $$selectedVal[0]
                      )
                    },
                    function($event) {
                      return _vm.onChangeFrom()
                    }
                  ]
                }
              },
              _vm._l(_vm.from_available, function(item) {
                return _c("option", { domProps: { value: item } }, [
                  _vm._v(_vm._s(item.toUpperCase()))
                ])
              }),
              0
            )
          ])
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "col-sm",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c("div", { staticClass: "form-group" }, [
            _c("label", { staticClass: "label" }, [_vm._v("To:")]),
            _vm._v(" "),
            _c(
              "select",
              {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.post.to,
                    expression: "post.to"
                  }
                ],
                staticClass: "select-css",
                on: {
                  change: [
                    function($event) {
                      var $$selectedVal = Array.prototype.filter
                        .call($event.target.options, function(o) {
                          return o.selected
                        })
                        .map(function(o) {
                          var val = "_value" in o ? o._value : o.value
                          return val
                        })
                      _vm.$set(
                        _vm.post,
                        "to",
                        $event.target.multiple
                          ? $$selectedVal
                          : $$selectedVal[0]
                      )
                    },
                    function($event) {
                      return _vm.onChangeTo()
                    }
                  ]
                }
              },
              _vm._l(_vm.to_available, function(item) {
                return _c("option", { domProps: { value: item } }, [
                  _vm._v(_vm._s(item.toUpperCase()))
                ])
              }),
              0
            )
          ])
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "col-sm",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c("div", { staticClass: "form-group" }, [
            _c("label", { staticClass: "label" }, [_vm._v("Range:")]),
            _vm._v(" "),
            _c(
              "select",
              {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.post.range,
                    expression: "post.range"
                  }
                ],
                staticClass: "select-css",
                on: {
                  change: function($event) {
                    var $$selectedVal = Array.prototype.filter
                      .call($event.target.options, function(o) {
                        return o.selected
                      })
                      .map(function(o) {
                        var val = "_value" in o ? o._value : o.value
                        return val
                      })
                    _vm.$set(
                      _vm.post,
                      "range",
                      $event.target.multiple ? $$selectedVal : $$selectedVal[0]
                    )
                  }
                }
              },
              [
                _c("option", [_vm._v("1d")]),
                _vm._v(" "),
                _c("option", [_vm._v("12h")]),
                _vm._v(" "),
                _c("option", [_vm._v("6h")]),
                _vm._v(" "),
                _c("option", [_vm._v("3h")]),
                _vm._v(" "),
                _c("option", [_vm._v("1h")]),
                _vm._v(" "),
                _c("option", [_vm._v("5m")]),
                _vm._v(" "),
                _c("option", [_vm._v("1m")])
              ]
            )
          ])
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "col-sm",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c(
            "div",
            { staticClass: "form-group" },
            [
              _c("label", { staticClass: "label" }, [_vm._v("Start:")]),
              _vm._v(" "),
              _c("datetime", {
                attrs: { type: "datetime" },
                model: {
                  value: _vm.post.start,
                  callback: function($$v) {
                    _vm.$set(_vm.post, "start", $$v)
                  },
                  expression: "post.start"
                }
              })
            ],
            1
          )
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "col-sm",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c(
            "div",
            { staticClass: "form-group" },
            [
              _c("label", { staticClass: "label" }, [_vm._v("End:")]),
              _vm._v(" "),
              _c("datetime", {
                attrs: { type: "datetime" },
                model: {
                  value: _vm.post.end,
                  callback: function($$v) {
                    _vm.$set(_vm.post, "end", $$v)
                  },
                  expression: "post.end"
                }
              })
            ],
            1
          )
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "col-sm",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c(
            "form",
            {
              on: {
                submit: function($event) {
                  $event.preventDefault()
                  return _vm.ohlc_chart(false)
                }
              }
            },
            [_vm._m(0)]
          )
        ]
      )
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "row" }, [
      _c(
        "div",
        { staticClass: "col-12" },
        [
          _c("card", { attrs: { type: "chart" } }, [
            _c(
              "div",
              { staticClass: "chart-area", attrs: { id: "chart_ohlc_cart" } },
              [
                _c("div", { attrs: { id: "chart_view_ohlc" } }, [
                  _c("div", { ref: "chart_ohlc", staticClass: "chart" })
                ])
              ]
            )
          ])
        ],
        1
      )
    ])
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "form-group" }, [
      _c("button", { staticClass: "btn btn-primary" }, [_vm._v("Query OHLC")])
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/QuickStats.vue?vue&type=template&id=42947894&":
/*!******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard/QuickStats.vue?vue&type=template&id=42947894& ***!
  \******************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("div", { staticClass: "row quick_stats_margin" }, [
      _c("div", { staticClass: "col-sm" }, [
        _c(
          "p",
          { staticClass: "title", staticStyle: { "text-align": "center" } },
          [_vm._v("Year established")]
        ),
        _c("br"),
        _c("p", { staticStyle: { "text-align": "center" } }, [
          _vm._v(_vm._s(_vm.year_established))
        ])
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "col-sm" }, [
        _c(
          "p",
          { staticClass: "title", staticStyle: { "text-align": "center" } },
          [_vm._v("Country")]
        ),
        _c("br"),
        _c("p", { staticStyle: { "text-align": "center" } }, [
          _vm._v(_vm._s(_vm.country))
        ])
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "col-sm" }, [
        _c(
          "p",
          { staticClass: "title", staticStyle: { "text-align": "center" } },
          [_vm._v("Centralized")]
        ),
        _c("br"),
        _c("p", { staticStyle: { "text-align": "center" } }, [
          _vm._v(_vm._s(_vm.centralized))
        ])
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "col-sm" }, [
        _c(
          "p",
          { staticClass: "title", staticStyle: { "text-align": "center" } },
          [_vm._v("Accepted Payment Methods")]
        ),
        _c("br"),
        _c("p", { staticStyle: { "text-align": "center" } }, [
          _vm._v(_vm._s(_vm.payment_methods))
        ])
      ])
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "row quick_stats_margin" }, [
      _vm.url != "None"
        ? _c("div", { staticClass: "col-sm" }, [
            _c(
              "p",
              { staticClass: "title", staticStyle: { "text-align": "center" } },
              [_vm._v("Url")]
            ),
            _c("br"),
            _c("a", { staticClass: "allign_links", attrs: { href: _vm.url } }, [
              _vm._v(_vm._s(_vm.url))
            ])
          ])
        : _c("div", { staticClass: "col-sm" }, [
            _c(
              "p",
              { staticClass: "title", staticStyle: { "text-align": "center" } },
              [_vm._v("Url")]
            ),
            _c("br"),
            _c("p", { staticStyle: { "text-align": "center" } }, [
              _vm._v(_vm._s(_vm.url))
            ])
          ]),
      _vm._v(" "),
      _vm.facebook != "None"
        ? _c("div", { staticClass: "col-sm" }, [
            _c(
              "p",
              { staticClass: "title", staticStyle: { "text-align": "center" } },
              [_vm._v("Facebook")]
            ),
            _c("br"),
            _c(
              "a",
              { staticClass: "allign_links", attrs: { href: _vm.facebook } },
              [_vm._v(_vm._s(_vm.facebook))]
            )
          ])
        : _c("div", { staticClass: "col-sm" }, [
            _c(
              "p",
              { staticClass: "title", staticStyle: { "text-align": "center" } },
              [_vm._v("Facebook")]
            ),
            _c("br"),
            _c("p", { staticStyle: { "text-align": "center" } }, [
              _vm._v(_vm._s(_vm.facebook))
            ])
          ]),
      _vm._v(" "),
      _vm.reddit != "None"
        ? _c("div", { staticClass: "col-sm" }, [
            _c(
              "p",
              { staticClass: "title", staticStyle: { "text-align": "center" } },
              [_vm._v("Redit")]
            ),
            _c("br"),
            _c(
              "a",
              { staticClass: "allign_links", attrs: { href: _vm.reddit } },
              [_vm._v(_vm._s(_vm.reddit))]
            )
          ])
        : _c("div", { staticClass: "col-sm" }, [
            _c(
              "p",
              { staticClass: "title", staticStyle: { "text-align": "center" } },
              [_vm._v("Redit")]
            ),
            _c("br"),
            _c("p", { staticStyle: { "text-align": "center" } }, [
              _vm._v(_vm._s(_vm.reddit))
            ])
          ]),
      _vm._v(" "),
      _c("div", { staticClass: "col-sm" }, [
        _c(
          "p",
          { staticClass: "title", staticStyle: { "text-align": "center" } },
          [_vm._v("Twitter handle")]
        ),
        _c("br"),
        _c("p", { staticStyle: { "text-align": "center" } }, [
          _vm._v(_vm._s(_vm.twitter))
        ])
      ])
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "row" }, [
      _c(
        "div",
        { staticClass: "col-sm" },
        [
          _c(
            "card",
            { staticClass: "card_quick_stats", attrs: { type: "chart" } },
            [
              _c(
                "div",
                {
                  staticClass: "chart-area chart-area_quick_stats",
                  attrs: { id: "volume_by_currency_card" }
                },
                [
                  _c("div", { attrs: { id: "volume_by_currency" } }, [
                    _c("div", {
                      ref: "chart_volume_by_currency",
                      staticClass: "chart"
                    })
                  ])
                ]
              )
            ]
          )
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "col-sm" },
        [
          _c(
            "card",
            { staticClass: "card_quick_stats", attrs: { type: "chart" } },
            [
              _c(
                "div",
                {
                  staticClass: "chart-area chart-area_quick_stats",
                  attrs: { id: "volume_by_pair_card" }
                },
                [
                  _c("div", { attrs: { id: "volume_by_pair" } }, [
                    _c("div", {
                      ref: "chart_volume_by_pair",
                      staticClass: "chart"
                    })
                  ])
                ]
              )
            ]
          )
        ],
        1
      )
    ])
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=template&id=626d6da4&":
/*!**********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=template&id=626d6da4& ***!
  \**********************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", { staticClass: "row realtime_margin" }, [
    _c(
      "div",
      { staticClass: "col-lg-4" },
      [
        _c("div", { staticClass: "row" }, [
          _c("div", { staticClass: "col-lg-4" }, [
            _c("div", { staticClass: "form-group" }, [
              _c("label", { staticClass: "label" }, [_vm._v("From:")]),
              _c("br"),
              _vm._v(" "),
              _c(
                "select",
                {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.post.value.from,
                      expression: "post.value.from"
                    }
                  ],
                  staticClass: "select-css",
                  on: {
                    change: [
                      function($event) {
                        var $$selectedVal = Array.prototype.filter
                          .call($event.target.options, function(o) {
                            return o.selected
                          })
                          .map(function(o) {
                            var val = "_value" in o ? o._value : o.value
                            return val
                          })
                        _vm.$set(
                          _vm.post.value,
                          "from",
                          $event.target.multiple
                            ? $$selectedVal
                            : $$selectedVal[0]
                        )
                      },
                      function($event) {
                        return _vm.onChangeFromValue()
                      }
                    ]
                  }
                },
                _vm._l(_vm.from_value_available, function(item) {
                  return _c("option", { domProps: { value: item } }, [
                    _vm._v(_vm._s(item.toUpperCase()))
                  ])
                }),
                0
              )
            ])
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "col-lg-4" }, [
            _c("div", { staticClass: "form-group" }, [
              _c("label", { staticClass: "label" }, [_vm._v("To:")]),
              _c("br"),
              _vm._v(" "),
              _c(
                "select",
                {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.post.value.to,
                      expression: "post.value.to"
                    }
                  ],
                  staticClass: "select-css",
                  on: {
                    change: [
                      function($event) {
                        var $$selectedVal = Array.prototype.filter
                          .call($event.target.options, function(o) {
                            return o.selected
                          })
                          .map(function(o) {
                            var val = "_value" in o ? o._value : o.value
                            return val
                          })
                        _vm.$set(
                          _vm.post.value,
                          "to",
                          $event.target.multiple
                            ? $$selectedVal
                            : $$selectedVal[0]
                        )
                      },
                      function($event) {
                        return _vm.onChangeToValue()
                      }
                    ]
                  }
                },
                _vm._l(_vm.to_value_available, function(item) {
                  return _c("option", { domProps: { value: item } }, [
                    _vm._v(_vm._s(item.toUpperCase()))
                  ])
                }),
                0
              )
            ])
          ])
        ]),
        _vm._v(" "),
        _c(
          "card",
          { attrs: { type: "chart" } },
          [
            _c("template", { slot: "header" }, [
              _c("div", { staticClass: "row" }, [
                _c("h5", { staticClass: "card-category" }, [
                  _vm._v("Realtime Value")
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "row" }, [
                _c("h3", { staticClass: "card-title" }, [
                  _vm._v(
                    "\n                        " +
                      _vm._s(_vm.currency_symbol_value) +
                      " " +
                      _vm._s(_vm.last_realtime_value) +
                      "\n                    "
                  )
                ])
              ])
            ]),
            _vm._v(" "),
            _c(
              "div",
              {
                staticClass: "chart-area",
                attrs: { id: "realtime_value_cart" }
              },
              [
                _c("div", { attrs: { id: "realtime" } }, [
                  _c("div", { ref: "chart_realtime", staticClass: "chart" })
                ])
              ]
            )
          ],
          2
        )
      ],
      1
    ),
    _vm._v(" "),
    _c(
      "div",
      { staticClass: "col-lg-4" },
      [
        _c("div", { staticClass: "row" }, [
          _c("div", { staticClass: "col-lg-4" }, [
            _c("div", { staticClass: "form-group" }, [
              _c("label", { staticClass: "label" }, [_vm._v("From:")]),
              _c("br"),
              _vm._v(" "),
              _c(
                "select",
                {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.post.volume.from,
                      expression: "post.volume.from"
                    }
                  ],
                  staticClass: "select-css",
                  on: {
                    change: [
                      function($event) {
                        var $$selectedVal = Array.prototype.filter
                          .call($event.target.options, function(o) {
                            return o.selected
                          })
                          .map(function(o) {
                            var val = "_value" in o ? o._value : o.value
                            return val
                          })
                        _vm.$set(
                          _vm.post.volume,
                          "from",
                          $event.target.multiple
                            ? $$selectedVal
                            : $$selectedVal[0]
                        )
                      },
                      function($event) {
                        return _vm.onChangeFromVolume()
                      }
                    ]
                  }
                },
                _vm._l(_vm.from_volume_available, function(item) {
                  return _c("option", { domProps: { value: item } }, [
                    _vm._v(_vm._s(item.toUpperCase()))
                  ])
                }),
                0
              )
            ])
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "col-lg-4" }, [
            _c("div", { staticClass: "form-group" }, [
              _c("label", { staticClass: "label" }, [_vm._v("To:")]),
              _c("br"),
              _vm._v(" "),
              _c(
                "select",
                {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.post.volume.to,
                      expression: "post.volume.to"
                    }
                  ],
                  staticClass: "select-css",
                  on: {
                    change: [
                      function($event) {
                        var $$selectedVal = Array.prototype.filter
                          .call($event.target.options, function(o) {
                            return o.selected
                          })
                          .map(function(o) {
                            var val = "_value" in o ? o._value : o.value
                            return val
                          })
                        _vm.$set(
                          _vm.post.volume,
                          "to",
                          $event.target.multiple
                            ? $$selectedVal
                            : $$selectedVal[0]
                        )
                      },
                      function($event) {
                        return _vm.onChangeToVolume()
                      }
                    ]
                  }
                },
                _vm._l(_vm.to_volume_available, function(item) {
                  return _c("option", { domProps: { value: item } }, [
                    _vm._v(_vm._s(item.toUpperCase()))
                  ])
                }),
                0
              )
            ])
          ])
        ]),
        _vm._v(" "),
        _c(
          "card",
          { attrs: { type: "chart" } },
          [
            _c("template", { slot: "header" }, [
              _c("div", { staticClass: "row" }, [
                _c("h5", { staticClass: "card-category" }, [
                  _vm._v("Realtime Volume")
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "row" }, [
                _c("h3", { staticClass: "card-title" }, [
                  _vm._v(
                    "\n                        " +
                      _vm._s(_vm.currency_symbol_volume) +
                      " " +
                      _vm._s(_vm.last_realtime_volume) +
                      "\n                    "
                  )
                ])
              ])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "chart-area" }, [
              _c("div", { attrs: { id: "volume" } }, [
                _c("div", { ref: "chart_volume", staticClass: "chart" })
              ])
            ])
          ],
          2
        )
      ],
      1
    )
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/ValueChart.vue?vue&type=template&id=550e6871&":
/*!******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard/ValueChart.vue?vue&type=template&id=550e6871& ***!
  \******************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("div", [
    _c("div", { staticClass: "row" }, [
      _c(
        "div",
        {
          staticClass: "col-sm",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c("div", { staticClass: "form-group" }, [
            _c("label", { staticClass: "label" }, [_vm._v("From:")]),
            _vm._v(" "),
            _c(
              "select",
              {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.post.from,
                    expression: "post.from"
                  }
                ],
                staticClass: "select-css",
                on: {
                  change: [
                    function($event) {
                      var $$selectedVal = Array.prototype.filter
                        .call($event.target.options, function(o) {
                          return o.selected
                        })
                        .map(function(o) {
                          var val = "_value" in o ? o._value : o.value
                          return val
                        })
                      _vm.$set(
                        _vm.post,
                        "from",
                        $event.target.multiple
                          ? $$selectedVal
                          : $$selectedVal[0]
                      )
                    },
                    function($event) {
                      return _vm.onChangeFrom()
                    }
                  ]
                }
              },
              _vm._l(_vm.from_available, function(item) {
                return _c("option", { domProps: { value: item } }, [
                  _vm._v(_vm._s(item.toUpperCase()))
                ])
              }),
              0
            )
          ])
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "col-sm",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c("div", { staticClass: "form-group" }, [
            _c("label", { staticClass: "label" }, [_vm._v("To:")]),
            _vm._v(" "),
            _c(
              "select",
              {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.post.to,
                    expression: "post.to"
                  }
                ],
                staticClass: "select-css",
                on: {
                  change: [
                    function($event) {
                      var $$selectedVal = Array.prototype.filter
                        .call($event.target.options, function(o) {
                          return o.selected
                        })
                        .map(function(o) {
                          var val = "_value" in o ? o._value : o.value
                          return val
                        })
                      _vm.$set(
                        _vm.post,
                        "to",
                        $event.target.multiple
                          ? $$selectedVal
                          : $$selectedVal[0]
                      )
                    },
                    function($event) {
                      return _vm.onChangeTo()
                    }
                  ]
                }
              },
              _vm._l(_vm.to_available, function(item) {
                return _c("option", { domProps: { value: item } }, [
                  _vm._v(_vm._s(item.toUpperCase()))
                ])
              }),
              0
            )
          ])
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "col-sm",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c("div", { staticClass: "form-group" }, [
            _c("label", { staticClass: "label" }, [_vm._v("Range:")]),
            _vm._v(" "),
            _c(
              "select",
              {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.post.range,
                    expression: "post.range"
                  }
                ],
                staticClass: "select-css",
                on: {
                  change: function($event) {
                    var $$selectedVal = Array.prototype.filter
                      .call($event.target.options, function(o) {
                        return o.selected
                      })
                      .map(function(o) {
                        var val = "_value" in o ? o._value : o.value
                        return val
                      })
                    _vm.$set(
                      _vm.post,
                      "range",
                      $event.target.multiple ? $$selectedVal : $$selectedVal[0]
                    )
                  }
                }
              },
              [
                _c("option", [_vm._v("1d")]),
                _vm._v(" "),
                _c("option", [_vm._v("12h")]),
                _vm._v(" "),
                _c("option", [_vm._v("6h")]),
                _vm._v(" "),
                _c("option", [_vm._v("3h")]),
                _vm._v(" "),
                _c("option", [_vm._v("1h")]),
                _vm._v(" "),
                _c("option", [_vm._v("5m")]),
                _vm._v(" "),
                _c("option", [_vm._v("1m")])
              ]
            )
          ])
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "col-sm",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c(
            "div",
            { staticClass: "form-group" },
            [
              _c("label", { staticClass: "label" }, [_vm._v("Start:")]),
              _vm._v(" "),
              _c("datetime", {
                attrs: { type: "datetime" },
                model: {
                  value: _vm.post.start,
                  callback: function($$v) {
                    _vm.$set(_vm.post, "start", $$v)
                  },
                  expression: "post.start"
                }
              })
            ],
            1
          )
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "col-sm",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c(
            "div",
            { staticClass: "form-group" },
            [
              _c("label", { staticClass: "label" }, [_vm._v("End:")]),
              _vm._v(" "),
              _c("datetime", {
                attrs: { type: "datetime" },
                model: {
                  value: _vm.post.end,
                  callback: function($$v) {
                    _vm.$set(_vm.post, "end", $$v)
                  },
                  expression: "post.end"
                }
              })
            ],
            1
          )
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "col-sm",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c(
            "form",
            {
              on: {
                submit: function($event) {
                  $event.preventDefault()
                  return _vm.value_chart(false)
                }
              }
            },
            [_vm._m(0)]
          )
        ]
      )
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "row" }, [
      _c(
        "div",
        { staticClass: "col-12" },
        [
          _c("card", { attrs: { type: "chart" } }, [
            _c(
              "div",
              { staticClass: "chart-area", attrs: { id: "chart_value_cart" } },
              [
                _c("div", { attrs: { id: "chart_view_value" } }, [
                  _c("div", { ref: "chart_value", staticClass: "chart" })
                ])
              ]
            )
          ])
        ],
        1
      )
    ])
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "form-group" }, [
      _c("button", { staticClass: "btn btn-primary" }, [_vm._v("Query Value")])
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Exchange_dashboard.vue?vue&type=template&id=724978c1&":
/*!****************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Exchange_dashboard.vue?vue&type=template&id=724978c1& ***!
  \****************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("div", { staticClass: "button_row" }),
      _vm._v(" "),
      _c("realtime_charts"),
      _vm._v(" "),
      _c("ohlc_chart"),
      _vm._v(" "),
      _c("value_chart")
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/pages/Dashboard/OhlcChart.vue":
/*!****************************************************!*\
  !*** ./resources/js/pages/Dashboard/OhlcChart.vue ***!
  \****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _OhlcChart_vue_vue_type_template_id_f9f8914c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./OhlcChart.vue?vue&type=template&id=f9f8914c& */ "./resources/js/pages/Dashboard/OhlcChart.vue?vue&type=template&id=f9f8914c&");
/* harmony import */ var _OhlcChart_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./OhlcChart.vue?vue&type=script&lang=js& */ "./resources/js/pages/Dashboard/OhlcChart.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _OhlcChart_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _OhlcChart_vue_vue_type_template_id_f9f8914c___WEBPACK_IMPORTED_MODULE_0__["render"],
  _OhlcChart_vue_vue_type_template_id_f9f8914c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/pages/Dashboard/OhlcChart.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/pages/Dashboard/OhlcChart.vue?vue&type=script&lang=js&":
/*!*****************************************************************************!*\
  !*** ./resources/js/pages/Dashboard/OhlcChart.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_OhlcChart_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./OhlcChart.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/OhlcChart.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_OhlcChart_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/pages/Dashboard/OhlcChart.vue?vue&type=template&id=f9f8914c&":
/*!***********************************************************************************!*\
  !*** ./resources/js/pages/Dashboard/OhlcChart.vue?vue&type=template&id=f9f8914c& ***!
  \***********************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_OhlcChart_vue_vue_type_template_id_f9f8914c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./OhlcChart.vue?vue&type=template&id=f9f8914c& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/OhlcChart.vue?vue&type=template&id=f9f8914c&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_OhlcChart_vue_vue_type_template_id_f9f8914c___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_OhlcChart_vue_vue_type_template_id_f9f8914c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/pages/Dashboard/QuickStats.vue":
/*!*****************************************************!*\
  !*** ./resources/js/pages/Dashboard/QuickStats.vue ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _QuickStats_vue_vue_type_template_id_42947894___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./QuickStats.vue?vue&type=template&id=42947894& */ "./resources/js/pages/Dashboard/QuickStats.vue?vue&type=template&id=42947894&");
/* harmony import */ var _QuickStats_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./QuickStats.vue?vue&type=script&lang=js& */ "./resources/js/pages/Dashboard/QuickStats.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _QuickStats_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./QuickStats.vue?vue&type=style&index=0&lang=css& */ "./resources/js/pages/Dashboard/QuickStats.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _QuickStats_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _QuickStats_vue_vue_type_template_id_42947894___WEBPACK_IMPORTED_MODULE_0__["render"],
  _QuickStats_vue_vue_type_template_id_42947894___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/pages/Dashboard/QuickStats.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/pages/Dashboard/QuickStats.vue?vue&type=script&lang=js&":
/*!******************************************************************************!*\
  !*** ./resources/js/pages/Dashboard/QuickStats.vue?vue&type=script&lang=js& ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./QuickStats.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/QuickStats.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/pages/Dashboard/QuickStats.vue?vue&type=style&index=0&lang=css&":
/*!**************************************************************************************!*\
  !*** ./resources/js/pages/Dashboard/QuickStats.vue?vue&type=style&index=0&lang=css& ***!
  \**************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/style-loader!../../../../node_modules/css-loader??ref--6-1!../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../node_modules/postcss-loader/src??ref--6-2!../../../../node_modules/vue-loader/lib??vue-loader-options!./QuickStats.vue?vue&type=style&index=0&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/QuickStats.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/pages/Dashboard/QuickStats.vue?vue&type=template&id=42947894&":
/*!************************************************************************************!*\
  !*** ./resources/js/pages/Dashboard/QuickStats.vue?vue&type=template&id=42947894& ***!
  \************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_template_id_42947894___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./QuickStats.vue?vue&type=template&id=42947894& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/QuickStats.vue?vue&type=template&id=42947894&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_template_id_42947894___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_template_id_42947894___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/pages/Dashboard/RealtimeCharts.vue":
/*!*********************************************************!*\
  !*** ./resources/js/pages/Dashboard/RealtimeCharts.vue ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _RealtimeCharts_vue_vue_type_template_id_626d6da4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./RealtimeCharts.vue?vue&type=template&id=626d6da4& */ "./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=template&id=626d6da4&");
/* harmony import */ var _RealtimeCharts_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./RealtimeCharts.vue?vue&type=script&lang=js& */ "./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _RealtimeCharts_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./RealtimeCharts.vue?vue&type=style&index=0&lang=css& */ "./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _RealtimeCharts_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _RealtimeCharts_vue_vue_type_template_id_626d6da4___WEBPACK_IMPORTED_MODULE_0__["render"],
  _RealtimeCharts_vue_vue_type_template_id_626d6da4___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/pages/Dashboard/RealtimeCharts.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=script&lang=js&":
/*!**********************************************************************************!*\
  !*** ./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RealtimeCharts_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./RealtimeCharts.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RealtimeCharts_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=style&index=0&lang=css&":
/*!******************************************************************************************!*\
  !*** ./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=style&index=0&lang=css& ***!
  \******************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_RealtimeCharts_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/style-loader!../../../../node_modules/css-loader??ref--6-1!../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../node_modules/postcss-loader/src??ref--6-2!../../../../node_modules/vue-loader/lib??vue-loader-options!./RealtimeCharts.vue?vue&type=style&index=0&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_RealtimeCharts_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_RealtimeCharts_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_RealtimeCharts_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_RealtimeCharts_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_RealtimeCharts_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=template&id=626d6da4&":
/*!****************************************************************************************!*\
  !*** ./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=template&id=626d6da4& ***!
  \****************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RealtimeCharts_vue_vue_type_template_id_626d6da4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./RealtimeCharts.vue?vue&type=template&id=626d6da4& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/RealtimeCharts.vue?vue&type=template&id=626d6da4&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RealtimeCharts_vue_vue_type_template_id_626d6da4___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_RealtimeCharts_vue_vue_type_template_id_626d6da4___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/pages/Dashboard/ValueChart.vue":
/*!*****************************************************!*\
  !*** ./resources/js/pages/Dashboard/ValueChart.vue ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ValueChart_vue_vue_type_template_id_550e6871___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ValueChart.vue?vue&type=template&id=550e6871& */ "./resources/js/pages/Dashboard/ValueChart.vue?vue&type=template&id=550e6871&");
/* harmony import */ var _ValueChart_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ValueChart.vue?vue&type=script&lang=js& */ "./resources/js/pages/Dashboard/ValueChart.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _ValueChart_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _ValueChart_vue_vue_type_template_id_550e6871___WEBPACK_IMPORTED_MODULE_0__["render"],
  _ValueChart_vue_vue_type_template_id_550e6871___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/pages/Dashboard/ValueChart.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/pages/Dashboard/ValueChart.vue?vue&type=script&lang=js&":
/*!******************************************************************************!*\
  !*** ./resources/js/pages/Dashboard/ValueChart.vue?vue&type=script&lang=js& ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ValueChart_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./ValueChart.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/ValueChart.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ValueChart_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/pages/Dashboard/ValueChart.vue?vue&type=template&id=550e6871&":
/*!************************************************************************************!*\
  !*** ./resources/js/pages/Dashboard/ValueChart.vue?vue&type=template&id=550e6871& ***!
  \************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ValueChart_vue_vue_type_template_id_550e6871___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./ValueChart.vue?vue&type=template&id=550e6871& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard/ValueChart.vue?vue&type=template&id=550e6871&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ValueChart_vue_vue_type_template_id_550e6871___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ValueChart_vue_vue_type_template_id_550e6871___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/pages/Exchange_dashboard.vue":
/*!***************************************************!*\
  !*** ./resources/js/pages/Exchange_dashboard.vue ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Exchange_dashboard_vue_vue_type_template_id_724978c1___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Exchange_dashboard.vue?vue&type=template&id=724978c1& */ "./resources/js/pages/Exchange_dashboard.vue?vue&type=template&id=724978c1&");
/* harmony import */ var _Exchange_dashboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Exchange_dashboard.vue?vue&type=script&lang=js& */ "./resources/js/pages/Exchange_dashboard.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _Exchange_dashboard_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Exchange_dashboard.vue?vue&type=style&index=0&lang=css& */ "./resources/js/pages/Exchange_dashboard.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _Exchange_dashboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Exchange_dashboard_vue_vue_type_template_id_724978c1___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Exchange_dashboard_vue_vue_type_template_id_724978c1___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/pages/Exchange_dashboard.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/pages/Exchange_dashboard.vue?vue&type=script&lang=js&":
/*!****************************************************************************!*\
  !*** ./resources/js/pages/Exchange_dashboard.vue?vue&type=script&lang=js& ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Exchange_dashboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib??ref--4-0!../../../node_modules/vue-loader/lib??vue-loader-options!./Exchange_dashboard.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Exchange_dashboard.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Exchange_dashboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/pages/Exchange_dashboard.vue?vue&type=style&index=0&lang=css&":
/*!************************************************************************************!*\
  !*** ./resources/js/pages/Exchange_dashboard.vue?vue&type=style&index=0&lang=css& ***!
  \************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Exchange_dashboard_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/style-loader!../../../node_modules/css-loader??ref--6-1!../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../node_modules/postcss-loader/src??ref--6-2!../../../node_modules/vue-loader/lib??vue-loader-options!./Exchange_dashboard.vue?vue&type=style&index=0&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Exchange_dashboard.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Exchange_dashboard_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Exchange_dashboard_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Exchange_dashboard_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Exchange_dashboard_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Exchange_dashboard_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/pages/Exchange_dashboard.vue?vue&type=template&id=724978c1&":
/*!**********************************************************************************!*\
  !*** ./resources/js/pages/Exchange_dashboard.vue?vue&type=template&id=724978c1& ***!
  \**********************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Exchange_dashboard_vue_vue_type_template_id_724978c1___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib??vue-loader-options!./Exchange_dashboard.vue?vue&type=template&id=724978c1& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Exchange_dashboard.vue?vue&type=template&id=724978c1&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Exchange_dashboard_vue_vue_type_template_id_724978c1___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Exchange_dashboard_vue_vue_type_template_id_724978c1___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);