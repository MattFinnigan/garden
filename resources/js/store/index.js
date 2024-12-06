import { createStore, createLogger } from 'vuex'
import plants from './modules/plants'
import crops from './modules/crops'
import locations from './modules/locations'
import beds from './modules/beds'
import crop_entries from './modules/crop_entries'

const debug = process.env.NODE_ENV !== 'production'

export default createStore({
  modules: {
    plants,
    crops,
    locations,
    beds,
    crop_entries
  },
  strict: debug,
  plugins: debug ? [createLogger()] : []
})