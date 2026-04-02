<template>
    <div class="dashboard-page">
        <div class="dashboard-shell">
            <section class="dashboard-hero">
                <div class="hero-content">
                    <div class="hero-meta-line">
                        <span class="hero-pill">Categorie</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ category.workspace }}</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ filteredProjects.length }} projecten</span>
                    </div>

                    <h1 class="hero-title">Categorie: {{ category.name }}</h1>

                    <p class="hero-subtitle">
                        Bekijk alle projecten binnen deze categorie.
                    </p>
                </div>
            </section>

            <section class="project-list-card">
                <div class="project-list-head">
                    <div>
                        <div class="section-kicker">Overzicht</div>
                        <h2 class="section-title">Projecten</h2>
                    </div>

                    <div class="project-list-controls">
                        <div class="search-field">
                            <v-icon size="18">mdi-magnify</v-icon>
                            <input v-model="search" type="text" placeholder="Zoek een project..." />
                        </div>
                    </div>
                </div>

                <div v-if="filteredProjects.length" class="project-table">
                    <div v-for="project in filteredProjects" :key="project.id" class="project-group">
                        <div class="project-row project-row-clickable" role="button" tabindex="0"
                            @click="goToProject(project.id)" @keydown.enter="goToProject(project.id)"
                            @keydown.space.prevent="goToProject(project.id)">
                            <div class="project-row-main">
                                <div class="project-icon">
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

                            <button class="project-row-right tree-toggle" type="button"
                                @click.stop="toggleProject(project.id)">
                                <v-icon size="18" class="project-arrow">
                                    {{ expandedProjects.includes(project.id) ? 'mdi-chevron-down' : 'mdi-chevron-right'
                                    }}
                                </v-icon>
                            </button>
                        </div>

                        <div v-if="expandedProjects.includes(project.id)" class="article-list">
                            <div v-for="article in project.articles" :key="article.id" class="article-row">
                                <div class="article-row-main">
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
                    <div class="empty-state-icon">
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
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const search = ref('')
const expandedProjects = ref([])

