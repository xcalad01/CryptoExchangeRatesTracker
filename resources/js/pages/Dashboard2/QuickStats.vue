<template>
    <div>
        <div class="row">
            <div class="p-col-12 p-lg-4">
                <div class="card">
                    <span class="title"> Price </span>
                    <span class="detail"> 24h Average <br /> Volume Weighted Price (AVWP)</span>
                    <span class="span_value"> {{currency_day_price}} </span>
                </div>
            </div>

            <div class="p-col-12 p-lg-4">
                <div class="card">
                    <span class="title"> 24h Low / High </span>
                    <span class="detail"> 24h AVWP <br /> Low / High Price </span>
                    <span class="span_value"> $ {{h24_min}} / $ {{h24_max}} </span>
                </div>
            </div>

            <div class="p-col-12 p-lg-4">
                <div class="card">
                    <span class="title"> Circulatting / Total Supply </span>
                    <span class="detail"> Publicly available and <br />circulating in the market </span>
                    <span class="span_value"> {{circulating_supply}} / <br /> {{total_supply}} </span>
                </div>
            </div>

            <div class="p-col-12 p-lg-4">
                <div class="card">
                    <span class="title"> All Time Low </span>
                    <span class="detail"> All time lowest price </span>
                    <span class="span_value"> $ 123 </span>
                </div>
            </div>

            <div class="p-col-12 p-lg-4">
                <div class="card">
                    <span class="title"> All Time High </span>
                    <span class="detail"> All time highest price </span>
                    <span class="span_value"> $ 19000 </span>
                </div>
            </div>


        </div>
    </div>
</template>

<script>
    import getSymbolFromCurrency from "currency-symbol-map";
    import { axios } from '../../plugins/axios';

    export default {
        data () {
            return {
                day_price: null,
                post: {},
                axios: axios,

                h24_min: null,
                h24_max: null,

                total_supply: null,
                circulating_supply: null
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
            day_price_value(){
                this.post.start = (new Date().setHours(0,0,0,0) + new Date().getTimezoneOffset() * - 1 * 60 * 1000) / 1000;
                this.post.end = (new Date().setHours(0,0,0,0) + new Date().getTimezoneOffset() * - 1 * 60 * 1000 + 86400000) / 1000;

                let url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/asset/value/" + this.asset + "/" + this.post.to + "/" + this.post.start + "/" + this.post.end;

                this.axios.get(url).then(response => (this.update_value(response.data)));
            },

            update_value(data){
                this.day_price = data['data'][0][1].toFixed(3);
            },

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

                this.h24_min = Math.min(...values).toFixed(3);
                this.h24_max = Math.max(...values).toFixed(3);
            },

            supply(){
                let url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/asset/info/" + this.asset;
                this.axios.get(url).then(response => (this.update_supply(response.data)));
            },

            update_supply(data){
                this.total_supply = data['data']['total_supply'];
                this.circulating_supply = data['data']['circulating_supply'];

            }


        },

        mounted() {
            this.asset = this.$route.name;
            this.post.to = 'usd';

            this.supply();
            this.day_price_value();
            this.low_high_24h();
        }
    }
</script>

<style lang="scss">
    .p-lg-4 {
        padding: 0.5rem !important;
        width: 33.3%;
    }
</style>
