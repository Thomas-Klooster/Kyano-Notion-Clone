import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import '@/assets/main.css'
import '@/assets/fonts/fonts.css'
import { AUTH_SESSION_EXPIRED_EVENT } from '@/api/api'

import axios from 'axios'

axios.defaults.withCredentials = true
axios.defaults.withXSRFToken = true
axios.defaults.baseURL = 'http://localhost:8000'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['Content-Type'] = 'application/json'
window.axios = axios

// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import { aliases, mdi } from 'vuetify/iconsets/mdi'
import '@mdi/font/css/materialdesignicons.css'

// Fonts
import '@fontsource/roboto/100.css'
import '@fontsource/roboto/300.css'
import '@fontsource/roboto/400.css'
import '@fontsource/roboto/500.css'
import '@fontsource/roboto/700.css'
import '@fontsource/roboto/900.css'

const app = createApp(App)

const pinia = createPinia()
app.use(pinia)
app.use(router)

const vuetify = createVuetify({
  icons: {
    defaultSet: 'mdi',
    aliases,
    sets: {
      mdi,
    },
  },
})
app.use(vuetify)
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

window.addEventListener(AUTH_SESSION_EXPIRED_EVENT, async () => {
  auth.clearSession()

  if (router.currentRoute.value.name !== 'login') {
    await router.replace({
      name: 'login',
      query: router.currentRoute.value.fullPath !== '/auth/login'
        ? { expired: '1', redirect: router.currentRoute.value.fullPath }
        : { expired: '1' },
    })
  }
})

auth.fetchUser().finally(() => {
app.mount('#app')  
})