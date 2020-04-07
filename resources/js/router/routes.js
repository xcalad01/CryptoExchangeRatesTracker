import DashboardLayout from "../../vue-black-dashboard-master/src/layout/dashboard/DashboardLayout.vue";
// GeneralViews
import NotFound from "../../vue-black-dashboard-master/src/pages/NotFoundPage.vue";

// Admin pages
const Dashboard = () => import(/* webpackChunkName: "dashboard" */"../../vue-black-dashboard-master/src/pages/Dashboard.vue");
const Profile = () => import(/* webpackChunkName: "common" */ "../../vue-black-dashboard-master/src/pages/Profile.vue");
const Notifications = () => import(/* webpackChunkName: "common" */"../../vue-black-dashboard-master/src/pages/Notifications.vue");
const Icons = () => import(/* webpackChunkName: "common" */ "../../vue-black-dashboard-master/src/pages/Icons.vue");
const Maps = () => import(/* webpackChunkName: "common" */ "../../vue-black-dashboard-master/src/pages/Maps.vue");
const Typography = () => import(/* webpackChunkName: "common" */ "../../vue-black-dashboard-master/src/pages/Typography.vue");
const TableList = () => import(/* webpackChunkName: "common" */ "../../vue-black-dashboard-master/src/pages/TableList.vue");

const routes = [
    {
        path: "/",
        component: DashboardLayout,
        redirect: "gdax",
        children: [
            {
                path: "kraken",
                name: "kraken",
                component: Dashboard
            },
            {
                path: "gdax",
                name: "gdax",
                component: Dashboard
            },
            {
                path: "bitfinex",
                name: "bitfinex",
                component: Dashboard
            },
            {
                path: "gemini",
                name: "gemini",
                component: Dashboard
            },
            {
                path: "bitstamp",
                name: "bitstamp",
                component: Dashboard
            },
            {
                path: "poloniex",
                name: "poloniex",
                component: Dashboard
            },
            {
                path: "binance",
                name: "binance",
                component: Dashboard
            },
            {
                path: "bittrex",
                name: "bittrex",
                component: Dashboard
            },
            {
                path: "hitbtc",
                name: "hitbtc",
                component: Dashboard
            },
            {
                path: "bitbay",
                name: "bitbay",
                component: Dashboard
            },
            {
                path: "okcoin",
                name: "okcoin",
                component: Dashboard
            },
            {
                path: "okex",
                name: "okex",
                component: Dashboard
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
