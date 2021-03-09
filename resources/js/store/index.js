import Vue from 'vue'
import Vuex from 'vuex'
import setup from './modules/setup'
import { authentication } from './modules/authentication.module'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    setup,
    authentication,
  },
})