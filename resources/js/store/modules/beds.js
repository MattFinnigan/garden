
const state = () => ({
  current: null
})

const getters = {}

const actions = {}

const mutations = {
  setCurrentBed (state, bed) {
    state.current = bed
  },
  setCurrentBedName (state, name) {
    state.current.name = name
  },
  setCurrentBedWidth (state, width) {
    state.current.w = width
  },
  setCurrentBedLength (state, len) {
    state.current.l = len
  },
  setCurrentBedDescription (state, description) {
    state.current.description = description
  },
  setCurrentBedImages (state, image) {
    state.current.images = image
  },
  addImageToCurrentBed (state, image) {
    state.current.images.push(image)
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}