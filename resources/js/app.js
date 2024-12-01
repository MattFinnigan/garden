import { createApp } from 'vue'
import { createMemoryHistory, createRouter } from 'vue-router'
import App from './components/App.vue'

import Landing from './components/pages/Landing.vue'
import Crops from './components/pages/Crops.vue'
import Plants from './components/pages/Plants.vue'
import Plots from './components/pages/Plots.vue'

import Form from './components/forms/common/Form.vue'
import Button from './components/common/Button.vue'
import Modal from './components/common/Modal.vue'
import Select from './components/forms/common/Select.vue'
import Input from './components/forms/common/Input.vue'

const routes = [
  { path: '/', name: '', component: Landing },
  { path: '/crops', name: 'crops', component: Crops },
  { path: '/plants', name: 'plants', component: Plants },
  { path: '/plots-beds', name: 'plots', component: Plots },
]

const router = createRouter({
  history: createMemoryHistory(),
  routes,
})

const app = createApp(App).use(router)
app.component('Form', Form)
app.component('Button', Button)
app.component('Modal', Modal)
app.component('Select', Select)
app.component('Input', Input)
app.mount('#app')