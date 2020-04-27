<template>
  <div>
    <div class="row">
      <div class="col-lg-8 ml-auto mr-auto">
        <div class="row">
          <div class="col-md-3" style="display:grid;justify-content: center">
            <label style="display:grid;justify-content: center">Day:</label><br>
            <datetime class="input-group.no-border" type="date" v-model="post.start"></datetime>
          </div>
          <div class="col-md-3" style="display:grid;justify-content: center">
            <label>Convert to:</label><br>
            <select v-model=post.to @change="onChangeTo()">
              <option v-for="item in to_available" :value="item.value">{{item.text}}</option>
            </select>
<!--            <div id="app">-->
<!--              <vueselect class="style-chooser" :options="options" label="title">-->
<!--                <template slot="option" slot-scope="option">-->
<!--                  <span class="fa" :class="option.icon"></span>-->
<!--                  {{ option.title }}-->
<!--                </template>-->
<!--              </vueselect>-->
<!--            </div>-->
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
      <spinner
        v-if="loading_day_price"
        :animation-duration="1000"
        :size="30"
        color="#41b581"
      />
      <span v-else style="text-align: center;font-size: 250%">{{currency_day_price}}</span>
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
        options: [
          {
            title: 'Read the Docs',
            icon: 'fa-book',
            url: 'https://codeclimate.com/github/sagalbot/vue-select'
          },
          {
            title: 'View on GitHub',
            icon: 'fa-github',
            url: 'https://codeclimate.com/github/sagalbot/vue-select'
          },
          {
            title: 'View on NPM',
            icon: 'fa-database',
            url: 'https://codeclimate.com/github/sagalbot/vue-select'
          },
          {
            title: 'View Codepen Examples',
            icon: 'fa-pencil',
            url: 'https://codeclimate.com/github/sagalbot/vue-select'
          }
        ]
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

    methods: {
      asset_value(init){
        if (init){
          this.post.start = (new Date().setHours(0,0,0,0) + new Date().getTimezoneOffset() * - 1 * 60 * 1000) / 1000;
          this.post.end = (new Date().setHours(0,0,0,0) + new Date().getTimezoneOffset() * - 1 * 60 * 1000 + 86400000) / 1000
        }
        else{
          this.post.start = Date.parse(this.post.start) / 1000;
          this.post.end = this.post.start + 86400;
        }

        this.loading_day_price = true;
        let url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/asset/value/" + this.asset + "/" + this.post.to + "/" + this.post.start + "/" + this.post.end;
        this.post.start = null;
        this.axios.get(url).then(response => (this.update_value(response.data)));
      },

      update_value(data){
        this.day_price = data['data'][0][1].toFixed(3);
        this.loading_day_price = false;
      },

      init_available(){
        let uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/fiat/all_supported";
        this.axios.get(uri).then(response => (this.finish_init_avail(response.data)));
      },

      finish_change_to(data){
        var new_fiat = data['data']['fiat'];
        var old_fiat = data['data']['old_fiat'];

        this.day_price = (this.day_price / old_fiat * new_fiat).toFixed(3);
      },

      onChangeTo(){
        if (!this.post.start){
          this.loading_day_price = true;
          var now = new Date().setSeconds(0, 0) / 1000;
          var url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/fiat/historical/" + now + "/" + this.post.to + "/" + this.old_to;
          this.old_to = this.post.to;
          this.axios.get(url).then(response => (this.finish_change_to(response.data)));
        }
      },

      finish_init_avail(data){
        var formatted = data['data'].map(function (item) {
          return {value: item, text: item.toUpperCase()}
        });
        this.to_available = formatted;
      },
    },

    mounted() {
      this.asset = this.$route.name;
      this.post.to = 'usd';
      this.old_to = this.post.to;
      this.title = this.$route.meta['title'];

      this.init_available();
      this.asset_value(true);},


  }
</script>

<style>
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

</style>
