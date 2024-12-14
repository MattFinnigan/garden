
const state = () => ({
  list: [],
  date: null,
})

const getters = {}

const actions = {}

const mutations = {
  setMaps (state, maps) {
    state.list = maps
  },
  setMapDate (state, date) {
    state.date = date
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}