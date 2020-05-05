/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import router from './routes';
import vuetify from './vuetify';
import store from './store';
import index from './views/index'
const app = new Vue({
    el: '#app',
    router,
    vuetify,
    store,
    components: {index},
    created() {
        const userInfo = localStorage.getItem('user')
        if (userInfo) {
            const userData = JSON.parse(userInfo)
            this.$store.commit('auth/setUserData', userData)
        }
        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response.status === 401) {
                    this.$store.dispatch('auth/logout')
                }
                return Promise.reject(error)
            }
        )
    },
});
