<!-- <template>
  <div class="admin-page-container">
    <div class="page-header">
      <div>
        <h1 class="page-title">Projects</h1>
        <p class="page-subtitle">
          Beheer projecten en filter deze per customer.
        </p>
      </div>

      <v-btn color="primary" rounded="lg" prepend-icon="mdi-plus" to="/admin/projects/new">
        Nieuw project
      </v-btn>
    </div>

    <v-card class="notion-card mb-4" flat rounded="xl">
      <div class="pa-4">
        <v-row>
          <v-col cols="12" md="4">
            <v-select class="notion-soft-input" label="Filter op customer"
              :items="['Alle customers', 'Kyano Digital', 'Studio North', 'Pixelworks']" variant="solo-filled" flat
              hide-details model-value="Alle customers" />
          </v-col>
        </v-row>
      </div>
    </v-card>

    <v-card class="notion-card" flat rounded="xl">
      <v-table class="notion-table">
        <thead>
          <tr>
            <th>Project</th>
            <th>Klant</th>
            <th>Artikelen</th>
            <th>Status</th>
            <th class="text-right">Acties</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="project in projects" :key="project.id">
            <td>{{ project.name }}</td>
            <td>{{ project.customer }}</td>
            <td>{{ project.articles }}</td>
            <td>
              <v-chip size="small" class="notion-chip">
                {{ project.status }}
              </v-chip>
            </td>
            <td class="text-right">
              <v-btn size="small" variant="text" :to="`/admin/projects/${project.id}`">
                Details
              </v-btn>
              <v-btn size="small" variant="text" :to="`/admin/projects/${project.id}/edit`">
                Bewerken
              </v-btn>
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card>
  </div>
</template>

<script setup>
const projects = [
  { id: 1, name: 'Knowledgebase Portal', customer: 'Kyano Digital', articles: 12, status: 'Actief' },
  { id: 2, name: 'Client Onboarding', customer: 'Studio North', articles: 5, status: 'Concept' },
  { id: 3, name: 'Support Docs', customer: 'Pixelworks', articles: 8, status: 'Actief' },
]
</script> -->

<template>
  <div class="entity-page">
    <div class="entity-shell">
      <section class="entity-hero">
        <div class="hero-content">
          <div class="hero-meta-line">
            <span class="hero-pill">Admin</span>
            <span class="hero-meta-separator">•</span>
            <span>{{ filteredProjects.length }} projecten</span>
          </div>

          <h1 class="hero-title">Projecten</h1>

          <p class="hero-subtitle">
            Beheer projecten en filter deze per klant.
          </p>
        </div>
      </section>

      <section class="entity-card">
        <div class="entity-card-head">
          <div>
            <div class="section-kicker">Overzicht</div>
            <h2 class="section-title">Alle projecten</h2>
          </div>

          <div class="entity-controls">
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
              <div class="entity-icon">
                <v-icon size="18">mdi-folder-outline</v-icon>
              </div>

              <div class="entity-info">
                <div class="entity-name">{{ project.name }}</div>

                <div class="entity-meta">
                  <span>{{ project.customer }}</span>
                  <span class="dot">•</span>
                  <span>{{ project.articles }} artikelen</span>
                  <span class="dot">•</span>

                  <v-chip size="small" class="entity-chip" :class="{
                    'entity-chip-active': project.status === 'Actief',
                    'entity-chip-draft': project.status === 'Concept'
                  }">
                    {{ project.status }}
                  </v-chip>
                </div>
              </div>
            </div>

            <div class="entity-actions">
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
          <div class="empty-state-icon">
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

<style scoped>
@import '../shared-admin-list.css';

.entity-controls {
  display: flex;
  align-items: center;
  gap: 14px;
  flex-wrap: wrap;
}

.entity-filter-select {
  min-width: 280px;
  height: 48px;
  border: 1px solid #e7e5e4;
  border-radius: 14px;
  background: #fbfbfa;
  display: flex;
  align-items: center;
  transition:
    border-color 0.18s ease,
    box-shadow 0.18s ease,
    background 0.18s ease;
}

.entity-filter-select:hover {
  background: #ffffff;
}

.entity-filter-select:focus-within {
  border-color: #c9e2ea;
  background: #ffffff;
  box-shadow: 0 0 0 4px rgba(36, 161, 199, 0.08);
}

.entity-filter-select :deep(.v-input__control) {
  width: 100%;
  height: 48px;
}

.entity-filter-select :deep(.v-field) {
  width: 100%;
  height: 48px;
  min-height: 48px !important;
  padding: 0 14px;
  display: flex;
  align-items: center;
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
}

.entity-filter-select :deep(.v-field__overlay),
.entity-filter-select :deep(.v-field__outline) {
  display: none;
}

.entity-filter-select :deep(.v-field__field) {
  height: 48px;
  display: flex;
  align-items: center;
}

.entity-filter-select :deep(.v-field__prepend-inner) {
  height: 48px;
  display: flex;
  align-items: center;
  padding-top: 0 !important;
  color: #787774;
}

.entity-filter-select :deep(.v-field__input) {
  height: 48px;
  min-height: 48px !important;
  display: flex;
  align-items: center;
  padding-top: 0 !important;
  padding-bottom: 0 !important;
  color: #191919;
  font-size: 0.96rem;
}

.entity-filter-select :deep(.v-select__selection) {
  display: flex;
  align-items: center;
  line-height: 1;
  color: #191919;
}

.entity-filter-select :deep(.v-field__append-inner) {
  height: 48px;
  display: flex;
  align-items: center !important;
  padding-top: 0 !important;
  color: #9a978f;
}

.entity-filter-select :deep(.v-icon) {
  opacity: 1;
}

.entity-create-btn {
  height: 48px !important;
  min-height: 48px !important;
  align-self: center;
}

:deep(.v-field__prepend-inner, .v-field, .v-field--variant-plain) {
  align-items: center !important;
}

@media (max-width: 860px) {
  .entity-filter-select {
    min-width: 100%;
  }
}
</style>