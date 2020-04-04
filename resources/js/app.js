
require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import VueAxios from 'vue-axios';
import axios from 'axios';

import App from './App.vue';


Vue.use(VueAxios, axios);

import FirstComponent from './components/Component.vue';
import DashboardComp from './components/Dashboard';

const routes = [
    {
        name: 'first_component',
        path: '/',
        component: FirstComponent,
    },
    {
        name: 'dashboard',
        path: '/dashboard',
        component: DashboardComp
    }
];

const router = new VueRouter({ mode: 'history', routes: routes});
const app = new Vue(Vue.util.extend({ router }, App)).$mount('#app');
