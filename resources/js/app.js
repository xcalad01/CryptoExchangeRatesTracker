
require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import VueAxios from 'vue-axios';
import axios from 'axios';

import App from './App.vue';
import { Datetime } from 'vue-datetime';
import 'vue-datetime/dist/vue-datetime.css'


Vue.use(VueAxios, axios);

import FirstComponent from './components/Component.vue';

const routes = [
    {
        name: 'first_component',
        path: '/',
        component: FirstComponent,
    }
];

const router = new VueRouter({ mode: 'history', routes: routes});
const app = new Vue(Vue.util.extend({ router }, App, Datetime)).$mount('#app');
