import { createStore, createLogger } from 'vuex'
import plants from './modules/plants'
import crops from './modules/crops'
import beds from './modules/beds'
import crop_entries from './modules/crop_entries'
import maps from './modules/maps'

const debug = process.env.NODE_ENV !== 'production'

export default createStore({
  modules: {
    plants,
    crops,
    beds,
    crop_entries,
    maps
  },
  strict: debug,
  plugins: debug ? [createLogger()] : []
})