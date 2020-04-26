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
            <select v-model=post.to @change="onChangeTo()">
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
      <span style="text-align: center;font-size: 250%">{{currency_day_price}}</span>
    </div>
  </div>
</template>

<script>
  import getSymbolFromCurrency from "currency-symbol-map";
  import { axios } from '../../plugins/axios';
  import { Datetime } from 'vue-datetime';

  export default {
    components: {
      datetime: Datetime
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

        let url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/asset/value/" + this.asset + "/" + this.post.to + "/" + this.post.start + "/" + this.post.end;
        this.axios.get(url).then(response => (this.update_value(response.data)));
      },

      update_value(data){
        this.day_price = data['data'][0][1].toFixed(3);
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
        if (this.post.start == null){
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

</style>
