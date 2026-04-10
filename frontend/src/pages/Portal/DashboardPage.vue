<template>
    <div class="dashboard-page">
        <div class="dashboard-shell">
            <section class="dashboard-hero">
                <div class="hero-content">
                    <div class="hero-meta-line">
                        <span class="hero-pill">Klantportaal</span>
                        <span class="hero-meta-separator">•</span>
                        <span>{{ filteredWorkspaces.length }} workspaces</span>
                    </div>

                    <h1 class="hero-title">Jouw workspaces</h1>

                    <p class="hero-subtitle">
                        Bekijk alle workspaces waar je toegang toe hebt en navigeer direct door categorieën,
                        projecten en artikelen heen.
                    </p>
                </div>
            </section>

            <section class="project-list-card">
                <div class="project-list-head">
                    <div>
                        <div class="section-kicker">Overzicht</div>
                        <h2 class="section-title">Alle workspaces</h2>
                    </div>

                    <div class="project-list-controls">
                        <div class="search-field">
                            <v-icon size="18">mdi-magnify</v-icon>
                            <input v-model="search" type="text" placeholder="Zoeken..." />
                        </div>
                    </div>
                </div>

                <div v-if="filteredWorkspaces.length" class="tree-list">
                    <div v-for="workspace in filteredWorkspaces" :key="workspace.id" class="tree-group">
                        <div class="tree-row tree-row-workspace">
                            <router-link :to="workspaceRoute(workspace)" class="tree-row-main tree-link">
                                <div class="tree-icon">
                                    <v-icon size="18">mdi-view-dashboard-outline</v-icon>
                                </div>

                                <div class="tree-info">
                                    <div class="tree-name">{{ workspace.name }}</div>
                                    <div class="tree-meta">
                                        <span>{{ workspace.categories.length }} categorieën</span>
                                        <span class="dot">•</span>
                                        <span>{{ countProjects(workspace) }} projecten</span>
                                        <span class="dot">•</span>
                                        <span>{{ countArticles(workspace) }} artikelen</span>
                                    </div>
                                </div>
                            </router-link>

                            <button class="tree-toggle" type="button" @click="toggleWorkspace(workspace.id)">
                                <v-icon size="18">
                                    {{ expandedWorkspaces.includes(workspace.id) ? 'mdi-chevron-down' :
                                        'mdi-chevron-right' }}
                                </v-icon>
                            </button>
                        </div>

                        <div v-if="expandedWorkspaces.includes(workspace.id)" class="tree-children">
                            <div v-for="category in workspace.categories" :key="category.id" class="tree-group">
                                <div class="tree-row tree-row-category">
                                    <router-link :to="categoryRoute(workspace, category)"
                                        class="tree-row-main tree-link">
                                        <div class="tree-icon">
                                            <v-icon size="18">mdi-folder-outline</v-icon>
                                        </div>

                                        <div class="tree-info">
                                            <div class="tree-name">{{ category.name }}</div>
                                            <div class="tree-meta">
                                                <span>{{ category.projects.length }} projecten</span>
                                                <span class="dot">•</span>
                                                <span>{{ countArticlesInCategory(category) }} artikelen</span>
                                            </div>
                                        </div>
                                    </router-link>

                                    <button class="tree-toggle" type="button" @click="toggleCategory(category.id)">
                                        <v-icon size="18">
                                            {{ expandedCategories.includes(category.id) ? 'mdi-chevron-down' :
                                                'mdi-chevron-right' }}
                                        </v-icon>
                                    </button>
                                </div>

                                <div v-if="expandedCategories.includes(category.id)" class="tree-children">
                                    <div v-for="project in category.projects" :key="project.id" class="tree-group">
                                        <div class="tree-row tree-row-project">
                                            <router-link :to="projectRoute(project)" class="tree-row-main tree-link">
                                                <div class="tree-icon">
                                                    <v-icon size="18">mdi-briefcase-outline</v-icon>
                                                </div>

                                                <div class="tree-info">
                                                    <div class="tree-name">{{ project.name }}</div>
                                                    <div class="tree-meta">
                                                        <span>{{ project.articles.length }} artikelen</span>
                                                        <span class="dot">•</span>
                                                        <span>{{ project.updatedAt }}</span>
                                                    </div>
                                                </div>
                                            </router-link>

                                            <button class="tree-toggle" type="button"
                                                @click="toggleProject(project.id)">
                                                <v-icon size="18">
                                                    {{ expandedProjects.includes(project.id) ? 'mdi-chevron-down' :
                                                        'mdi-chevron-right' }}
                                                </v-icon>
                                            </button>
                                        </div>

                                        <div v-if="expandedProjects.includes(project.id)"
                                            class="tree-children tree-children-articles">
                                            <router-link v-for="article in project.articles" :key="article.id"
                                                :to="articleRoute(project, article)"
                                                class="tree-row tree-row-article tree-link">
                                                <div class="tree-row-main">
                                                    <div class="tree-icon">
                                                        <v-icon size="18">mdi-file-document-outline</v-icon>
                                                    </div>

                                                    <div class="tree-info">
                                                        <div class="tree-name">{{ article.title }}</div>
                                                        <div class="tree-meta">
                                                            <span>{{ article.tags.join(', ') }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <v-icon size="18" class="project-arrow">mdi-chevron-right</v-icon>
                                            </router-link>
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
                    <h3>Niets gevonden</h3>
                    <p>Probeer een andere zoekterm.</p>
                </div>
            </section>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const search = ref('')

const expandedWorkspaces = ref([1])
const expandedCategories = ref([11])
const expandedProjects = ref([111])

const workspaces = ref([
    {
        id: 1,
        name: 'Kyano',
        categories: [
            {
                id: 1,
                name: 'Development',
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
            },
            {
                id: 2,
                name: 'Documentatie',
                projects: [
                    {
                        id: 121,
                        name: 'Handleidingen',
                        updatedAt: '2 dagen geleden',
                        articles: [
                            { id: 1211, title: 'Installatie handleiding', tags: ['Setup'] },
                            { id: 1212, title: 'Content beheer', tags: ['CMS'] },
                        ],
                    },
                ],
            },
        ],
    },
    {
        id: 2,
        name: 'Client Portal',
        categories: [
            {
                id: 3,
                name: 'Support',
                projects: [
                    {
                        id: 211,
                        name: 'Kennisbank',
                        updatedAt: 'Vorige week',
                        articles: [
                            { id: 2111, title: 'Veelgestelde vragen', tags: ['FAQ'] },
                            { id: 2112, title: 'Toegang en rechten', tags: ['Rechten', 'Users'] },
                        ],
                    },
                ],
            },
        ],
    },
])

const filteredWorkspaces = computed(() => {
    const query = search.value.trim().toLowerCase()

    if (!query) return workspaces.value

    return workspaces.value
        .map(workspace => {
            const workspaceMatches = workspace.name.toLowerCase().includes(query)

            const categories = workspace.categories
                .map(category => {
                    const categoryMatches = category.name.toLowerCase().includes(query)

                    const projects = category.projects
                        .map(project => {
                            const projectMatches =
                                project.name.toLowerCase().includes(query) ||
                                project.updatedAt.toLowerCase().includes(query)

                            const articles = project.articles.filter(article =>
                                article.title.toLowerCase().includes(query) ||
                                article.tags.some(tag => tag.toLowerCase().includes(query))
                            )

                            if (projectMatches || articles.length) {
                                return {
                                    ...project,
                                    articles: projectMatches ? project.articles : articles,
                                }
                            }

                            return null
                        })
                        .filter(Boolean)

                    if (categoryMatches || projects.length) {
                        return {
                            ...category,
                            projects: categoryMatches ? category.projects : projects,
                        }
                    }

                    return null
                })
                .filter(Boolean)

            if (workspaceMatches || categories.length) {
                return {
                    ...workspace,
                    categories: workspaceMatches ? workspace.categories : categories,
                }
            }

            return null
        })
        .filter(Boolean)
})

function toggleWorkspace(id) {
    expandedWorkspaces.value = expandedWorkspaces.value.includes(id)
        ? expandedWorkspaces.value.filter(item => item !== id)
        : [...expandedWorkspaces.value, id]
}

function toggleCategory(id) {
    expandedCategories.value = expandedCategories.value.includes(id)
        ? expandedCategories.value.filter(item => item !== id)
        : [...expandedCategories.value, id]
}

function toggleProject(id) {
    expandedProjects.value = expandedProjects.value.includes(id)
        ? expandedProjects.value.filter(item => item !== id)
        : [...expandedProjects.value, id]
}

function countProjects(workspace) {
    return workspace.categories.reduce((total, category) => total + category.projects.length, 0)
}

function countArticles(workspace) {
    return workspace.categories.reduce((total, category) => {
        return total + category.projects.reduce((sum, project) => sum + project.articles.length, 0)
    }, 0)
}

function countArticlesInCategory(category) {
    return category.projects.reduce((total, project) => total + project.articles.length, 0)
}

function workspaceRoute(workspace) {
    return {
        name: '',
        params: {
            id: workspace.id,
        },
    }
}

function categoryRoute(workspace, category) {
    return {
        name: 'category',
        params: {
            workspaceId: workspace.id,
            id: category.id,
        },
    }
}

function projectRoute(project) {
    return {
        name: 'project',
        params: {
            id: project.id,
        },
    }
}

function articleRoute(project, article) {
    return {
        name: 'article',
        params: {
            projectId: project.id,
            id: article.id,
        },
    }
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
    /* background: linear-gradient(340deg, rgba(36, 161, 199, 1) 0%, rgba(76, 180, 212, 1) 100%); */
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
    min-width: 360px;
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

.tree-list {
    display: flex;
    flex-direction: column;
}

.tree-group {
    display: flex;
    flex-direction: column;
}

.tree-children {
    padding-left: 26px;
    border-left: 1px solid #f1efeb;
    margin-left: 28px;
}

.tree-children-articles {
    padding-bottom: 8px;
}

.tree-row {
    min-height: 76px;
    padding: 0 20px 0 28px;
    border-bottom: 1px solid #f0efec;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    transition: background 0.15s ease;
}

.tree-row:hover {
    background: #fbfbfa;
}

.tree-row-main {
    min-width: 0;
    display: flex;
    align-items: center;
    gap: 14px;
    flex: 1;
}

.tree-link {
    text-decoration: none;
    color: inherit;
}

.tree-row-workspace {
    background: #fff;
}

.tree-row-category {
    background: #fdfdfc;
}

.tree-row-project {
    background: #fff;
}

.tree-row-article {
    min-height: 68px;
    padding-right: 28px;
}

.tree-icon {
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

.tree-info {
    min-width: 0;
}

.tree-name {
    color: #191919;
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.3;
}

.tree-meta {
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

.project-arrow {
    color: #b6b4af;
    flex-shrink: 0;
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

    .search-field {
        min-width: 100%;
    }

    .tree-row {
        padding-left: 20px;
        padding-right: 20px;
    }

    .tree-children {
        margin-left: 20px;
        padding-left: 18px;
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

    .tree-row {
        align-items: flex-start;
        flex-direction: column;
        padding-top: 16px;
        padding-bottom: 16px;
    }

    .tree-toggle {
        align-self: flex-end;
        margin-top: -44px;
    }

    .tree-row-article {
        padding-right: 20px;
    }
}
</style>