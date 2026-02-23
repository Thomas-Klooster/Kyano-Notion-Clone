import Home from '@/views/Home.vue'
import MoreSettings from '@/views/MoreSettings.vue'
import Settings from '@/views/Settings.vue'
import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', component: Home, name: 'Home', meta: { breadcrumb: 'Home' } },
    { path: '/settings',
      meta: { breadcrumb: 'Settings'},
      children: [
        {
          path: '',
          component: Settings,
        },
        {
          path: 'more-settings',
          component: MoreSettings,
          meta: { breadcrumb: 'More Settings'}
        }
      ]
    }
  ]
})
export default router
