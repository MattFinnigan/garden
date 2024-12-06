
const state = () => ({
  list: [],
  current: null
})

const getters = {}

const actions = {}

const mutations = {
  setCurrentPlant (state, plant) {
    state.current = plant
  },
  setPlants (state, plants) {
    state.list = plants
  },
  setCurrentPlantName (state, name) {
    state.current.name = name
  },
  setCurrentPlantDescription (state, description) {
    state.current.description = description
  },
  setCurrentPlantVariety (state, variety) {
    state.current.variety = variety
  },
  setCurrentPlantImage (state, image) {
    state.current.image = image
  },
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}