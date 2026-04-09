<template>
  <div class="entity-page">
    <div class="entity-shell">
      <section class="entity-hero">
        <div class="hero-content">
          <div class="hero-meta-line">
            <span class="hero-pill">Admin</span>
            <span class="hero-meta-separator">•</span>
            <span>{{ filteredCategories.length }} categorieën</span>
          </div>

          <h1 class="hero-title">Categorieën</h1>

          <p class="hero-subtitle">
            Beheer je Categorieën.
          </p>
        </div>
      </section>

      <section class="entity-card">
        <div class="entity-card-head">
          <div>
            <div class="section-kicker">Overzicht</div>
            <h2 class="section-title">Alle categorieën</h2>
          </div>

          <div class="entity-controls">
            <div class="search-field">
              <v-icon size="18">mdi-magnify</v-icon>
              <input v-model="search" type="text" placeholder="Zoek een categorie..." />
            </div>

            <v-btn class="entity-create-btn" variant="text" prepend-icon="mdi-plus">
              Nieuwe catergorie
            </v-btn>
          </div>
        </div>

        <div v-if="filteredCategories.length" class="entity-list">
          <div v-for="category in filteredCategories" :key="category.id" class="entity-row">
            <div class="entity-row-main">
              <div class="entity-icon entity-icon-soft">
                <v-icon size="18">mdi-shape-outline</v-icon>
              </div>

              <div class="entity-info">
                <div class="entity-name">{{ category.name }}</div>
                <div class="entity-meta">
                  <span>{{ category.workspace }}</span>
                  <span class="dot">•</span>
                  <span>{{ category.projects }} projecten</span>
                  <span class="dot">•</span>
                  <span>{{ category.articles }} artikelen</span>
                </div>
              </div>
            </div>

            <div class="entity-actions">
              <v-btn size="small" variant="text">Bewerken</v-btn>
              <v-btn size="small" variant="text" class="delete-btn">Verwijderen</v-btn>
            </div>
          </div>
        </div>

        <div v-else class="empty-state">
          <div class="empty-state-icon">
            <v-icon size="24">mdi-shape-outline</v-icon>
          </div>
          <h3>Geen categorieën gevonden</h3>
          <p>Probeer een andere zoekterm.</p>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const search = ref('')

const categories = ref([
  { id: 1, name: 'Development', workspace: 'Kyano Core', projects: 4, articles: 22, scope: 'Workspace' },
  { id: 2, name: 'Support', workspace: 'Support Center', projects: 3, articles: 17, scope: 'Workspace' },
  { id: 3, name: 'Onboarding', workspace: 'Client Portal', projects: 2, articles: 11, scope: 'Project' },
])

const filteredCategories = computed(() => {
  const query = search.value.trim().toLowerCase()

  if (!query) return categories.value

  return categories.value.filter(category =>
    category.name.toLowerCase().includes(query) ||
    category.workspace.toLowerCase().includes(query) ||
    category.scope.toLowerCase().includes(query)
  )
})
</script>

<style scoped>
@import '../shared-admin-list.css';
</style>
