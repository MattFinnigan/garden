import { createApp } from 'vue'
import store from './store'
import { createWebHistory, createRouter } from 'vue-router'
import App from './components/App.vue'
import vClickOutside from 'v-click-outside'

import Landing from './components/pages/Landing.vue'
import Crops from './components/pages/Crops.vue'
import Plants from './components/pages/Plants.vue'
import Locations from './components/pages/Locations.vue'
import Crop from './components/pages/Crop.vue'

import Form from './components/forms/common/Form.vue'
import Button from './components/common/Button.vue'
import Modal from './components/common/Modal.vue'
import Select from './components/forms/common/Select.vue'
import Input from './components/forms/common/Input.vue'
import Link from './components/common/Link.vue'
import Display from './components/forms/common/Display.vue'
import Table from './components/common/Table.vue'
import Icon from './components/common/Icon.vue'
import Images from './components/forms/common/Images.vue'
import Checkbox from './components/forms/common/Checkbox.vue'

const routes = [
  { path: '/', name: '', component: Landing },
  { path: '/crops', name: 'crops', component: Crops },
  { path: '/plants', name: 'plants', component: Plants },
  { path: '/locations-beds', name: 'locations', component: Locations },
  { path: '/crop/:id', name: 'crop', component: Crop },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

const app = createApp(App).use(router)
app.use(store)
app.component('Form', Form)
app.component('Button', Button)
app.component('Modal', Modal)
app.component('Select', Select)
app.component('Input', Input)
app.component('Link', Link)
app.component('Display', Display)
app.component('Table', Table)
app.component('Icon', Icon)
app.component('Images', Images)
app.component('Checkbox', Checkbox)
app.use(vClickOutside)
app.mount('#app')