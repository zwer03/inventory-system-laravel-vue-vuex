/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import router from './routes';
import vuetify from './vuetify';
import store from './store';
import Application from './Application';
/* Services */
import {
    errorService
} from './services/error.service';

new Vue({
    el: '#app',
    router,
    vuetify,
    store,
    components: {
        Application
    },
    created() {
        const hasUserData = localStorage.getItem('user')
        if (hasUserData) {
            const userData = JSON.parse(hasUserData)
            this.$store.commit('authentication/SET_USER_DATA', userData)
            // this.$store.dispatch("authentication/getPermissions");
        }

        axios.interceptors.response.use(
            response => response,

            error => {
                // this.$bvToast.toast(errorService.handleError(error), {
                //     title: 'Error Message',
                //     autoHideDelay: 5000,
                //     variant: 'danger',
                //     toaster: 'b-toaster-top-center',
                //     solid: true
                // })
                this.snackBar = true;
                this.snackBarColor = "error";
                this.snackBarTxt = errorService.handleError(error);
                return Promise.reject(error)
            }
        )

    },

    template: '<Application/>'
});
