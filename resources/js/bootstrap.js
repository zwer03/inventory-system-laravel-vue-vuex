import Vue from 'vue';
import axios from 'axios';
import VueRouter from 'vue-router';
import VuePageTransition from 'vue-page-transition';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import PortalVue from 'portal-vue'

import NProgress from '../../node_modules/nprogress/nprogress.js';
import '../../node_modules/nprogress/nprogress.css';
import Form from './utilities/Form';
/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
window.Vue = Vue;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.baseURL = process.env.MIX_SENTRY_DSN_PUBLIC+'/api';

window.NProgress = NProgress;
window.Form = Form;
Vue.use(VueRouter);
Vue.use(VuePageTransition);
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(PortalVue);


