import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import '@/assets/main.css'
import 'highlight.js/styles/github-dark.css'
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

router.beforeEach(async (to, from, next,) => {
const auth = useAuthStore()
if (!auth.initialized) {
  await auth.fetchUser()
}
if (to.meta.requiresAuth && !auth.isAuthenticated) {
  next({name : 'login'})
  return
}
next()


})
  
window.addEventListener(AUTH_SESSION_EXPIRED_EVENT, async () => {
  const auth = useAuthStore()
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

async function bootstrap() {
  /* initializeren van auth user verwijderd wegens in de index.js 
     word dat al gedaan, dus voorkom ik 3 keer user ophalen */
  app.mount('#app')
}

bootstrap()
