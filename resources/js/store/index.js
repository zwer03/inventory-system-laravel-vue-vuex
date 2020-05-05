import Vue from 'vue'
import Vuex from 'vuex'
import setup from './modules/setup'
import auth from './modules/auth'

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    setup,
    auth,
  },
})