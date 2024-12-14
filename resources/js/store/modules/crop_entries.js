
const state = () => ({
  list: [],
  current: null
})

const getters = {}

const actions = {}

const mutations = {
  setCurrentCropEntry (state, entry) {
    state.current = entry
  },
  setCurrentCropEntryLocation (state, id) {
    state.current.location_id = id
  },
  setCurrentCropEntryBed (state, id) {
    state.current.bed_id = id
  },
  setCurrentCropEntryAction (state, action) {
    state.current.action = action
  },
  setCurrentCropEntryStage (state, stage) {
    state.current.stage = stage
  },
  setCurrentCropEntryQty (state, qty) {
    state.current.qty = qty
  },
  setCurrentCropEntryNotes (state, notes) {
    state.current.notes = notes
  },
  setCurrentCropEntryImage (state, image) {
    state.current.image = image
  },
  setCurrentCropEntryArea (state, area) {
    state.current.area = area
  },
  setCurrentCropEntryDatetimestamp (state, date) {
    state.current.datetimestamp = date
  },
  setCurrentCropEntryPlantPos (state, position) {
    state.current.plant_pos = position
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}