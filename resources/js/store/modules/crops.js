
const state = () => ({
  list: [],
  current: null,
  mode: 'view'
})

const getters = {}

const actions = {}

const mutations = {
  setCurrentCrop (state, crop) {
    state.current = crop
  },
  setCrops (state, crops) {
    state.list = crops
  },
  setCurrentCropPlant (state, id) {
    state.current.plant_id = id
  },
  setCurrentCropEntries (state, entries) {
    state.current.crop_entries = entries
  },
  setMode (state, mode) {
    state.mode = mode
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}