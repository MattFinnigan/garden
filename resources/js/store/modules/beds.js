
const state = () => ({
  current: null,
  list: []
})

const getters = {}

const actions = {}

const mutations = {
  setBeds (state, beds) {
    state.list = beds
  },
  setCurrentBed (state, bed) {
    state.current = bed
  },
  setCurrentBedName (state, name) {
    state.current.name = name
  },
  setCurrentBedWidth (state, width) {
    state.current.width = width
  },
  setCurrentBedHeight (state, len) {
    state.current.height = len
  },
  setCurrentBedDescription (state, description) {
    state.current.description = description
  },
  setCurrentBedImages (state, image) {
    state.current.images = image
  },
  addImageToCurrentBed (state, image) {
    state.current.images.push(image)
  },
  removeImageFromCurrentBed (state, index) {
    state.current.images.splice(index, 1)
  },
  setCurrentBedDeactivated (state, deactivated) {
    state.current.deactivated = deactivated
  }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}