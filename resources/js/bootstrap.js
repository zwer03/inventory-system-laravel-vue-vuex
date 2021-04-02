import Vue from 'vue';
import axios from 'axios';
import VueRouter from 'vue-router';
import VuePageTransition from 'vue-page-transition';
// import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
// import 'bootstrap/dist/css/bootstrap.css'
// import 'bootstrap-vue/dist/bootstrap-vue.css'
// import PortalVue from 'portal-vue'

import NProgress from '../../node_modules/nprogress/nprogress.js';
import '../../node_modules/nprogress/nprogress.css';
import Form from './utilities/Form';

// import Echo from 'laravel-echo';
/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
window.Vue = Vue;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = `${window.location.hostname}/api`;

window.NProgress = NProgress;
window.Form = Form;
window.Pusher = require('pusher-js');

Vue.use(VueRouter);
Vue.use(VuePageTransition);
// Vue.use(BootstrapVue);
// Vue.use(IconsPlugin);
// Vue.use(PortalVue);

// window.Echo = new Echo({
//     broadcaster: "pusher",
//     key: process.env.MIX_PUSHER_APP_KEY,
//     wsHost: window.location.hostname,
//     wsPort: 6001,
//     forceTLS: false,
//     disableStats: true,
//   });




