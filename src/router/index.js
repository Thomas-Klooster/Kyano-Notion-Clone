import Home from '@/views/Home.vue'
import MoreSettings from '@/views/MoreSettings.vue'
import Settings from '@/views/Settings.vue'

// AUTH
import LoginPage from '@/pages/Auth/LoginPage.vue'
import RegisterPage from '@/pages/Auth/RegisterPage.vue'
import ForgotPasswordPage from '@/pages/Auth/ForgotPasswordPage.vue'
import ResetPasswordPage from '@/pages/Auth/ResetPasswordPage.vue'

import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', component: Home, meta: { breadcrumb: 'Home' } },
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
    },
    { path: '/auth',
      children: [
        { path: 'login', name: 'login', component: LoginPage, meta: { breadcrumb: 'Login'} },
        { path: 'register', name: 'register', component: RegisterPage, meta: { breadcrumb: 'Register' } },
        { path: 'forgot-password', name: "forgot-password", component: ForgotPasswordPage, meta: { breadcrumb: "Forgot Password" } },
        { path: 'reset-password', name: "reset-password", component: ResetPasswordPage, meta: { breadcrumb: "Reset Password" } },
      ]
    },
  ]
})
export default router
