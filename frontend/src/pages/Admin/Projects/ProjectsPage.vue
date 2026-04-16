<template>
  <div class="entity-page">
    <div class="entity-shell page-shell">
      <section class="entity-hero hero">
        <div class="hero-content u-min-w-0">
          <div class="hero-meta-line u-flex-center u-wrap u-gap-8">
            <span class="hero-pill u-inline-flex u-items-center">Admin</span>
            <span class="hero-meta-separator">•</span>
            <span>{{ filteredProjects.length }} projecten</span>
          </div>

          <h1 class="hero-title">Projecten</h1>

          <p class="hero-subtitle">
            Beheer projecten en filter deze per klant.
          </p>
        </div>
      </section>

      <section class="entity-card card card-elevated card-rounded-2xl">
        <div class="entity-card-head card-head">
          <div>
            <div class="section-kicker">Overzicht</div>
            <h2 class="section-title">Alle projecten</h2>
          </div>

          <div class="entity-controls u-flex u-items-center u-wrap u-gap-14">
            <v-select v-model="selectedCustomer" :items="['Alle klanten', ...customerOptions]" variant="plain"
              hide-details flat menu-icon="mdi-chevron-down" class="entity-filter-select">
              <template #prepend-inner>
                <v-icon size="18">mdi-filter-variant</v-icon>
              </template>
            </v-select>

            <v-btn class="entity-create-btn" variant="text" prepend-icon="mdi-plus" to="/admin/projects/new">
              Nieuw project
            </v-btn>
          </div>
        </div>

        <div v-if="filteredProjects.length" class="entity-list">
          <div v-for="project in filteredProjects" :key="project.id" class="entity-row">
            <div class="entity-row-main">
              <div class="entity-icon icon-box">
                <v-icon size="18">mdi-folder-outline</v-icon>
              </div>

              <div class="entity-info">
                <div class="entity-name">{{ project.name }}</div>

                <div class="entity-meta">
                  <span>{{ project.customer }}</span>
                  <span class="dot">•</span>
                  <span>{{ project.articles }} artikelen</span>
                  <span class="dot">•</span>

                  <v-chip size="small" class="entity-chip" :class="{ 'entity-chip-active': project.status === 'Actief', 'entity-chip-draft': 'Concept' }">
                    {{ project.status }}
                  </v-chip>
                </div>
              </div>
            </div>

            <div class="entity-actions u-flex u-items-center u-wrap u-gap-10">
              <v-btn size="small" variant="text" :to="`/admin/projects/${project.id}`">
                Details
              </v-btn>

              <v-btn size="small" variant="text" :to="`/admin/projects/${project.id}/edit`">
                Bewerken
              </v-btn>
            </div>
          </div>
        </div>

        <div v-else class="empty-state">
          <div class="empty-state-icon icon-box">
            <v-icon size="24">mdi-folder-off-outline</v-icon>
          </div>
          <h3>Geen projecten gevonden</h3>
          <p>Probeer een andere filter of voeg een nieuw project toe.</p>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const selectedCustomer = ref('Alle klanten')

const projects = ref([
  {
    id: 1,
    name: 'Knowledgebase Portal',
    customer: 'Kyano Digital',
    articles: 12,
    status: 'Actief',
  },
  {
    id: 2,
    name: 'Client Onboarding',
    customer: 'Studio North',
    articles: 5,
    status: 'Concept',
  },
  {
    id: 3,
    name: 'Support Docs',
    customer: 'Pixelworks',
    articles: 8,
    status: 'Actief',
  },
])

const customerOptions = computed(() => {
  return [...new Set(projects.value.map(project => project.customer))]
})

const filteredProjects = computed(() => {
  if (selectedCustomer.value === 'Alle klanten') {
    return projects.value
  }

  return projects.value.filter(
    project => project.customer === selectedCustomer.value
  )
})
</script>
