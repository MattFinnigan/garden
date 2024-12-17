
const state = () => ({
  list: [],
  current: null,
  mode: 'edit',
  selectCropMode: false,
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
  },
  setCurrentCropDaysToHarvest (state, days) {
    state.current.days_to_harvest = days
  },
  setSelectCropMode (state, mode) {
    state.selectCropMode = mode
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}