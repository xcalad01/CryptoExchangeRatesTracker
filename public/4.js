(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[4],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Assets_dashboard.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Assets_dashboard.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Dashboard2_Value__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Dashboard2/Value */ "./resources/js/pages/Dashboard2/Value.vue");
/* harmony import */ var _Dashboard2_AvwpChart__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Dashboard2/AvwpChart */ "./resources/js/pages/Dashboard2/AvwpChart.vue");
/* harmony import */ var _Dashboard2_QuickStats__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Dashboard2/QuickStats */ "./resources/js/pages/Dashboard2/QuickStats.vue");
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    valuecomponent: _Dashboard2_Value__WEBPACK_IMPORTED_MODULE_0__["default"],
    avwpcomponent: _Dashboard2_AvwpChart__WEBPACK_IMPORTED_MODULE_1__["default"],
    quickstats: _Dashboard2_QuickStats__WEBPACK_IMPORTED_MODULE_2__["default"]
  },
  data: function data() {
    return {};
  },
  mounted: function mounted() {},
  methods: {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/AvwpChart.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard2/AvwpChart.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var highcharts__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! highcharts */ "./node_modules/highcharts/highcharts.js");
/* harmony import */ var highcharts__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(highcharts__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _plugins_axios__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../plugins/axios */ "./resources/js/plugins/axios.js");
/* harmony import */ var vue_datetime__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vue-datetime */ "./node_modules/vue-datetime/dist/vue-datetime.js");
/* harmony import */ var vue_datetime__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(vue_datetime__WEBPACK_IMPORTED_MODULE_2__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    datetime: vue_datetime__WEBPACK_IMPORTED_MODULE_2__["Datetime"]
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
      axios: _plugins_axios__WEBPACK_IMPORTED_MODULE_1__["axios"],
      asset: null,
      awvp_chart_data: null,
      awvp_chart: null,
      title: null,
      old_to: null
    };
  },
  mounted: function mounted() {
    this.asset = this.$route.name;
    this.post.to = 'usd';
    this.old_to = this.post.to;
    this.title = this.$route.meta['title'];
    this.init_available();
    this.query_awvp_chart_data(true);
  },
  methods: {
    query_awvp_chart_data: function query_awvp_chart_data(init) {
      var _this = this;

      if (init) {
        this.post.start = (new Date().setHours(0, 0, 0, 0) + new Date().getTimezoneOffset() * -1 * 60 * 1000) / 1000;
        this.post.end = (new Date().setHours(0, 0, 0, 0) + new Date().getTimezoneOffset() * -1 * 60 * 1000 + 86400000) / 1000;
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
      this.awvp_chart = new highcharts__WEBPACK_IMPORTED_MODULE_0___default.a.Chart(options);
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
    finish_change_to: function finish_change_to(data) {
      var new_fiat = data['data']['fiat'];
      var old_fiat = data['data']['old_fiat'];
      var new_data = this.awvp_chart_data.map(function (item) {
        return [item[0], item[1] / old_fiat * new_fiat];
      });
      this.awvp_chart.series[0].setData(new_data);
      this.awvp_chart_data = new_data;
    },
    onChangeTo: function onChangeTo() {
      var _this2 = this;

      var now = new Date().setSeconds(0, 0) / 1000;
      var url = "http://" + "167.86.75.179" + ":" + "8001" + "/api/fiat/historical/" + now + "/" + this.post.to + "/" + this.old_to;
      this.old_to = this.post.to;
      this.axios.get(url).then(function (response) {
        return _this2.finish_change_to(response.data);
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
      var _this3 = this;

      var uri = "http://" + "167.86.75.179" + ":" + "8001" + "/api/fiat/all_supported";
      this.axios.get(uri).then(function (response) {
        return _this3.finish_init_avail(response.data);
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var currency_symbol_map__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! currency-symbol-map */ "./node_modules/currency-symbol-map/currency-symbol-map.js");
/* harmony import */ var currency_symbol_map__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(currency_symbol_map__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _plugins_axios__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../plugins/axios */ "./resources/js/plugins/axios.js");
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


/* harmony default export */ __webpack_exports__["default"] = ({
  data: function data() {
    return {
      day_price: null,
      post: {},
      axios: _plugins_axios__WEBPACK_IMPORTED_MODULE_1__["axios"],
      h24_min: null,
      h24_max: null,
      total_supply: null,
      circulating_supply: null,
      all_time_min: null,
      all_time_max: null,
      old_to: null,
      to_available: null
    };
  },
  computed: {
    currency_day_price: function currency_day_price() {
      if (this.day_price == null) {
        return null;
      }

      return currency_symbol_map__WEBPACK_IMPORTED_MODULE_0___default()(this.post.to.toUpperCase()) + " " + this.day_price;
    },
    all_time_min_perc: function all_time_min_perc() {
      if (this.day_price == null || this.all_time_min == null) {
        return null;
      }

      return ((this.all_time_min - this.day_price) / this.all_time_min * 100 * -1).toFixed(2);
    },
    all_time_max_perc: function all_time_max_perc() {
      if (this.day_price == null || this.all_time_max == null) {
        return null;
      }

      return ((this.all_time_max - this.day_price) / this.all_time_max * 100 * -1).toFixed(2);
    }
  },
  methods: {
    low_high_24h: function low_high_24h() {
      var _this = this;

      this.post.end = (Date.now() / 1000).toFixed(0);
      this.post.start = this.post.end - 86400;
      var url = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/asset/value/" + this.asset + "/" + this.post.to + "/" + this.post.start + "/" + this.post.end + "/" + "1m";
      this.axios.get(url).then(function (response) {
        return _this.update_24h_value(response.data);
      });
    },
    update_24h_value: function update_24h_value(data) {
      var values = data['data'].map(function (item) {
        return item[1];
      });
      this.day_price = values[values.length - 1];
      this.h24_min = Math.min.apply(Math, _toConsumableArray(values)).toFixed(3);
      this.h24_max = Math.max.apply(Math, _toConsumableArray(values)).toFixed(3);
    },
    supply: function supply() {
      var _this2 = this;

      var url = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/asset/info/" + this.asset;
      this.axios.get(url).then(function (response) {
        return _this2.update_supply(response.data);
      });
    },
    update_supply: function update_supply(data) {
      this.total_supply = data['data']['total_supply'];

      if (data['data']['circulating_supply'] !== null) {
        this.circulating_supply = data['data']['circulating_supply'];
      } else {
        this.circulating_supply = "XXX";
      }
    },
    all_time_values: function all_time_values() {
      var _this3 = this;

      var url = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/asset/all_time/" + this.asset;
      this.axios.get(url).then(function (response) {
        return _this3.finish_all_time_values(response.data);
      });
    },
    finish_all_time_values: function finish_all_time_values(data) {
      this.all_time_min = data['data']['min'];
      this.all_time_max = data['data']['max'];
    },
    finish_change_to: function finish_change_to(data) {
      var new_fiat = data['data']['fiat'];
      var old_fiat = data['data']['old_fiat'];
      this.day_price = (this.day_price / old_fiat * new_fiat).toFixed(3);
      this.loading_day_price = false;
    },
    onChangeTo: function onChangeTo() {
      var _this4 = this;

      if (!this.post.start) {
        this.loading_day_price = true;
        var now = new Date().setSeconds(0, 0) / 1000;
        var url = "http://" + "167.86.75.179" + ":" + "8001" + "/api/fiat/historical/" + now + "/" + this.post.to + "/" + this.old_to;
        this.old_to = this.post.to;
        this.axios.get(url).then(function (response) {
          return _this4.finish_change_to(response.data);
        });
      }
    }
  },
  mounted: function mounted() {
    this.asset = this.$route.name;
    this.post.to = 'usd';
    this.old_to = this.post.to;
    this.supply();
    this.all_time_values();
    this.low_high_24h();
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/Value.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard2/Value.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var currency_symbol_map__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! currency-symbol-map */ "./node_modules/currency-symbol-map/currency-symbol-map.js");
/* harmony import */ var currency_symbol_map__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(currency_symbol_map__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _plugins_axios__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../plugins/axios */ "./resources/js/plugins/axios.js");
/* harmony import */ var vue_datetime__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vue-datetime */ "./node_modules/vue-datetime/dist/vue-datetime.js");
/* harmony import */ var vue_datetime__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(vue_datetime__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var vue_select__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vue-select */ "./node_modules/vue-select/dist/vue-select.js");
/* harmony import */ var vue_select__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(vue_select__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var vue_select_dist_vue_select_css__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! vue-select/dist/vue-select.css */ "./node_modules/vue-select/dist/vue-select.css");
/* harmony import */ var vue_select_dist_vue_select_css__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(vue_select_dist_vue_select_css__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var epic_spinners__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! epic-spinners */ "./node_modules/epic-spinners/src/lib.js");
/* harmony import */ var humanize_plus__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! humanize-plus */ "./node_modules/humanize-plus/dist/humanize.js");
/* harmony import */ var humanize_plus__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(humanize_plus__WEBPACK_IMPORTED_MODULE_6__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    datetime: vue_datetime__WEBPACK_IMPORTED_MODULE_2__["Datetime"],
    vueselect: vue_select__WEBPACK_IMPORTED_MODULE_3___default.a,
    spinner: epic_spinners__WEBPACK_IMPORTED_MODULE_5__["HalfCircleSpinner"]
  },
  data: function data() {
    return {
      post: {},
      day_price: null,
      asset: null,
      old_to: null,
      title: null,
      axios: _plugins_axios__WEBPACK_IMPORTED_MODULE_1__["axios"],
      to_available: null,
      selected: null,
      loading_day_price: true,
      render_value: true,
      left_value_converter: null,
      right_value_converter: null,
      fresh_day_price: null,
      date: null,
      old_to_converter: null
    };
  },
  computed: {
    currency_day_price: function currency_day_price() {
      if (!this.day_price) {
        return "No data";
      }

      return currency_symbol_map__WEBPACK_IMPORTED_MODULE_0___default()(this.post.to.toUpperCase()) + " " + humanize_plus__WEBPACK_IMPORTED_MODULE_6___default.a.formatNumber(this.day_price, 4);
    }
  },
  watch: {
    left_value_converter: function left_value_converter() {
      this.right_value_converter = this.left_value_converter * this.fresh_day_price;
    }
  },
  methods: {
    formatDate: function formatDate(date) {
      var d = new Date(date),
          month = '' + (d.getMonth() + 1),
          day = '' + d.getDate(),
          year = d.getFullYear();
      if (month.length < 2) month = '0' + month;
      if (day.length < 2) day = '0' + day;
      return [year, month, day].join('-');
    },
    asset_value: function asset_value(init) {
      var _this = this;

      if (init) {
        this.date = this.formatDate(new Date());
        this.post.start = (new Date().setHours(0, 0, 0, 0) + new Date().getTimezoneOffset() * -1 * 60 * 1000) / 1000;
        this.post.end = (new Date().setHours(0, 0, 0, 0) + new Date().getTimezoneOffset() * -1 * 60 * 1000 + 86400000) / 1000;
      } else {
        this.date = this.formatDate(new Date(this.post.start));
        this.post.start = Date.parse(this.post.start) / 1000;
        this.post.end = this.post.start + 86400;
      }

      this.loading_day_price = true;
      var url = "http://" + "167.86.75.179" + ":" + "8001" + "/api/crypto/historical/asset/value/" + this.asset + "/" + this.post.to + "/" + this.post.start + "/" + this.post.end;
      this.axios.get(url).then(function (response) {
        return _this.update_value(response.data, init);
      });
    },
    update_value: function update_value(data, init) {
      if (data['data'].length) {
        console.log(data['data']);
        this.day_price = data['data'][0][1].toFixed(3);
      } else {
        this.day_price = "No data available :(";
      }

      this.loading_day_price = false;

      if (init) {
        this.fresh_day_price = this.day_price;
      }
    },
    init_available: function init_available() {
      var _this2 = this;

      var uri = "http://" + "167.86.75.179" + ":" + "8001" + "/api/fiat/all_supported";
      this.axios.get(uri).then(function (response) {
        return _this2.finish_init_avail(response.data);
      });
    },
    finish_change_to: function finish_change_to(data) {
      var new_fiat = data['data']['fiat'];
      var old_fiat = data['data']['old_fiat'];
      this.day_price = (this.day_price / old_fiat * new_fiat).toFixed(3);
      this.loading_day_price = false;
    },
    onChangeTo: function onChangeTo() {
      var _this3 = this;

      this.loading_day_price = true;
      var url = "http://" + "167.86.75.179" + ":" + "8001" + "/api/fiat/historical/" + this.post.start + "/" + this.post.to + "/" + this.old_to;
      this.old_to = this.post.to;
      this.axios.get(url).then(function (response) {
        return _this3.finish_change_to(response.data);
      });
    },
    finish_change_to_converter: function finish_change_to_converter(data) {
      var new_fiat = data['data']['fiat'];
      var old_fiat = data['data']['old_fiat'];
      this.right_value_converter = this.right_value_converter / old_fiat * new_fiat;
    },
    onChangeToConverter: function onChangeToConverter() {
      var _this4 = this;

      var now = new Date().setSeconds(0, 0) / 1000;
      var url = "http://" + "167.86.75.179" + ":" + "8001" + "/api/fiat/historical/" + now + "/" + this.post.converter_to + "/" + this.old_to_converter;
      this.old_to_converter = this.post.converter_to;

      if (this.left_value_converter) {
        this.axios.get(url).then(function (response) {
          return _this4.finish_change_to_converter(response.data);
        });
      }
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
    value_click: function value_click() {
      this.render_value = true;
    },
    converter_click: function converter_click() {
      this.render_value = false;
    }
  },
  mounted: function mounted() {
    this.asset = this.$route.name;
    this.post.to = 'usd';
    this.post.converter_to = 'usd';
    this.old_to = this.post.to;
    this.old_to_converter = this.post.converter_to;
    this.title = this.$route.meta['title'];
    this.init_available();
    this.asset_value(true);
  }
});

/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=style&index=0&lang=scss&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=style&index=0&lang=scss& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, ".p-lg-4 {\n  padding: 0.5rem !important;\n  width: 33.3%;\n}\n.percentage_red {\n  position: absolute;\n  float: right;\n  font-size: 13px;\n  margin-bottom: 10px;\n  color: red;\n  font-stretch: semi-expanded;\n  box-sizing: inherit;\n  border-radius: 3px;\n  top: 10px;\n  right: 10px;\n  padding: inherit;\n  padding-top: 7px;\n  padding-right: 14px;\n  padding-bottom: 7px;\n  padding-left: 14px;\n  margin-top: 37px;\n}\n.percentage_green {\n  position: absolute;\n  float: right;\n  font-size: 13px;\n  margin-bottom: 10px;\n  color: green;\n  font-stretch: semi-expanded;\n  box-sizing: inherit;\n  border-radius: 3px;\n  top: 10px;\n  right: 10px;\n  padding: inherit;\n  padding-top: 7px;\n  padding-right: 14px;\n  padding-bottom: 7px;\n  padding-left: 14px;\n  margin-top: 37px;\n}\n.convert_to {\n  width: 15%;\n  float: right !important;\n  position: absolute;\n  margin-bottom: 10px;\n  margin-top: 37px;\n  top: 10px;\n  right: 10px;\n}", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/Value.vue?vue&type=style&index=0&lang=scss&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard2/Value.vue?vue&type=style&index=0&lang=scss& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, ".style-chooser .vs__search::-webkit-input-placeholder {\n  background: #333434;\n  border: none;\n  color: #394066;\n  text-transform: lowercase;\n  font-variant: small-caps;\n}\n.style-chooser .vs__search::-moz-placeholder {\n  background: #333434;\n  border: none;\n  color: #394066;\n  text-transform: lowercase;\n  font-variant: small-caps;\n}\n.style-chooser .vs__search:-ms-input-placeholder {\n  background: #333434;\n  border: none;\n  color: #394066;\n  text-transform: lowercase;\n  font-variant: small-caps;\n}\n.style-chooser .vs__search::-ms-input-placeholder {\n  background: #333434;\n  border: none;\n  color: #394066;\n  text-transform: lowercase;\n  font-variant: small-caps;\n}\n.style-chooser .vs__search::placeholder,\n.style-chooser .vs__dropdown-toggle,\n.style-chooser .vs__dropdown-menu {\n  background: #333434;\n  border: none;\n  color: #394066;\n  text-transform: lowercase;\n  font-variant: small-caps;\n}\n.style-chooser .vs__clear,\n.style-chooser .vs__open-indicator {\n  fill: #394066;\n}\n.value_area {\n  background-color: #66f;\n  padding-top: 7px;\n  padding-bottom: 7px;\n  padding-right: 14px;\n  padding-left: 14px;\n  border-radius: 3px;\n  margin-top: 10px;\n}\n.value_area .text_area {\n  text-align: center;\n  font-size: 200%;\n  color: white;\n}\n.card_custom {\n  height: 250px;\n}\n.button_row {\n  display: -webkit-box;\n  display: flex;\n  flex-wrap: wrap;\n  margin-left: -15px;\n  -webkit-box-pack: center;\n  justify-content: right;\n  margin-right: 10px;\n}\n.value_options_row {\n  margin-top: 30px;\n}\n.converter_inputs {\n  margin-top: 40px;\n}\n.converter_title {\n  font-size: 20px;\n  color: white;\n  font-weight: bold;\n}\n.converter_input_text {\n  font-size: 15px;\n  text-align: center;\n}\n.date {\n  float: right;\n  margin-left: 20px;\n  text-align: center;\n  display: -webkit-box;\n  display: flex;\n  font-size: 100%;\n  color: white;\n  position: relative;\n  vertical-align: bottom;\n  top: 40px;\n}", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=style&index=0&lang=scss&":
/*!******************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=style&index=0&lang=scss& ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../node_modules/css-loader!../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../node_modules/postcss-loader/src??ref--7-2!../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../node_modules/vue-loader/lib??vue-loader-options!./QuickStats.vue?vue&type=style&index=0&lang=scss& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=style&index=0&lang=scss&");

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

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/Value.vue?vue&type=style&index=0&lang=scss&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard2/Value.vue?vue&type=style&index=0&lang=scss& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../node_modules/css-loader!../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../node_modules/postcss-loader/src??ref--7-2!../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../node_modules/vue-loader/lib??vue-loader-options!./Value.vue?vue&type=style&index=0&lang=scss& */ "./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/Value.vue?vue&type=style&index=0&lang=scss&");

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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Assets_dashboard.vue?vue&type=template&id=00ed6e61&":
/*!**************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Assets_dashboard.vue?vue&type=template&id=00ed6e61& ***!
  \**************************************************************************************************************************************************************************************************************/
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
      _c("br"),
      _vm._v(" "),
      _c("quickstats"),
      _vm._v(" "),
      _c("br"),
      _vm._v(" "),
      _c("br"),
      _vm._v(" "),
      _c("br"),
      _vm._v(" "),
      _c("valuecomponent"),
      _vm._v(" "),
      _c("br"),
      _vm._v(" "),
      _c("br"),
      _vm._v(" "),
      _c("avwpcomponent")
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/AvwpChart.vue?vue&type=template&id=1f57d768&":
/*!******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard2/AvwpChart.vue?vue&type=template&id=1f57d768& ***!
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
            _c("label", { staticClass: "label" }, [_vm._v("Convert to:")]),
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
          _c("div", { staticClass: "form-group" }, [
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
          ])
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
          _c("card", { attrs: { type: "chart" } }, [
            _c(
              "div",
              { staticClass: "chart-area", attrs: { id: "chart_awvp_cart" } },
              [
                _c("div", { attrs: { id: "chart_view_awvp" } }, [
                  _c("div", { ref: "chart_awvp", staticClass: "chart" })
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
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=template&id=379d222a&":
/*!*******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=template&id=379d222a& ***!
  \*******************************************************************************************************************************************************************************************************************/
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
      _c("div", { staticClass: "p-col-12 p-lg-4" }, [
        _c("div", { staticClass: "card" }, [
          _c("span", { staticClass: "title" }, [_vm._v(" Price ")]),
          _vm._v(" "),
          _vm._m(0),
          _vm._v(" "),
          _c("span", { staticClass: "span_value" }, [
            _vm._v(" " + _vm._s(_vm.currency_day_price) + " ")
          ]),
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
              staticClass: "select-css convert_to",
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
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "p-col-12 p-lg-4" }, [
        _c("div", { staticClass: "card" }, [
          _c("span", { staticClass: "title" }, [_vm._v(" 24h Low / High ")]),
          _vm._v(" "),
          _vm._m(1),
          _vm._v(" "),
          _c("span", { staticClass: "span_value" }, [
            _vm._v(
              " $ " + _vm._s(_vm.h24_min) + " / $ " + _vm._s(_vm.h24_max) + " "
            )
          ])
        ])
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "p-col-12 p-lg-4" }, [
        _c("div", { staticClass: "card" }, [
          _vm._m(2),
          _vm._v(" "),
          _c("span", { staticClass: "detail" }, [
            _vm._v(" Publicly available and circulating in the market ")
          ]),
          _vm._v(" "),
          _c("span", { staticClass: "span_value" }, [
            _vm._v(" " + _vm._s(_vm.circulating_supply) + " / "),
            _c("br"),
            _vm._v(" " + _vm._s(_vm.total_supply) + " ")
          ])
        ])
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "p-col-12 p-lg-4" }, [
        _c("div", { staticClass: "card" }, [
          _c("span", { staticClass: "title" }, [_vm._v(" All Time Low ")]),
          _vm._v(" "),
          _c("span", { staticClass: "detail" }, [
            _vm._v(" All time lowest price ")
          ]),
          _vm._v(" "),
          _c("span", { staticClass: "span_value" }, [
            _vm._v(" $ " + _vm._s(_vm.all_time_min) + " ")
          ]),
          _vm._v(" "),
          _vm.all_time_min_perc < 0
            ? _c("span", { staticClass: "percentage_red" }, [
                _vm._v(" " + _vm._s(_vm.all_time_min_perc) + " % ")
              ])
            : _vm._e(),
          _vm._v(" "),
          _vm.all_time_min_perc >= 0
            ? _c("span", { staticClass: "percentage_green" }, [
                _vm._v(" " + _vm._s(_vm.all_time_min_perc) + " % ")
              ])
            : _vm._e()
        ])
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "p-col-12 p-lg-4" }, [
        _c("div", { staticClass: "card" }, [
          _c("span", { staticClass: "title" }, [_vm._v(" All Time High ")]),
          _vm._v(" "),
          _c("span", { staticClass: "detail" }, [
            _vm._v(" All time highest price ")
          ]),
          _vm._v(" "),
          _c("span", { staticClass: "span_value" }, [
            _vm._v(" $ " + _vm._s(_vm.all_time_max) + " ")
          ]),
          _vm._v(" "),
          _vm.all_time_max_perc < 0
            ? _c("span", { staticClass: "percentage_red" }, [
                _vm._v(" " + _vm._s(_vm.all_time_max_perc) + " % ")
              ])
            : _vm._e(),
          _vm._v(" "),
          _vm.all_time_max_perc >= 0
            ? _c("span", { staticClass: "percentage_green" }, [
                _vm._v(" " + _vm._s(_vm.all_time_max_perc) + " % ")
              ])
            : _vm._e()
        ])
      ])
    ])
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("span", { staticClass: "detail" }, [
      _vm._v(" Latest "),
      _c("br"),
      _vm._v(" Volume Weighted Average Price (VWAP)")
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("span", { staticClass: "detail" }, [
      _vm._v(" 24h VWAP "),
      _c("br"),
      _vm._v(" Low / High Price ")
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("span", { staticClass: "title" }, [
      _vm._v(" Circulatting / "),
      _c("br"),
      _vm._v("  Total Supply ")
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/Value.vue?vue&type=template&id=4d4139a9&":
/*!**************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/pages/Dashboard2/Value.vue?vue&type=template&id=4d4139a9& ***!
  \**************************************************************************************************************************************************************************************************************/
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
      _c("div", { staticClass: "card card_custom" }, [
        _c("div", { staticClass: "button_row" }, [
          _c("div", { staticClass: "btn-group btn-group-toggle float-right" }, [
            _c(
              "label",
              {
                staticClass: "btn btn-sm btn-primary btn-simple",
                attrs: { id: "0" }
              },
              [
                _c("input", {
                  attrs: {
                    type: "radio",
                    name: "options",
                    autocomplete: "off"
                  },
                  on: { click: _vm.value_click }
                }),
                _vm._v(" Value ")
              ]
            ),
            _vm._v(" "),
            _c(
              "label",
              {
                staticClass: "btn btn-sm btn-primary btn-simple",
                attrs: { id: "1" }
              },
              [
                _c("input", {
                  attrs: {
                    type: "radio",
                    name: "options",
                    autocomplete: "off"
                  },
                  on: { click: _vm.converter_click }
                }),
                _vm._v(" Converter ")
              ]
            )
          ])
        ]),
        _vm._v(" "),
        _vm.render_value
          ? _c("div", [
              _vm._m(0),
              _vm._v(" "),
              _c("div", { staticClass: "row value_options_row" }, [
                _c("div", { staticClass: "col-lg-8 ml-auto mr-auto" }, [
                  _c("div", { staticClass: "row" }, [
                    _c(
                      "div",
                      {
                        staticClass: "col-sm",
                        staticStyle: {
                          display: "grid",
                          "justify-content": "center"
                        }
                      },
                      [
                        _c(
                          "div",
                          { staticClass: "form-group" },
                          [
                            _c("label", { staticClass: "label" }, [
                              _vm._v("Day:")
                            ]),
                            _c("br"),
                            _vm._v(" "),
                            _c("datetime", {
                              staticClass: "input-group.no-border",
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
                        )
                      ]
                    ),
                    _vm._v(" "),
                    _c(
                      "div",
                      {
                        staticClass: "col-sm",
                        staticStyle: {
                          display: "grid",
                          "justify-content": "center"
                        }
                      },
                      [
                        _c("div", { staticClass: "form-group" }, [
                          _c("label", { staticClass: "label" }, [
                            _vm._v("Convert to:")
                          ]),
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
                              staticClass: "select-css",
                              on: {
                                change: [
                                  function($event) {
                                    var $$selectedVal = Array.prototype.filter
                                      .call($event.target.options, function(o) {
                                        return o.selected
                                      })
                                      .map(function(o) {
                                        var val =
                                          "_value" in o ? o._value : o.value
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
                              return _c(
                                "option",
                                { domProps: { value: item.value } },
                                [_vm._v(_vm._s(item.text))]
                              )
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
                        staticStyle: {
                          display: "grid",
                          "justify-content": "center"
                        }
                      },
                      [
                        _c("div", { staticClass: "form-group" }, [
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
                            [_vm._m(1)]
                          )
                        ])
                      ]
                    )
                  ])
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "row" }, [
                _c(
                  "div",
                  { staticClass: "value_area" },
                  [
                    _vm.loading_day_price
                      ? _c("spinner", {
                          attrs: {
                            "animation-duration": 1000,
                            size: 30,
                            color: "#ffffff"
                          }
                        })
                      : _c("span", { staticClass: "text_area" }, [
                          _vm._v(_vm._s(_vm.currency_day_price))
                        ])
                  ],
                  1
                ),
                _vm._v(" "),
                _c("span", { staticClass: "date" }, [_vm._v(_vm._s(_vm.date))])
              ])
            ])
          : _c("div", [
              _c("div", { staticClass: "row" }, [
                _c("span", { staticClass: "converter_title" }, [
                  _vm._v(
                    " " +
                      _vm._s(_vm.asset.toUpperCase()) +
                      " vs. Fiat converter"
                  )
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "row converter_inputs" }, [
                _c("div", { staticClass: "col-sm" }, [
                  _c("div", { staticClass: "form-group" }, [
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.left_value_converter,
                          expression: "left_value_converter"
                        }
                      ],
                      staticClass: "form-control converter_input_text",
                      attrs: {
                        "aria-describedby": "addon-right addon-left",
                        placeholder: _vm.asset.toUpperCase(),
                        type: "number"
                      },
                      domProps: { value: _vm.left_value_converter },
                      on: {
                        input: function($event) {
                          if ($event.target.composing) {
                            return
                          }
                          _vm.left_value_converter = $event.target.value
                        }
                      }
                    })
                  ])
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "col-sm" }, [
                  _c("div", { staticClass: "form-group" }, [
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.right_value_converter,
                          expression: "right_value_converter"
                        }
                      ],
                      staticClass: "form-control converter_input_text",
                      attrs: {
                        "aria-describedby": "addon-right addon-left",
                        placeholder: "USD",
                        type: "number"
                      },
                      domProps: { value: _vm.right_value_converter },
                      on: {
                        input: function($event) {
                          if ($event.target.composing) {
                            return
                          }
                          _vm.right_value_converter = $event.target.value
                        }
                      }
                    })
                  ])
                ]),
                _vm._v(" "),
                _c(
                  "div",
                  {
                    staticClass: "col-sm",
                    staticStyle: {
                      display: "grid",
                      "justify-content": "center"
                    }
                  },
                  [
                    _c("div", { staticClass: "form-group" }, [
                      _c("label", { staticClass: "label" }, [
                        _vm._v("Convert to:")
                      ]),
                      _c("br"),
                      _vm._v(" "),
                      _c(
                        "select",
                        {
                          directives: [
                            {
                              name: "model",
                              rawName: "v-model",
                              value: _vm.post.converter_to,
                              expression: "post.converter_to"
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
                                  "converter_to",
                                  $event.target.multiple
                                    ? $$selectedVal
                                    : $$selectedVal[0]
                                )
                              },
                              function($event) {
                                return _vm.onChangeToConverter()
                              }
                            ]
                          }
                        },
                        _vm._l(_vm.to_available, function(item) {
                          return _c(
                            "option",
                            { domProps: { value: item.value } },
                            [_vm._v(_vm._s(item.text))]
                          )
                        }),
                        0
                      )
                    ])
                  ]
                )
              ])
            ])
      ])
    ])
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "row" }, [
      _c("span", { staticClass: "converter_title" }, [
        _vm._v(" Daily Volume Weighted Average Price ")
      ])
    ])
  },
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

/***/ "./resources/js/pages/Assets_dashboard.vue":
/*!*************************************************!*\
  !*** ./resources/js/pages/Assets_dashboard.vue ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Assets_dashboard_vue_vue_type_template_id_00ed6e61___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Assets_dashboard.vue?vue&type=template&id=00ed6e61& */ "./resources/js/pages/Assets_dashboard.vue?vue&type=template&id=00ed6e61&");
/* harmony import */ var _Assets_dashboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Assets_dashboard.vue?vue&type=script&lang=js& */ "./resources/js/pages/Assets_dashboard.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Assets_dashboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Assets_dashboard_vue_vue_type_template_id_00ed6e61___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Assets_dashboard_vue_vue_type_template_id_00ed6e61___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/pages/Assets_dashboard.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/pages/Assets_dashboard.vue?vue&type=script&lang=js&":
/*!**************************************************************************!*\
  !*** ./resources/js/pages/Assets_dashboard.vue?vue&type=script&lang=js& ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Assets_dashboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib??ref--4-0!../../../node_modules/vue-loader/lib??vue-loader-options!./Assets_dashboard.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Assets_dashboard.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Assets_dashboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/pages/Assets_dashboard.vue?vue&type=template&id=00ed6e61&":
/*!********************************************************************************!*\
  !*** ./resources/js/pages/Assets_dashboard.vue?vue&type=template&id=00ed6e61& ***!
  \********************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Assets_dashboard_vue_vue_type_template_id_00ed6e61___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../node_modules/vue-loader/lib??vue-loader-options!./Assets_dashboard.vue?vue&type=template&id=00ed6e61& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Assets_dashboard.vue?vue&type=template&id=00ed6e61&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Assets_dashboard_vue_vue_type_template_id_00ed6e61___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Assets_dashboard_vue_vue_type_template_id_00ed6e61___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/pages/Dashboard2/AvwpChart.vue":
/*!*****************************************************!*\
  !*** ./resources/js/pages/Dashboard2/AvwpChart.vue ***!
  \*****************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _AvwpChart_vue_vue_type_template_id_1f57d768___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./AvwpChart.vue?vue&type=template&id=1f57d768& */ "./resources/js/pages/Dashboard2/AvwpChart.vue?vue&type=template&id=1f57d768&");
/* harmony import */ var _AvwpChart_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./AvwpChart.vue?vue&type=script&lang=js& */ "./resources/js/pages/Dashboard2/AvwpChart.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _AvwpChart_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _AvwpChart_vue_vue_type_template_id_1f57d768___WEBPACK_IMPORTED_MODULE_0__["render"],
  _AvwpChart_vue_vue_type_template_id_1f57d768___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/pages/Dashboard2/AvwpChart.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/pages/Dashboard2/AvwpChart.vue?vue&type=script&lang=js&":
/*!******************************************************************************!*\
  !*** ./resources/js/pages/Dashboard2/AvwpChart.vue?vue&type=script&lang=js& ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AvwpChart_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./AvwpChart.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/AvwpChart.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AvwpChart_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/pages/Dashboard2/AvwpChart.vue?vue&type=template&id=1f57d768&":
/*!************************************************************************************!*\
  !*** ./resources/js/pages/Dashboard2/AvwpChart.vue?vue&type=template&id=1f57d768& ***!
  \************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_AvwpChart_vue_vue_type_template_id_1f57d768___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./AvwpChart.vue?vue&type=template&id=1f57d768& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/AvwpChart.vue?vue&type=template&id=1f57d768&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_AvwpChart_vue_vue_type_template_id_1f57d768___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_AvwpChart_vue_vue_type_template_id_1f57d768___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/pages/Dashboard2/QuickStats.vue":
/*!******************************************************!*\
  !*** ./resources/js/pages/Dashboard2/QuickStats.vue ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _QuickStats_vue_vue_type_template_id_379d222a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./QuickStats.vue?vue&type=template&id=379d222a& */ "./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=template&id=379d222a&");
/* harmony import */ var _QuickStats_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./QuickStats.vue?vue&type=script&lang=js& */ "./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _QuickStats_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./QuickStats.vue?vue&type=style&index=0&lang=scss& */ "./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=style&index=0&lang=scss&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _QuickStats_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _QuickStats_vue_vue_type_template_id_379d222a___WEBPACK_IMPORTED_MODULE_0__["render"],
  _QuickStats_vue_vue_type_template_id_379d222a___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/pages/Dashboard2/QuickStats.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=script&lang=js&":
/*!*******************************************************************************!*\
  !*** ./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./QuickStats.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=style&index=0&lang=scss&":
/*!****************************************************************************************!*\
  !*** ./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=style&index=0&lang=scss& ***!
  \****************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/style-loader!../../../../node_modules/css-loader!../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../node_modules/postcss-loader/src??ref--7-2!../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../node_modules/vue-loader/lib??vue-loader-options!./QuickStats.vue?vue&type=style&index=0&lang=scss& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=style&index=0&lang=scss&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=template&id=379d222a&":
/*!*************************************************************************************!*\
  !*** ./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=template&id=379d222a& ***!
  \*************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_template_id_379d222a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./QuickStats.vue?vue&type=template&id=379d222a& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/QuickStats.vue?vue&type=template&id=379d222a&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_template_id_379d222a___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_QuickStats_vue_vue_type_template_id_379d222a___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/pages/Dashboard2/Value.vue":
/*!*************************************************!*\
  !*** ./resources/js/pages/Dashboard2/Value.vue ***!
  \*************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Value_vue_vue_type_template_id_4d4139a9___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Value.vue?vue&type=template&id=4d4139a9& */ "./resources/js/pages/Dashboard2/Value.vue?vue&type=template&id=4d4139a9&");
/* harmony import */ var _Value_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Value.vue?vue&type=script&lang=js& */ "./resources/js/pages/Dashboard2/Value.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _Value_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Value.vue?vue&type=style&index=0&lang=scss& */ "./resources/js/pages/Dashboard2/Value.vue?vue&type=style&index=0&lang=scss&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _Value_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Value_vue_vue_type_template_id_4d4139a9___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Value_vue_vue_type_template_id_4d4139a9___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/pages/Dashboard2/Value.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/pages/Dashboard2/Value.vue?vue&type=script&lang=js&":
/*!**************************************************************************!*\
  !*** ./resources/js/pages/Dashboard2/Value.vue?vue&type=script&lang=js& ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Value_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./Value.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/Value.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Value_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/pages/Dashboard2/Value.vue?vue&type=style&index=0&lang=scss&":
/*!***********************************************************************************!*\
  !*** ./resources/js/pages/Dashboard2/Value.vue?vue&type=style&index=0&lang=scss& ***!
  \***********************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_Value_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/style-loader!../../../../node_modules/css-loader!../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../node_modules/postcss-loader/src??ref--7-2!../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../node_modules/vue-loader/lib??vue-loader-options!./Value.vue?vue&type=style&index=0&lang=scss& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/Value.vue?vue&type=style&index=0&lang=scss&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_Value_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_Value_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_Value_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_Value_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_vue_loader_lib_index_js_vue_loader_options_Value_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/pages/Dashboard2/Value.vue?vue&type=template&id=4d4139a9&":
/*!********************************************************************************!*\
  !*** ./resources/js/pages/Dashboard2/Value.vue?vue&type=template&id=4d4139a9& ***!
  \********************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Value_vue_vue_type_template_id_4d4139a9___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./Value.vue?vue&type=template&id=4d4139a9& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/pages/Dashboard2/Value.vue?vue&type=template&id=4d4139a9&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Value_vue_vue_type_template_id_4d4139a9___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Value_vue_vue_type_template_id_4d4139a9___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);