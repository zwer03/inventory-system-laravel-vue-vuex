// initial state
const state = {
  user: null
}

// getters
const getters = {
  isLogged: state => !!state.user
}

// actions
const actions = {
  login({ commit }, credentials) {
    return axios
      .post('/login', credentials)
      .then(({ data }) => {
        if(data.error)
          alert(data.error)
        else
          commit('setUserData', data)
      })
  },

  logout({ commit }) {
    commit('clearUserData')
  }
}

// mutations
const mutations = {
  setUserData(state, userData) {
    state.user = userData
    localStorage.setItem('user', JSON.stringify(userData))

    axios.defaults.headers.common.Authorization = `Bearer ${userData.access_token}`
  },

  clearUserData() {
    localStorage.removeItem('user')
    // location.reload()
    location.href = "/login";
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}