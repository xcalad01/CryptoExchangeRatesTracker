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
                path: "kraken",
                name: "kraken",
                component: Dashboard2,
                meta: { title: "kraken" },
            },
            {
                path: "gdax",
                name: "gdax",
                component: Dashboard2,
                meta: { title: "gdax" },
            },
            {
                path: "bitfinex",
                name: "bitfinex",
                component: Dashboard2,
                meta: { title: "bitfinex" },
            },
            {
                path: "gemini",
                name: "gemini",
                component: Dashboard2,
                meta: { title: "gemini" },
            },
            {
                path: "bitstamp",
                name: "bitstamp",
                component: Dashboard2,
                meta: { title: "bitstamp" },
            },
            {
                path: "poloniex",
                name: "poloniex",
                component: Dashboard2,
                meta: { title: "poloniex" },

            },
            {
                path: "binance",
                name: "binance",
                component: Dashboard2,
                meta: { title: "binance" },

            },
            {
                path: "bittrex",
                name: "bittrex",
                component: Dashboard2,
                meta: { title: "bittrex" },

            },
            {
                path: "hitbtc",
                name: "hitbtc",
                component: Dashboard2,
                meta: { title: "hitbtc" },

            },
            {
                path: "bitbay",
                name: "bitbay",
                component: Dashboard2,
                meta: { title: "bitbay" },

            },
            {
                path: "okcoin",
                name: "okcoin",
                component: Dashboard2,
                meta: { title: "okcoin" },

            },
            {
                path: "okex",
                name: "okex",
                component: Dashboard2,
                meta: { title: "okex" },

            }

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
