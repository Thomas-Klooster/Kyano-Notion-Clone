<template>
    <div class="dashboard-page">
        <div class="dashboard-shell page-shell">
            <section class="dashboard-hero hero">
                <div class="hero-content u-min-w-0">
                    <div class="hero-meta-line u-flex-center u-wrap u-gap-8">
                        <span class="hero-pill u-inline-flex u-items-center">Categorie</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ category.workspace }}</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ filteredProjects.length }} projecten</span>
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
                            @click="goToProject(project.id)" @keydown.enter="goToProject(project.id)"
                            @keydown.space.prevent="goToProject(project.id)">
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

                            <button class="project-row-right u-gap-12 tree-toggle" type="button"
                                @click.stop="toggleProject(project.slug)">
                                <v-icon size="18" class="project-arrow">
                                    {{ expandedProjects.includes(project.id) ? 'mdi-chevron-down' : 'mdi-chevron-right'
                                    }}
                                </v-icon>
                            </button>
                        </div>

                        <div v-if="expandedProjects.includes(project.id)" class="article-list">
                            <div v-for="article in project.articles" :key="article.id" class="article-row">
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
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const auth = useAuthStore()
const route = useRoute()
const router = useRouter()
const loading = ref(false)
const error = ref(false)
const search = ref('')
const expandedProjects = ref([])

const category = ref({ name: '', projects: [] })

    onMounted(async () => {
    if (!auth.initialized) return
    loading.value = true
    try {
        await axios.get('/sanctum/csrf-cookie');
        const { data } = await axios.get(`/api/categories/${route.params.slug}`)
        category.value = data
    } catch (err) {
        error.value = 'Geen projecten gevonden'
    } finally {
        loading.value = false
    }
})


const filteredProjects = computed(() => {
    const query = search.value.trim().toLowerCase()

    if (!query) return category.value.projects

    return category.value.projects.filter(project =>
        project.name.toLowerCase().includes(query)
    )
})

function toggleProject(id) {
    expandedProjects.value = expandedProjects.value.includes(id)
        ? expandedProjects.value.filter(projectId => projectId !== id)
        : [...expandedProjects.value, id]
}

function goToProject(slug) {
    router.push(`/project/${slug}`)
}

</script>

