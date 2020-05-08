import DashboardLayout from "../components/layout/dashboard/DashboardLayout.vue";
import NotFound from "../pages/NotFoundPage.vue";

const Exchange_dashboard = () => import("../pages/Exchange_dashboard.vue");
const Assets_dashboard = () => import("../pages/Assets_dashboard.vue");

const routes = [
    {
        path: "/",
        redirect: "/exchange/gdax",
        hidden: true
    },
    {
        path: "/exchange",
        component: DashboardLayout,
        redirect: "/exchange/gdax",
        name: "Exchanges",
        meta: { title: "exchanges" },
        children: [
            {
                path: "kraken",
                name: "kraken",
                component: Exchange_dashboard,
                meta: { title: "kraken"},
            },
            {
                path: "gdax",
                name: "gdax",
                component: Exchange_dashboard,
                meta: { title: "gdax" },
            },
            {
                path: "bitfinex",
                name: "bitfinex",
                component: Exchange_dashboard,
                meta: { title: "bitfinex" },
            },
            {
                path: "gemini",
                name: "gemini",
                component: Exchange_dashboard,
                meta: { title: "gemini" },
            },
            {
                path: "bitstamp",
                name: "bitstamp",
                component: Exchange_dashboard,
                meta: { title: "bitstamp" },
            },
            {
                path: "poloniex",
                name: "poloniex",
                component: Exchange_dashboard,
                meta: { title: "poloniex" },

            },
            {
                path: "binance",
                name: "binance",
                component: Exchange_dashboard,
                meta: { title: "binance" },

            },
            {
                path: "bittrex",
                name: "bittrex",
                component: Exchange_dashboard,
                meta: { title: "bittrex" },

            },
            {
                path: "hitbtc",
                name: "hitbtc",
                component: Exchange_dashboard,
                meta: { title: "hitbtc" },

            },
            {
                path: "bitbay",
                name: "bitbay",
                component: Exchange_dashboard,
                meta: { title: "bitbay" },

            },
            {
                path: "okcoin",
                name: "okcoin",
                component: Exchange_dashboard,
                meta: { title: "okcoin" },

            },
            {
                path: "okex",
                name: "okex",
                component: Exchange_dashboard,
                meta: { title: "okex" },

            },
            {
                path: "cex",
                name: "CEX.IO",
                component: Exchange_dashboard,
                meta: { title: "cex"},
            },

        ]
    },
    {
        path: "/asset",
        component: DashboardLayout,
        redirect: "/exchange/gdax",
        name: "Assets",
        meta: { title: "assets" },
        children: [
            {
                path: "btc",
                name: "btc",
                component: Assets_dashboard,
                meta: { title: "Bitcoin" },
            },
            {
                path: "trx",
                name: "trx",
                component: Assets_dashboard,
                meta: { title: "Tron" },
            },
            {
                path: "xmr",
                name: "xmr",
                component: Assets_dashboard,
                meta: { title: "Monero" },
            },
            {
                path: "bch",
                name: "bch",
                component: Assets_dashboard,
                meta: { title: "Bitcoin Cash" },
            },
            {
                path: "ltc",
                name: "ltc",
                component: Assets_dashboard,
                meta: { title: "Litecoin" },
            },
            {
                path: "eos",
                name: "eos",
                component: Assets_dashboard,
                meta: { title: "EOS" },

            },
            {
                path: "eth",
                name: "eth",
                component: Assets_dashboard,
                meta: { title: "Ethereum" },

            },
            {
                path: "xrp",
                name: "xrp",
                component: Assets_dashboard,
                meta: { title: "XRP" },

            },
            {
                path: "ada",
                name: "ada",
                component: Assets_dashboard,
                meta: { title: "Cardano" },

            },
            {
                path: "xlm",
                name: "xlm",
                component: Assets_dashboard,
                meta: { title: "Stellar" },

            },
            {
                path: "xtz",
                name: "xtz",
                component: Assets_dashboard,
                meta: { title: "Tezos" },

            },
            {
                path: "neo",
                name: "neo",
                component: Assets_dashboard,
                meta: { title: "Neo" },

            },
            {
                path: "dash",
                name: "dash",
                component: Assets_dashboard,
                meta: { title: "Dash" },

            },
            {
                path: "etc",
                name: "etc",
                component: Assets_dashboard,
                meta: { title: "Ethereum Classis" },

            },
            {
                path: "zec",
                name: "zec",
                component: Assets_dashboard,
                meta: { title: "ZCash" },

            },
            {
                path: "xem",
                name: "xem",
                component: Assets_dashboard,
                meta: { title: "Nem" },

            },
            {
                path: "doge",
                name: "doge",
                component: Assets_dashboard,
                meta: { title: "Dogecoin" },

            },
            {
                path: "qtum",
                name: "qtum",
                component: Assets_dashboard,
                meta: { title: "Qtum" },

            },
            {
                path: "btg",
                name: "btg",

                component: Assets_dashboard,
                meta: { title: "Bitcoin Gold" },

            },
            {
                path: "zrx",
                name: "xrz",
                component: Assets_dashboard,
                meta: { title: "0x" },

            },
            {
                path: "usdt",
                name: "usdt",
                component: Assets_dashboard,
                meta: { title: "Tether" },

            },

        ]
    },
    { path: "*", component: NotFound, hidden: true },
];

/**
 * Asynchronously load view (Webpack Lazy loading compatible)
 * The specified component must be inside the Views folder
 * @param  {string} name  the filename (basename) of the view to load.
 function view(name) {
   var res= require('../components/Exchange_dashboard/Views/' + name + '.vue');
   return res;
};**/

export default routes;
