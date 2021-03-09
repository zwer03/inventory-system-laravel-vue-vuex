export const authentication = {
    namespaced: true,
    state: { user: null, roles: [], permissions: [] },
    actions: {
        async login({ commit, dispatch }, userData) {
            localStorage.setItem('user', JSON.stringify(userData));
            await commit('SET_USER_DATA', userData);
            // await dispatch('getPermissions');
        },
        logout({ commit }) {
            localStorage.removeItem('user');
            // localStorage.removeItem('permission');
            commit('RESET_USER_DATA');
        },
        // async getPermissions({ commit }) {
        //     const response = await axios.get('role_permission');
        //     commit('SET_ROLE_PERMISSION', response.data);
        //     localStorage.setItem('permission', JSON.stringify(response.data.permissions));
        //     // SecureLS.set('permission', response.data);
        // },
    },
    mutations: {
        SET_USER_DATA(state, user) {
            state.user = user;
        },
        // SET_ROLE_PERMISSION(state, data) {
        //     state.roles = data.roles;
        //     state.permissions = data.permissions;
        // },
        RESET_USER_DATA(state) {
            state.user = null;
            state.roles = [];
            state.permissions = [];
        }
    }
}