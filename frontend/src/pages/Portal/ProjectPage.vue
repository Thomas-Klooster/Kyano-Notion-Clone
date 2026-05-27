<template>
  <div class="entity-page admin-studio-page">
    <div class="entity-shell page-shell admin-studio-shell">
      <section class="entity-hero hero admin-hero">
        <div class="admin-hero-bg-shapes" aria-hidden="true">
          <div class="admin-hero-shape admin-hero-shape-1" />
          <div class="admin-hero-shape admin-hero-shape-2" />
        </div>

        <div class="hero-content u-min-w-0">
          <div class="hero-meta-line u-flex-center u-wrap u-gap-8">
            <span class="hero-pill u-inline-flex u-items-center">Project</span>
          </div>

          <h1 class="hero-title">{{ project.name }}</h1>
          <p class="hero-subtitle">Bekijk alle artikelen binnen dit project.</p>
        </div>
      </section>

      <div class="project-hero-subtitle-section">
        <div class="project-description-card card card-elevated card-rounded 2xl mb-3">
          <div class="project-list-head card-head section-kicker">
            <h4>Beschrijving</h4>
          </div>
          
          <div v-if="loading" class="empty-state">
            <v-icon size="30">mdi-loading mdi-spin</v-icon>
            <p>Project beschrijving wordt geladen...</p>
          </div>

          <div v-else-if="error" class="empty-state">
              <v-icon size="30">mdi-alert-outline</v-icon>
              <p>{{ error }}</p>
            </div>

            <p  v-else class="hero-project-subtitle" style="background: rgb(255, 255, 255, 0.15); border-radius: 1rem">
            {{ projectDescription }}
          </p>
        </div>

        <div class="project-description-card card card-elevated card-rounded 2xl mb-3">
          <div class="project-list-head card-head section-kicker">
            <h4>Project info</h4>
          </div>

          <article>
            <div v-if="loading" class="empty-state">
              <v-icon size="30">mdi-loading mdi-spin</v-icon>
              <p>Projectinformatie wordt geladen...</p>
            </div>

            <div v-else-if="error" class="empty-state">
              <v-icon size="30">mdi-alert-outline</v-icon>
              <p>{{ error }}</p>
            </div>

            <div v-else class="child-rows">
              <button v-for="item in projectInfoItems" :key="item.label"
                class="projects-child-row project-info-row-button" type="button"
                :disabled="!item.action" @click="handleProjectInfoClick(item)">
                <div class="child-row-main">
                  <div class="entity-icon icon-box entity-icon-soft">
                    <v-icon size="18">{{ item.icon }}</v-icon>
                  </div>

                  <div>
                    <div class="entity-name">{{ item.value }}</div>
                    <div class="entity-meta">
                      <span>{{ item.label }}</span>
                      <span v-if="item.helper" class="dot">•</span>
                      <span v-if="item.helper">{{ item.helper }}</span>
                    </div>
                  </div>
                </div>
              </button>
            </div>
          </article>
        </div>
      </div>

      <section ref="articlesSection" class="project-list-card card card-elevated card-rounded-2xl">
        <div class="project-list-head card-head">
          <div>
            <div class="section-kicker">Overzicht</div>
            <h2 class="section-title">Artikelen</h2>
          </div>

                    <div class="project-list-controls u-flex u-items-center u-wrap u-gap-12">
                        <div class="search-field">
                            <v-icon size="18">mdi-magnify</v-icon>
                            <input v-model="search" type="text" placeholder="Zoek een artikel..." />
                        </div>
                    </div>
                </div>

                <div v-if="loading" class="empty-state">
                    <v-icon size="24">mdi-loading mdi-spin</v-icon>
                    <p>Projecten zijn aan het laden...</p>
                </div>

                <div v-else-if="error" class="empty-state">
                    <v-icon size="30">mdi-alert-circle-outline</v-icon>
                    <p>{{ error }}</p>
                </div>

                <div v-else-if="filteredArticles.length" class="project-table-wrap">
                    <div class="project-table">
                        <div v-for="article in filteredArticles" :key="article.id"
                            class="project-row project-row-clickable" role="button" tabindex="0"
                            @click="goToArticle(article.slug)" @keydown.enter="goToArticle(article.slug)"
                            @keydown.space.prevent="goToArticle(article.slug)">
                            <div class="project-row-main u-min-w-0">
                                <div class="project-icon icon-box">
                                    <v-icon size="18">mdi-file-document-outline</v-icon>
                                </div>

                                <div class="project-info">
                                    <div class="project-name">{{ article.title }}</div>
                                    <div class="project-meta">
                                        <span>{{ formatArticleTags(article) }}</span>
                                        <span class="dot">•</span>
                                        <span>{{ article.updated_at }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="empty-state">
                    <div class="empty-state-icon icon-box">
                        <v-icon size="24">mdi-file-search-outline</v-icon>
                    </div>
                    <h3>Geen artikelen gevonden</h3>
                    <p>Probeer een andere zoekterm.</p>
                </div>
            </section>
        </div>
    </div>
</template>

<script setup>
import { getProject } from '@/services/projectService'
import { computed, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const articlesSection = ref(null)
const search = ref('')
const loading = ref(false)
const error = ref(false)
const project = ref({
  name: '',
  description: '',
  workspace: '',
  workspaceSlug: '',
  category: '',
  categorySlug: '',
  articles: [],
})

function normalizeTagNames(tags) {
  if (!Array.isArray(tags)) return []

  return tags
    .map((tag) => {
      if (typeof tag === 'string') return tag
      if (tag && typeof tag === 'object' && 'name' in tag) return tag.name
      return ''
    })
    .filter(Boolean)
}

function formatArticleTags(article) {
  const tagNames = normalizeTagNames(article?.tags)
  return tagNames.length ? tagNames.join(', ') : 'Geen tags'
}

const projectDescription = computed(() => {
  const description = project.value.description?.trim()
  return description || 'Geen beschrijving beschikbaar voor dit project.'
})

const articleCount = computed(() => project.value.articles?.length ?? 0)

const articleCountLabel = computed(() =>
  articleCount.value === 1 ? '1 artikel' : `${articleCount.value} artikelen`,
)

const projectInfoItems = computed(() => [
  {
    label: 'Workspace',
    value: project.value.workspace || 'Geen workspace gekoppeld',
    helper: 'Open workspace',
    icon: 'mdi-view-dashboard-outline',
    action: project.value.workspaceSlug ? () => goToWorkspace(project.value.workspaceSlug) : null,
  },
  {
    label: 'Categorie',
    value: project.value.category || 'Geen categorie gekoppeld',
    helper: 'Open categorie',
    icon: 'mdi-shape-outline',
    action: project.value.categorySlug ? () => goToCategory(project.value.categorySlug) : null,
  },
  {
    label: 'Artikelen',
    value: articleCountLabel.value,
    helper: 'Ga naar overzicht',
    icon: 'mdi-file-document-multiple-outline',
    action: scrollToArticles,
  },
])

watch(
  () => route.params.slug,
  () => {
    loadArticles()
  },
  { immediate: true },
)

async function loadArticles() {
  loading.value = true
  error.value = false
  search.value = ''
  project.value = {
    name: '',
    description: '',
    workspace: '',
    workspaceSlug: '',
    category: '',
    categorySlug: '',
    articles: [],
  }

  try {
    const current = await getProject(route.params.slug)
    project.value.name = current.name
    project.value.description = current.description ?? ''
    project.value.category = current.category
    project.value.categorySlug = current.category_slug ?? ''
    project.value.workspace = current.workspace
    project.value.workspaceSlug = current.workspace_slug ?? ''
    project.value.articles = current.articles ?? []
  } catch {
    error.value = 'Dit project kon niet worden gevonden.'
  } finally {
    loading.value = false
  }
}

const filteredArticles = computed(() => {
    const articles = project.value.articles ?? []
    const query = search.value.trim().toLowerCase()

    if (!query) return articles

    return articles.filter(article =>
        article.title.toLowerCase().includes(query) ||
        article.status.toLowerCase().includes(query) ||
        normalizeTagNames(article.tags).some(tag => tag.toLowerCase().includes(query))
    )
})

function goToArticle(slug) {
  router.push(`/article/${slug}`)
}

function goToCategory(slug) {
  router.push(`/category/${slug}`)
}

function goToWorkspace(slug) {
  router.push(`/workspace/${slug}`)
}

function scrollToArticles() {
  articlesSection.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

function handleProjectInfoClick(item) {
  item.action?.()
}
</script>
