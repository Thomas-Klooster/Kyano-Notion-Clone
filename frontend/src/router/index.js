import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    
    {
      path: '/:pathMatch(.*)*',
      name: 'Not Found',
      component: () => import('@/views/NotFoundComponent.vue'),
    },

    {
      path: '/',
      redirect: '/auth/login',
    },

    {
      path: '/dashboard',
      name: 'Dashboard',
      component: () => import('@/pages/Portal/DashboardPage.vue'),
      meta: { requiresAuth: true, breadcrumb: 'Dashboard' },
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
      path: '/project/:slug',
      name: 'project',
      component: () => import('@/pages/Portal/ProjectPage.vue'),
      meta: { requiresAuth: true, breadcrumb: 'Project' },
    },
    {
      path: '/article/:slug',
      name: 'article',
      component: () => import('@/pages/Portal/ArticlePreviewPage.vue'),
      meta: { requiresAuth: true, breadcrumb: 'Article' },
    },

    {
      path: '/profile',
      name: 'profile',
      component: () => import('@/pages/Portal/ProfilePage.vue'),
      meta: { requiresAuth: true, breadcrumb: 'Profiel' },
    },
    {
      path: '/settings',
      redirect: { name: 'profile' },
    },
    {
      path: '/settings/more-settings',
      redirect: { name: 'profile' },
    },

    {
      path: '/auth',
      children: [
        {
          path: 'login',
          name: 'login',
          component: () => import('@/pages/Auth/LoginPage.vue'),
          meta: { guestOnly: true, breadcrumb: 'Login' },
        },
        {
          path: 'register',
          name: 'register',
          component: () => import('@/pages/Auth/RegisterPage.vue'),
          meta: { guestOnly: true, breadcrumb: 'Register' },
        },
        {
          path: 'forgot-password',
          name: 'forgot-password',
          component: () => import('@/pages/Auth/ForgotPasswordPage.vue'),
          meta: { guestOnly: true, breadcrumb: 'Forgot Password' },
        },
        {
          path: 'reset-password',
          name: 'reset-password',
          component: () => import('@/pages/Auth/ResetPasswordPage.vue'),
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
          component: () => import('@/pages/Admin/AdminOverviewPage.vue'),
        },
      //   {
      //     path: 'customers',
      //     name: 'admin-customers',
      //     component: CustomersPage,
      //     meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Customers module' },
      //   },
      //   {
      //     path: 'customers/new',
      //     name: 'admin-customers-new',
      //     component: CustomerFormPage,
      //     meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Customer create/edit form' },
      //   },
      //   {
      //     path: 'customers/:id/edit',
      //     name: 'admin-customers-edit',
      //     component: CustomerFormPage,
      //     meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Customer create/edit form' },
      //   },
      //   {
      //     path: 'workspaces',
      //     name: 'admin-workspaces',
      //     component: WorkspacesPage,
      //     meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Workspaces' },
      //   },
      //   {
      //     path: 'categories',
      //     name: 'admin-categories',
      //     component: CategoriesPage,
      //     meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Categories' },
      //   },
      //   {
      //     path: 'categories/new',
      //     name: 'admin-categories-new',
      //     component: CategoriesFormPage,
      //     meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Categories create/edit form' },
      //   },
      //   {
      //     path: 'categories/:id/edit',
      //     name: 'admin-categories-edit',
      //     component: CategoriesFormPage,
      //     meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Categories create/edit form' },
      //   },
      //   {
      //     path: 'projects',
      //     name: 'admin-projects',
      //     component: ProjectsPage,
      //     meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Projects' },
      //   },
      //   {
      //     path: 'projects/new',
      //     name: 'admin-projects-new',
      //     component: ProjectFormPage,
      //     meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Project create/edit' },
      //   },
      //   {
      //     path: 'projects/:id/edit',
      //     name: 'admin-projects-edit',
      //     component: ProjectFormPage,
      //     meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Project create/edit' },
      //   },
      //   {
      //     path: 'projects/:id',
      //     name: 'admin-project-detail',
      //     component: ProjectDetailPage,
      //     meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Project detail page' },
      //   },
        {
          path: 'articles/:slug/new',
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
          path: 'articles/:slug',
          name: 'article-preview',
          component: () => import('@/pages/Portal/ArticlePreviewPage.vue'),
          meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Article preview' },
        },
      ],
    },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  if (!auth.initialized) {
    return true
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return {
      name: 'login',
      query: to.fullPath !== '/profile' ? { redirect: to.fullPath } : {},
    }
  }

  if (to.meta.guestOnly && auth.isAuthenticated) {
    return auth.isAdmin ? { name: 'admin-overview' } : { name: 'Dashboard' }
  }

  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return { name: 'Dashboard' }
  }

  return true
})

export default router
