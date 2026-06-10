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
      redirect: '/dashboard',
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
      meta: { requiresAuth: true, breadcrumb: 'Categorie' },
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
      path: '/article/new',
      name: 'article-new',
      component: () => import('@/pages/Admin/Articles/ArticleEditorPage.vue'),
      meta: { requiresAuth: true, requiresAdmin: true, breadcrumb: 'Artikel editor pagina' },
    },
    {
      path: '/article/:slug',
      name: 'article',
      component: () => import('@/pages/Portal/ArticlePreviewPage.vue'),
      meta: { requiresAuth: true, breadcrumb: 'Artikel' },
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
          meta: { guestOnly: true, breadcrumb: 'Registreren' },
        },
        {
          path: 'forgot-password',
          name: 'forgot-password',
          component: () => import('@/pages/Auth/ForgotPasswordPage.vue'),
          meta: { guestOnly: true, breadcrumb: 'Wachtwoord vergeten' },
        },
        {
          path: 'reset-password',
          name: 'reset-password',
          component: () => import('@/pages/Auth/ResetPasswordPage.vue'),
          meta: { guestOnly: true, breadcrumb: 'Wachtwoord vernieuwen' },
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
        {
          path: 'articles/:slug',
          name: 'admin-articles-new',
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
