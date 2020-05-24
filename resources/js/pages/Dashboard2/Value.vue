<template>
  <div>
    <div class="row">
        <div class="card card_custom">
            <div class="button_row">
                <div class="btn-group btn-group-toggle float-right">
                    <label id="0" class="btn btn-sm btn-primary btn-simple">
                        <input v-on:click="value_click" type="radio" name="options" autocomplete="off"> Value </input>
                    </label>
                    <label id="1" class="btn btn-sm btn-primary btn-simple">
                        <input v-on:click="converter_click" type="radio" name="options" autocomplete="off"> Converter </input>
                    </label>
                </div>
            </div>
            <div v-if="render_value">
                <div class="row">
                    <span class="converter_title"> Daily Volume Weighted Average Price </span>
                </div>
                <div class="row value_options_row">
                    <div class="col-lg-8 ml-auto mr-auto">
                        <div class="row">
                            <div class="col-sm" style="display:grid;justify-content: center">
                                <div class="form-group">
                                    <label class="label">Day:</label><br>
                                    <datetime class="input-group.no-border" type="date" v-model="post.start"></datetime>
                                </div>
                            </div>
                            <div class="col-sm" style="display:grid;justify-content: center">
                                <div class="form-group">
                                    <label class="label">Convert to:</label><br>
                                    <select class="select-css" v-model=post.to @change="onChangeTo()">
                                        <option v-for="item in to_available" :value="item.value">{{item.text}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm" style="display:grid;justify-content: center">
                                <div class="form-group">
                                    <form @submit.prevent="asset_value(false)">
                                        <div class="form-group">
                                            <button class="btn btn-primary">Query Day Value</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="value_area">
                        <spinner
                            v-if="loading_day_price"
                            :animation-duration="1000"
                            :size="30"
                            color="#ffffff"
                        />
                        <span v-else class="text_area">{{currency_day_price}}</span>
                    </div>
                    <span class="date">{{date}}</span>
                </div>
            </div>
            <div v-else>
                <div class="row">
                    <span class="converter_title"> {{asset.toUpperCase()}} vs. Fiat converter</span>
                </div>
                <div class="row converter_inputs">
                    <div class="col-sm">
                        <div class="form-group">
                            <input v-model="left_value_converter" aria-describedby="addon-right addon-left" v-bind:placeholder=asset.toUpperCase() class="form-control converter_input_text" type="number">
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="form-group">
                            <input v-model="right_value_converter" aria-describedby="addon-right addon-left" placeholder="USD" class="form-control converter_input_text" type="number">
                        </div>
                    </div>
                    <div class="col-sm" style="display:grid;justify-content: center">
                        <div class="form-group">
                            <label class="label">Convert to:</label><br>
                            <select class="select-css" v-model=post.converter_to @change="onChangeToConverter()">
                                <option v-for="item in to_available" :value="item.value">{{item.text}}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
  </div>
</template>

<script>
  import getSymbolFromCurrency from "currency-symbol-map";
  import { axios } from '../../plugins/axios';
  import { Datetime } from 'vue-datetime';
  import VueSelect from 'vue-select';
  import 'vue-select/dist/vue-select.css';
  import { HalfCircleSpinner } from 'epic-spinners';
  import Humanize from 'humanize-plus';

  export default {
    components: {
      datetime: Datetime,
      vueselect: VueSelect,
      spinner: HalfCircleSpinner
    },

    data () {
      return {
        post: {},
        day_price: null,
        asset: null,
        old_to: null,
        title: null,
        axios: axios,
        to_available: null,
        selected: null,
        loading_day_price: true,
          render_value: true,
          left_value_converter: null,
          right_value_converter: null,
          fresh_day_price: null,
          date: null,
          old_to_converter: null

      }
    },

    computed: {
      currency_day_price(){
        if (!this.day_price){
          return "No data";
        }

        console.log("DAY PRICE");
        console.log(this.day_price);
        return getSymbolFromCurrency(this.post.to.toUpperCase()) + " " + Humanize.formatNumber(this.day_price, 4);
      }
    },

    watch: {
        left_value_converter: function () {
            this.right_value_converter = this.left_value_converter * this.fresh_day_price;
        }
    },


    methods: {
        formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2)
                month = '0' + month;
            if (day.length < 2)
                day = '0' + day;

            return [year, month, day].join('-');
        },

      asset_value(init){
        if (init){
          this.date = this.formatDate(new Date());
          this.post.start = (new Date().setHours(0,0,0,0) + new Date().getTimezoneOffset() * - 1 * 60 * 1000) / 1000;
          this.post.end = (new Date().setHours(0,0,0,0) + new Date().getTimezoneOffset() * - 1 * 60 * 1000 + 86400000) / 1000
        }
        else{
          this.date = this.formatDate(new Date(this.post.start));
          this.post.start = Date.parse(this.post.start) / 1000;
          this.post.end = this.post.start + 86400;
        }

        this.loading_day_price = true;
        let url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/asset/value/" + this.asset + "/" + this.post.to + "/" + this.post.start + "/" + this.post.end;
        this.axios.get(url).then(response => (this.update_value(response.data, init)));
      },

      update_value(data, init){
        if (data['data'].length){
            console.log(data['data']);
            this.day_price = data['data'][0][1].toFixed(3);
        }
        else{
            this.day_price = "No data available :("
        }
        this.loading_day_price = false;
        if (init){
            this.fresh_day_price = this.day_price;
        }
      },

      init_available(){
        let uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/fiat/all_supported";
        this.axios.get(uri).then(response => (this.finish_init_avail(response.data)));
      },

      finish_change_to(data){
        var new_fiat = data['data']['fiat'];
        var old_fiat = data['data']['old_fiat'];

        this.day_price = (this.day_price / old_fiat * new_fiat).toFixed(3);
        this.loading_day_price = false;
      },

      onChangeTo(){
          this.loading_day_price = true;
          var url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/fiat/historical/" + this.post.start + "/" + this.post.to + "/" + this.old_to;
          this.old_to = this.post.to;
          this.axios.get(url).then(response => (this.finish_change_to(response.data)));
      },

        finish_change_to_converter(data){
            var new_fiat = data['data']['fiat'];
            var old_fiat = data['data']['old_fiat'];

            this.right_value_converter = this.right_value_converter / old_fiat * new_fiat;
        },

        onChangeToConverter(){
            var now = new Date().setSeconds(0, 0) / 1000;
            var url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/fiat/historical/" + now + "/" + this.post.converter_to + "/" + this.old_to_converter;
            this.old_to_converter = this.post.converter_to;
            if (this.left_value_converter){
                this.axios.get(url).then(response => (this.finish_change_to_converter(response.data)));
            }
        },

      finish_init_avail(data){
        var formatted = data['data'].map(function (item) {
          return {value: item, text: item.toUpperCase()}
        });
        this.to_available = formatted;
      },

      value_click(){
        this.render_value = true;
      },

      converter_click(){
        this.render_value = false;
      }
    },

    mounted() {
      this.asset = this.$route.name;
      this.post.to = 'usd';
      this.post.converter_to = 'usd';
      this.old_to = this.post.to;
      this.old_to_converter = this.post.converter_to;
      this.title = this.$route.meta['title'];

      this.init_available();
      this.asset_value(true);

      },


  }