const category = ref({
    id: 1,
    name: 'Development',
    workspace: 'Kyano',
    projects: [
        {
            id: 111,
            name: 'Frontend',
            updatedAt: 'Vandaag bijgewerkt',
            articles: [
                { id: 1111, title: 'Design system richtlijnen', tags: ['Design', 'UI'] },
                { id: 1112, title: 'Component structuur', tags: ['Vue', 'Frontend'] },
            ],
        },
        {
            id: 112,
            name: 'Backend',
            updatedAt: 'Gisteren bijgewerkt',
            articles: [
                { id: 1121, title: 'API authenticatie', tags: ['Laravel', 'Auth'] },
                { id: 1122, title: 'Database structuur', tags: ['Database', 'Backend'] },
            ],
        },
    ],
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

function goToProject(id) {
    router.push(`/project/${id}`)
}
</script>

<style scoped>
.dashboard-page {
    min-height: 100%;
    background: #f7f7f5;
}

.dashboard-shell {
    max-width: 1200px;
    margin: 0 auto;
    padding: 32px 28px 72px;
}

.dashboard-hero {
    min-height: 200px;
    border-radius: 26px;
    background: #24A1C7;
    margin-bottom: 20px;
    padding: 32px 36px;
    display: flex;
    align-items: end;
}

.hero-content {
    max-width: 760px;
}

.hero-meta-line {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 14px;
    color: rgba(255, 255, 255, 0.86);
    font-size: 0.92rem;
}

.hero-pill {
    display: inline-flex;
    align-items: center;
    min-height: 28px;
    padding: 0 10px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.18);
    color: #ffffff;
    font-size: 0.82rem;
    font-weight: 600;
}

.hero-meta-separator {
    color: rgba(255, 255, 255, 0.6);
}

.hero-title {
    margin: 0;
    color: #ffffff;
    font-size: 3rem;
    line-height: 1.05;
    font-weight: 800;
    letter-spacing: -0.03em;
}

.hero-subtitle {
    max-width: 720px;
    margin: 16px 0 0;
    color: rgba(255, 255, 255, 0.86);
    font-size: 1.02rem;
    line-height: 1.8;
}

.tree-toggle {
    width: 40px;
    height: 40px;
    border: none;
    background: transparent;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #7b7974;
    cursor: pointer;
    flex-shrink: 0;
    transition: background 0.15s ease, color 0.15s ease;
}

.tree-toggle:hover {
    background: #f2f1ee;
    color: #191919;
}

.project-list-card {
    border: 1px solid #ececea;
    border-radius: 26px;
    background: #ffffff;
    box-shadow: 0 2px 10px rgba(15, 15, 15, 0.03);
    overflow: hidden;
}

.project-list-head {
    padding: 28px 28px 22px;
    border-bottom: 1px solid #f0efec;
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
}

.section-kicker {
    margin-bottom: 8px;
    color: #787774;
    font-size: 0.86rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.section-title {
    margin: 0;
    color: #191919;
    font-size: 1.7rem;
    line-height: 1.2;
    font-weight: 700;
    letter-spacing: -0.02em;
}

.project-list-controls {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.search-field {
    min-width: 320px;
    height: 48px;
    padding: 0 14px;
    border-radius: 14px;
    border: 1px solid #e7e5e4;
    background: #fbfbfa;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #787774;
}

.search-field input {
    width: 100%;
    border: none;
    outline: none;
    background: transparent;
    color: #191919;
    font-size: 0.96rem;
}

.search-field input::placeholder {
    color: #9b9a97;
}

.project-table {
    display: flex;
    flex-direction: column;
}

.project-group {
    border-bottom: 1px solid #f0efec;
}

.project-group:last-child {
    border-bottom: none;
}

.project-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    padding: 20px 28px;
    transition: background 0.15s ease;
}

.project-row:hover {
    background: #fbfbfa;
}

.project-row-clickable {
    cursor: pointer;
}

.project-row-main {
    min-width: 0;
    display: flex;
    align-items: center;
    gap: 14px;
}

.project-icon,
.article-icon {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    background: #ffffff;
    border: 1px solid #ececea;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #5f5e5b;
    flex-shrink: 0;
}

.project-info,
.article-info {
    min-width: 0;
}

.project-name,
.article-title {
    color: #191919;
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.3;
}

.project-meta,
.article-meta {
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    color: #8a8884;
    font-size: 0.9rem;
}

.dot {
    color: #c0beb9;
}

.project-row-right {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
}

.project-customer {
    color: #787774;
    font-size: 0.92rem;
    font-weight: 500;
}

.article-list {
    padding: 0 28px 16px 84px;
    background: #fcfcfb;
}

.article-row {
    min-height: 68px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    padding: 14px 0;
    border-top: 1px solid #f0efec;
}

.article-row-main {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
}

.empty-state {
    padding: 56px 28px;
    text-align: center;
}

.empty-state-icon {
    width: 58px;
    height: 58px;
    margin: 0 auto 16px;
    border-radius: 18px;
    background: #f3f3f1;
    border: 1px solid #ececea;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #5f5e5b;
}

.empty-state h3 {
    margin: 0 0 8px;
    color: #191919;
    font-size: 1.15rem;
}

.empty-state p {
    margin: 0;
    color: #787774;
    line-height: 1.7;
}

@media (max-width: 900px) {
    .dashboard-shell {
        padding-left: 20px;
        padding-right: 20px;
    }

    .hero-title {
        font-size: 2.3rem;
    }

    .project-list-head {
        padding: 22px 20px 18px;
    }

    .project-row {
        padding: 18px 20px;
    }

    .article-list {
        padding: 0 20px 16px 20px;
    }

    .search-field {
        min-width: 100%;
    }
}

@media (max-width: 640px) {
    .dashboard-hero {
        min-height: 180px;
        padding: 24px 20px;
        border-radius: 20px;
    }

    .hero-title {
        font-size: 2rem;
    }

    .hero-subtitle {
        font-size: 0.98rem;
    }

    .project-row,
    .article-row {
        align-items: flex-start;
        flex-direction: column;
    }

    .project-row-right {
        width: 100%;
        justify-content: space-between;
    }
}
</style>