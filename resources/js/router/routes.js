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
        redirect: "/dashboard",
        children: [
            {
                path: "dashboard",
                name: "dashboard",
                component: Dashboard
            },
            {
                path: "profile",
                name: "profile",
                component: Profile
            },
            {
                path: "notifications",
                name: "notifications",
                component: Notifications
            },
            {
                path: "icons",
                name: "icons",
                component: Icons
            },
            {
                path: "maps",
                name: "maps",
                component: Maps
            },
            {
                path: "typography",
                name: "typography",
                component: Typography
            },
            {
                path: "table-list",
                name: "table-list",
                component: TableList
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
