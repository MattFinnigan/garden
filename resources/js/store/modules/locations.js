
const state = () => ({
  list: [],
  current: null
})

const getters = {}

const actions = {}

const mutations = {
  setCurrentLocation (state, location) {
    state.current = location
  },
  setCurrentLocationName (state, name) {
    state.current.name = name
  },
  setCurrentLocationsBeds (state, beds) {
    state.current.beds = beds
  },
  setLocations (state, locations) {
    state.list = locations
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}