
const state = () => ({
  list: []
})

const getters = {}

const actions = {}

const mutations = {
  setMaps (state, maps) {
    state.list = maps
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}