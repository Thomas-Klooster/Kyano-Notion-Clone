<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { onMounted } from 'vue'

const auth = useAuthStore()
const router = useRouter()

onMounted(async () => {
  if (!auth.initialized) {
    await auth.fetchUser()
  }
})

const route = useRoute()

const breadcrumbItems = computed(() => {
  return route.matched
    .filter(segment => segment.meta?.breadcrumb || segment.name)
    .map(segment => {
      const path = segment.path
        .replace(/:\w+/g, param => route.params[param.replace(':', '')])
      return {
        text: segment.meta?.breadcrumb || segment.name,
        to: { path },
        disabled: route.path === segment.path
      }
    })
})

const drawer = ref(false)

const userInitials = computed(() => {
  const name = auth.user?.name
  if (!name) return '?'
  const parts = name.trim().split(' ')
  if (parts.length === 1) return parts[0][0].toUpperCase()
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
})

async function handleLogout() {
  await auth.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <v-app>
    <div v-if="drawer" class="sidebar-overlay" @click="drawer = false" />

    <v-navigation-drawer
      v-model="drawer"
      temporary
      :width="260"
      color="#fbfbfa"
      class="app-sidebar"
    >

      <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
          <v-icon size="16" color="white">mdi-book-open-variant</v-icon>
        </div>
        <div class="sidebar-brand-text">
          <span class="sidebar-brand-name">Knowledgebase</span>
          <span class="sidebar-brand-sub">Kyano Digital</span>
        </div>
      </div>

      <div class="sidebar-scroll">

        <div v-if="!auth.isAuthenticated" class="sidebar-auth-card">
          <div class="sidebar-section-kicker">Account</div>
          <p class="sidebar-auth-hint">Log in om toegang te krijgen tot jouw werkruimte.</p>
          <router-link to="/auth/login" class="sidebar-auth-btn sidebar-auth-btn--primary" @click="drawer = false">
            <v-icon size="15">mdi-login-variant</v-icon>
            Inloggen
          </router-link>
          <router-link to="/auth/register" class="sidebar-auth-btn sidebar-auth-btn--ghost" @click="drawer = false">
            <v-icon size="15">mdi-account-plus-outline</v-icon>
            Registreren
          </router-link>
        </div>

        <div v-else class="sidebar-profile-card">
          <div class="sidebar-profile-header">
            <div class="sidebar-avatar">{{ userInitials }}</div>
            <div class="sidebar-profile-info">
              <div class="sidebar-profile-name">{{ auth.user?.name }}</div>
              <div class="sidebar-profile-role">
                <span class="sidebar-role-badge" :class="auth.isAdmin ? 'sidebar-role-badge--admin' : 'sidebar-role-badge--customer'">
                  {{ auth.isAdmin ? 'Admin' : 'Klant' }}
                </span>
              </div>
            </div>
          </div>
          <div class="sidebar-profile-email">{{ auth.user?.email }}</div>
          <button class="sidebar-logout-btn" @click="handleLogout">
            <v-icon size="13">mdi-logout-variant</v-icon>
            Uitloggen
          </button>
        </div>

        <div class="sidebar-divider" />

        <div class="sidebar-nav-section">
          <div class="sidebar-section-kicker">Navigatie</div>
          <nav class="sidebar-nav">
            <router-link to="/" class="sidebar-nav-item" active-class="sidebar-nav-item--active" exact @click="drawer = false">
              <div class="sidebar-nav-icon"><v-icon size="16">mdi-home-outline</v-icon></div>
              <span>Home</span>
            </router-link>
            <router-link to="/settings" class="sidebar-nav-item" active-class="sidebar-nav-item--active" @click="drawer = false">
              <div class="sidebar-nav-icon"><v-icon size="16">mdi-cog-outline</v-icon></div>
              <span>Instellingen</span>
            </router-link>
          </nav>
        </div>

        <template v-if="auth.isAdmin">
          <div class="sidebar-divider" />
          <div class="sidebar-nav-section">
            <div class="sidebar-section-kicker">Beheer</div>
            <nav class="sidebar-nav">
              <router-link to="/admin" class="sidebar-nav-item sidebar-nav-item--admin" active-class="sidebar-nav-item--active" @click="drawer = false">
                <div class="sidebar-nav-icon"><v-icon size="16">mdi-shield-crown-outline</v-icon></div>
                <span>Admin omgeving</span>
              </router-link>
            </nav>
          </div>
        </template>

      </div>
    </v-navigation-drawer>

    <v-main style="background: var(--color-bg-page);">
      <div>
        <v-app-bar :elevation="0" density="compact">
          <template v-slot:prepend>
            <v-app-bar-nav-icon @click="drawer = !drawer" />
            <v-breadcrumbs :items="breadcrumbItems">
              <template v-slot:item="{ item }">
                <v-breadcrumbs-item :to="item.to" :disabled="item.disabled">
                  {{ item.text }}
                </v-breadcrumbs-item>
              </template>
            </v-breadcrumbs>
          </template>
        </v-app-bar>
        <router-view />
      </div>
    </v-main>
  </v-app>
</template>