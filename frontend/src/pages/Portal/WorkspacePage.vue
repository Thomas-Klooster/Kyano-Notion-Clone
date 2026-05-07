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
                        <span class="hero-pill u-inline-flex u-items-center">Workspace</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ filteredCategories.length  === 1 ? '1 categorie' : `${filteredCategories.length} categorieën` }}</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ totalProjects === 1 ? '1 project' : `${totalProjects} projecten`}}</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ totalArticles === 1 ? '1 artikel' : `${totalArticles} artikelen` }}</span>
                    </div>
                    <h1 class="hero-title">{{ workspace.name }}</h1>

                    <p class="hero-subtitle">
                        Bekijk alle categorieën, projecten en artikelen binnen deze workspace.
                    </p>
                </div>
            </section>

            <section class="project-list-card card card-elevated card-rounded-2xl">
                <div class="project-list-head card-head">
                    <div>
                        <div class="section-kicker">Overzicht</div>
                        <h2 class="section-title">Categorieën</h2>
                    </div>

                    <div class="project-list-controls u-flex u-items-center u-wrap u-gap-12">
                        <div class="search-field">
                            <v-icon size="18">mdi-magnify</v-icon>
                            <input v-model="search" type="text" placeholder="Zoek een categorie, project of artikel..." />
                        </div>
                    </div>
                </div>

                <div v-if="loading" class="empty-state">
                <v-icon size="24">mdi-loading mdi-spin</v-icon>
                <p>Categorieën zijn aan het laden...</p>    
                </div>

                <div v-else-if="error" class="empty-state">
                    <v-icon size="30">mdi-alert-circle-outline</v-icon>
                    <p>{{ error }}</p>
                </div>

                <div v-else-if="filteredCategories.length" class="project-table">
                    <div
                        v-for="category in filteredCategories"
                        :key="category.slug"
                        class="project-group"
                    >
                        <div
                            class="project-row project-row-clickable"
                            role="button"
                            tabindex="0"
                            @click="goToCategory(category.slug)"
                            @keydown.enter="goToCategory(category.slug)"
                            @keydown.space.prevent="goToCategory(category.slug)"
                        >
                            <div class="project-row-main u-min-w-0">
                                <div class="project-icon icon-box">
                                    <v-icon size="18">mdi-folder-outline</v-icon>
                                </div>

                                <div class="project-info">
                                    <div class="project-name">{{ category.name }}</div>
                                    <div class="project-meta">
                                        <span>{{ category.projects.length }} projecten</span>
                                        <span class="dot">•</span>
                                        <span>{{ countArticlesInCategory(category) }} artikelen</span>
                                    </div>
                                </div>
                            </div>

                            <button
                                class="project-row-right u-gap-12 tree-toggle"
                                type="button"
                                @click.stop="toggleCategory(category.slug)"
                            >
                                <v-icon size="18" class="project-arrow">
                                    {{ expandedCategories.includes(category.slug) ? 'mdi-chevron-down' : 'mdi-chevron-right' }}
                                </v-icon>
                            </button>
                        </div>

                        <div v-if="expandedCategories.includes(category.slug)" class="article-list">
                            <div
                                v-for="project in category.projects"
                                :key="project.slug"
                                class="project-group project-group-nested"
                            >
                                <div
                                    class="project-row project-row-clickable project-row-nested"
                                    role="button"
                                    tabindex="0"
                                    @click="goToProject(project.slug)"
                                    @keydown.enter="goToProject(project.slug)"
                                    @keydown.space.prevent="goToProject(project.slug)"
                                >
                                    <div class="project-row-main u-min-w-0">
                                        <div class="project-icon icon-box">
                                            <v-icon size="18">mdi-briefcase-outline</v-icon>
                                        </div>

                                        <div class="project-info">
                                            <div class="project-name">{{ project.name }}</div>
                                            <div class="project-meta">
                                                <span>{{ project.articles.length }} artikelen</span>
                                                <span class="dot">•</span>
                                                <span>{{ project.updated_at }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <button
                                        class="project-row-right u-gap-12 tree-toggle"
                                        type="button"
                                        @click.stop="toggleProject(project.slug)"
                                    >
                                        <v-icon size="18" class="project-arrow">
                                            {{ expandedProjects.includes(project.slug) ? 'mdi-chevron-down' : 'mdi-chevron-right' }}
                                        </v-icon>
                                    </button>
                                </div>

                                <div v-if="expandedProjects.includes(project.slug)" class="article-list article-list-nested">
                                    <div
                                        v-for="article in project.articles"
                                        :key="article.slug"
                                        class="article-row article-row-clickable"
                                        role="button"
                                        tabindex="0"
                                        @click="goToArticle(article.slug)"
                                        @keydown.enter="goToArticle(article.slug)"
                                        @keydown.space.prevent="goToArticle(article.slug)"
                                    >
                                        <div class="article-row-main u-min-w-0">
                                            <div class="article-icon">
                                                <v-icon size="18">mdi-file-document-outline</v-icon>
                                            </div>

                                            <div class="article-info">
                                                <div class="article-title">{{ article.title }}</div>
                                                <div class="article-meta">
                                                <span>{{ (article.tags ?? []).join(', ') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <v-icon size="18" class="project-arrow">mdi-chevron-right</v-icon>
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
                    <h3>Geen resultaten gevonden</h3>
                    <p>Probeer een andere zoekterm.</p>
                </div>
            </section>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { getWorkspaces } from '@/services/workspaceService'
import { useRouter, useRoute } from "vue-router";

const router = useRouter()
const route = useRoute()
const search = ref('')
const workspace = ref({ name: '', categories: [], projects: [], articles: [] })
const loading = ref(false)
const error = ref(false)
const expandedCategories = ref([])
const expandedProjects = ref([])

const categories = computed(() => workspace.value.categories ?? [])


onMounted(loadCategories)

async function loadCategories() {
    loading.value = true
    error.value = false
    try {
        const allWorkspaces = await getWorkspaces()
        const current = allWorkspaces.find(w => w.slug === route.params.slug)
        if (current) {
            workspace.value.name = current.name
            workspace.value.categories = current.categories
        }
    } catch (err) {
        error.value = 'Kon categorieën niet inladen...'
    } finally {
        loading.value = false
    }
}

const filteredCategories = computed(() => {
    const query = search.value.trim().toLowerCase()

    if (!query) return categories.value

    return categories.value
        .map(category => {
            const categoryMatches = category.name.toLowerCase().includes(query)

            const projects = category.projects
                .map(project => {
                   const projectName = (project.name ?? '').toLowerCase()
                   const updated_at = String(
                    project.updated_at ?? project.updated_at ?? ''
                   ).toLowerCase()

                   const projectMatches = 
                   projectName.includes(query) ||
                   updated_at.includes(query)


                    const articles = (project.articles ?? []).filter(article =>{
                        const title = (article.title ?? '').toLowerCase()
                        const tags = Array.isArray(article.tags) ? article.tags : []

                        return (
                            title.includes(query) ||
                            tags.some(tag =>
                                String(tag).toLowerCase().includes(query)
                            )
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

            if (categoryMatches || projects.length) {
                return {
                    ...category,
                    projects: categoryMatches ? (category.projects ?? []) : projects,
                }
            }

            return null
        })
        .filter(Boolean)
})

const totalProjects = computed(() => {
    return filteredCategories.value.reduce((total, category) => {
        return total + category.projects.length
    }, 0)
})

const totalArticles = computed(() => {
    return filteredCategories.value.reduce((total, category) => {
        return total + category.projects.reduce((sum, project) => sum + project.articles.length, 0)
    }, 0)
})

function countArticlesInCategory(category) {
    return category.projects.reduce((total, project) => total + project.articles.length, 0)
}

function toggleCategory(id) {
    expandedCategories.value = expandedCategories.value.includes(id)
        ? expandedCategories.value.filter(categoryId => categoryId !== id)
        : [...expandedCategories.value, id]
}

function toggleProject(id) {
    expandedProjects.value = expandedProjects.value.includes(id)
        ? expandedProjects.value.filter(projectId => projectId !== id)
        : [...expandedProjects.value, id]
}

function goToCategory(slug) {
    router.push(`/category/${slug}`)
}

function goToProject(slug) {
    router.push(`/project/${slug}`)
}

function goToArticle(slug) {
    router.push(`/article/${slug}`)
}
</script>
