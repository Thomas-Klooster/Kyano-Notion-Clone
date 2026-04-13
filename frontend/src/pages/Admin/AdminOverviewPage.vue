<template>
  <div class="admin-dashboard-page">
    <div class="admin-dashboard-shell">
      <section class="admin-hero">
        <div class="admin-hero-content">
          <div class="hero-meta-line">
            <span class="hero-pill">Admin</span>
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
        <article v-for="stat in stats" :key="stat.label" class="stat-card">
          <div class="stat-label">{{ stat.label }}</div>
          <div class="stat-value">{{ stat.value }}</div>
          <div class="stat-meta">{{ stat.meta }}</div>
        </article>
      </section>

      <section class="module-card">
        <div class="module-card-head">
          <div>
            <div class="section-kicker">Overzicht</div>
            <h2 class="section-title">Admin modules</h2>
          </div>
        </div>

        <div class="module-grid">
          <router-link v-for="module in modules" :key="module.title" :to="module.to" class="module-tile">
            <div class="module-tile-top">
              <div class="module-icon">
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

<style scoped>
.admin-dashboard-page {
  min-height: 100%;
  background: #f7f7f5;
}

.admin-dashboard-shell {
  max-width: 1200px;
  margin: 0 auto;
  padding: 32px 28px 72px;
}

.admin-hero {
  min-height: 220px;
  border-radius: 26px;
  background: linear-gradient(135deg, #24a1c7 0%, #1b7e9a 100%);
  margin-bottom: 20px;
  padding: 32px 36px;
  display: flex;
  align-items: end;
}

.admin-hero-content {
  max-width: 780px;
}

.hero-meta-line {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 14px;
  color: rgba(255, 255, 255, 0.86);
  font-size: 0.92rem;
}

.hero-pill {
  display: inline-flex;
  align-items: center;
  min-height: 28px;
  padding: 0 10px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.18);
  color: #ffffff;
  font-size: 0.82rem;
  font-weight: 600;
}

.hero-meta-separator {
  color: rgba(255, 255, 255, 0.6);
}

.hero-title {
  margin: 0;
  color: #ffffff;
  font-size: 3rem;
  line-height: 1.05;
  font-weight: 800;
  letter-spacing: -0.03em;
}

.hero-subtitle {
  max-width: 760px;
  margin: 16px 0 0;
  color: rgba(255, 255, 255, 0.86);
  font-size: 1.02rem;
  line-height: 1.8;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 16px;
  margin-bottom: 20px;
}

.stat-card {
  padding: 20px 22px;
  border-radius: 22px;
  border: 1px solid #ececea;
  background: #ffffff;
  box-shadow: 0 2px 10px rgba(15, 15, 15, 0.03);
}

.stat-label {
  color: #787774;
  font-size: 0.86rem;
  font-weight: 600;
  margin-bottom: 10px;
}

.stat-value {
  color: #191919;
  font-size: 2rem;
  line-height: 1;
  font-weight: 800;
  letter-spacing: -0.04em;
}

.stat-meta {
  margin-top: 10px;
  color: #6b6a66;
  font-size: 0.92rem;
  line-height: 1.6;
}

.module-card {
  border: 1px solid #ececea;
  border-radius: 26px;
  background: #ffffff;
  box-shadow: 0 2px 10px rgba(15, 15, 15, 0.03);
  overflow: hidden;
}

.module-card-head {
  padding: 28px 28px 22px;
  border-bottom: 1px solid #f0efec;
  display: flex;
  align-items: end;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
}

.section-kicker {
  margin-bottom: 8px;
  color: #787774;
  font-size: 0.86rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.section-title {
  margin: 0;
  color: #191919;
  font-size: 1.7rem;
  line-height: 1.2;
  font-weight: 700;
  letter-spacing: -0.02em;
}

.module-card-caption {
  max-width: 460px;
  color: #6b6a66;
  font-size: 0.96rem;
  line-height: 1.7;
}

.module-grid {
  padding: 24px 28px 28px;
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 16px;
}

.module-tile {
  padding: 22px;
  border-radius: 22px;
  border: 1px solid #eceae6;
  background: #fcfcfb;
  text-decoration: none;
  transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
}

.module-tile:hover {
  transform: translateY(-2px);
  border-color: #ddd9d4;
  background: #ffffff;
  box-shadow: 0 12px 28px rgba(15, 15, 15, 0.06);
}

.module-tile-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 18px;
}

.module-icon {
  width: 42px;
  height: 42px;
  border-radius: 14px;
  background: #f3f8fa;
  border: 1px solid #d9ebf1;
  color: #177f9d;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.module-count {
  color: #191919;
  font-size: 1rem;
  font-weight: 700;
}

.module-title {
  color: #191919;
  font-size: 1.18rem;
  font-weight: 700;
  margin-bottom: 10px;
}

.module-description {
  margin: 0;
  color: #6b6a66;
  font-size: 0.95rem;
  line-height: 1.7;
}

.module-meta {
  margin-top: 18px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  color: #787774;
  font-size: 0.92rem;
  font-weight: 600;
}

@media (max-width: 1100px) {
  .stats-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 860px) {

  .module-grid,
  .stats-grid {
    grid-template-columns: 1fr;
  }

  .admin-dashboard-shell {
    padding: 24px 18px 56px;
  }

  .admin-hero {
    padding: 28px 22px;
  }

  .hero-title {
    font-size: 2.4rem;
  }

  .module-card-head,
  .module-grid {
    padding-left: 20px;
    padding-right: 20px;
  }
}
</style>