<!--
 * Dashboard2/QuickStats.vue
 *
 * Asset quick stats component
 * @author     Filip Caladi (xcalad01)
 * @license    MIT
 -->

<template>
    <div>
        <div class="row">
            <select class="select-css convert_to" v-model=post.to @change="onChangeTo()">
                <option v-for="item in to_available" :value="item.value">{{item.text}}</option>
            </select>
        </div>
        <div class="row">
            <div class="p-col-12 p-lg-4">
                <div class="card">
                    <span class="title"> Price </span>
                    <span class="detail"> Latest <br /> Volume Weighted Average Price (VWAP)</span>
                    <span class="span_value"> {{currency_day_price}} </span>
                </div>
            </div>

            <div class="p-col-12 p-lg-4">
                <div class="card">
                    <span class="title"> 24h Low / High </span>
                    <span class="detail"> 24h VWAP <br /> Low / High Price </span>
                    <span class="span_value"> {{currency_h24_min}} / {{currency_h24_max}} </span>
                </div>
            </div>

            <div class="p-col-12 p-lg-4">
                <div class="card">
                    <span class="title"> Circulatting / <br />  Total Supply </span>
                    <span class="detail"> Publicly available and circulating in the market </span>
                    <span class="span_value"> {{circulating_supply}} / <br /> {{total_supply}} </span>
                </div>
            </div>

            <div class="p-col-12 p-lg-4">
                <div class="card">
                    <span class="title"> All Time Low </span>
                    <span class="detail"> All time lowest price </span>
                    <span class="span_value"> {{currency_all_time_min}} </span>
                    <span v-if="all_time_min_perc < 0" class="percentage_red"> {{all_time_min_perc}} % </span>
                    <span v-if="all_time_min_perc >= 0" class="percentage_green"> {{all_time_min_perc}} % </span>

                </div>
            </div>

            <div class="p-col-12 p-lg-4">
                <div class="card">
                    <span class="title"> All Time High </span>
                    <span class="detail"> All time highest price </span>
                    <span class="span_value"> {{currency_all_time_max}} </span>
                    <span v-if="all_time_max_perc < 0" class="percentage_red"> {{all_time_max_perc}} % </span>
                    <span v-if="all_time_max_perc >= 0" class="percentage_green"> {{all_time_max_perc}} % </span>
                </div>
            </div>


        </div>
    </div>
</template>

