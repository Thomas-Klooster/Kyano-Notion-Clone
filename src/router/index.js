import Home from '@/views/Home.vue'
import MoreSettings from '@/views/MoreSettings.vue'
import Settings from '@/views/Settings.vue'

// AUTH
import LoginPage from '@/pages/Auth/LoginPage.vue'
import RegisterPage from '@/pages/Auth/RegisterPage.vue'
import ForgotPasswordPage from '@/pages/Auth/ForgotPasswordPage.vue'
import ResetPasswordPage from '@/pages/Auth/ResetPasswordPage.vue'

// ADMIN
import AdminOverviewPage from '@/pages/Admin/AdminOverviewPage.vue'
import CustomersPage from '@/pages/Admin/CustomersPage.vue'
import CustomerFormPage from '@/pages/Admin/CustomerFormPage.vue'
import ProjectsPage from '@/pages/Admin/ProjectsPage.vue'
import ProjectFormPage from '@/pages/Admin/ProjectFormPage.vue'
import ProjectDetailPage from '@/pages/Admin/ProjectDetailPage.vue'

import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', component: Home, meta: { breadcrumb: 'Home' } },

    {
      path: '/settings',
      meta: { breadcrumb: 'Settings' },
      children: [
        {
          path: '',
          component: Settings,
        },
        {
          path: 'more-settings',
          component: MoreSettings,
          meta: { breadcrumb: 'More Settings' },
        },
      ],
    },

    {
      path: '/auth',
      children: [
        { path: 'login', name: 'login', component: LoginPage, meta: { breadcrumb: 'Login' } },
        { path: 'register', name: 'register', component: RegisterPage, meta: { breadcrumb: 'Register' } },
        { path: 'forgot-password', name: 'forgot-password', component: ForgotPasswordPage, meta: { breadcrumb: 'Forgot Password' } },
        { path: 'reset-password', name: 'reset-password', component: ResetPasswordPage, meta: { breadcrumb: 'Reset Password' } },
      ],
    },

    {
      path: '/admin',
      meta: { breadcrumb: 'Admin omgeving' },
      children: [
        {
          path: '',
          name: 'admin-overview',
          component: AdminOverviewPage,
        },
        {
          path: 'customers',
          name: 'admin-customers',
          component: CustomersPage,
          meta: { breadcrumb: 'Customers module' },
        },
        {
          path: 'customers/new',
          name: 'admin-customers-new',
          component: CustomerFormPage,
          meta: { breadcrumb: 'Customer create/edit form' },
        },
        {
          path: 'customers/:id/edit',
          name: 'admin-customers-edit',
          component: CustomerFormPage,
          meta: { breadcrumb: 'Customer create/edit form' },
        },
        {
          path: 'projects',
          name: 'admin-projects',
          component: ProjectsPage,
          meta: { breadcrumb: 'Projects' },
        },
        {
          path: 'projects/new',
          name: 'admin-projects-new',
          component: ProjectFormPage,
          meta: { breadcrumb: 'Project create/edit' },
        },
        {
          path: 'projects/:id/edit',
          name: 'admin-projects-edit',
          component: ProjectFormPage,
          meta: { breadcrumb: 'Project create/edit' },
        },
        {
          path: 'projects/:id',
          name: 'admin-project-detail',
          component: ProjectDetailPage,
          meta: { breadcrumb: 'Project detail page' },
        },
      ],
    },
  ],
})

export default router