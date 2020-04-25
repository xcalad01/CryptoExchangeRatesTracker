import DashboardLayout from "../../vue-black-dashboard-master/src/layout/dashboard/DashboardLayout.vue";
// GeneralViews
import NotFound from "../../vue-black-dashboard-master/src/pages/NotFoundPage.vue";

// Admin pages
const Dashboard = () => import(/* webpackChunkName: "dashboard" */"../../vue-black-dashboard-master/src/pages/Dashboard.vue");
const Dashboard2 = () => import(/* webpackChunkName: "dashboard" */"../../vue-black-dashboard-master/src/pages/Dashboard2.vue");

const routes = [
    {
        path: "/",
        component: DashboardLayout,
        redirect: "/exchange/gdax",
    },
    {
        path: "/exchange",
        component: DashboardLayout,
        redirect: "/exchange/gdax",
        meta: { title: "exchanges" },
        children: [
            {
                path: "kraken",
                name: "kraken",
                component: Dashboard,
                meta: { title: "kraken"},
            },
            {
                path: "gdax",
                name: "gdax",
                component: Dashboard,
                meta: { title: "gdax" },
            },
            {
                path: "bitfinex",
                name: "bitfinex",
                component: Dashboard,
                meta: { title: "bitfinex" },
            },
            {
                path: "gemini",
                name: "gemini",
                component: Dashboard,
                meta: { title: "gemini" },
            },
            {
                path: "bitstamp",
                name: "bitstamp",
                component: Dashboard,
                meta: { title: "bitstamp" },
            },
            {
                path: "poloniex",
                name: "poloniex",
                component: Dashboard,
                meta: { title: "poloniex" },

            },
            {
                path: "binance",
                name: "binance",
                component: Dashboard,
                meta: { title: "binance" },

            },
            {
                path: "bittrex",
                name: "bittrex",
                component: Dashboard,
                meta: { title: "bittrex" },

            },
            {
                path: "hitbtc",
                name: "hitbtc",
                component: Dashboard,
                meta: { title: "hitbtc" },

            },
            {
                path: "bitbay",
                name: "bitbay",
                component: Dashboard,
                meta: { title: "bitbay" },

            },
            {
                path: "okcoin",
                name: "okcoin",
                component: Dashboard,
                meta: { title: "okcoin" },

            },
            {
                path: "okex",
                name: "okex",
                component: Dashboard,
                meta: { title: "okex" },

            }

        ]
    },
    {
        path: "/asset",
        component: DashboardLayout,
        redirect: "/exchange/gdax",
        meta: { title: "assets" },
        children: [
            {
                path: "btc",
                name: "btc",
                component: Dashboard2,
                meta: { title: "Bitcoin" },
            },
            {
                path: "trx",
                name: "trx",
                component: Dashboard2,
                meta: { title: "Tron" },
            },
            {
                path: "xmr",
                name: "xmr",
                component: Dashboard2,
                meta: { title: "Monero" },
            },
            {
                path: "bch",
                name: "bch",
                component: Dashboard2,
                meta: { title: "Bitcoin Cash" },
            },
            {
                path: "ltc",
                name: "ltc",
                component: Dashboard2,
                meta: { title: "Litecoin" },
            },
            {
                path: "eos",
                name: "eos",
                component: Dashboard2,
                meta: { title: "EOS" },

            },
            {
                path: "eth",
                name: "eth",
                component: Dashboard2,
                meta: { title: "Ethereum" },

            },
            {
                path: "xrp",
                name: "xrp",
                component: Dashboard2,
                meta: { title: "XRP" },

            },
            {
                path: "ada",
                name: "ada",
                component: Dashboard2,
                meta: { title: "Cardano" },

            },
            {
                path: "xlm",
                name: "xlm",
                component: Dashboard2,
                meta: { title: "Stellar" },

            },
            {
                path: "xtz",
                name: "xtz",
                component: Dashboard2,
                meta: { title: "Tezos" },

            },
            {
                path: "neo",
                name: "neo",
                component: Dashboard2,
                meta: { title: "Neo" },

            },
            {
                path: "dash",
                name: "dash",
                component: Dashboard2,
                meta: { title: "Dash" },

            },
            {
                path: "etc",
                name: "etc",
                component: Dashboard2,
                meta: { title: "Ethereum Classis" },

            },
            {
                path: "zec",
                name: "zec",
                component: Dashboard2,
                meta: { title: "ZCash" },

            },
            {
                path: "xem",
                name: "xem",
                component: Dashboard2,
                meta: { title: "Nem" },

            },
            {
                path: "doge",
                name: "doge",
                component: Dashboard2,
                meta: { title: "Dogecoin" },

            },
            {
                path: "qtum",
                name: "qtum",
                component: Dashboard2,
                meta: { title: "Qtum" },

            },
            {
                path: "btg",
                name: "btg",

                component: Dashboard2,
                meta: { title: "Bitcoin Gold" },

            },
            {
                path: "zrx",
                name: "xrz",
                component: Dashboard2,
                meta: { title: "0x" },

            },
            {
                path: "usdt",
                name: "usdt",
                component: Dashboard2,
                meta: { title: "Tether" },

            },

        ]
    },
    { path: "*", component: NotFound },
];

/**
 * Asynchronously load view (Webpack Lazy loading compatible)
 * The specified component must be inside the Views folder
 * @param  {string} name  the filename (basename) of the view to load.
 function view(name) {
   var res= require('../components/Dashboard/Views/' + name + '.vue');
   return res;
};**/

export default routes;
