// initial state
const state = {
  fresh: false,
  dashboard: [],
  warehouses: [],
  storages: [],
  companies: [],
  products: [],
  inventories: [],
  transaction: [],
  at_critical_level_products: []
}

// getters
const getters = {
  at_critical_level_products: state => state.at_critical_level_products
}

// actions
const actions = {
  getDashboard({ state, commit }) {
    commit('getDashboard')
  },
  setWarehouse({ state, commit }, warehouses) {
    commit('setWarehouse', warehouses)
  },
  setStorage({ state, commit }, storages) {
    commit('setStorage', storages)
  },
  setCompany({ state, commit }, companies) {
    commit('setCompany', companies)
  },
  setProduct({ state, commit }, products) {
    commit('setProduct', products)
  },
  setInventory({ state, commit }, inventory) {
    commit('setInventory', inventory)
  },
  updateInventory({ state, commit }, inventory) {
    commit('updateInventory', inventory)
  },
  deleteInventory({ state, commit }, index) {
    commit('deleteInventory', index)
  },
  resetInventory({ state, commit }) {
    commit('resetInventory')
  },
  updateProduct({ state, commit }, products) {
    commit('updateProduct', products)
  },
  setTransaction({ state, commit }, transaction) {
    commit('setTransaction', transaction)
  },
  getCriticalLevelProducts({ state, commit }) {
    commit('getCriticalLevelProducts')
  },
}

// mutations
const mutations = {
  getDashboard(state) {
    axios.get("/warehouses/get")
    .then(response => {
      state.warehouses = response.data
    })
    .catch(error => {alert(error)})

    axios.get("/transactions/get")
    .then(response => {
      state.dashboard = response.data.transactions
      if (!response.data.notFresh) // Transaction has no data
        state.fresh = true
    })
    .catch(error => {alert(error)})
  },
  setWarehouse(state, warehouses) {
    state.warehouses = warehouses
  },
  setStorage(state, storages) {
    state.storages = storages
  },
  setCompany(state, companies) {
    state.companies = companies
  },
  setProduct(state, products) {
    state.products = products
  },
  setInventory(state, inventory) {
    state.inventories.push(inventory)
  },
  updateInventory(state, inventory) {
    Object.assign(inventory.find, inventory.replace)
  },
  deleteInventory(state, index) {
    state.inventories.splice(index, 1)
  },
  resetInventory(state) {
    state.inventories = []
  },
  updateProduct(state, products) {
    state.products.unshift(products)
  },
  setTransaction(state, transaction) {
    state.transaction = transaction
    let inventories = []
    if (transaction.transaction_details.length) {
      transaction.transaction_details.forEach(value => {
        // {"id":2,"product":"Barcode Scanner","location":"WH1 1ST FLOOR","storage_id":1,"quantity":0}
        let inventory = {}
        inventory.id = value.product_id
        inventory.product = value.product.name
        inventory.location = value.storage.name
        inventory.old_storage_id = value.old_storage_id
        inventory.storage_id = value.storage_id
        inventory.quantity = value.quantity
        inventory.unit = value.product.unit
        inventories.push(inventory)
      })
    }
    state.inventories = inventories
  },
  getCriticalLevelProducts(state){
    axios.get("/inventories/getCriticalLevelProducts")
      .then(response => {
        state.at_critical_level_products = response.data
      })
      .catch(error => {alert(error)})
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}