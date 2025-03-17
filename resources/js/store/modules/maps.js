
const state = () => ({
  list: [],
  month: null,
})

const getters = {}

const actions = {}

const mutations = {
  setMaps (state, maps) {
    state.list = maps
  },
  setMapMonth (state, date) {
    state.month = date
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}