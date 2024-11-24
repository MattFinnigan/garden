import { createApp } from 'vue'
import { createMemoryHistory, createRouter } from 'vue-router'
import axios from 'axios'
import App from './components/App.vue'

import Landing from './components/pages/Landing.vue'
import '../css/app.css'

const axiosInstance = axios.create({
  withCredentials: true,
})

const routes = [
  { path: '/', component: Landing }
]

const router = createRouter({
  history: createMemoryHistory(),
  routes,
})

const app = createApp(App).use(router)
app.config.globalProperties.$axios = { ...axiosInstance }
app.mount('#app')