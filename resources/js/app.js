import { createApp } from 'vue'
import { createMemoryHistory, createRouter } from 'vue-router'
import App from './components/App.vue'

import Crops from './components/pages/Crops.vue'
import Landing from './components/pages/Landing.vue'

import '../css/app.css'

import Form from './components/forms/common/Form.vue'
import Button from './components/common/Button.vue'
import Modal from './components/common/Modal.vue'

const routes = [
  { path: '/', component: Landing },
  { path: '/crops', component: Crops }
]

const router = createRouter({
  history: createMemoryHistory(),
  routes,
})

const app = createApp(App).use(router)
app.component('Form', Form)
app.component('Button', Button)
app.component('Modal', Modal)
app.mount('#app')