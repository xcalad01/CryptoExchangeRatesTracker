/*
 =========================================================
 * Vue Black Dashboard - v1.1.0
 =========================================================

 * Product Page: https://www.creative-tim.com/product/black-dashboard
 * Copyright 2018 Creative Tim (http://www.creative-tim.com)

 =========================================================

 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

 */
import Vue from "vue";
import VueRouter from "vue-router";
import RouterPrefetch from 'vue-router-prefetch'
import App from "./App.vue";
// TIP: change to import router from "./router/starterRouter"; to start with a clean layout
import router from "./router";

import BlackDashboard from "../vue-black-dashboard-master/src/plugins/blackDashboard";
import i18n from "./i18n"
import '../vue-black-dashboard-master/src/registerServiceWorker'

import VueAxios from '../vue-black-dashboard-master/src/plugins/axios';

import { Datetime } from 'vue-datetime';
import 'vue-datetime/dist/vue-datetime.css';


import Cryptoicon from 'vue-cryptoicon';
import {Btc, Eth, Xrp, Ltc, Bch, Eos, Usdt, Ada, Trx, Zec, Xlm, Xmr, Etc, Xtz, Xem, Doge, Btg, Qtum, Zrx, Neo, Dash} from 'vue-cryptoicon/src/icons';
Cryptoicon.add([Btc, Eth, Xrp, Ltc, Bch, Eos, Usdt, Ada, Trx, Zec, Xlm, Xmr, Etc, Xtz, Xem, Doge, Btg, Qtum, Zrx, Neo, Dash]);


import ElementUI from 'element-ui';

Vue.use(BlackDashboard);
Vue.use(VueRouter);
Vue.use(RouterPrefetch);
Vue.use(VueAxios);
Vue.use(Datetime);
Vue.use(ElementUI);
Vue.use(Cryptoicon);

/* eslint-disable no-new */
new Vue({
    router,
    i18n,
    render: h => h(App)
}).$mount("#app");
