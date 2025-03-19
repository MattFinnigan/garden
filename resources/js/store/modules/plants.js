
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
  setCurrentPlantDaysToHarvest (state, daysToHarvest) {
    state.current.days_to_harvest = daysToHarvest
  },
  setCurrentPlantSowFrom (state, sowFrom) {
    state.current.sow_from = sowFrom
  },
  setCurrentPlantSowTo (state, sowTo) {
    state.current.sow_to = sowTo
  },
  setCurrentPlantSpacing (state, spacing) {
    state.current.spacing = spacing
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}