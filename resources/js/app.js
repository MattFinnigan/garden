import { createApp } from 'vue'
import { createWebHistory, createRouter } from 'vue-router'
import App from './components/App.vue'

import Landing from './components/pages/Landing.vue'
import Crops from './components/pages/Crops.vue'
import Plants from './components/pages/Plants.vue'
import Plots from './components/pages/Plots.vue'
import Crop from './components/pages/Crop.vue'

import Form from './components/forms/common/Form.vue'
import Button from './components/common/Button.vue'
import Modal from './components/common/Modal.vue'
import Select from './components/forms/common/Select.vue'
import Input from './components/forms/common/Input.vue'
import Link from './components/common/Link.vue'
import Display from './components/forms/common/Display.vue'
import Table from './components/common/Table.vue'

const routes = [
  { path: '/', name: '', component: Landing },
  { path: '/crops', name: 'crops', component: Crops },
  { path: '/plants', name: 'plants', component: Plants },
  { path: '/plots-beds', name: 'plots', component: Plots },
  { path: '/crop/:id', name: 'crop', component: Crop },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

const app = createApp(App).use(router)
app.component('Form', Form)
app.component('Button', Button)
app.component('Modal', Modal)
app.component('Select', Select)
app.component('Input', Input)
app.component('Link', Link)
app.component('Display', Display)
app.component('Table', Table)
app.mount('#app')