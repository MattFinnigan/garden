
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
  setCurrentCropEntrySpacingX (state, value) {
    state.current.spacing_x = value
  },
  setCurrentCropEntrySpacingY (state, value) {
    state.current.spacing_y = value
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