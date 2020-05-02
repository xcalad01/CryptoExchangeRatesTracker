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
                    <span class="title"> 7d Low / High </span>
                    <span class="detail"> 7d AVWP <br /> Low / High Price </span>
                    <span class="span_value"> $ 7023 / $ 7023 </span>
                </div>
            </div>

            <div class="p-col-12 p-lg-4">
                <div class="card">
                    <span class="title"> 24h Trading Volume </span>
                    <span class="detail">  <br /> 24h Trading Volume </span>
                    <span class="span_value"> $ 7023 </span>
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
                h24_max: null
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
                this.post.start = (new Date().setHours(0,0,0,0) + new Date().getTimezoneOffset() * - 1 * 60 * 1000) / 1000;
                this.post.end = (new Date().setHours(0,0,0,0) + new Date().getTimezoneOffset() * - 1 * 60 * 1000 + 86400000) / 1000;

                let url = "http://" + process.env.MIX_API_URL + ":" + process.env.MIX_API_PORT + "/api/crypto/historical/asset/value/" + this.asset + "/" + this.post.to + "/" + this.post.start + "/" + this.post.end;

                this.axios.get(url).then(response => (this.update_24h_value(response.data)));
            },

            update_24h_value(data){
                this.h24_min = min(data['data'][0]).toFixed(3);
                this.h24_max = max(data['data'][0]).toFixed(3);
            }
        },

        mounted() {
            this.asset = this.$route.name;
            this.post.to = 'usd';

            this.day_price_value();
            this.low_high_24h();
        }
    }
</script>

<style lang="scss">
    .p-lg-4 {
        padding: 0.5rem !important;
        width: 25.0%;
    }
</style>