<script>
    import getSymbolFromCurrency from "currency-symbol-map";
    import { axios } from '../../plugins/axios';
    import Humanize from 'humanize-plus';

    export default {
        data () {
            return {
                day_price: null,
                post: {},
                axios: axios,

                h24_min: null,
                h24_max: null,

                total_supply: null,
                circulating_supply: null,

                all_time_min: null,
                all_time_max: null,

                old_to:null,
                to_available: null
            }
        },

        computed: {
            currency_day_price(){
                if (this.day_price == null){
                    return null;
                }

                return getSymbolFromCurrency(this.post.to.toUpperCase()) + " " + Humanize.formatNumber(this.day_price, 5);
            },

            currency_h24_min(){
                if (this.h24_min == null){
                    return null;
                }

                return getSymbolFromCurrency(this.post.to.toUpperCase()) + " " +  Humanize.formatNumber(this.h24_min, 5);
            },

            currency_h24_max(){
                if (this.h24_max == null){
                    return null;
                }

                return getSymbolFromCurrency(this.post.to.toUpperCase()) + " " + Humanize.formatNumber(this.h24_max, 5);
            },

            currency_all_time_min(){
                if (this.all_time_min == null){
                    return null;
                }

                return getSymbolFromCurrency(this.post.to.toUpperCase()) + " " + Humanize.formatNumber(this.all_time_min, 5);
            },

            currency_all_time_max(){
                if (this.all_time_max == null){
                    return null;
                }

                return getSymbolFromCurrency(this.post.to.toUpperCase()) + " " + Humanize.formatNumber(this.all_time_max, 5);
            },

            all_time_min_perc(){
                if (this.day_price == null || this.all_time_min == null){
                    return null
                }

                return (((this.all_time_min - this.day_price) / this.all_time_min) * 100 * -1).toFixed(2);
            },

            all_time_max_perc(){
                if (this.day_price == null || this.all_time_max == null){
                    return null
                }

                return (((this.all_time_max - this.day_price) / this.all_time_max) * 100 * -1).toFixed(2);
            }
        },

        methods: {
            low_high_24h(){
                this.post.end = (Date.now() / 1000).toFixed(0);
                this.post.start = this.post.end - 86400;

                let url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/asset/value/" + this.asset + "/" + this.post.to + "/" + this.post.start + "/" + this.post.end + "/" + "1m";

                this.axios.get(url).then(response => (this.update_24h_value(response.data)));
            },

            update_24h_value(data){
                var values = data['data'].map(function (item) {
                    return item[1];
                });

                this.day_price = values[values.length - 1];
                this.h24_min = Math.min(...values).toFixed(3);
                this.h24_max = Math.max(...values).toFixed(3);
            },

            supply(){
                let url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/asset/info/" + this.asset;
                this.axios.get(url).then(response => (this.update_supply(response.data)));
            },

            update_supply(data){
                this.total_supply = Humanize.formatNumber(data['data']['total_supply']);
                if (data['data']['circulating_supply'] !== null){
                    this.circulating_supply = Humanize.formatNumber(data['data']['circulating_supply']);
                }
                else{
                    this.circulating_supply = "XXX"
                }

            },

            all_time_values(){
                let url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/asset/all_time/" + this.asset;
                this.axios.get(url).then(response => (this.finish_all_time_values(response.data)));

            },

            finish_all_time_values(data){
                this.all_time_min = data['data']['min'];
                this.all_time_max = data['data']['max'];
            },

            finish_change_to(data){
                var new_fiat = data['data']['fiat'];
                var old_fiat = data['data']['old_fiat'];

                this.day_price = (this.day_price / old_fiat * new_fiat).toFixed(3);
                this.h24_min = (this.h24_min / old_fiat * new_fiat).toFixed(3);
                this.h24_max = (this.h24_max / old_fiat * new_fiat).toFixed(3);
                this.all_time_min = (this.all_time_min / old_fiat * new_fiat).toFixed(3);
                this.all_time_max = (this.all_time_max / old_fiat * new_fiat).toFixed(3);
                this.loading_day_price = false;
            },

            onChangeTo(){
                this.loading_day_price = true;
                var now = new Date().setSeconds(0, 0) / 1000;
                var url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/fiat/historical/" + now + "/" + this.post.to + "/" + this.old_to;
                this.old_to = this.post.to;
                this.axios.get(url).then(response => (this.finish_change_to(response.data)));
            },

            init_available(){
                let uri = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/fiat/all_supported";
                this.axios.get(uri).then(response => (this.finish_init_avail(response.data)));
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

            this.init_available();
            this.supply();
            this.all_time_values();
            this.low_high_24h();
        }
    }
</script>

<style lang="scss">
    .p-lg-4 {
        padding: 0.5rem !important;
        width: 33.3%;
    }

    .percentage_red {
        position: absolute;
        float: right;
        font-size: 13px;
        margin-bottom: 10px;
        color: red;
        font-stretch: semi-expanded;
        box-sizing: inherit;
        border-radius: 3px;
        top: 10px;
        right: 10px;
        padding: inherit;
        padding-top: 7px;
        padding-right: 14px;
        padding-bottom: 7px;
        padding-left: 14px;
        margin-top: 37px;
    }

    .percentage_green {
        position: absolute;
        float: right;
        font-size: 13px;
        margin-bottom: 10px;
        color: green;
        font-stretch: semi-expanded;
        box-sizing: inherit;
        border-radius: 3px;
        top: 10px;
        right: 10px;
        padding: inherit;
        padding-top: 7px;
        padding-right: 14px;
        padding-bottom: 7px;
        padding-left: 14px;
        margin-top: 37px;
    }

    .convert_to {
        width: 6%;
        float: right !important;
        position: absolute;
        margin-bottom: 10px;
        margin-top: 45px;
        top: 10px;
        right: 10px;
    }
</style>
