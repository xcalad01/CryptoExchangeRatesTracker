(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["dashboard"],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/vue-black-dashboard-master/src/pages/Dashboard.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/vue-black-dashboard-master/src/pages/Dashboard.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _plugins_axios__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../plugins/axios */ "./resources/vue-black-dashboard-master/src/plugins/axios.js");
/* harmony import */ var vue_datetime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-datetime */ "./node_modules/vue-datetime/dist/vue-datetime.js");
/* harmony import */ var vue_datetime__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(vue_datetime__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var highcharts__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! highcharts */ "./node_modules/highcharts/highcharts.js");
/* harmony import */ var highcharts__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(highcharts__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var highcharts_highstock__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! highcharts/highstock */ "./node_modules/highcharts/highstock.js");
/* harmony import */ var highcharts_highstock__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(highcharts_highstock__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var currency_symbol_map__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! currency-symbol-map */ "./node_modules/currency-symbol-map/currency-symbol-map.js");
/* harmony import */ var currency_symbol_map__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(currency_symbol_map__WEBPACK_IMPORTED_MODULE_4__);
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
//
//
//
//
//
//
//
//
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
      ohlc_chart: null,
      value_chart: null,
      realtime_chart: null,
      volume_chart: null,
      post: {},
      axios: _plugins_axios__WEBPACK_IMPORTED_MODULE_0__["axios"],
      hist_available: [],
      from_available: null,
      to_available: null,
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
      old_to: null,
      exchange: null,
      start_date: null,
      end_date: null,
      currency_symbol: null
    };
  },
  methods: {
    ohlc_value_chart: function ohlc_value_chart(init) {
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

      if (this.ohlc_chart == null) {
        this.init_ohlc_chart();
      }

      this.ohlc_chart.showLoading();
      this.axios.get(url_value).then(function (response) {
        return _this.update_ohlc_chart(response.data);
      });
      var url_volume = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/" + "value" + "/" + this.post.start + "/" + this.post.end + "/" + this.exchange + "/" + this.post.range + "/" + this.post.from + "/" + this.post.to;

      if (this.value_chart == null) {
        this.init_value_chart();
      }

      this.value_chart.showLoading();
      this.axios.get(url_volume).then(function (response) {
        return _this.create_update_value_chart(response.data);
      });
    },
    init_ohlc_chart: function init_ohlc_chart() {
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
          text: this.post.from + " vs. " + this.post.to
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
          }
        }]
      };
      this.ohlc_chart = new highcharts_highstock__WEBPACK_IMPORTED_MODULE_3___default.a.Chart(options);
    },
    init_value_chart: function init_value_chart() {
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
          text: this.post.from + " vs. " + this.post.to
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
        }]
      };
      this.value_chart = new highcharts__WEBPACK_IMPORTED_MODULE_2___default.a.Chart(options);
    },
    update_ohlc_chart: function update_ohlc_chart(data) {
      this.ohlc_chart_data = data['data'].map(function (item) {
        return [item['x'] * 1000].concat(item["y"]);
      });

      if (this.ohlc_chart != null) {
        this.ohlc_chart.hideLoading();
        this.ohlc_chart.series[0].setData(this.ohlc_chart_data);
      }
    },
    create_update_value_chart: function create_update_value_chart(data) {
      this.value_chart_data = data['data'].map(function (item) {
        return [(item[0] + 2 * 3600) * 1000, item[1]];
      });

      if (this.value_chart != null) {
        this.value_chart.hideLoading();
        this.value_chart.series[0].setData(this.value_chart_data);
      }
    },
    create_update_realtime_value: function create_update_realtime_value() {
      var card = document.getElementById('realtime_value_cart');
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
      this.realtime_chart = new highcharts__WEBPACK_IMPORTED_MODULE_2___default.a.Chart(options);
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
      this.volume_chart = new highcharts__WEBPACK_IMPORTED_MODULE_2___default.a.Chart(options);
      this.getNewSeriesVolume(true);
      this.timeout_id_volume = setTimeout(function () {
        global_component_instance.interval_id_volume = global_component_instance.getNewSeriesVolume(false);
        global_component_instance.interval_id_volume = setInterval(function () {
          global_component_instance.interval_id_volume = global_component_instance.getNewSeriesVolume(false);
        }, 60 * 1000);
      }, global_component_instance.real_time_volume_interval * 1000);
    },
    save_realtime_response_data_value: function save_realtime_response_data_value(data, init, date) {
      if (init) {
        this.real_time_data = data['data'].map(function (item) {
          return [item[0] * 1000, item[1]];
        });
        this.last_realtime_value = this.real_time_data[this.real_time_data.length - 1]['y'];
        this.realtime_chart.series[0].setData(this.real_time_data);
      } else {
        this.real_time_data.push([date * 1000, data['data']]);
        this.realtime_chart.series[0].addPoint(this.real_time_data[this.real_time_data.length - 1]);

        if (data['data']) {
          this.last_realtime_value = data['data'];
        }

        this.real_time_value_interval = 60;
      }
    },
    save_realtime_response_data_volume: function save_realtime_response_data_volume(data, init, lastDateVolume) {
      var _this$real_time_volum;

      var last_value = this.last_realtime_volume;

      (_this$real_time_volum = this.real_time_volume_data).push.apply(_this$real_time_volum, _toConsumableArray(data['data'].map(function (item) {
        if (item['y']) {
          last_value = item['y'];
        }

        return [item['x'] * 1000, item['y']];
      })));

      if (init) {
        this.volume_chart.series[0].setData(this.real_time_volume_data);
      } else {
        this.volume_chart.series[0].addPoint(this.real_time_volume_data[this.real_time_volume_data.length - 1], true, true);
      }

      this.last_realtime_volume = last_value;
    },
    getNewSeriesValue: function getNewSeriesValue(init) {
      var _this2 = this;

      if (this.lastDateValue == null) {
        this.lastDateValue = new Date().setSeconds(0, 0) / 1000;
      }

      if (init) {
        var now = new Date() / 1000;
        this.real_time_value_interval = this.lastDateValue + 60 + 50 - now;
      }

      var value_uri = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/last" + "/" + (this.lastDateValue - 60) + "/" + this.exchange + "/" + this.post.from + "/" + this.post.to + "/" + init;
      _plugins_axios__WEBPACK_IMPORTED_MODULE_0__["axios"].get(value_uri).then(function (response) {
        return global_component_instance.save_realtime_response_data_value(response.data, init, _this2.lastDateValue);
      });
      this.lastDateValue += 60;
    },
    getNewSeriesVolume: function getNewSeriesVolume(init) {
      var _this3 = this;

      if (this.lastDateVolume == null) {
        this.lastDateVolume = new Date().setSeconds(0, 0) / 1000;
      }

      var volume_uri;

      if (init) {
        var now = new Date() / 1000;
        this.real_time_volume_interval = this.lastDateVolume + 60 + 50 - now;
        volume_uri = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/volume" + "/" + (this.lastDateVolume - 1080) + "/" + (this.lastDateVolume + 60) + "/" + this.exchange + "/" + "1m" + "/" + this.post.from + "/" + this.post.to;
      } else {
        volume_uri = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/volume" + "/" + (this.lastDateVolume - 60) + "/" + this.lastDateVolume + "/" + this.exchange + "/" + "1m" + "/" + this.post.from + "/" + this.post.to;
      }

      _plugins_axios__WEBPACK_IMPORTED_MODULE_0__["axios"].get(volume_uri).then(function (response) {
        return global_component_instance.save_realtime_response_data_volume(response.data, init, _this3.lastDateVolume);
      });
      this.lastDateVolume += 60;
    },
    finish_init_avail: function finish_init_avail(data) {
      this.from_available = data['from'];
      this.to_available = data['to'];
    },
    init_available: function init_available() {
      var _this4 = this;

      var uri = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/pairs/" + this.exchange;
      this.axios.get(uri).then(function (response) {
        return _this4.finish_init_avail(response.data);
      });
    },
    onChangeFrom: function onChangeFrom() {
      this.clear_all_timeouts_intervals();
      this.create_update_realtime_value();
      this.create_update_realtime_volume();
      this.ohlc_value_chart(true);
    },
    finish_change_to: function finish_change_to(data) {
      var new_fiat = data['data']['fiat'];
      var old_fiat = data['data']['old_fiat'];
      var new_data = this.real_time_data.map(function (item) {
        if (item[1] != null) {
          return [item[0], item[1] / old_fiat * new_fiat];
        }
      });
      this.realtime_chart.series[0].setData(new_data);
      this.real_time_data = new_data;
      this.last_realtime_value = new_data.slice(-1)[0]['y'];
      new_data = this.real_time_volume_data.map(function (item) {
        if (item[1] != null) {
          return [item[0], item[1] / old_fiat * new_fiat];
        }
      });
      this.volume_chart.series[0].setData(new_data);
      this.real_time_volume_data = new_data;
      this.last_realtime_volume = this.last_realtime_volume / old_fiat * new_fiat;
      new_data = this.ohlc_chart_data.map(function (item) {
        return [item[0], item[1] / old_fiat * new_fiat, item[2] / old_fiat * new_fiat, item[3] / old_fiat * new_fiat, item[4] / old_fiat * new_fiat];
      });
      this.ohlc_chart.series[0].setData(new_data);
      this.ohlc_chart_data = new_data;
      new_data = this.value_chart_data.map(function (item) {
        return [item[0], item[1] / old_fiat * new_fiat];
      });
      this.value_chart.series[0].setData(new_data);
      this.value_chart_data = new_data;
    },
    onChangeTo: function onChangeTo() {
      var _this5 = this;

      var now = new Date().setSeconds(0, 0) / 1000;
      var url = "http://" + "167.86.75.179" + ":" + "8001" + "/api/fiat/historical/" + now + "/" + this.post.to + "/" + this.old_to;
      this.old_to = this.post.to;
      this.axios.get(url).then(function (response) {
        return _this5.finish_change_to(response.data);
      });
    },
    clear_all_timeouts_intervals: function clear_all_timeouts_intervals() {
      clearTimeout(this.timeout_id_value);
      clearTimeout(this.timeout_id_volume);
      clearInterval(this.interval_id_value);
      clearInterval(this.interval_id_volume);
    }
  },
  watch: {
    old_to: function old_to() {
      console.log("currency_symbol");
      console.log(this.post.to);

      if (this.post.to) {
        this.currency_symbol = currency_symbol_map__WEBPACK_IMPORTED_MODULE_4___default()(this.post.to.toUpperCase());
      }
    }
  },
  mounted: function mounted() {
    var usd_exchanges = ["kraken", "gdax", "bitfinex", "gemini", "bitstamp", "bitbay", "okcoin"];
    this.exchange = this.$route.name;
    this.post.from = 'btc';
    this.post.to = usd_exchanges.includes(this.exchange) ? 'usd' : 'usdt';
    this.old_to = this.post.to;
    this.init_available();
    global_component_instance = this;
    this.ohlc_value_chart(true);
    this.create_update_realtime_value();
    this.create_update_realtime_volume();
  },
  beforeDestroy: function beforeDestroy() {
    this.clear_all_timeouts_intervals();
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/vue-black-dashboard-master/src/pages/Dashboard2.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/vue-black-dashboard-master/src/pages/Dashboard2.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue_datetime__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue-datetime */ "./node_modules/vue-datetime/dist/vue-datetime.js");
/* harmony import */ var vue_datetime__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue_datetime__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var highcharts__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! highcharts */ "./node_modules/highcharts/highcharts.js");
/* harmony import */ var highcharts__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(highcharts__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _plugins_axios__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../plugins/axios */ "./resources/vue-black-dashboard-master/src/plugins/axios.js");
/* harmony import */ var currency_symbol_map__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! currency-symbol-map */ "./node_modules/currency-symbol-map/currency-symbol-map.js");
/* harmony import */ var currency_symbol_map__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(currency_symbol_map__WEBPACK_IMPORTED_MODULE_3__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
      to_available: [{
        value: "usd",
        text: "USD"
      }, {
        value: "eur",
        text: "EUR"
      }],
      post: {},
      axios: _plugins_axios__WEBPACK_IMPORTED_MODULE_2__["axios"],
      asset: null,
      awvp_chart_data: null,
      awvp_chart: null,
      day_price: null,
      title: null,
      old_to: null
    };
  },
  computed: {
    currency_day_price: function currency_day_price() {
      if (this.day_price == null) {
        return null;
      }

      return currency_symbol_map__WEBPACK_IMPORTED_MODULE_3___default()(this.post.to.toUpperCase()) + " " + this.day_price;
    }
  },
  mounted: function mounted() {
    this.asset = this.$route.name;
    this.post.to = 'usd';
    this.old_to = this.post.to;
    this.title = this.$route.meta['title'];
    this.init_available();
    this.asset_value(true);
    this.query_awvp_chart_data(true);
  },
  methods: {
    query_awvp_chart_data: function query_awvp_chart_data(init) {
      var _this = this;

      if (init) {
        this.post.start = new Date().setHours(0, 0, 0, 0) / 1000;
        this.post.end = new Date().setHours(0, 0, 0, 0) / 1000 + 86400;
        this.post.range = '1h';
      } else {
        this.post.start = Date.parse(this.post.start) / 1000;
        this.post.end = Date.parse(this.post.end) / 1000;
      }

      var url = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/asset/value/" + this.asset + "/" + this.post.to + "/" + this.post.start + "/" + this.post.end + "/" + this.post.range;

      if (this.awvp_chart == null) {
        this.init_awvp_chart();
      }

      this.awvp_chart.showLoading();
      this.axios.get(url).then(function (response) {
        return _this.update_awvp_chart(response.data);
      });
    },
    init_awvp_chart: function init_awvp_chart() {
      var card = document.getElementById('chart_awvp_cart');
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
        }]
      };
      this.awvp_chart = new highcharts__WEBPACK_IMPORTED_MODULE_1___default.a.Chart(options);
    },
    update_awvp_chart: function update_awvp_chart(data) {
      this.awvp_chart_data = data['data'].map(function (item) {
        return [item[0] * 1000, item[1]];
      });

      if (this.awvp_chart != null) {
        this.awvp_chart.hideLoading();
        this.awvp_chart.series[0].setData(this.awvp_chart_data);
      }
    },
    asset_value: function asset_value(init) {
      var _this2 = this;

      if (init) {
        this.post.start = (new Date().setHours(0, 0, 0, 0) + new Date().getTimezoneOffset() * -1 * 60 * 1000) / 1000;
        this.post.end = (new Date().setHours(0, 0, 0, 0) + new Date().getTimezoneOffset() * -1 * 60 * 1000 + 86400000) / 1000;
      } else {
        this.post.start = Date.parse(this.post.start) / 1000;
        this.post.end = this.post.start + 86400;
      }

      var url = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/asset/value/" + this.asset + "/" + this.post.to + "/" + this.post.start + "/" + this.post.end;
      this.axios.get(url).then(function (response) {
        return _this2.update_value(response.data);
      });
    },
    update_value: function update_value(data) {
      this.day_price = data['data'][0][1].toFixed(3);
    },
    finish_change_to: function finish_change_to(data) {
      var new_fiat = data['data']['fiat'];
      var old_fiat = data['data']['old_fiat'];
      this.day_price = (this.day_price / old_fiat * new_fiat).toFixed(3);
    },
    onChangeTo: function onChangeTo() {
      var _this3 = this;

      var now = new Date().setSeconds(0, 0) / 1000;
      var url = "http://" + "167.86.75.179" + ":" + "8001" + "/api/fiat/historical/" + now + "/" + this.post.to + "/" + this.old_to;
      this.old_to = this.post.to;
      this.axios.get(url).then(function (response) {
        return _this3.finish_change_to(response.data);
      });
    },
    finish_init_avail: function finish_init_avail(data) {
      var formatted = data['data'].map(function (item) {
        return {
          value: item,
          text: item.toUpperCase()
        };
      });
      this.to_available = formatted;
    },
    init_available: function init_available() {
      var _this4 = this;

      var uri = "http://" + "167.86.75.179" + ":" + "8001" + "/api/fiat/all_supported";
      this.axios.get(uri).then(function (response) {
        return _this4.finish_init_avail(response.data);
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/currency-symbol-map/currency-symbol-map.js":
/*!*****************************************************************!*\
  !*** ./node_modules/currency-symbol-map/currency-symbol-map.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

var currencySymbolMap = __webpack_require__(/*! ./map */ "./node_modules/currency-symbol-map/map.js")

module.exports = function getSymbolFromCurrency (currencyCode) {
  if (typeof currencyCode !== 'string') return undefined
  var code = currencyCode.toUpperCase()
  if (!currencySymbolMap.hasOwnProperty(code)) return undefined
  return currencySymbolMap[code]
}

module.exports.currencySymbolMap = currencySymbolMap


/***/ }),

