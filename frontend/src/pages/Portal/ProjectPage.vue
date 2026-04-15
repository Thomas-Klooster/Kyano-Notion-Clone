<template>
    <div class="dashboard-page">
        <div class="dashboard-shell">
            <section class="dashboard-hero">
                <div class="hero-content">
                    <div class="hero-meta-line">
                        <span class="hero-pill">Project</span>
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

            <section class="project-list-card">
                <div class="project-list-head">
                    <div>
                        <div class="section-kicker">Overzicht</div>
                        <h2 class="section-title">Artikelen</h2>
                    </div>

                    <div class="project-list-controls">
                        <div class="search-field">
                            <v-icon size="18">mdi-magnify</v-icon>
                            <input v-model="search" type="text" placeholder="Zoek een artikel..." />
                        </div>
                    </div>
                </div>

                <div v-if="filteredArticles.length" class="project-table-wrap">
                    <div class="project-table">
                        <div v-for="article in filteredArticles" :key="article.id"
                            class="project-row project-row-clickable" role="button" tabindex="0"
                            @click="goToArticle(article.id)" @keydown.enter="goToArticle(article.id)"
                            @keydown.space.prevent="goToArticle(article.id)">
                            <div class="project-row-main">
                                <div class="project-icon">
                                    <v-icon size="18">mdi-file-document-outline</v-icon>
                                </div>

                                <div class="project-info">
                                    <div class="project-name">{{ article.title }}</div>
                                    <div class="project-meta">
                                        <span>{{ article.tags.join(', ') }}</span>
                                        <span class="dot">•</span>
                                        <span>{{ article.updatedAt }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="empty-state">
                    <div class="empty-state-icon">
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
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const search = ref('')

const project = ref({
    id: 111,
    name: 'Frontend',
    workspace: 'Kyano',
    category: 'Development',
    articles: [
        {
            id: 1111,
            title: 'Design system richtlijnen',
            tags: ['Design', 'UI'],
            updatedAt: 'Vandaag bijgewerkt',
            status: 'Gepubliceerd',
        },
        {
            id: 1112,
            title: 'Component structuur',
            tags: ['Vue', 'Frontend'],
            updatedAt: 'Gisteren bijgewerkt',
            status: 'Concept',
        },
        {
            id: 1113,
            title: 'Navigatie en routing',
            tags: ['Router', 'Frontend'],
            updatedAt: '2 dagen geleden',
            status: 'Gepubliceerd',
        },
        {
            id: 1114,
            title: 'Form validatie',
            tags: ['Forms', 'UX'],
            updatedAt: 'Vorige week',
            status: 'Gepubliceerd',
        },
    ],
})

const filteredArticles = computed(() => {
    const query = search.value.trim().toLowerCase()

    if (!query) return project.value.articles

    return project.value.articles.filter(article =>
        article.title.toLowerCase().includes(query) ||
        article.status.toLowerCase().includes(query) ||
        article.tags.some(tag => tag.toLowerCase().includes(query))
    )
})

function goToArticle(id) {
    router.push(`/article/${id}`)
}
</script>

