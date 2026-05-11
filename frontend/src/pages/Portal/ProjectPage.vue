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
                        <span class="hero-meta-separator">•</span>
                        <span>{{ project.workspace }}</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ project.category }}</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ filteredArticles.length === 1 ? '1 artikel' : `${filteredArticles.length} artikelen`}}</span>
                    </div>

                    <h1 class="hero-title">{{ project.name }}</h1>

                    <p class="hero-subtitle" style="background: rgb(255, 255, 255, 0.15); padding: .75rem 1.5rem; border-radius: 1rem;">
                        <h3>Beschrijving</h3>
                        {{ projectDescription }}
                    </p>
                </div>
            </section>

            <section class="project-list-card card card-elevated card-rounded-2xl">
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
import { useRouter, useRoute } from 'vue-router'

const route = useRoute()
const router = useRouter()
const search = ref('')
const loading = ref(false)
const error = ref(false)
const project = ref({ name: '', description: '', workspace: '', category: '', articles: [] })

function normalizeTagNames(tags) {
    if (!Array.isArray(tags)) return []

    return tags
        .map(tag => {
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

watch(
    () => route.params.slug,
    () => {
        loadArticles()
    },
    { immediate: true }
)

async function loadArticles() {

     loading.value = true
     error.value = false
     search.value = ''
     project.value = { name: '', description: '', workspace: '', category: '', articles: [] }

     try {
        const current = await getProject(route.params.slug)
        project.value.name = current.name
        project.value.description = current.description ?? ''
        project.value.category = current.category
        project.value.workspace = current.workspace
        project.value.articles = current.articles ?? []
     } catch(err) {
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
</script>