</script>

<style lang="scss">
  .style-chooser .vs__search::placeholder,
  .style-chooser .vs__dropdown-toggle,
  .style-chooser .vs__dropdown-menu {
    background: #333434;
    border: none;
    color: #394066;
    text-transform: lowercase;
    font-variant: small-caps;
  }

  .style-chooser .vs__clear,
  .style-chooser .vs__open-indicator {
    fill: #394066;
  }



  .value_area {

      .text_area {
          text-align: center;
          font-size: 200%;
          color: rgb(255, 255, 255);
      }

      background-color: #66f;

      padding-top: 7px;

      padding-bottom: 7px;

      padding-right: 14px;

      padding-left: 14px;

      border-radius: 3px;

      margin-top: 10px;
  }

  .card_custom {
      height: 250px;
  }

    .button_row {
        display: -webkit-box;
        display: flex;
        flex-wrap: wrap;
        margin-left: -15px;
        -webkit-box-pack: center;
        justify-content: right;
        margin-right: 10px;
    }

    .value_options_row {
        margin-top: 30px;
    }
    .converter_inputs {
        margin-top: 40px;
    }

    .converter_title {
        font-size: 20px;
        color: white;
        font-weight: bold;
    }

    .converter_input_text {
        font-size: 15px;
        text-align: center;
    }

    .date {
        float: right;
        margin-left: 20px;
        text-align: center;
        display: flex;
        font-size: 100%;
        color: white;
        position: relative;
        vertical-align: bottom;
        top: 40px;
    }
</style>
