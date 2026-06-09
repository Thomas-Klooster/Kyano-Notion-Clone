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
                        <span class="hero-pill u-inline-flex u-items-center">Categorie</span>
                        <!-- <span class="hero-meta-separator">•</span> -->
                        <span>{{ category.workspace }}</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ filteredProjects.length === 1 ? '1 project' : `${filteredProjects.length} projecten`
                            }}</span>
                    </div>

                    <h1 class="hero-title">{{ category.name }}</h1>

                    <p class="hero-subtitle">
                        Bekijk alle projecten binnen deze categorie.
                    </p>
                </div>
            </section>

            <section class="project-list-card card card-elevated card-rounded-2xl">
                <div class="project-list-head card-head">
                    <div>
                        <div class="section-kicker">Overzicht</div>
                        <h2 class="section-title">Projecten</h2>
                    </div>

                    <div class="project-list-controls u-flex u-items-center u-wrap u-gap-12">
                        <div class="search-field">
                            <v-icon size="18">mdi-magnify</v-icon>
                            <input v-model="search" type="text" placeholder="Zoek een project..." />
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

                <div v-else-if="filteredProjects.length" class="project-table">
                    <div v-for="project in filteredProjects" :key="project.id" class="project-group">
                        <div class="project-row project-row-clickable" role="button" tabindex="0"
                            @click="goToProject(project.slug)" @keydown.enter="goToProject(project.slug)"
                            @keydown.space.prevent="goToProject(project.slug)">
                            <div class="project-row-main u-min-w-0">
                                <div class="project-icon icon-box">
                                    <v-icon size="18">mdi-briefcase-outline</v-icon>
                                </div>

                                <div class="project-info">
                                    <div class="project-name">{{ project.name }}</div>
                                    <div class="project-meta">
                                        <span>{{ project.articles.length === 1 ? '1 artikel' : `${project.articles.length} artikelen`}}</span>
                                        <span class="dot">•</span>
                                        <span>{{ project.updated_at }}</span>
                                    </div>
                                </div>
                            </div>

                            <button class="project-row-right u-gap-12 tree-toggle" type="button"
                                @click.stop="toggleProject(project.slug)">
                                <v-icon size="18" class="project-arrow">
                                    {{ expandedProjects.includes(project.slug) ? 'mdi-chevron-down' :
                                        'mdi-chevron-right'
                                    }}
                                </v-icon>
                            </button>
                        </div>

                        <div v-if="expandedProjects.includes(project.slug)" class="article-list">
                            <div v-for="article in project.articles" :key="article.slug"
                                class="tree-row tree-row-project" role="button" tabindex="0"
                                @click="goToArticle(article.slug)" @keydown.enter="goToArticle(article.slug)"
                                @keydown.space.prevent="goToArticle(article.slug)">
                                <div class="article-row-main u-min-w-0">
                                    <div class="article-icon">
                                        <v-icon size="18">mdi-file-document-outline</v-icon>
                                    </div>

                                    <div class="article-info">
                                        <div class="article-title">{{ article.title }}</div>
                                        <div class="article-meta">
                                            <span>{{ formatArticleTags(article) }}</span>
                                            <span v-if="article.updated_at" class="dot">•</span>
                                            <span v-if="article.updated_at">{{ article.updated_at }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="empty-state">
                    <div class="empty-state-icon icon-box">
                        <v-icon size="24">mdi-folder-search-outline</v-icon>
                    </div>
                    <h3>Geen projecten gevonden</h3>
                    <p>Probeer een andere zoekterm.</p>
                </div>
            </section>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getCategory } from '@/services/categoryService'

const route = useRoute()
const router = useRouter()
const loading = ref(false)
const error = ref(false)
const search = ref('')
const expandedProjects = ref([])

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

const category = ref({ name: '', workspace: '', projects: [], articles: [], tags: [] })

watch(
    () => route.params.slug,
    () => {
        loadProjects()
    },
    { immediate: true }
)

async function loadProjects() {
    loading.value = true
    error.value = false
    search.value = ''
    expandedProjects.value = []
    category.value = { name: '', workspace: '', projects: [], articles: [], tags: [] }

    try {
        const current = await getCategory(route.params.slug)
        category.value.name = current.name
        category.value.workspace = current.workspace
        category.value.projects = current.projects ?? []
        category.value.articles = current.articles ?? []
    } catch (err) {
        error.value = 'Deze categorie kon niet worden gevonden.'
    } finally {
        loading.value = false
    }
}

const filteredProjects = computed(() => {
    const query = search.value.trim().toLowerCase()

    if (!query) return category.value.projects

    return category.value.projects
        .map(project => {
            const projectName = (project.name ?? '').toLowerCase()
            const projectMatches = projectName.includes(query)

            const articles = (project.articles ?? []).filter(article => {
                const title = (article.title ?? '').toLowerCase()
                const tags = normalizeTagNames(article.tags)

                return (
                    title.includes(query) ||
                    tags.some(tag => tag.toLowerCase().includes(query))
                )
            })

            if (projectMatches || articles.length) {
                return {
                    ...project,
                    articles: projectMatches ? (project.articles ?? []) : articles,
                }
            }

            return null
        })
        .filter(Boolean)
})

function toggleProject(id) {
    expandedProjects.value = expandedProjects.value.includes(id)
        ? expandedProjects.value.filter(projectId => projectId !== id)
        : [...expandedProjects.value, id]
}

function goToProject(slug) {
    router.push(`/project/${slug}`)
}

function goToArticle(slug) {
    router.push(`/article/${slug}`)
}
</script>