/***/ "./node_modules/currency-symbol-map/map.js":
/*!*************************************************!*\
  !*** ./node_modules/currency-symbol-map/map.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = {
  'AED': 'د.إ',
  'AFN': '؋',
  'ALL': 'L',
  'AMD': '֏',
  'ANG': 'ƒ',
  'AOA': 'Kz',
  'ARS': '$',
  'AUD': '$',
  'AWG': 'ƒ',
  'AZN': '₼',
  'BAM': 'KM',
  'BBD': '$',
  'BDT': '৳',
  'BGN': 'лв',
  'BHD': '.د.ب',
  'BIF': 'FBu',
  'BMD': '$',
  'BND': '$',
  'BOB': '$b',
  'BRL': 'R$',
  'BSD': '$',
  'BTC': '฿',
  'BTN': 'Nu.',
  'BWP': 'P',
  'BYR': 'Br',
  'BYN': 'Br',
  'BZD': 'BZ$',
  'CAD': '$',
  'CDF': 'FC',
  'CHF': 'CHF',
  'CLP': '$',
  'CNY': '¥',
  'COP': '$',
  'CRC': '₡',
  'CUC': '$',
  'CUP': '₱',
  'CVE': '$',
  'CZK': 'Kč',
  'DJF': 'Fdj',
  'DKK': 'kr',
  'DOP': 'RD$',
  'DZD': 'دج',
  'EEK': 'kr',
  'EGP': '£',
  'ERN': 'Nfk',
  'ETB': 'Br',
  'ETH': 'Ξ',
  'EUR': '€',
  'FJD': '$',
  'FKP': '£',
  'GBP': '£',
  'GEL': '₾',
  'GGP': '£',
  'GHC': '₵',
  'GHS': 'GH₵',
  'GIP': '£',
  'GMD': 'D',
  'GNF': 'FG',
  'GTQ': 'Q',
  'GYD': '$',
  'HKD': '$',
  'HNL': 'L',
  'HRK': 'kn',
  'HTG': 'G',
  'HUF': 'Ft',
  'IDR': 'Rp',
  'ILS': '₪',
  'IMP': '£',
  'INR': '₹',
  'IQD': 'ع.د',
  'IRR': '﷼',
  'ISK': 'kr',
  'JEP': '£',
  'JMD': 'J$',
  'JOD': 'JD',
  'JPY': '¥',
  'KES': 'KSh',
  'KGS': 'лв',
  'KHR': '៛',
  'KMF': 'CF',
  'KPW': '₩',
  'KRW': '₩',
  'KWD': 'KD',
  'KYD': '$',
  'KZT': 'лв',
  'LAK': '₭',
  'LBP': '£',
  'LKR': '₨',
  'LRD': '$',
  'LSL': 'M',
  'LTC': 'Ł',
  'LTL': 'Lt',
  'LVL': 'Ls',
  'LYD': 'LD',
  'MAD': 'MAD',
  'MDL': 'lei',
  'MGA': 'Ar',
  'MKD': 'ден',
  'MMK': 'K',
  'MNT': '₮',
  'MOP': 'MOP$',
  'MRO': 'UM',
  'MRU': 'UM',
  'MUR': '₨',
  'MVR': 'Rf',
  'MWK': 'MK',
  'MXN': '$',
  'MYR': 'RM',
  'MZN': 'MT',
  'NAD': '$',
  'NGN': '₦',
  'NIO': 'C$',
  'NOK': 'kr',
  'NPR': '₨',
  'NZD': '$',
  'OMR': '﷼',
  'PAB': 'B/.',
  'PEN': 'S/.',
  'PGK': 'K',
  'PHP': '₱',
  'PKR': '₨',
  'PLN': 'zł',
  'PYG': 'Gs',
  'QAR': '﷼',
  'RMB': '￥',
  'RON': 'lei',
  'RSD': 'Дин.',
  'RUB': '₽',
  'RWF': 'R₣',
  'SAR': '﷼',
  'SBD': '$',
  'SCR': '₨',
  'SDG': 'ج.س.',
  'SEK': 'kr',
  'SGD': '$',
  'SHP': '£',
  'SLL': 'Le',
  'SOS': 'S',
  'SRD': '$',
  'SSP': '£',
  'STD': 'Db',
  'STN': 'Db',
  'SVC': '$',
  'SYP': '£',
  'SZL': 'E',
  'THB': '฿',
  'TJS': 'SM',
  'TMT': 'T',
  'TND': 'د.ت',
  'TOP': 'T$',
  'TRL': '₤',
  'TRY': '₺',
  'TTD': 'TT$',
  'TVD': '$',
  'TWD': 'NT$',
  'TZS': 'TSh',
  'UAH': '₴',
  'UGX': 'USh',
  'USD': '$',
  'UYU': '$U',
  'UZS': 'лв',
  'VEF': 'Bs',
  'VND': '₫',
  'VUV': 'VT',
  'WST': 'WS$',
  'XAF': 'FCFA',
  'XBT': 'Ƀ',
  'XCD': '$',
  'XOF': 'CFA',
  'XPF': '₣',
  'YER': '﷼',
  'ZAR': 'R',
  'ZWD': 'Z$'
}


/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/vue-black-dashboard-master/src/pages/Dashboard.vue?vue&type=template&id=cf5d2d52&":
/*!***********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/vue-black-dashboard-master/src/pages/Dashboard.vue?vue&type=template&id=cf5d2d52& ***!
  \***********************************************************************************************************************************************************************************************************************************/
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
          staticClass: "col-md-2",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c("div", { staticClass: "form-group" }, [
            _c("label", [_vm._v("From:")]),
            _c("br"),
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
                return _c("option", { domProps: { value: item.value } }, [
                  _vm._v(_vm._s(item.text))
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
          staticClass: "col-md-2",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c("div", { staticClass: "form-group" }, [
            _c("label", [_vm._v("To:")]),
            _c("br"),
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
                return _c("option", { domProps: { value: item.value } }, [
                  _vm._v(_vm._s(item.text))
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
          staticClass: "col-md-2",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c("div", { staticClass: "form-group" }, [
            _c("label", [_vm._v("Range:")]),
            _c("br"),
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
          staticClass: "col-md-2",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c(
            "label",
            { staticStyle: { display: "grid", "justify-content": "center" } },
            [_vm._v("Start:")]
          ),
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
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "col-md-2",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c(
            "label",
            { staticStyle: { display: "grid", "justify-content": "center" } },
            [_vm._v("End:")]
          ),
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
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "col-md-2",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c(
            "form",
            {
              on: {
                submit: function($event) {
                  $event.preventDefault()
                  return _vm.ohlc_value_chart(false)
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
          _c("card", { attrs: { type: "chart", id: "chart_ohlc_cart" } }, [
            _c("div", { staticClass: "chart-area" }, [
              _c("div", { attrs: { id: "chart_view_ohlc" } }, [
                _c("div", { ref: "chart_ohlc", staticClass: "chart" })
              ])
            ])
          ])
        ],
        1
      )
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "row" }, [
      _c(
        "div",
        { staticClass: "col-12" },
        [
          _c("card", { attrs: { type: "chart", id: "chart_value_cart" } }, [
            _c("div", { staticClass: "chart-area" }, [
              _c("div", { attrs: { id: "chart_view_value" } }, [
                _c("div", { ref: "chart_value", staticClass: "chart" })
              ])
            ])
          ])
        ],
        1
      )
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "row" }, [
      _c(
        "div",
        { staticClass: "col-lg-4" },
        [
          _c(
            "card",
            { attrs: { type: "chart" } },
            [
              _c("template", { slot: "header" }, [
                _c("h5", { staticClass: "card-category" }, [
                  _vm._v("Realtime Value")
                ]),
                _vm._v(" "),
                _c("h3", { staticClass: "card-title" }, [
                  _c("i", {
                    staticClass: "tim-icons icon-bell-55 text-primary "
                  }),
                  _vm._v(
                    _vm._s(_vm.last_realtime_value) +
                      " " +
                      _vm._s(_vm.currency_symbol)
                  )
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
          _c(
            "card",
            { attrs: { type: "chart" } },
            [
              _c("template", { slot: "header" }, [
                _c("h5", { staticClass: "card-category" }, [
                  _vm._v("Realtime Volume")
                ]),
                _vm._v(" "),
                _c("h3", { staticClass: "card-title" }, [
                  _c("i", {
                    staticClass: "tim-icons icon-delivery-fast text-info "
                  }),
                  _vm._v(
                    _vm._s(_vm.last_realtime_volume) +
                      " " +
                      _vm._s(_vm.currency_symbol)
                  )
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
      ),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "col-lg-4", class: { "text-right": _vm.isRTL } },
        [_c("div", { attrs: { id: "highchart" } })]
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
      _c("button", { staticClass: "btn btn-primary" }, [_vm._v("Query")])
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/vue-black-dashboard-master/src/pages/Dashboard2.vue?vue&type=template&id=71f36aeb&":
/*!************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/vue-black-dashboard-master/src/pages/Dashboard2.vue?vue&type=template&id=71f36aeb& ***!
  \************************************************************************************************************************************************************************************************************************************/
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
      _c("div", { staticClass: "col-lg-8 ml-auto mr-auto" }, [
        _c("div", { staticClass: "row" }, [
          _c(
            "div",
            {
              staticClass: "col-md-3",
              staticStyle: { display: "grid", "justify-content": "center" }
            },
            [
              _c(
                "label",
                {
                  staticStyle: { display: "grid", "justify-content": "center" }
                },
                [_vm._v("Day:")]
              ),
              _c("br"),
              _vm._v(" "),
              _c("datetime", {
                attrs: { type: "date" },
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
          ),
          _vm._v(" "),
          _c(
            "div",
            {
              staticClass: "col-md-3",
              staticStyle: { display: "grid", "justify-content": "center" }
            },
            [
              _c("label", [_vm._v("Convert to:")]),
              _c("br"),
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
                  return _c("option", { domProps: { value: item.value } }, [
                    _vm._v(_vm._s(item.text))
                  ])
                }),
                0
              )
            ]
          ),
          _vm._v(" "),
          _c(
            "div",
            {
              staticClass: "col-md-4",
              staticStyle: { display: "grid", "justify-content": "center" }
            },
            [
              _c(
                "form",
                {
                  on: {
                    submit: function($event) {
                      $event.preventDefault()
                      return _vm.asset_value(false)
                    }
                  }
                },
                [_vm._m(0)]
              )
            ]
          )
        ])
      ])
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "row" }, [
      _c(
        "span",
        { staticStyle: { "text-align": "center", "font-size": "250%" } },
        [_vm._v(_vm._s(_vm.currency_day_price))]
      )
    ]),
    _vm._v(" "),
    _c("br"),
    _vm._v(" "),
    _c("br"),
    _vm._v(" "),
    _c("div", { staticClass: "row" }, [
      _c(
        "div",
        {
          staticClass: "col-md-2",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c("label", [_vm._v("Convert to:")]),
          _c("br"),
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
                      $event.target.multiple ? $$selectedVal : $$selectedVal[0]
                    )
                  },
                  function($event) {
                    return _vm.onChangeFrom()
                  }
                ]
              }
            },
            _vm._l(_vm.to_available, function(item) {
              return _c("option", { domProps: { value: item.value } }, [
                _vm._v(_vm._s(item.text))
              ])
            }),
            0
          )
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "col-md-2",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c("label", [_vm._v("Range:")]),
          _c("br"),
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
        ]
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "col-md-2",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c(
            "label",
            { staticStyle: { display: "grid", "justify-content": "center" } },
            [_vm._v("Start:")]
          ),
          _c("br"),
          _vm._v(" "),
          _c("datetime", {
            attrs: { type: "date" },
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
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "col-md-2",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c(
            "label",
            { staticStyle: { display: "grid", "justify-content": "center" } },
            [_vm._v("End:")]
          ),
          _c("br"),
          _vm._v(" "),
          _c("datetime", {
            attrs: { type: "date" },
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
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "col-md-2",
          staticStyle: { display: "grid", "justify-content": "center" }
        },
        [
          _c(
            "form",
            {
              on: {
                submit: function($event) {
                  $event.preventDefault()
                  return _vm.query_awvp_chart_data(false)
                }
              }
            },
            [
              _c("button", { staticClass: "btn btn-primary" }, [
                _vm._v("Query")
              ])
            ]
          )
        ]
      )
    ]),
    _vm._v(" "),
    _c("br"),
    _vm._v(" "),
    _c("br"),
    _vm._v(" "),
    _c("div", { staticClass: "row" }, [
      _c(
        "div",
        { staticClass: "col-12" },
        [
          _c("card", { attrs: { type: "chart", id: "chart_awvp_cart" } }, [
            _c("div", { staticClass: "chart-area" }, [
              _c("div", { attrs: { id: "chart_view_awvp" } }, [
                _c("div", { ref: "chart_awvp", staticClass: "chart" })
              ])
            ])
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
      _c("button", { staticClass: "btn btn-primary" }, [
        _vm._v("Query Day Value")
      ])
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./resources/vue-black-dashboard-master/src/pages/Dashboard.vue":
/*!**********************************************************************!*\
  !*** ./resources/vue-black-dashboard-master/src/pages/Dashboard.vue ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Dashboard_vue_vue_type_template_id_cf5d2d52___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Dashboard.vue?vue&type=template&id=cf5d2d52& */ "./resources/vue-black-dashboard-master/src/pages/Dashboard.vue?vue&type=template&id=cf5d2d52&");
/* harmony import */ var _Dashboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Dashboard.vue?vue&type=script&lang=js& */ "./resources/vue-black-dashboard-master/src/pages/Dashboard.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Dashboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Dashboard_vue_vue_type_template_id_cf5d2d52___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Dashboard_vue_vue_type_template_id_cf5d2d52___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/vue-black-dashboard-master/src/pages/Dashboard.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/vue-black-dashboard-master/src/pages/Dashboard.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************!*\
  !*** ./resources/vue-black-dashboard-master/src/pages/Dashboard.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Dashboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./Dashboard.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/vue-black-dashboard-master/src/pages/Dashboard.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Dashboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/vue-black-dashboard-master/src/pages/Dashboard.vue?vue&type=template&id=cf5d2d52&":
/*!*****************************************************************************************************!*\
  !*** ./resources/vue-black-dashboard-master/src/pages/Dashboard.vue?vue&type=template&id=cf5d2d52& ***!
  \*****************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Dashboard_vue_vue_type_template_id_cf5d2d52___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./Dashboard.vue?vue&type=template&id=cf5d2d52& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/vue-black-dashboard-master/src/pages/Dashboard.vue?vue&type=template&id=cf5d2d52&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Dashboard_vue_vue_type_template_id_cf5d2d52___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Dashboard_vue_vue_type_template_id_cf5d2d52___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/vue-black-dashboard-master/src/pages/Dashboard2.vue":
/*!***********************************************************************!*\
  !*** ./resources/vue-black-dashboard-master/src/pages/Dashboard2.vue ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Dashboard2_vue_vue_type_template_id_71f36aeb___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Dashboard2.vue?vue&type=template&id=71f36aeb& */ "./resources/vue-black-dashboard-master/src/pages/Dashboard2.vue?vue&type=template&id=71f36aeb&");
/* harmony import */ var _Dashboard2_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Dashboard2.vue?vue&type=script&lang=js& */ "./resources/vue-black-dashboard-master/src/pages/Dashboard2.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Dashboard2_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Dashboard2_vue_vue_type_template_id_71f36aeb___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Dashboard2_vue_vue_type_template_id_71f36aeb___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/vue-black-dashboard-master/src/pages/Dashboard2.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/vue-black-dashboard-master/src/pages/Dashboard2.vue?vue&type=script&lang=js&":
/*!************************************************************************************************!*\
  !*** ./resources/vue-black-dashboard-master/src/pages/Dashboard2.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Dashboard2_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./Dashboard2.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/vue-black-dashboard-master/src/pages/Dashboard2.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Dashboard2_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/vue-black-dashboard-master/src/pages/Dashboard2.vue?vue&type=template&id=71f36aeb&":
/*!******************************************************************************************************!*\
  !*** ./resources/vue-black-dashboard-master/src/pages/Dashboard2.vue?vue&type=template&id=71f36aeb& ***!
  \******************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Dashboard2_vue_vue_type_template_id_71f36aeb___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./Dashboard2.vue?vue&type=template&id=71f36aeb& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/vue-black-dashboard-master/src/pages/Dashboard2.vue?vue&type=template&id=71f36aeb&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Dashboard2_vue_vue_type_template_id_71f36aeb___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Dashboard2_vue_vue_type_template_id_71f36aeb___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/vue-black-dashboard-master/src/plugins/axios.js":
/*!*******************************************************************!*\
  !*** ./resources/vue-black-dashboard-master/src/plugins/axios.js ***!
  \*******************************************************************/
/*! exports provided: axios, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "axios", function() { return axios; });
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! axios */ "./node_modules/axios/index.js");
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(axios__WEBPACK_IMPORTED_MODULE_0__);
 // insert all your axios logic here

var axios = axios__WEBPACK_IMPORTED_MODULE_0___default.a;
/* harmony default export */ __webpack_exports__["default"] = ({
  install: function install(Vue, options) {
    Vue.prototype.$axios = axios__WEBPACK_IMPORTED_MODULE_0___default.a;
  }
});

/***/ })

}]);