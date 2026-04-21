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
import CategoriesFormPage from '@/pages/Admin/Categories/CategoriesFormPage.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/test',
      name: 'test',
      component: () => import('@/pages/Admin/NewAdminLayoutTestPage.vue'),
      meta: { breadcrumb: 'test'}
    },
    {
      path: '/',
      name: 'Dashboard',
      component: () => import('@/pages/Portal/DashboardPage.vue'),
      meta: { guestOnly: true, breadcrumb: 'Dashboard' },
    },
    {
      path: '/category/:slug',
      name: 'category',
      component: () => import('@/pages/Portal/CategoryPage.vue'),
      meta: { requiresAuth: true, breadcrumb: 'Category' },
    },
    {
      path: '/workspace/:slug',
      name: 'workspace',
      component: () => import('@/pages/Portal/WorkspacePage.vue'),
      meta: { requiresAuth: true, breadcrumb: 'Workspace' },
    },
    {
      path: '/project/:id',
      name: 'project',
      component: () => import('@/pages/Portal/ProjectPage.vue'),
      meta: { requiresAuth: true, breadcrumb: 'Project' },
    },
    {
      path: '/article/:id',
      name: 'article',
      component: () => import('@/pages/Portal/ArticlePreviewPage.vue'),
      meta: { requiresAuth: true, breadcrumb: 'Article' },
    },

    {
      path: '/settings',
      meta: { requiresAuth: true, breadcrumb: 'Settings' },
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
        { path: 'login', name: 'login', component: LoginPage, meta: { guestOnly: true, breadcrumb: 'Login' } },
        {
          path: 'register',
          name: 'register',
          component: RegisterPage,
          meta: { guestOnly: true, breadcrumb: 'Register' },
        },
        {
          path: 'forgot-password',
          name: 'forgot-password',
          component: ForgotPasswordPage,
          meta: { guestOnly: true, breadcrumb: 'Forgot Password' },
        },
        {
          path: 'reset-password',
          name: 'reset-password',
          component: ResetPasswordPage,
          meta: { guestOnly: true, breadcrumb: 'Reset Password' },
        },
      ],
    },

    {
      path: '/admin',
      meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Admin omgeving' },
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
          meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Customers module' },
        },
        {
          path: 'customers/new',
          name: 'admin-customers-new',
          component: CustomerFormPage,
          meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Customer create/edit form' },
        },
        {
          path: 'customers/:id/edit',
          name: 'admin-customers-edit',
          component: CustomerFormPage,
          meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Customer create/edit form' },
        },
        {
          path: 'workspaces',
          name: 'admin-workspaces',
          component: WorkspacesPage,
          meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Workspaces' },
        },
        {
          path: 'categories',
          name: 'admin-categories',
          component: CategoriesPage,
          meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Categories' },
        },
        {
          path: 'categories/new',
          name: 'admin-categories-new',
          component: CategoriesFormPage,
          meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Categories create/edit form' },
        },
        {
          path: 'categories/:id/edit',
          name: 'admin-categories-edit',
          component: CategoriesFormPage,
          meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Categories create/edit form' },
        },
        {
          path: 'projects',
          name: 'admin-projects',
          component: ProjectsPage,
          meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Projects' },
        },
        {
          path: 'projects/new',
          name: 'admin-projects-new',
          component: ProjectFormPage,
          meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Project create/edit' },
        },
        {
          path: 'projects/:id/edit',
          name: 'admin-projects-edit',
          component: ProjectFormPage,
          meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Project create/edit' },
        },
        {
          path: 'projects/:id',
          name: 'admin-project-detail',
          component: ProjectDetailPage,
          meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Project detail page' },
        },
        {
          path: 'articles',
          name: 'admin-articles',
          component: () => import('@/pages/Admin/Articles/ArticlesPage.vue'),
          meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Artikelen' },
        },
        {
          path: 'articles/new',
          name: 'admin-articles-new',
          component: () => import('@/pages/Admin/Articles/ArticleEditorPage.vue'),
          meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Article editor page' },
        },
        {
          path: 'articles/:id/edit',
          name: 'admin-articles-edit',
          component: () => import('@/pages/Admin/Articles/ArticleEditorPage.vue'),
          meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Article editor page' },
        },
        {
          path: '/articles/:id',
          name: 'article-preview',
          component: () => import('@/pages/Portal/ArticlePreviewPage.vue'),
          meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Article preview' },
        },
      ],
    },
  ],
})

// import { useAuthStore } from '@/stores/auth'

// router.beforeEach(async (to) => {
//   const auth = useAuthStore()

//   if (!auth.initialized) {
//     await auth.fetchUser()
//   }

//   if (to.meta.requiresAuth && !auth.isAuthenticated) {
//     return { name: 'login' }
//   }

//   if (to.meta.guestOnly && auth.isAuthenticated) {
//     return auth.isAdmin ? { name: 'admin-overview' } : { name: 'Dashboard' }
//   }

//   if (to.meta.requiresAdmin && !auth.isAdmin) {
//     return { name: 'Dashboard' }
//   }

//   return true
// })

export default router
