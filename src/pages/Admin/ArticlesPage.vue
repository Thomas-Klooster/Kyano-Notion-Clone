<template>
  <div>
    <div class="page-header">
      <div>
        <h1 class="page-title">Articles</h1>
        <p class="page-subtitle">
          Beheer knowledgebase-artikelen per project, categorie en status.
        </p>
      </div>

      <v-btn color="primary" rounded="lg" prepend-icon="mdi-plus" to="/admin/articles/new">
        Nieuw artikel
      </v-btn>
    </div>

    <v-card class="notion-card mb-4" flat rounded="xl">
      <div class="pa-4">
        <v-row>
          <v-col>
            <v-text-field
              v-model="search"
              class="notion-soft-input"
              label="Zoek op titel, samenvatting of categorie"
              prepend-inner-icon="mdi-magnify"
              variant="solo-filled"
              flat
              hide-details
            />
          </v-col>
        </v-row>
        <v-row>
          <v-col>
            <v-select
              v-model="selectedProject"
              class="notion-soft-input"
              :items="projects"
              label="Project"
              variant="solo-filled"
              flat
              hide-details
            />
          </v-col>

          <v-col>
            <v-select
              v-model="selectedStatus"
              class="notion-soft-input"
              :items="statuses"
              label="Status"
              variant="solo-filled"
              flat
              hide-details
            />
          </v-col>

          <v-col>
            <v-select
              v-model="sortBy"
              class="notion-soft-input"
              :items="sortOptions"
              label="Sorteren"
              variant="solo-filled"
              flat
              hide-details
            />
          </v-col>
        </v-row>
      </div>
    </v-card>

    <v-card class="notion-card" flat rounded="xl">
      <v-table class="notion-table">
        <thead>
          <tr>
            <th>Artikel</th>
            <th>Project</th>
            <th>Categorie</th>
            <th>Status</th>
            <th>Laatst gewijzigd</th>
            <th class="text-right">Acties</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="article in sortedArticles" :key="article.id">
            <td>
              <div class="font-weight-medium">{{ article.title }}</div>
              <div class="text-body-2 notion-muted">{{ article.summary }}</div>
              <div class="text-caption notion-muted mt-1">/{{ article.slug }}</div>
            </td>

            <td>{{ article.project }}</td>
            <td>{{ article.category }}</td>

            <td>
              <v-chip size="small" class="notion-chip">
                {{ article.status }}
              </v-chip>
            </td>

            <td>{{ article.updatedAt }}</td>

            <td class="text-right">
              <v-btn size="small" variant="text" :to="`/admin/articles/${article.id}/edit`">
                Bewerken
              </v-btn>
            </td>
          </tr>

          <tr v-if="sortedArticles.length === 0">
            <td colspan="6">
              <div class="notion-empty-state ma-4">
                <div class="text-subtitle-1 font-weight-medium mb-1">Geen artikelen gevonden</div>
                <p class="page-subtitle mb-0">Pas je filters aan of maak een nieuw artikel aan.</p>
              </div>
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const search = ref('')
const selectedProject = ref('Alle projecten')
const selectedStatus = ref('Alle statussen')
const sortBy = ref('Recent gewijzigd')

const projects = ['Alle projecten', 'Knowledgebase Portal', 'Client Onboarding', 'Support Docs']

const statuses = ['Alle statussen', 'Draft', 'Published', 'Archived']

const sortOptions = ['Recent gewijzigd', 'Titel A-Z']

const articles = ref([
  {
    id: 1,
    title: 'Installatie handleiding',
    slug: 'installatie-handleiding',
    summary: 'Stapsgewijze uitleg voor het opzetten van het project.',
    project: 'Knowledgebase Portal',
    category: 'Installatie',
    status: 'Published',
    updatedAt: '2026-03-10',
  },
  {
    id: 2,
    title: 'Inloggen als klant',
    slug: 'inloggen-als-klant',
    summary: 'Hoe klanten toegang krijgen tot hun eigen omgeving.',
    project: 'Client Onboarding',
    category: 'Toegang',
    status: 'Draft',
    updatedAt: '2026-03-12',
  },
  {
    id: 3,
    title: 'Problemen oplossen',
    slug: 'problemen-oplossen',
    summary: 'Veelvoorkomende issues en mogelijke oplossingen.',
    project: 'Support Docs',
    category: 'Troubleshooting',
    status: 'Published',
    updatedAt: '2026-03-11',
  },
])

const filteredArticles = computed(() => {
  const query = search.value.toLowerCase().trim()

  return articles.value.filter((article) => {
    const matchesSearch =
      !query ||
      article.title.toLowerCase().includes(query) ||
      article.summary.toLowerCase().includes(query) ||
      article.category.toLowerCase().includes(query) ||
      article.slug.toLowerCase().includes(query)

    const matchesProject =
      selectedProject.value === 'Alle projecten' || article.project === selectedProject.value

    const matchesStatus =
      selectedStatus.value === 'Alle statussen' || article.status === selectedStatus.value

    return matchesSearch && matchesProject && matchesStatus
  })
})

const sortedArticles = computed(() => {
  const items = [...filteredArticles.value]

  if (sortBy.value === 'Titel A-Z') {
    return items.sort((a, b) => a.title.localeCompare(b.title))
  }

  return items.sort((a, b) => b.updatedAt.localeCompare(a.updatedAt))
})
</script>
