<template>
  <div class="entity-page">
    <div class="entity-shell page-shell">
      <section class="entity-hero hero">
        <div class="hero-content u-min-w-0">
          <div class="hero-meta-line u-flex-center u-wrap u-gap-8">
            <span class="hero-pill u-inline-flex u-items-center">Admin</span>
            <span class="hero-meta-separator">•</span>
            <span>{{ filteredArticles.length }} artikelen</span>
          </div>

          <h1 class="hero-title">Artikelen</h1>

          <p class="hero-subtitle">
            Beheer knowledgebase-artikelen per project, categorie en status.
          </p>
        </div>
      </section>

      <section class="entity-card card card-elevated card-rounded-2xl">
        <div class="entity-card-head card-head article-card-head">
          <div class="article-head-top">
            <div>
              <div class="section-kicker">Overzicht</div>
              <h2 class="section-title">Alle artikelen</h2>
            </div>

            <v-btn
              class="entity-create-btn"
              variant="text"
              prepend-icon="mdi-plus"
              to="/admin/articles/new"
            >
              Nieuw artikel
            </v-btn>
          </div>

          <div class="article-filters-row u-gap-12">
            <div class="search-field article-search-field">
              <v-icon size="18">mdi-magnify</v-icon>
              <input
                v-model="search"
                type="text"
                placeholder="Zoek op titel, samenvatting of categorie..."
              />
            </div>

            <div class="filter-select">
              <select v-model="selectedProject">
                <option v-for="project in projects" :key="project" :value="project">
                  {{ project }}
                </option>
              </select>
              <v-icon size="18">mdi-chevron-down</v-icon>
            </div>

            <div class="filter-select">
              <select v-model="selectedStatus">
                <option v-for="status in statuses" :key="status" :value="status">
                  {{ status }}
                </option>
              </select>
              <v-icon size="18">mdi-chevron-down</v-icon>
            </div>

            <div class="filter-select">
              <select v-model="sortBy">
                <option v-for="option in sortOptions" :key="option" :value="option">
                  {{ option }}
                </option>
              </select>
              <v-icon size="18">mdi-chevron-down</v-icon>
            </div>
          </div>
        </div>

        <div v-if="sortedArticles.length" class="entity-list">
          <div
            v-for="article in sortedArticles"
            :key="article.id"
            class="entity-row article-row"
          >
            <div class="entity-row-main article-row-main u-min-w-0">
              <div class="entity-icon icon-box">
                <v-icon size="18">mdi-file-document-outline</v-icon>
              </div>

              <div class="entity-info article-info">
                <div class="entity-name article-title-row">
                  <span>{{ article.title }}</span>

                  <v-chip
                    size="small"
                    class="entity-chip article-status-chip"
                    :class="statusClass(article.status)"
                  >
                    {{ article.status }}
                  </v-chip>
                </div>

                <div class="article-summary">
                  {{ article.summary }}
                </div>

                <div class="entity-meta article-meta">
                  <span>{{ article.project }}</span>
                  <span class="dot">•</span>
                  <span>{{ article.category }}</span>
                  <span class="dot">•</span>
                  <span>Laatst gewijzigd: {{ formatDate(article.updatedAt) }}</span>
                </div>
              </div>
            </div>

            <div class="entity-actions u-flex u-items-center u-wrap u-gap-10 article-actions">
              <v-btn
                size="small"
                variant="text"
                :to="`/admin/articles/${article.id}/edit`"
              >
                Bewerken
              </v-btn>
            </div>
          </div>
        </div>

        <div v-else class="empty-state">
          <div class="empty-state-icon icon-box">
            <v-icon size="24">mdi-file-document-outline</v-icon>
          </div>
          <h3>Geen artikelen gevonden</h3>
          <p>Pas je filters aan of maak een nieuw artikel aan.</p>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const search = ref('')
const selectedProject = ref('Alle projecten')
const selectedStatus = ref('Alle statussen')
const sortBy = ref('Recent gewijzigd')

const projects = [
  'Alle projecten',
  'Knowledgebase Portal',
  'Client Onboarding',
  'Support Docs',
]

const statuses = ['Alle statussen', 'Draft', 'Published', 'Archived']
const sortOptions = ['Recent gewijzigd', 'Titel A-Z']

const articles = ref([
  {
    id: 1,
    title: 'Installatie handleiding',
    summary: 'Stapsgewijze uitleg voor het opzetten van het project.',
    project: 'Knowledgebase Portal',
    category: 'Installatie',
    status: 'Published',
    updatedAt: '2026-03-10',
  },
  {
    id: 2,
    title: 'Inloggen als klant',
    summary: 'Hoe klanten toegang krijgen tot hun eigen omgeving.',
    project: 'Client Onboarding',
    category: 'Toegang',
    status: 'Draft',
    updatedAt: '2026-03-12',
  },
  {
    id: 3,
    title: 'Problemen oplossen',
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
      article.category.toLowerCase().includes(query) 

    const matchesProject =
      selectedProject.value === 'Alle projecten' ||
      article.project === selectedProject.value

    const matchesStatus =
      selectedStatus.value === 'Alle statussen' ||
      article.status === selectedStatus.value

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

function formatDate(date) {
  return new Intl.DateTimeFormat('nl-NL', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  }).format(new Date(date))
}

function statusClass(status) {
  return `status-${status.toLowerCase()}`
}
</script>

