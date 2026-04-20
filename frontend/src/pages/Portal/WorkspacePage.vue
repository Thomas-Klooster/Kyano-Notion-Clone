<template>
    <div class="dashboard-page">
        <div class="dashboard-shell page-shell">
            <section class="dashboard-hero hero">
                <div class="hero-content u-min-w-0">
                    <div class="hero-meta-line u-flex-center u-wrap u-gap-8">
                        <span class="hero-pill u-inline-flex u-items-center">Workspace</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ filteredCategories.length }} categorieën</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ totalProjects }} projecten</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ totalArticles }} artikelen</span>
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
                        :key="category.id"
                        class="project-group"
                    >
                        <div
                            class="project-row project-row-clickable"
                            role="button"
                            tabindex="0"
                            @click="goToCategory(category.id)"
                            @keydown.enter="goToCategory(category.id)"
                            @keydown.space.prevent="goToCategory(category.id)"
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
                                @click.stop="toggleCategory(category.id)"
                            >
                                <v-icon size="18" class="project-arrow">
                                    {{ expandedCategories.includes(category.id) ? 'mdi-chevron-down' : 'mdi-chevron-right' }}
                                </v-icon>
                            </button>
                        </div>

                        <div v-if="expandedCategories.includes(category.id)" class="article-list">
                            <div
                                v-for="project in category.projects"
                                :key="project.id"
                                class="project-group project-group-nested"
                            >
                                <div
                                    class="project-row project-row-clickable project-row-nested"
                                    role="button"
                                    tabindex="0"
                                    @click="goToProject(project.id)"
                                    @keydown.enter="goToProject(project.id)"
                                    @keydown.space.prevent="goToProject(project.id)"
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
                                                <span>{{ project.updatedAt }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <button
                                        class="project-row-right u-gap-12 tree-toggle"
                                        type="button"
                                        @click.stop="toggleProject(project.id)"
                                    >
                                        <v-icon size="18" class="project-arrow">
                                            {{ expandedProjects.includes(project.id) ? 'mdi-chevron-down' : 'mdi-chevron-right' }}
                                        </v-icon>
                                    </button>
                                </div>

                                <div v-if="expandedProjects.includes(project.id)" class="article-list article-list-nested">
                                    <div
                                        v-for="article in project.articles"
                                        :key="article.id"
                                        class="article-row article-row-clickable"
                                        role="button"
                                        tabindex="0"
                                        @click="goToArticle(article.id)"
                                        @keydown.enter="goToArticle(article.id)"
                                        @keydown.space.prevent="goToArticle(article.id)"
                                    >
                                        <div class="article-row-main u-min-w-0">
                                            <div class="article-icon">
                                                <v-icon size="18">mdi-file-document-outline</v-icon>
                                            </div>

                                            <div class="article-info">
                                                <div class="article-title">{{ article.title }}</div>
                                                <div class="article-meta">
                                                    <span>{{ article.tags.join(', ') }}</span>
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
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()
const search = ref('')
const workspace = ref({ name: '' })
const loading = ref(false)
const error = ref(false)
const expandedCategories = ref([11])
const expandedProjects = ref([111])

const categories = ref([])


onMounted(async () => {
    if (!auth.initialized) return
    loading.value = true
    try {
        await axios.get('/sanctum/csrf-cookie');
        const [categoriesRes, workspaceRes, articlesRes] = await Promise.all([
            axios.get('/api/categories'),
            axios.get('/api/workspaces'),
            // axios.get('/api/articles')
        ])
        categories.value = categoriesRes.data
        workspace.value = workspaceRes.data[0]
        // article.value = articlesRes.data
    } catch (err) {
        error.value = 'Geen categorieen gevonden'
    } finally {
        loading.value = false
    }
})


/* 

1. Onmounted async
2. if auth not initialized return loading.value  tue
3. attempt to try to axios get sanctum csrf cookie route
4. call a repose to get the route of the api afterwards call the value and make it a data response
5. catch any errors on the way and give it a value that nothing is found
6. finally loading value = false

*/




const filteredCategories = computed(() => {
    const query = search.value.trim().toLowerCase()

    if (!query) return categories.value

    return categories.value
        .map(category => {
            const categoryMatches = category.name.toLowerCase().includes(query)

            const projects = category.projects
                .map(project => {
                   const projectName = (project.name ?? '').toLowerCase()
                   const updatedAt = String(
                    project.updatedAt ?? project.updated_at ?? ''
                   ).toLowerCase()

                   const projectMatches = 
                   projectName.includes(query) ||
                   updatedAt.includes(query)


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

function goToCategory(id) {
    router.push(`/category/${id}`)
}

function goToProject(id) {
    router.push(`/project/${id}`)
}

function goToArticle(id) {
    router.push(`/article/${id}`)
}
</script>