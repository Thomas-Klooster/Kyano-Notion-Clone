<template>
  <div class="entity-page">
    <div class="entity-shell">
      <section class="entity-hero">
        <div class="hero-content">
          <div class="hero-meta-line">
            <span class="hero-pill">Admin</span>
            <span class="hero-meta-separator">•</span>
            <span>{{ filteredWorkspaces.length }} workspaces</span>
          </div>

          <h1 class="hero-title">Workspaces</h1>

          <p class="hero-subtitle">
            Beheer je workspaces.
          </p>
        </div>
      </section>

      <section class="entity-card">
        <div class="entity-card-head">
          <div>
            <div class="section-kicker">Overzicht</div>
            <h2 class="section-title">Alle workspaces</h2>
          </div>

          <div class="entity-controls">
            <div class="search-field">
              <v-icon size="18">mdi-magnify</v-icon>
              <input v-model="search" type="text" placeholder="Zoek een workspace..." />
            </div>

            <v-btn color="primary" rounded="xl" prepend-icon="mdi-plus">
              Nieuwe workspace
            </v-btn>
          </div>
        </div>

        <div v-if="filteredWorkspaces.length" class="entity-list">
          <div v-for="workspace in filteredWorkspaces" :key="workspace.id" class="entity-row">
            <div class="entity-row-main">
              <div class="entity-icon">
                <v-icon size="18">mdi-view-dashboard-outline</v-icon>
              </div>

              <div class="entity-info">
                <div class="entity-name">{{ workspace.name }}</div>
                <div class="entity-meta">
                  <span>{{ workspace.customer }}</span>
                  <span class="dot">•</span>
                  <span>{{ workspace.categories }} categorieën</span>
                  <span class="dot">•</span>
                  <span>{{ workspace.projects }} projecten</span>
                </div>
              </div>
            </div>

            <div class="entity-actions">
              <v-chip size="small" class="entity-chip">{{ workspace.status }}</v-chip>
              <v-btn size="small" variant="text">Bewerken</v-btn>
            </div>
          </div>
        </div>

        <div v-else class="empty-state">
          <div class="empty-state-icon">
            <v-icon size="24">mdi-view-dashboard-outline</v-icon>
          </div>
          <h3>Geen workspaces gevonden</h3>
          <p>Probeer een andere zoekterm.</p>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const search = ref('')

const workspaces = ref([
  { id: 1, name: 'Kyano Core', customer: 'Kyano Digital', categories: 4, projects: 9, status: 'Actief' },
  { id: 2, name: 'Support Center', customer: 'Kyano Digital', categories: 3, projects: 6, status: 'Actief' },
  { id: 3, name: 'Client Portals', customer: 'キヤノデジタル', categories: 5, projects: 8, status: 'Concept' },
])

const filteredWorkspaces = computed(() => {
  const query = search.value.trim().toLowerCase()

  if (!query) return workspaces.value

  return workspaces.value.filter(workspace =>
    workspace.name.toLowerCase().includes(query) ||
    workspace.customer.toLowerCase().includes(query) ||
    workspace.status.toLowerCase().includes(query)
  )
})
</script>

<style scoped>
@import '../shared-admin-list.css';
</style>
