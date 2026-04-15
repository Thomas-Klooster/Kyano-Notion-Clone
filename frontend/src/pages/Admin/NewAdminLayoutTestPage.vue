<template>
  <div class="entity-page admin-studio-page">
    <div class="entity-shell admin-studio-shell">
      <section class="entity-hero">
        <div class="hero-content">
          <div class="hero-meta-line">
            <span class="hero-pill">Admin</span>
            <span class="hero-meta-separator">•</span>
            <span>{{ totalRecords }} records</span>
            <span class="hero-meta-separator">•</span>
            <span>1 content studio</span>
          </div>

          <h1 class="hero-title">Kyano Knowledgebase Admin</h1>

          <p class="hero-subtitle">
            Beheer je workspaces, categorieën, projecten en artikelen.
          </p>
        </div>
      </section>

      <section class="stats-grid">
        <article class="stat-card">
          <div class="stat-label">Workspaces</div>
          <div class="stat-value">{{ counts.workspaces }}</div>
        </article>

        <article class="stat-card">
          <div class="stat-label">Categorieën</div>
          <div class="stat-value">{{ counts.categories }}</div>
        </article>

        <article class="stat-card">
          <div class="stat-label">Projecten</div>
          <div class="stat-value">{{ counts.projects }}</div>
        </article>

        <article class="stat-card">
          <div class="stat-label">Artikelen</div>
          <div class="stat-value">{{ counts.articles }}</div>
        </article>
      </section>

      <section class="entity-card studio-card">
        <div class="entity-card-head studio-head">
          <div>
            <div class="section-kicker">Overzicht</div>
            <h2 class="section-title">Content structuur</h2>
          </div>

          <div class="entity-controls studio-controls">
            <v-select
              v-model="selectedCustomer"
              :items="customerOptions"
              variant="plain"
              density="comfortable"
              hide-details
              flat
              class="studio-select"
            />

            <v-select
              v-model="selectedKind"
              :items="kindOptions"
              variant="plain"
              density="comfortable"
              hide-details
              flat
              class="studio-select"
            />

            <div class="search-field studio-search-field">
              <v-icon size="18">mdi-magnify</v-icon>
              <input v-model="search" type="text" placeholder="Zoek in workspaces, categorieën, projecten of artikelen..." />
            </div>

            <v-menu location="bottom end">
              <template #activator="{ props }">
                <v-btn v-bind="props" rounded="xl" prepend-icon="mdi-plus" class="entity-create-btn">
                  Nieuw item
                </v-btn>
              </template>

              <v-list>
                <v-list-item @click="openCreateDialog('workspace')">
                  <v-list-item-title>Nieuwe workspace</v-list-item-title>
                </v-list-item>
                <v-list-item @click="openCreateDialog('category', selectedWorkspaceId ? { workspaceId: selectedWorkspaceId } : null)">
                  <v-list-item-title>Nieuwe categorie</v-list-item-title>
                </v-list-item>
                <v-list-item @click="openCreateDialog('project', selectedCategoryId ? { categoryId: selectedCategoryId } : null)">
                  <v-list-item-title>Nieuw project</v-list-item-title>
                </v-list-item>
                <v-list-item @click="openCreateDialog('article', selectedProjectId ? { projectId: selectedProjectId } : null)">
                  <v-list-item-title>Nieuw artikel</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </div>
        </div>

        <div class="studio-layout">
          <aside class="studio-tree-panel">
            <div class="panel-section-head">
              <div>
                <div class="panel-kicker">Navigatie</div>
                <h3 class="panel-title">Structuur</h3>
              </div>
            </div>

            <div v-if="filteredWorkspaces.length" class="tree-list">
              <article
                v-for="workspace in filteredWorkspaces"
                :key="workspace.id"
                class="tree-card"
              >
                <div class="tree-row tree-row-root">
                  <button class="tree-row-trigger" @click="selectEntity('workspace', workspace.id)">
                    <div class="tree-row-main">
                      <div class="entity-icon">
                        <v-icon size="18">mdi-view-dashboard-outline</v-icon>
                      </div>

                      <div class="tree-row-info">
                        <div class="tree-row-title">
                          {{ workspace.name }}
                        </div>
                        <div class="tree-row-meta">
                          <span>{{ workspace.customer }}</span>
                          <span class="dot">•</span>
                          <span>{{ workspace.categories.length }} categorieën</span>
                        </div>
                      </div>
                    </div>
                  </button>

                  <div class="tree-row-side">
                    <v-chip size="small" class="entity-chip">Workspace</v-chip>
                    <v-btn icon size="small" variant="text" class="tree-toggle-btn" @click.stop="toggleWorkspace(workspace.id)">
                      <v-icon size="18">{{ isExpanded(expandedWorkspaces, workspace.id) ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                    </v-btn>
                  </div>
                </div>

                <div class="tree-children" v-show="isExpanded(expandedWorkspaces, workspace.id)">
                  <article
                    v-for="category in workspace.categories"
                    :key="category.id"
                    class="tree-branch"
                    v-show="matchesEntity(category.name, workspace.name) && (selectedKind === 'Alles' || selectedKind === 'Categorieën') || selectedKind === 'Alles'"
                  >
                    <div class="tree-row tree-row-child">
                      <button class="tree-row-trigger" @click="selectEntity('category', category.id)">
                        <div class="tree-row-main">
                          <div class="entity-icon entity-icon-soft">
                            <v-icon size="18">mdi-shape-outline</v-icon>
                          </div>
                          <div class="tree-row-info">
                            <div class="tree-row-title">{{ category.name }}</div>
                            <div class="tree-row-meta">
                              <span>{{ category.projects.length }} projecten</span>
                              <span class="dot">•</span>
                              <span>{{ countArticlesInCategory(category) }} artikelen</span>
                            </div>
                          </div>
                        </div>
                      </button>

                      <div class="tree-row-side">
                        <v-btn icon size="small" variant="text" class="tree-toggle-btn" @click.stop="toggleCategory(category.id)">
                          <v-icon size="18">{{ isExpanded(expandedCategories, category.id) ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                        </v-btn>
                      </div>
                    </div>

                    <div class="tree-children nested-level" v-show="isExpanded(expandedCategories, category.id)">
                      <article
                        v-for="project in category.projects"
                        :key="project.id"
                        class="tree-branch"
                        v-show="projectVisible(project, category, workspace)"
                      >
                        <div class="tree-row tree-row-child">
                          <button class="tree-row-trigger" @click="selectEntity('project', project.id)">
                            <div class="tree-row-main">
                              <div class="entity-icon entity-icon-soft project-icon">
                                <v-icon size="18">mdi-briefcase-outline</v-icon>
                              </div>
                              <div class="tree-row-info">
                                <div class="tree-row-title">{{ project.name }}</div>
                                <div class="tree-row-meta">
                                  <span>{{ project.status }}</span>
                                  <span class="dot">•</span>
                                  <span>{{ project.articles.length }} artikelen</span>
                                </div>
                              </div>
                            </div>
                          </button>

                          <div class="tree-row-side">
                            <v-btn icon size="small" variant="text" class="tree-toggle-btn" @click.stop="toggleProject(project.id)">
                              <v-icon size="18">{{ isExpanded(expandedProjects, project.id) ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                            </v-btn>
                          </div>
                        </div>

                        <div class="tree-children nested-level articles-level" v-show="isExpanded(expandedProjects, project.id)">
                          <button
                            v-for="article in project.articles"
                            :key="article.id"
                            v-show="articleVisible(article, project, category, workspace)"
                            class="tree-row tree-row-leaf"
                            @click="selectEntity('article', article.id)"
                          >
                            <div class="tree-row-main">
                              <div class="entity-icon entity-icon-soft article-icon">
                                <v-icon size="18">mdi-file-document-outline</v-icon>
                              </div>
                              <div class="tree-row-info">
                                <div class="tree-row-title">{{ article.title }}</div>
                                <div class="tree-row-meta">
                                  <span>{{ article.status }}</span>
                                  <span class="dot">•</span>
                                  <span>{{ article.updatedAt }}</span>
                                </div>
                              </div>
                            </div>
                          </button>
                        </div>
                      </article>
                    </div>
                  </article>
                </div>
              </article>
            </div>

            <div v-else class="empty-state compact-empty">
              <div class="empty-state-icon">
                <v-icon size="24">mdi-file-tree-outline</v-icon>
              </div>
              <h3>Niets gevonden</h3>
              <p>Pas je filters aan of maak direct een nieuw item aan.</p>
            </div>
          </aside>

          <section class="studio-detail-panel">
            <template v-if="selectedEntity">
              <div class="detail-head">
                <div>
                  <div class="section-kicker">{{ selectedEntityTypeLabel }}</div>
                  <h3 class="detail-title">{{ selectedEntity.name || selectedEntity.title }}</h3>
                  <p class="detail-subtitle">{{ selectedEntityDescription }}</p>
                </div>

                <div class="entity-actions detail-actions">
                  <v-btn v-if="selectedEntityType !== 'article'" size="small" variant="tonal" @click="openCreateChildDialog" class="entity-create-btn">
                    {{ createChildButtonLabel }}
                  </v-btn>
                  <v-btn size="small" variant="text" @click="openEditDialog(selectedEntityType, selectedEntity.id)">
                    Bewerken
                  </v-btn>
                  <v-btn size="small" variant="text" class="delete-btn" @click="openDeleteDialog(selectedEntityType, selectedEntity.id)">
                    Verwijderen
                  </v-btn>
                </div>
              </div>

              <div class="detail-grid">
                <article class="detail-card">
                  <div class="detail-card-head">
                    <div>
                      <div class="panel-kicker">Basisinformatie</div>
                      <h4 class="panel-title">Eigenschappen</h4>
                    </div>
                  </div>

                  <div class="detail-meta-grid">
                    <div class="meta-item">
                      <span class="meta-label">Klant</span>
                      <span class="meta-value">{{ selectedEntityCustomer }}</span>
                    </div>
                    <div class="meta-item" v-if="selectedEntityType !== 'workspace'">
                      <span class="meta-label">Bovenliggend item</span>
                      <span class="meta-value">{{ selectedParentLabel }}</span>
                    </div>
                    <div class="meta-item" v-if="selectedEntityType === 'project'">
                      <span class="meta-label">Status</span>
                      <span class="meta-value">{{ selectedEntity.status }}</span>
                    </div>
                    <div class="meta-item" v-if="selectedEntityType === 'article'">
                      <span class="meta-label">Slug</span>
                      <span class="meta-value">/{{ selectedEntity.slug }}</span>
                    </div>
                    <div class="meta-item" v-if="selectedEntityType === 'article'">
                      <span class="meta-label">Laatst gewijzigd</span>
                      <span class="meta-value">{{ selectedEntity.updatedAt }}</span>
                    </div>
                  </div>
                </article>

                <article v-if="showsSummaryCard" class="detail-card">
                  <div class="detail-card-head">
                    <div>
                      <div class="panel-kicker">Inhoud</div>
                      <h4 class="panel-title">Samenvatting</h4>
                    </div>
                  </div>

                  <p class="detail-description">
                    {{ selectedEntity.summary || selectedEntity.description || defaultEntityDescription }}
                  </p>
                </article>
              </div>

              <article v-if="showsRelationsCard" class="detail-card child-list-card">
                <div class="detail-card-head child-list-head">
                  <div>
                    <div class="panel-kicker">Relaties</div>
                    <h4 class="panel-title">{{ childSectionTitle }}</h4>
                  </div>

                  <v-btn class="entity-create-btn" size="small" variant="text" @click="openCreateChildDialog">
                    {{ createChildButtonLabel }}
                  </v-btn>
                </div>

                <div v-if="childRows.length" class="child-rows">
                  <div v-for="child in childRows" :key="`${child.type}-${child.id}`" class="child-row">
                    <div class="child-row-main">
                      <div class="entity-icon entity-icon-soft">
                        <v-icon size="18">{{ iconForType(child.type) }}</v-icon>
                      </div>

                      <div>
                        <div class="entity-name">{{ child.name || child.title }}</div>
                        <div class="entity-meta">
                          <span>{{ labelForType(child.type) }}</span>
                          <span v-if="child.status" class="dot">•</span>
                          <span v-if="child.status">{{ child.status }}</span>
                          <span v-if="child.updatedAt" class="dot">•</span>
                          <span v-if="child.updatedAt">{{ child.updatedAt }}</span>
                        </div>
                      </div>
                    </div>

                    <div class="entity-actions">
                      <v-btn size="small" variant="text" @click="selectEntity(child.type, child.id)">
                        Openen
                      </v-btn>
                      <v-btn size="small" variant="text" @click="openEditDialog(child.type, child.id)">
                        Bewerken
                      </v-btn>
                    </div>
                  </div>
                </div>

                <div v-else class="empty-state compact-empty child-empty">
                  <div class="empty-state-icon">
                    <v-icon size="24">mdi-plus-box-outline</v-icon>
                  </div>
                  <h3>Nog geen gekoppelde items</h3>
                  <p>Voeg hier direct een onderliggend item toe.</p>
                </div>
              </article>
            </template>

            <div v-else class="empty-detail-state">
              <div class="empty-state-icon">
                <v-icon size="24">mdi-cursor-default-click-outline</v-icon>
              </div>
              <h3>Selecteer een item</h3>
              <p>
                Kies links een workspace, categorie, project of artikel om de details en acties te bekijken.
              </p>
            </div>
          </section>
        </div>
      </section>
    </div>

    <v-dialog v-model="editorOpen" max-width="680">
      <v-card class="dialog-card" rounded="xl">
        <div class="dialog-head">
          <div>
            <div class="section-kicker">{{ dialogMode === 'create' ? 'Nieuw item' : 'Bewerken' }}</div>
            <h3 class="dialog-title">
              {{ dialogMode === 'create' ? `Nieuwe ${labelForType(dialogType).toLowerCase()}` : `Bewerk ${labelForType(dialogType).toLowerCase()}` }}
            </h3>
          </div>
        </div>

        <div class="dialog-body">
          <v-text-field
            v-model="draft.name"
            :label="dialogType === 'article' ? 'Titel' : 'Naam'"
            variant="solo-filled"
            flat
            hide-details
            class="notion-soft-input mb-4"
          />

          <v-textarea
            v-if="dialogType === 'project' || dialogType === 'article'"
            v-model="draft.summary"
            :label="dialogType === 'article' ? 'Samenvatting' : 'Beschrijving'"
            variant="solo-filled"
            flat
            hide-details
            rows="4"
            class="notion-soft-input mb-4"
          />

          <v-select
            v-if="dialogType === 'workspace'"
            v-model="draft.customer"
            :items="customerOnlyOptions"
            label="Klant"
            variant="solo-filled"
            flat
            hide-details
            class="notion-soft-input mb-4"
          />

          <v-select
            v-if="dialogType === 'category'"
            v-model="draft.workspaceId"
            :items="workspaceSelectOptions"
            item-title="label"
            item-value="value"
            label="Workspace"
            variant="solo-filled"
            flat
            hide-details
            class="notion-soft-input mb-4"
          />

          <v-select
            v-if="dialogType === 'project'"
            v-model="draft.categoryId"
            :items="categorySelectOptions"
            item-title="label"
            item-value="value"
            label="Categorie"
            variant="solo-filled"
            flat
            hide-details
            class="notion-soft-input mb-4"
          />

          <template v-if="dialogType === 'article'">
            <v-select
              v-model="draft.projectId"
              :items="projectSelectOptions"
              item-title="label"
              item-value="value"
              label="Project"
              variant="solo-filled"
              flat
              hide-details
              class="notion-soft-input mb-4"
            />

            <v-text-field
              v-model="draft.slug"
              label="Slug"
              variant="solo-filled"
              flat
              hide-details
              class="notion-soft-input mb-4"
            />

            <v-select
              v-model="draft.status"
              :items="articleStatusOptions"
              label="Status"
              variant="solo-filled"
              flat
              hide-details
              class="notion-soft-input"
            />
          </template>
        </div>

        <div class="dialog-actions">
          <v-btn variant="text" @click="editorOpen = false">Annuleren</v-btn>
          <v-btn color="primary" rounded="xl" @click="saveDraft">Opslaan</v-btn>
        </div>
      </v-card>
    </v-dialog>

    <v-dialog v-model="deleteOpen" max-width="520">
      <v-card class="dialog-card" rounded="xl">
        <div class="dialog-head">
          <div>
            <div class="section-kicker">Verwijderen</div>
            <h3 class="dialog-title">Weet je het zeker?</h3>
          </div>
        </div>

        <div class="dialog-body">
          <p class="detail-description mb-0">
            Je staat op het punt om <strong>{{ deleteTarget?.name || deleteTarget?.title }}</strong>
            te verwijderen.
          </p>
        </div>

        <div class="dialog-actions">
          <v-btn variant="text" @click="deleteOpen = false">Annuleren</v-btn>
          <v-btn color="error" rounded="xl" @click="confirmDelete">Verwijderen</v-btn>
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'

const search = ref('')
const selectedCustomer = ref('Alle klanten')
const selectedKind = ref('Alles')
const router = useRouter()

const expandedWorkspaces = ref([1])
const expandedCategories = ref([])
const expandedProjects = ref([])

const editorOpen = ref(false)
const deleteOpen = ref(false)
const dialogMode = ref('create')
const dialogType = ref('workspace')
const deleteType = ref(null)
const deleteId = ref(null)

const selectedEntityType = ref('workspace')
const selectedWorkspaceId = ref(1)
const selectedCategoryId = ref(null)
const selectedProjectId = ref(null)
const selectedArticleId = ref(null)

const workspaceId = ref(3)
const categoryId = ref(5)
const projectId = ref(6)
const articleId = ref(8)

const draft = reactive({
  id: null,
  name: '',
  summary: '',
  customer: '',
  workspaceId: null,
  categoryId: null,
  projectId: null,
  status: '',
  slug: '',
})

const workspaceData = ref([
  {
    id: 1,
    name: 'Kyano Core',
    customer: 'Kyano Digital',
    description: 'Centrale workspace voor klantportalen en interne documentatie.',
    categories: [
      {
        id: 1,
        name: 'Development',
        description: 'Technische documentatie en implementatieflows.',
        projects: [
          {
            id: 1,
            name: 'Knowledgebase Portal',
            status: 'Actief',
            description: 'Documentatieportal voor klanten met projectspecifieke content.',
            articles: [
              {
                id: 1,
                title: 'Inloggen als klant',
                slug: 'inloggen-als-klant',
                status: 'Published',
                updatedAt: '2026-04-10',
                summary: 'Uitleg over inloggen, autorisatie en toegang tot eigen projecten.',
              },
              {
                id: 2,
                title: 'Nieuwe artikelen publiceren',
                slug: 'nieuwe-artikelen-publiceren',
                status: 'Draft',
                updatedAt: '2026-04-11',
                summary: 'Stappenplan voor het aanmaken en publiceren van nieuwe artikelen.',
              },
            ],
          },
          {
            id: 2,
            name: 'Customer Dashboard',
            status: 'Concept',
            description: 'Dashboard voor klantnavigatie en projecttoegang.',
            articles: [
              {
                id: 3,
                title: 'Projecten filteren',
                slug: 'projecten-filteren',
                status: 'Published',
                updatedAt: '2026-04-08',
                summary: 'Hoe klanten projecten en categorieën snel terugvinden.',
              },
            ],
          },
        ],
      },
      {
        id: 2,
        name: 'Support',
        description: 'FAQ, probleemoplossing en supportflows.',
        projects: [
          {
            id: 3,
            name: 'Support Center',
            status: 'Actief',
            description: 'Supportdocumentatie voor veelgestelde vragen.',
            articles: [
              {
                id: 4,
                title: 'Problemen oplossen',
                slug: 'problemen-oplossen',
                status: 'Published',
                updatedAt: '2026-04-07',
                summary: 'Veelvoorkomende issues en mogelijke oplossingen.',
              },
            ],
          },
        ],
      },
    ],
  },
  {
    id: 2,
    name: 'Studio North',
    customer: 'Studio North',
    description: 'Workspace voor onboarding en projectoverdracht.',
    categories: [
      {
        id: 3,
        name: 'Onboarding',
        description: 'Content voor onboarding en overdracht.',
        projects: [
          {
            id: 4,
            name: 'Client Onboarding',
            status: 'Actief',
            description: 'Onboardingproject voor nieuwe klanten.',
            articles: [
              {
                id: 5,
                title: 'Eerste oplevering bekijken',
                slug: 'eerste-oplevering-bekijken',
                status: 'Draft',
                updatedAt: '2026-04-09',
                summary: 'Handleiding voor de eerste review door de klant.',
              },
            ],
          },
        ],
      },
    ],
  },
])

const customerOnlyOptions = computed(() => [...new Set(workspaceData.value.map((workspace) => workspace.customer))])
const customerOptions = computed(() => ['Alle klanten', ...customerOnlyOptions.value])
const kindOptions = ['Alles', 'Workspaces', 'Categorieën', 'Projecten', 'Artikelen']
const articleStatusOptions = ['Draft', 'Published', 'Archived']

const workspaceSelectOptions = computed(() =>
  workspaceData.value.map((workspace) => ({
    label: `${workspace.name} · ${workspace.customer}`,
    value: workspace.id,
  }))
)

const categorySelectOptions = computed(() =>
  workspaceData.value.flatMap((workspace) =>
    workspace.categories.map((category) => ({
      label: `${category.name} · ${workspace.name}`,
      value: category.id,
    }))
  )
)

const projectSelectOptions = computed(() =>
  workspaceData.value.flatMap((workspace) =>
    workspace.categories.flatMap((category) =>
      category.projects.map((project) => ({
        label: `${project.name} · ${category.name}`,
        value: project.id,
      }))
    )
  )
)

const normalizedSearch = computed(() => search.value.trim().toLowerCase())

const filteredWorkspaces = computed(() => {
  return workspaceData.value
    .filter((workspace) => {
      const customerMatch = selectedCustomer.value === 'Alle klanten' || workspace.customer === selectedCustomer.value
      if (!customerMatch) return false

      if (!normalizedSearch.value) return true

      return workspaceMatchesSearch(workspace)
    })
    .map((workspace) => ({
      ...workspace,
      categories: workspace.categories
        .filter((category) => categoryVisible(category, workspace))
        .map((category) => ({
          ...category,
          projects: category.projects
            .filter((project) => projectVisible(project, category, workspace))
            .map((project) => ({
              ...project,
              articles: project.articles.filter((article) => articleVisible(article, project, category, workspace)),
            })),
        })),
    }))
    .filter((workspace) => {
      if (selectedKind.value === 'Alles' || selectedKind.value === 'Workspaces') return true
      if (selectedKind.value === 'Categorieën') return workspace.categories.length > 0
      if (selectedKind.value === 'Projecten') return workspace.categories.some((category) => category.projects.length > 0)
      if (selectedKind.value === 'Artikelen') {
        return workspace.categories.some((category) => category.projects.some((project) => project.articles.length > 0))
      }
      return true
    })
})

const counts = computed(() => {
  const workspaces = filteredWorkspaces.value.length
  const categories = filteredWorkspaces.value.reduce((sum, workspace) => sum + workspace.categories.length, 0)
  const projects = filteredWorkspaces.value.reduce(
    (sum, workspace) => sum + workspace.categories.reduce((inner, category) => inner + category.projects.length, 0),
    0,
  )
  const articles = filteredWorkspaces.value.reduce(
    (sum, workspace) =>
      sum +
      workspace.categories.reduce(
        (categorySum, category) =>
          categorySum + category.projects.reduce((projectSum, project) => projectSum + project.articles.length, 0),
        0,
      ),
    0,
  )

  return { workspaces, categories, projects, articles }
})

const totalRecords = computed(() => counts.value.workspaces + counts.value.categories + counts.value.projects + counts.value.articles)

const selectedEntity = computed(() => {
  if (selectedEntityType.value === 'workspace') {
    return findWorkspace(selectedWorkspaceId.value)
  }

  if (selectedEntityType.value === 'category') {
    return findCategory(selectedCategoryId.value)?.category ?? null
  }

  if (selectedEntityType.value === 'project') {
    return findProject(selectedProjectId.value)?.project ?? null
  }

  if (selectedEntityType.value === 'article') {
    return findArticle(selectedArticleId.value)?.article ?? null
  }

  return null
})

const selectedEntityTypeLabel = computed(() => labelForType(selectedEntityType.value))

const showsSummaryCard = computed(() => selectedEntityType.value === 'project' || selectedEntityType.value === 'article')

const showsRelationsCard = computed(() => selectedEntityType.value !== 'article')

const createChildButtonLabel = computed(() => {
  if (selectedEntityType.value === 'workspace') return 'Categorie toevoegen'
  if (selectedEntityType.value === 'category') return 'Project toevoegen'
  if (selectedEntityType.value === 'project') return 'Artikel toevoegen'
  return 'Toevoegen'
})

const selectedEntityDescription = computed(() => {
  if (!selectedEntity.value) return ''

  const descriptions = {
    workspace: 'Workspace-overzicht met gekoppelde categorieën, projecten en artikelen.',
    category: 'Categorie waarin gerelateerde projecten en artikelen samenkomen.',
    project: 'Project met gekoppelde kennisartikelen en publicatiestatus.',
    article: 'Artikel binnen een project, met status, slug en laatste wijziging.',
  }

  return descriptions[selectedEntityType.value]
})

const defaultEntityDescription = computed(() => {
  if (!selectedEntity.value) return ''
  return 'Nog geen aanvullende beschrijving ingevuld voor dit item.'
})

const selectedEntityCustomer = computed(() => {
  if (selectedEntityType.value === 'workspace') return selectedEntity.value?.customer ?? '-'
  if (selectedEntityType.value === 'category') return findCategory(selectedCategoryId.value)?.workspace.customer ?? '-'
  if (selectedEntityType.value === 'project') return findProject(selectedProjectId.value)?.workspace.customer ?? '-'
  if (selectedEntityType.value === 'article') return findArticle(selectedArticleId.value)?.workspace.customer ?? '-'
  return '-'
})

const selectedParentLabel = computed(() => {
  if (selectedEntityType.value === 'category') {
    return findCategory(selectedCategoryId.value)?.workspace.name ?? '-'
  }
  if (selectedEntityType.value === 'project') {
    return findProject(selectedProjectId.value)?.category.name ?? '-'
  }
  if (selectedEntityType.value === 'article') {
    return findArticle(selectedArticleId.value)?.project.name ?? '-'
  }
  return '-'
})

const childRows = computed(() => {
  if (!selectedEntity.value) return []

  if (selectedEntityType.value === 'workspace') {
    return selectedEntity.value.categories.map((category) => ({ ...category, type: 'category' }))
  }

  if (selectedEntityType.value === 'category') {
    return selectedEntity.value.projects.map((project) => ({ ...project, type: 'project' }))
  }

  if (selectedEntityType.value === 'project') {
    return selectedEntity.value.articles.map((article) => ({ ...article, name: article.title, type: 'article' }))
  }

  return []
})

const childSectionTitle = computed(() => {
  if (selectedEntityType.value === 'workspace') return 'Categorieën binnen deze workspace'
  if (selectedEntityType.value === 'category') return 'Projecten binnen deze categorie'
  if (selectedEntityType.value === 'project') return 'Artikelen binnen dit project'
  return 'Dit artikel heeft geen onderliggende items'
})

const deleteTarget = computed(() => {
  if (!deleteType.value || deleteId.value == null) return null
  return getEntity(deleteType.value, deleteId.value)
})

function workspaceMatchesSearch(workspace) {
  const q = normalizedSearch.value
  if (!q) return true

  return (
    workspace.name.toLowerCase().includes(q) ||
    workspace.customer.toLowerCase().includes(q) ||
    workspace.description.toLowerCase().includes(q) ||
    workspace.categories.some((category) => categoryMatchesSearch(category, workspace))
  )
}

function categoryMatchesSearch(category, workspace) {
  const q = normalizedSearch.value
  if (!q) return true

  return (
    category.name.toLowerCase().includes(q) ||
    category.description.toLowerCase().includes(q) ||
    workspace.name.toLowerCase().includes(q) ||
    category.projects.some((project) => projectMatchesSearch(project, category, workspace))
  )
}

function projectMatchesSearch(project, category, workspace) {
  const q = normalizedSearch.value
  if (!q) return true

  return (
    project.name.toLowerCase().includes(q) ||
    project.description.toLowerCase().includes(q) ||
    project.status.toLowerCase().includes(q) ||
    category.name.toLowerCase().includes(q) ||
    workspace.name.toLowerCase().includes(q) ||
    project.articles.some((article) => articleMatchesSearch(article, project, category, workspace))
  )
}

function articleMatchesSearch(article, project, category, workspace) {
  const q = normalizedSearch.value
  if (!q) return true

  return (
    article.title.toLowerCase().includes(q) ||
    article.summary.toLowerCase().includes(q) ||
    article.slug.toLowerCase().includes(q) ||
    article.status.toLowerCase().includes(q) ||
    project.name.toLowerCase().includes(q) ||
    category.name.toLowerCase().includes(q) ||
    workspace.name.toLowerCase().includes(q)
  )
}

function categoryVisible(category, workspace) {
  if (selectedKind.value === 'Workspaces') return false
  if (!normalizedSearch.value) return true
  return categoryMatchesSearch(category, workspace)
}

function projectVisible(project, category, workspace) {
  if (selectedKind.value === 'Workspaces' || selectedKind.value === 'Categorieën') return false
  if (!normalizedSearch.value) return true
  return projectMatchesSearch(project, category, workspace)
}

function articleVisible(article, project, category, workspace) {
  if (selectedKind.value !== 'Alles' && selectedKind.value !== 'Artikelen') return false
  if (!normalizedSearch.value) return true
  return articleMatchesSearch(article, project, category, workspace)
}

function matchesEntity(...values) {
  if (!normalizedSearch.value) return true
  return values.some((value) => String(value).toLowerCase().includes(normalizedSearch.value))
}

function countArticlesInCategory(category) {
  return category.projects.reduce((sum, project) => sum + project.articles.length, 0)
}

function isExpanded(list, id) {
  const items = Array.isArray(list) ? list : list?.value
  return Array.isArray(items) ? items.includes(id) : false
}

function toggleExpanded(listRef, id) {
  const items = Array.isArray(listRef?.value) ? listRef.value : []
  listRef.value = items.includes(id)
    ? items.filter((item) => item !== id)
    : [...items, id]
}

function toggleWorkspace(id) {
  toggleExpanded(expandedWorkspaces, id)
}

function toggleCategory(id) {
  toggleExpanded(expandedCategories, id)
}

function toggleProject(id) {
  toggleExpanded(expandedProjects, id)
}

function findWorkspace(id) {
  return workspaceData.value.find((workspace) => workspace.id === id) ?? null
}

function findCategory(id) {
  for (const workspace of workspaceData.value) {
    const category = workspace.categories.find((item) => item.id === id)
    if (category) return { workspace, category }
  }
  return null
}

function findProject(id) {
  for (const workspace of workspaceData.value) {
    for (const category of workspace.categories) {
      const project = category.projects.find((item) => item.id === id)
      if (project) return { workspace, category, project }
    }
  }
  return null
}

function findArticle(id) {
  for (const workspace of workspaceData.value) {
    for (const category of workspace.categories) {
      for (const project of category.projects) {
        const article = project.articles.find((item) => item.id === id)
        if (article) return { workspace, category, project, article }
      }
    }
  }
  return null
}

function getEntity(type, id) {
  if (type === 'workspace') return findWorkspace(id)
  if (type === 'category') return findCategory(id)?.category ?? null
  if (type === 'project') return findProject(id)?.project ?? null
  if (type === 'article') return findArticle(id)?.article ?? null
  return null
}

function selectEntity(type, id) {
  selectedEntityType.value = type

  if (type === 'workspace') {
    if (!isExpanded(expandedWorkspaces, id)) {
      expandedWorkspaces.value = [...expandedWorkspaces.value, id]
    }
    selectedWorkspaceId.value = id
    selectedCategoryId.value = null
    selectedProjectId.value = null
    selectedArticleId.value = null
  }

  if (type === 'category') {
    const result = findCategory(id)
    if (!result) return
    if (!isExpanded(expandedWorkspaces, result.workspace.id)) {
      expandedWorkspaces.value = [...expandedWorkspaces.value, result.workspace.id]
    }
    if (!isExpanded(expandedCategories, id)) {
      expandedCategories.value = [...expandedCategories.value, id]
    }
    selectedWorkspaceId.value = result.workspace.id
    selectedCategoryId.value = id
    selectedProjectId.value = null
    selectedArticleId.value = null
  }

  if (type === 'project') {
    const result = findProject(id)
    if (!result) return
    if (!isExpanded(expandedWorkspaces, result.workspace.id)) {
      expandedWorkspaces.value = [...expandedWorkspaces.value, result.workspace.id]
    }
    if (!isExpanded(expandedCategories, result.category.id)) {
      expandedCategories.value = [...expandedCategories.value, result.category.id]
    }
    if (!isExpanded(expandedProjects, id)) {
      expandedProjects.value = [...expandedProjects.value, id]
    }
    selectedWorkspaceId.value = result.workspace.id
    selectedCategoryId.value = result.category.id
    selectedProjectId.value = id
    selectedArticleId.value = null
  }

  if (type === 'article') {
    const result = findArticle(id)
    if (!result) return
    if (!isExpanded(expandedWorkspaces, result.workspace.id)) {
      expandedWorkspaces.value = [...expandedWorkspaces.value, result.workspace.id]
    }
    if (!isExpanded(expandedCategories, result.category.id)) {
      expandedCategories.value = [...expandedCategories.value, result.category.id]
    }
    if (!isExpanded(expandedProjects, result.project.id)) {
      expandedProjects.value = [...expandedProjects.value, result.project.id]
    }
    selectedWorkspaceId.value = result.workspace.id
    selectedCategoryId.value = result.category.id
    selectedProjectId.value = result.project.id
    selectedArticleId.value = id
  }
}

function labelForType(type) {
  const labels = {
    workspace: 'Workspace',
    category: 'Categorie',
    project: 'Project',
    article: 'Artikel',
  }

  return labels[type] ?? type
}

function iconForType(type) {
  const icons = {
    workspace: 'mdi-view-dashboard-outline',
    category: 'mdi-shape-outline',
    project: 'mdi-briefcase-outline',
    article: 'mdi-file-document-outline',
  }

  return icons[type] ?? 'mdi-circle-outline'
}

function resetDraft() {
  draft.id = null
  draft.name = ''
  draft.summary = ''
  draft.customer = selectedCustomer.value !== 'Alle klanten' ? selectedCustomer.value : customerOnlyOptions.value[0] ?? ''
  draft.workspaceId = selectedWorkspaceId.value
  draft.categoryId = selectedCategoryId.value
  draft.projectId = selectedProjectId.value
  draft.status = ''
  draft.slug = ''
}

function openCreateDialog(type, defaults = null) {
  dialogMode.value = 'create'
  dialogType.value = type
  resetDraft()

  if (type === 'project') {
    draft.status = 'Concept'
  }

  if (type === 'article') {
    draft.status = 'Draft'
  }

  if (defaults?.workspaceId) draft.workspaceId = defaults.workspaceId
  if (defaults?.categoryId) draft.categoryId = defaults.categoryId
  if (defaults?.projectId) draft.projectId = defaults.projectId

  editorOpen.value = true
}

function openEditDialog(type, id) {
  if (type === 'article') {
    router.push(`/admin/articles/${id}/edit`)
    return
  }

  const entity = getEntity(type, id)
  if (!entity) return

  dialogMode.value = 'edit'
  dialogType.value = type
  resetDraft()

  if (type === 'workspace') {
    draft.id = entity.id
    draft.name = entity.name
    draft.summary = entity.description ?? ''
    draft.customer = entity.customer
  }

  if (type === 'category') {
    const result = findCategory(id)
    draft.id = entity.id
    draft.name = entity.name
    draft.summary = entity.description ?? ''
    draft.workspaceId = result?.workspace.id ?? null
  }

  if (type === 'project') {
    const result = findProject(id)
    draft.id = entity.id
    draft.name = entity.name
    draft.summary = entity.description ?? ''
    draft.categoryId = result?.category.id ?? null
    draft.status = entity.status
  }

  if (type === 'article') {
    const result = findArticle(id)
    draft.id = entity.id
    draft.name = entity.title
    draft.summary = entity.summary ?? ''
    draft.projectId = result?.project.id ?? null
    draft.slug = entity.slug
    draft.status = entity.status
  }

  editorOpen.value = true
}

function openCreateChildDialog() {
  if (selectedEntityType.value === 'workspace' && selectedEntity.value) {
    openCreateDialog('category', { workspaceId: selectedEntity.value.id })
    return
  }

  if (selectedEntityType.value === 'category' && selectedEntity.value) {
    openCreateDialog('project', { categoryId: selectedEntity.value.id })
    return
  }

  if (selectedEntityType.value === 'project' && selectedEntity.value) {
    openCreateDialog('article', { projectId: selectedEntity.value.id })
    return
  }
}

function saveDraft() {
  if (dialogMode.value === 'create') {
    createEntity()
  } else {
    updateEntity()
  }

  editorOpen.value = false
}

function createEntity() {
  if (dialogType.value === 'workspace') {
    const newWorkspace = {
      id: workspaceId.value++,
      name: draft.name,
      customer: draft.customer,
      description: draft.summary,
      categories: [],
    }
    workspaceData.value.unshift(newWorkspace)
    selectEntity('workspace', newWorkspace.id)
    return
  }

  if (dialogType.value === 'category') {
    const workspace = findWorkspace(draft.workspaceId)
    if (!workspace) return

    const newCategory = {
      id: categoryId.value++,
      name: draft.name,
      description: draft.summary,
      projects: [],
    }
    workspace.categories.unshift(newCategory)
    selectEntity('category', newCategory.id)
    return
  }

  if (dialogType.value === 'project') {
    const result = findCategory(draft.categoryId)
    if (!result) return

    const newProject = {
      id: projectId.value++,
      name: draft.name,
      description: draft.summary,
      status: draft.status || 'Concept',
      articles: [],
    }
    result.category.projects.unshift(newProject)
    selectEntity('project', newProject.id)
    return
  }

  if (dialogType.value === 'article') {
    const result = findProject(draft.projectId)
    if (!result) return

    const newArticle = {
      id: articleId.value++,
      title: draft.name,
      summary: draft.summary,
      slug: draft.slug || slugify(draft.name),
      status: draft.status || 'Draft',
      updatedAt: '2026-04-13',
    }
    result.project.articles.unshift(newArticle)
    selectEntity('article', newArticle.id)
  }
}

function updateEntity() {
  if (dialogType.value === 'workspace') {
    const workspace = findWorkspace(draft.id)
    if (!workspace) return
    workspace.name = draft.name
    workspace.description = draft.summary
    workspace.customer = draft.customer
    selectEntity('workspace', workspace.id)
    return
  }

  if (dialogType.value === 'category') {
    const result = findCategory(draft.id)
    if (!result) return

    if (result.workspace.id !== draft.workspaceId) {
      result.workspace.categories = result.workspace.categories.filter((category) => category.id !== draft.id)
      const targetWorkspace = findWorkspace(draft.workspaceId)
      if (!targetWorkspace) return
      targetWorkspace.categories.unshift(result.category)
    }

    result.category.name = draft.name
    result.category.description = draft.summary
    selectEntity('category', result.category.id)
    return
  }

  if (dialogType.value === 'project') {
    const result = findProject(draft.id)
    if (!result) return

    if (result.category.id !== draft.categoryId) {
      result.category.projects = result.category.projects.filter((project) => project.id !== draft.id)
      const targetCategory = findCategory(draft.categoryId)
      if (!targetCategory) return
      targetCategory.category.projects.unshift(result.project)
    }

    result.project.name = draft.name
    result.project.description = draft.summary
    result.project.status = draft.status
    selectEntity('project', result.project.id)
    return
  }

  if (dialogType.value === 'article') {
    const result = findArticle(draft.id)
    if (!result) return

    if (result.project.id !== draft.projectId) {
      result.project.articles = result.project.articles.filter((article) => article.id !== draft.id)
      const targetProject = findProject(draft.projectId)
      if (!targetProject) return
      targetProject.project.articles.unshift(result.article)
    }

    result.article.title = draft.name
    result.article.summary = draft.summary
    result.article.slug = draft.slug || slugify(draft.name)
    result.article.status = draft.status
    result.article.updatedAt = '2026-04-13'
    selectEntity('article', result.article.id)
  }
}

function openDeleteDialog(type, id) {
  deleteType.value = type
  deleteId.value = id
  deleteOpen.value = true
}

function confirmDelete() {
  if (deleteType.value === 'workspace') {
    workspaceData.value = workspaceData.value.filter((workspace) => workspace.id !== deleteId.value)
    if (selectedEntityType.value === 'workspace' && selectedWorkspaceId.value === deleteId.value) {
      selectedWorkspaceId.value = workspaceData.value[0]?.id ?? null
    }
  }

  if (deleteType.value === 'category') {
    const result = findCategory(deleteId.value)
    if (result) {
      result.workspace.categories = result.workspace.categories.filter((category) => category.id !== deleteId.value)
    }
  }

  if (deleteType.value === 'project') {
    const result = findProject(deleteId.value)
    if (result) {
      result.category.projects = result.category.projects.filter((project) => project.id !== deleteId.value)
    }
  }

  if (deleteType.value === 'article') {
    const result = findArticle(deleteId.value)
    if (result) {
      result.project.articles = result.project.articles.filter((article) => article.id !== deleteId.value)
    }
  }

  if (selectedEntityType.value === deleteType.value) {
    selectedEntityType.value = 'workspace'
    selectedCategoryId.value = null
    selectedProjectId.value = null
    selectedArticleId.value = null
  }

  deleteOpen.value = false
}

function slugify(value) {
  return String(value)
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
}

selectEntity('workspace', 1)
</script>

