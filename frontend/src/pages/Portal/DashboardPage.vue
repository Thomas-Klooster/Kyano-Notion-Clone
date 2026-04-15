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
