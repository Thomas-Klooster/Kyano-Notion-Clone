<template>
    <div class="dashboard-page">
        <div class="dashboard-shell page-shell">
            <section class="dashboard-hero hero">
                <div class="hero-content u-min-w-0">
                    <div class="hero-meta-line u-flex-center u-wrap u-gap-8">
                        <span class="hero-pill u-inline-flex u-items-center">Project</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ project.workspace }}</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ project.category }}</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ filteredArticles.length }} artikelen</span>
                    </div>

                    <h1 class="hero-title">Project: {{ project.name }}</h1>

                    <p class="hero-subtitle">
                        Bekijk alle artikelen binnen dit project.
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
                                        <!-- <span>{{ article.tags.join(', ') }}</span> -->
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
import { getProjects } from '@/services/projectService'
import { computed, onMounted, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const route = useRoute()
const router = useRouter()
const search = ref('')
const loading = ref(false)
const error = ref(false)
const project = ref({ name: '', workspace: '', articles: [] })



onMounted(loadArticles)

async function loadArticles() {

     loading.value = true
     error.value = false

     try {
        const allProjects = await getProjects()
        const current = allProjects.find(c => c.slug === route.params.slug)
        console.log('found:', current)
        console.log(allProjects[0])
        console.log('articles:', current.articles)
        console.log('project:', project)

        console.log('route params:', route.params)

        if (current) {
            project.value.name = current.name
            project.value.category = current.category
            project.value.articles = current.articles
        }

     } catch(err) {
        error.value = 'Geen artikelen gevonden'
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
        article.tags.some(tag => tag.toLowerCase().includes(query))
    )
})
function goToArticle(slug) {
    router.push(`/article/${slug}`)
}
</script>

