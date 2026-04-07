import Home from '@/pages/Portal/DashboardPage.vue'
import MoreSettings from '@/views/MoreSettings.vue'
import Settings from '@/views/Settings.vue'

// AUTH
import LoginPage from '@/pages/Auth/LoginPage.vue'
import RegisterPage from '@/pages/Auth/RegisterPage.vue'
import ForgotPasswordPage from '@/pages/Auth/ForgotPasswordPage.vue'
import ResetPasswordPage from '@/pages/Auth/ResetPasswordPage.vue'

// ADMIN
import AdminOverviewPage from '@/pages/Admin/AdminOverviewPage.vue'
import CustomersPage from '@/pages/Admin/Customers/CustomersPage.vue'
import CustomerFormPage from '@/pages/Admin/Customers/CustomerFormPage.vue'
import ProjectsPage from '@/pages/Admin/Projects/ProjectsPage.vue'
import ProjectFormPage from '@/pages/Admin/Projects/ProjectFormPage.vue'
import ProjectDetailPage from '@/pages/Admin/Projects/ProjectDetailPage.vue'
import WorkspacesPage from '@/pages/Admin/Workspaces/WorkspacesPage.vue'
import CategoriesPage from '@/pages/Admin/Categories/CategoriesPage.vue'

import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Dashboard',
      component: () => import('@/pages/Portal/DashboardPage.vue'),
      meta: { breadcrumb: 'Dashboard' },
    },
    {
      path: '/category/:id',
      name: 'category',
      component: () => import('@/pages/Portal/CategoryPage.vue'),
      meta: { breadcrumb: 'Category' },
    },
    {
      path: '/project/:id',
      name: 'project',
      component: () => import('@/pages/Portal/ProjectPage.vue'),
      meta: { breadcrumb: 'Project' },
    },
    {
      path: '/article/:id',
      name: 'article',
      component: () => import('@/pages/Portal/ArticlePreviewPage.vue'),
      meta: { breadcrumb: 'Article' },
    },

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
        {
          path: 'register',
          name: 'register',
          component: RegisterPage,
          meta: { breadcrumb: 'Register' },
        },
        {
          path: 'forgot-password',
          name: 'forgot-password',
          component: ForgotPasswordPage,
          meta: { breadcrumb: 'Forgot Password' },
        },
        {
          path: 'reset-password',
          name: 'reset-password',
          component: ResetPasswordPage,
          meta: { breadcrumb: 'Reset Password' },
        },
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
          path: 'workspaces',
          name: 'admin-workspaces',
          component: WorkspacesPage,
          meta: { breadcrumb: 'Workspaces' },
        },
        {
          path: 'categories',
          name: 'admin-categories',
          component: CategoriesPage,
          meta: { breadcrumb: 'Categories' },
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
        {
          path: 'articles',
          name: 'admin-articles',
          component: () => import('@/pages/Admin/Articles/ArticlesPage.vue'),
          meta: { breadcrumb: 'Articles' },
        },
        {
          path: 'articles/new',
          name: 'admin-articles-new',
          component: () => import('@/pages/Admin/Articles/ArticleEditorPage.vue'),
          meta: { breadcrumb: 'Article editor page' },
        },
        {
          path: 'articles/:id/edit',
          name: 'admin-articles-edit',
          component: () => import('@/pages/Admin/Articles/ArticleEditorPage.vue'),
          meta: { breadcrumb: 'Article editor page' },
        },
        {
          path: '/articles/:id',
          name: 'article-preview',
          component: () => import('@/pages/Portal/ArticlePreviewPage.vue'),
          meta: { breadcrumb: 'Article preview' },
        },
      ],
    },
  ],
})

export default router
