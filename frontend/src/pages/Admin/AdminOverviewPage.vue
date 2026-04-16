<template>
  <div class="admin-dashboard-page">
    <div class="entity-shell page-shell">
      <section class="admin-hero hero">
        <div class="admin-hero-content">
          <div class="hero-meta-line u-flex-center u-wrap u-gap-8">
            <span class="hero-pill u-inline-flex u-items-center">Admin</span>
            <span class="hero-meta-separator">•</span>
            <span>{{ totalEntities }} records</span>
            <span class="hero-meta-separator">•</span>
            <span>5 beheermodules</span>
          </div>

          <h1 class="hero-title">Admin Pagina</h1>

          <p class="hero-subtitle">
            Beheer je klanten, workspaces, categorieën, projecten en artikelen.
          </p>
        </div>
      </section>

      <section class="stats-grid">
        <article v-for="stat in stats" :key="stat.label" class="stat-card card card-elevated card-rounded-xl">
          <div class="stat-label">{{ stat.label }}</div>
          <div class="stat-value">{{ stat.value }}</div>
          <div class="stat-meta">{{ stat.meta }}</div>
        </article>
      </section>

      <section class="module-card card card-elevated card-rounded-2xl">
        <div class="module-card-head card-head">
          <div>
            <div class="section-kicker">Overzicht</div>
            <h2 class="section-title">Admin modules</h2>
          </div>
        </div>

        <div class="module-grid">
          <router-link v-for="module in modules" :key="module.title" :to="module.to" class="module-tile">
            <div class="module-tile-top">
              <div class="module-icon icon-box">
                <v-icon size="20">{{ module.icon }}</v-icon>
              </div>

              <div class="module-count">{{ module.count }}</div>
            </div>

            <div class="module-title">{{ module.title }}</div>
            <p class="module-description">{{ module.description }}</p>

            <div class="module-meta">
              <span>{{ module.meta }}</span>
              <v-icon size="18">mdi-chevron-right</v-icon>
            </div>
          </router-link>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const stats = [
  { label: 'Klanten', value: 12, meta: '' },
  { label: 'Workspaces', value: 6, meta: '' },
  { label: 'Categorieën', value: 18, meta: '' },
  { label: 'Projecten', value: 28, meta: '' },
  { label: 'Artikelen', value: 143, meta: '' },
]

const modules = [
  {
    title: 'Klanten',
    to: '/admin/customers',
    icon: 'mdi-account-group',
    count: '12',
    meta: 'Beheer Klanten',
    description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam vitae rhoncus nulla, non fermentum velit. Morbi posuere egestas dignissim.',
  },
  {
    title: 'Workspaces',
    to: '/admin/workspaces',
    icon: 'mdi-view-dashboard-outline',
    count: '6',
    meta: 'Beheer Workspaces',
    description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam vitae rhoncus nulla, non fermentum velit. Morbi posuere egestas dignissim.',
  },
  {
    title: 'Categorieën',
    to: '/admin/categories',
    icon: 'mdi-shape-outline',
    count: '18',
    meta: 'Beheer Categorieën',
    description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam vitae rhoncus nulla, non fermentum velit. Morbi posuere egestas dignissim.',
  },
  {
    title: 'Projecten',
    to: '/admin/projects',
    icon: 'mdi-briefcase-outline',
    count: '28',
    meta: 'Beheer Projecten',
    description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam vitae rhoncus nulla, non fermentum velit. Morbi posuere egestas dignissim.',
  },
  {
    title: 'Artikelen',
    to: '/admin/articles',
    icon: 'mdi-file-document-outline',
    count: '143',
    meta: 'Beheer Artikelen',
    description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam vitae rhoncus nulla, non fermentum velit. Morbi posuere egestas dignissim.',
  },
]

const totalEntities = computed(() =>
  stats.reduce((sum, stat) => sum + Number(stat.value), 0)
)
</script>

