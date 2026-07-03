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
            <span class="hero-pill u-inline-flex u-items-center">
              <span class="hero-pill-dot" /> Admin </span>
            <span class="hero-meta-separator"> • </span>
            <span> {{ totalRecords }} records </span>
          </div>
          <h1 class="hero-title">Kyano Knowledgebase</h1>
          <p class="hero-subtitle">Beheer je workspaces, categorieën, projecten en artikelen.</p>
        </div>
      </section>

      <section class="stats-grid admin-stats-grid">
        <article class="admin-stat-card card card-elevated admin-stat-card--ws">
          <div class="admin-stat-card__body">
            <div class="admin-stat-icon-wrap">
              <v-icon size="20">mdi-view-dashboard-outline</v-icon>
            </div>
            <div class="admin-stat-card__text">
              <div class="admin-stat-card__value">{{ counts.workspaces }}</div>
              <div class="admin-stat-card__label">{{ filteredWorkspaces.length === 1 ? 'Workspace' : 'Workspaces' }}
              </div>
            </div>
          </div>
          <div class="admin-stat-card__bg-shape" aria-hidden="true" />
        </article>
        <article class="admin-stat-card card card-elevated admin-stat-card--ws">
          <div class="admin-stat-card__body">
            <div class="admin-stat-icon-wrap">
              <v-icon size="20">mdi-shape-outline</v-icon>
            </div>
            <div class="admin-stat-card__text">
              <div class="admin-stat-card__value">{{ counts.categories }}</div>
              <div class="admin-stat-card__label">Categorieën</div>
            </div>
          </div>
          <div class="admin-stat-card__bg-shape" aria-hidden="true" />
        </article>
        <article class="admin-stat-card card card-elevated admin-stat-card--ws">
          <div class="admin-stat-card__body">
            <div class="admin-stat-icon-wrap">
              <v-icon size="20">mdi-briefcase-outline</v-icon>
            </div>
            <div class="admin-stat-card__text">
              <div class="admin-stat-card__value">{{ counts.projects }}</div>
              <div class="admin-stat-card__label">Projecten</div>
            </div>
          </div>
          <div class="admin-stat-card__bg-shape" aria-hidden="true" />
        </article>
        <article class="admin-stat-card card card-elevated admin-stat-card--ws">
          <div class="admin-stat-card__body">
            <div class="admin-stat-icon-wrap">
              <v-icon size="20">mdi-file-document-outline</v-icon>
            </div>
            <div class="admin-stat-card__text">
              <div class="admin-stat-card__value">{{ counts.articles }}</div>
              <div class="admin-stat-card__label">Artikelen</div>
            </div>
          </div>
          <div class="admin-stat-card__bg-shape" aria-hidden="true" />
        </article>
      </section>

      <div class="admin-tabs">
        <button class="admin-tab" :class="{ 'admin-tab--active': activeTab === 'content' }"
          @click="activeTab = 'content'">
          <v-icon size="16" class="admin-tab-icon">mdi-file-tree-outline</v-icon>
          Content structuur
          <span class="admin-tab-count">{{ totalRecords }}</span>
        </button>
        <button class="admin-tab" :class="{ 'admin-tab--active': activeTab === 'customers' }"
          @click="activeTab = 'customers'">
          <v-icon size="16" class="admin-tab-icon">mdi-account-group-outline</v-icon>
          Klantenbeheer
          <span class="admin-tab-count">{{ customersData.length }}</span>
        </button>

        <button class="admin-tab" :class="{ 'admin-tab--active': activeTab === 'Reviews' }"
          @click="activeTab = 'Reviews'">
          <v-icon size="16" class="admin-tab-icon">mdi-comment-outline</v-icon>
          Feedback
          <span class="admin-tab-count">{{ reviewRecords.length }}</span>
        </button>
      </div>

      <section v-if="activeTab === 'content'" style="border-radius: 0 0 26px 26px !important;"
        class="entity-card card card-elevated card-rounded-2xl studio-card">

        <div class="studio-toolbar">
          <v-select v-model="selectedCustomer" :items="customerOptions" item-title="title" item-value="value"
            variant="solo-filled" density="comfortable" flat hide-details class="studio-toolbar-select" />
          <v-select v-model="selectedKind" :items="kindOptions" variant="solo-filled" density="comfortable" flat
            hide-details class="studio-toolbar-select" />
          <div class="search-field studio-search-field studio-toolbar-search">
            <v-icon size="18">mdi-magnify</v-icon>
            <input v-model="search" type="text"
              placeholder="Zoek in workspaces, categorieën, projecten of artikelen..." />
          </div>
          <div class="studio-toolbar-spacer" />
          <v-menu location="bottom end">
            <template #activator="{ props }">
              <v-btn v-bind="props" prepend-icon="mdi-plus" class="entity-create-btn">
                Nieuw item
              </v-btn>
            </template>
            <v-list>
              <v-list-item @click="openCreateDialog('workspace')">
                <v-list-item-title>Nieuwe workspace</v-list-item-title>
              </v-list-item>
              <v-list-item
                @click="openCreateDialog('category', selectedWorkspaceId ? { workspaceId: selectedWorkspaceId } : null)">
                <v-list-item-title>Nieuwe categorie</v-list-item-title>
              </v-list-item>
              <v-list-item
                @click="openCreateDialog('project', selectedCategoryId ? { categoryId: selectedCategoryId } : null)">
                <v-list-item-title>Nieuw project</v-list-item-title>
              </v-list-item>
              <v-list-item
                @click="openCreateDialog('article', selectedProjectId ? { projectId: selectedProjectId } : null)">
                <v-list-item-title>Nieuw artikel</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </div>

        <div class="studio-layout">
          <aside class="studio-tree-panel" style="max-height: 120vh; overflow-y: auto;">
            <div class="panel-section-head">
              <div class="panel-kicker">Navigatie</div>
              <h3 class="panel-title">Structuur</h3>
            </div>


            <div v-if="loading" class="empty-state">
              <v-icon size="30">mdi-loading mdi-spin</v-icon>
              <p>Artikelen zijn aan het laden...</p>
            </div>

            <div v-else-if="OverviewError" class="empty-state">
              <v-icon size="30">mdi-alert-circle-outline</v-icon>
              <p>{{ OverviewError }}</p>
            </div>

            <div v-else-if="filteredWorkspaces.length" class="tree-list">
              <article v-for="workspace in filteredWorkspaces" :key="workspace.id"
                class="tree-card card card-elevated card-rounded-xl">
                <div class="tree-row tree-row-root"
                  :class="{ 'tree-row--selected': selectedEntityType === 'workspace' && selectedWorkspaceId === workspace.id }">
                  <button class="tree-row-trigger" @click="selectEntity('workspace', workspace.id)">
                    <div class="tree-row-main u-min-w-0">
                      <div class="entity-icon icon-box">
                        <v-icon size="18">mdi-view-dashboard-outline</v-icon>
                      </div>
                      <div class="tree-row-info">
                        <div class="tree-row-title">{{ workspace.name }}</div>
                        <div class="tree-row-meta">
                          <span>{{ formatWorkspaceCustomers(workspace) }}</span>
                          <span class="dot">•</span>
                          <span>{{ workspace.categories.length }} categorieën</span>
                        </div>
                      </div>
                    </div>
                  </button>
                  <div class="tree-row-side">
                    <v-btn icon size="small" variant="text" class="tree-toggle-btn"
                      @click.stop="toggleWorkspace(workspace.id)">
                      <v-icon size="18">{{ isExpanded(expandedWorkspaces, workspace.id) ? 'mdi-chevron-up' :
                        'mdi-chevron-down'
                      }}</v-icon>
                    </v-btn>
                  </div>
                </div>

                <div class="tree-children" v-show="isExpanded(expandedWorkspaces, workspace.id)">
                  <article v-for="category in workspace.categories" :key="category.id" class="tree-branch">
                    <div class="tree-row tree-row-child"
                      :class="{ 'tree-row--selected': selectedEntityType === 'category' && selectedCategoryId === category.id }">
                      <button class="tree-row-trigger" @click="selectEntity('category', category.id)">
                        <div class="tree-row-main u-min-w-0">
                          <div class="entity-icon icon-box entity-icon-soft">
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
                        <v-btn icon size="small" variant="text" class="tree-toggle-btn"
                          @click.stop="toggleCategory(category.id)">
                          <v-icon size="18">{{ isExpanded(expandedCategories, category.id) ? 'mdi-chevron-up' :
                            'mdi-chevron-down' }}</v-icon>
                        </v-btn>
                      </div>
                    </div>

                    <div class="tree-children nested-level" v-show="isExpanded(expandedCategories, category.id)">
                      <article v-for="project in category.projects" :key="project.id" class="tree-branch">
                        <div class="tree-row tree-row-child"
                          :class="{ 'tree-row--selected': selectedEntityType === 'project' && selectedProjectId === project.id }">
                          <button class="tree-row-trigger" @click="selectEntity('project', project.id)">
                            <div class="tree-row-main u-min-w-0">
                              <div class="entity-icon icon-box entity-icon-soft project-icon">
                                <v-icon size="18">mdi-briefcase-outline</v-icon>
                              </div>
                              <div class="tree-row-info">
                                <div class="tree-row-title">{{ project.name }}</div>
                                <div class="tree-row-meta">
                                  <span>{{ project.articles.length }} artikelen</span>
                                </div>
                              </div>
                            </div>
                          </button>
                          <div class="tree-row-side">
                            <v-btn icon size="small" variant="text" class="tree-toggle-btn"
                              @click.stop="toggleProject(project.id)">
                              <v-icon size="18">{{ isExpanded(expandedProjects, project.id) ? 'mdi-chevron-up' :
                                'mdi-chevron-down' }}</v-icon>
                            </v-btn>
                          </div>
                        </div>

                        <div class="tree-children nested-level articles-level"
                          v-show="isExpanded(expandedProjects, project.id)">
                          <button v-for="article in project.articles" :key="article.id" class="tree-row tree-row-leaf"
                            :class="{ 'tree-row--selected': selectedEntityType === 'article' && selectedArticleId === article.id }"
                            @click="selectEntity('article', article.id)">
                            <div class="tree-row-main u-min-w-0">
                              <div class="entity-icon icon-box entity-icon-soft article-icon">
                                <v-icon size="18">mdi-file-document-outline</v-icon>
                              </div>
                              <div class="tree-row-info">
                                <div class="tree-row-title">{{ article.title }}</div>
                                <div class="tree-row-meta">
                                  <span>{{ article.status }}</span>
                                  <span class="dot">•</span>
                                  <span>{{ article.updated_at }}</span>
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
              <div class="empty-state-icon icon-box">
                <v-icon size="24">mdi-file-tree-outline</v-icon>
              </div>
              <h3>Niets gevonden</h3>
              <p>Pas je filters aan of maak direct een nieuw item aan.</p>
            </div>
          </aside>

          <section class="studio-detail-panel" style="max-height: 120vh; overflow-y: auto;">
            <template v-if="selectedEntity">
              <div class="detail-head">
                <div>
                  <div class="section-kicker">{{ selectedEntityTypeLabel }}</div>
                  <h3 class="detail-title">{{ selectedEntity.name || selectedEntity.title }}</h3>
                  <p class="detail-subtitle">{{ selectedEntityDescription }}</p>
                </div>
                <div class="entity-actions u-flex u-items-center u-wrap u-gap-10 detail-actions">
                  <v-btn size="small" variant="text" @click="openEditDialog(selectedEntityType, selectedEntity.id)">
                    Bewerken
                  </v-btn>
                  <v-btn size="small" variant="text" class="delete-btn"
                    @click="openDeleteDialog(selectedEntityType, selectedEntity.id)">
                    Verwijderen
                  </v-btn>
                </div>
              </div>
              <div class="detail-grid">
                <article class="detail-card card card-rounded-xl"
                  :class="{ 'detail-card-full': selectedEntityType === 'workspace' || selectedEntityType === 'project' }">
                  <div class="detail-card-head">
                    <h4 class="panel-title">
                      {{ selectedEntityType === 'workspace' || selectedEntityType === 'project' ? 'Toegang klanten' :
                        'Eigenschappen' }}
                    </h4>
                  </div>
                  <div v-if="loading" class="empty-state">
                    <v-icon size="30">mdi-alert-outline</v-icon>
                    <p>{{ error }}</p>
                  </div>

                  <div v-else-if="OverviewError" class="empty-state">
                    <v-icon size="30">mdi-alert-circle-outline</v-icon>
                    <p>{{ OverviewError }}</p>
                  </div>
                  <template v-else-if="selectedEntityType === 'workspace' || selectedEntityType === 'project'">
                    <div class="workspace-access-card">
                      <div class="workspace-access-header">
                        <div class="workspace-access-header-copy">
                          <span class="meta-value" v-if="selectedEntityType === 'project'">
                            Categorie: {{ selectedParentLabel }}
                          </span>
                          <span class="meta-value">
                            {{ selectedEntity.customerAccess?.length || 0 }} van {{ customerOnlyRecords.length }}
                            klanten hebben toegang
                          </span>
                        </div>
                        <div class="workspace-access-header-actions">
                          <div class="search-field">
                            <v-icon size="18">mdi-magnify</v-icon>
                            <input v-model="workspaceCustomerSearch" type="text" placeholder="Zoek klant" />
                          </div>
                          <v-btn prepend-icon="mdi-content-save-outline" class="save-btn"
                            @click="saveWorkspaceMembers(selectedEntityType, selectedEntity.id)">
                            Opslaan
                          </v-btn>
                          <v-snackbar v-model="snackbar" timer="bottom" :timer-color="timerColor" :color="snackbarColor"
                            :timeout="3000" location="top end">
                            {{ snackbarMessage }}
                          </v-snackbar>
                          <v-btn variant="text" size="small"
                            @click="updateWorkspaceCustomerAccess(selectedEntityType, selectedEntity.id, customerOnlyRecords.map(c => c.id))">
                            Alles selecteren
                          </v-btn>
                          <v-btn variant="text" size="small" color="red"
                            @click="updateWorkspaceCustomerAccess(selectedEntityType, selectedEntity.id, [])">
                            Alles wissen
                          </v-btn>
                        </div>
                      </div>
                      <div class="workspace-access-table-shell">
                        <v-data-table :headers="workspaceCustomerHeaders" :items="customerOnlyRecords"
                          :search="workspaceCustomerSearch" items-per-page="-1" fixed-header height="360" hover
                          class="workspace-access-table" @click:row="toggleWorkspaceCustomerAccessFromRow">
                          <template #item.access="{ item }">
                            <div class="workspace-access-check" @click.stop>
                              <v-checkbox-btn :model-value="(selectedEntity.customerAccess || []).includes(item.id)"
                                @update:model-value="
                                  toggleWorkspaceCustomerAccess(selectedEntityType, selectedEntity.id, item.id, $event)
                                  " />
                            </div>
                          </template>
                          <template #item.name="{ item }">
                            <div class="workspace-access-name-cell">
                              <div class="workspace-access-name">{{ item.name }}</div>
                              <div class="workspace-access-email">{{ item.email }}</div>
                            </div>
                          </template>

                          <template #item.companyName="{ item }">
                            <span>{{ item.companyName }}</span>
                          </template>

                          <template #item.address="{ item }">
                            <span class="workspace-access-address">{{ item.address }}</span>
                          </template>

                          <template #item.tel="{ item }">
                            <span class="workspace-access-tel">{{ item.tel }}</span>
                          </template>

                          <template #item.role="{ item }">
                            <v-chip size="small" variant="tonal" class="entity-chip"
                              :class="item.role === 'admin' ? 'entity-chip-admin' : 'entity-chip-customer'">
                              {{ item.role }}
                            </v-chip>
                          </template>

                          <template #bottom></template>
                        </v-data-table>
                      </div>
                    </div>
                  </template>

                  <div v-else class="detail-meta-grid">
                    <div class="meta-item">
                      <span class="meta-label">Klant</span>
                      <span class="meta-value">{{ selectedEntityCustomer }}</span>
                    </div>
                    <div class="meta-item" v-if="selectedEntityType !== 'workspace'">
                      <span class="meta-label">Bovenliggend item</span>
                      <span class="meta-value">{{ selectedParentLabel }}</span>
                    </div>

                    <div class="meta-item" v-if="selectedEntityType === 'article'">
                      <span class="meta-label">Laatst gewijzigd</span>
                      <span class="meta-value">{{ selectedEntity.updated_at }}</span>
                    </div>
                  </div>
                </article>
                <article v-if="showsSummaryCard" class="detail-card card card-rounded-xl">
                  <div class="detail-card-head">
                    <div class="panel-kicker">Samenvatting</div>
                  </div>
                  <p class="detail-description">
                    {{ selectedEntity.description || selectedEntity.summary || defaultEntityDescription }}
                  </p>
                </article>
              </div>

              <article v-if="showsRelationsCard" class="detail-card card card-rounded-xl child-list-card">
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
                      <div class="entity-icon icon-box entity-icon-soft">
                        <v-icon size="18">{{ iconForType(child.type) }}</v-icon>
                      </div>
                      <div>
                        <div class="entity-name">{{ child.name || child.title }}</div>
                        <div class="entity-meta">
                          <span>{{ labelForType(child.type) }}</span>
                          <span v-if="child.status" class="dot">•</span>
                          <span v-if="child.status">{{ child.status }}</span>
                          <span v-if="child.updated_at" class="dot">•</span>
                          <span v-if="child.updated_at">{{ child.updated_at }}</span>
                        </div>
                      </div>
                    </div>
                    <div class="entity-actions u-flex u-items-center u-wrap u-gap-10">
                      <v-btn size="small" variant="text" @click="selectEntity(child.type, child.id)">Openen</v-btn>
                      <v-btn size="small" variant="text" @click="openEditDialog(child.type, child.id)">Bewerken</v-btn>
                    </div>
                  </div>
                </div>
                <div v-else class="empty-state compact-empty child-empty">
                  <div class="empty-state-icon icon-box">
                    <v-icon size="24">mdi-plus-box-outline</v-icon>
                  </div>
                  <h3>Nog geen gekoppelde items</h3>
                  <p>Voeg hier direct een onderliggend item toe.</p>
                </div>
              </article>
            </template>

            <div v-if="loading" class="empty-state">
              <v-icon size="30">mdi-loading mdi-spin</v-icon>
              <p>Workspaces zijn aan het inladen...</p>
            </div>

            <div v-else-if="OverviewError" class="empty-detail-state">
              <div class="empty-state-icon icon-box">
                <v-icon size="24">mdi-cursor-default-click-outline</v-icon>
              </div>
              <h3>Selecteer een item</h3>
              <p>Kies links een workspace, categorie, project of artikel om de details en acties te bekijken.</p>
            </div>
          </section>
        </div>
      </section>

      <section v-if="activeTab === 'customers'" style="border-radius: 0 0 26px 26px !important;"
        class="entity-card card card-elevated card-rounded-2xl studio-card">
        <div class="studio-toolbar">
          <div class="search-field studio-search-field studio-toolbar-search">
            <v-icon size="18">mdi-magnify</v-icon>
            <input v-model="customerSearch" type="text"
              placeholder="Zoek op bedrijfsnaam, contactpersoon, e-mail of telefoon..." />
          </div>
          <div class="studio-toolbar-spacer" />
          <v-btn prepend-icon="mdi-plus" class="entity-create-btn" @click="openCustomerCreateDialog">
            Nieuwe klant
          </v-btn>
        </div>

        <div class="studio-layout">

          <aside class="studio-tree-panel" style="max-height: 120vh; overflow-y: auto;">
            <div class="panel-section-head">
              <div class="panel-kicker">Lijst</div>
              <h3 class="panel-title">Klanten</h3>
            </div>

            <div v-if="filteredCustomers.length" class="tree-list">
              <article v-for="customer in filteredCustomers" :key="customer.id"
                class="tree-card card card-elevated card-rounded-xl">
                <div class="tree-row tree-row-root"
                  :class="{ 'tree-row--selected': selectedCustomerCrudId === customer.id }">
                  <button class="tree-row-trigger" @click="selectCustomer(customer.id)">
                    <div class="tree-row-main u-min-w-0">
                      <div class="entity-icon icon-box">
                        <v-icon size="18">mdi-domain</v-icon>
                      </div>
                      <div class="tree-row-info">
                        <div class="tree-row-title">{{ customer.companyName }}</div>
                        <div class="tree-row-meta">
                          <span>{{ customer.name }}</span>
                          <span class="dot">•</span>
                          <span>{{ customer.email }}</span>
                        </div>
                      </div>
                    </div>
                  </button>
                  <div class="tree-row-side">
                    <v-chip size="small" class="entity-chip">
                      {{ customer.role === 'admin' ? 'Admin' : 'klant' }}
                    </v-chip>
                  </div>
                </div>
              </article>
            </div>

            <div v-else class="empty-state compact-empty">
              <div class="empty-state-icon icon-box">
                <v-icon size="24">mdi-domain-off</v-icon>
              </div>
              <h3>Geen klanten gevonden</h3>
              <p>Pas je zoekopdracht aan of maak een nieuwe klant aan.</p>
            </div>
          </aside>

          <section class="studio-detail-panel" style="max-height: 120vh; overflow-y: auto;">
            <template v-if="selectedCustomerRecord">
              <div class="detail-head">
                <div>
                  <div class="section-kicker">Klant</div>
                  <h3 class="detail-title">{{ selectedCustomerRecord.companyName }}</h3>
                  <p class="detail-subtitle">Beheer klantgegevens en contactinformatie.</p>
                </div>
                <div class="entity-actions u-flex u-items-center u-wrap u-gap-10 detail-actions">
                  <v-btn size="small" variant="text" @click="openCustomerEditDialog(selectedCustomerRecord.id)">
                    BEWERKEN
                  </v-btn>
                  <v-btn size="small" variant="text" class="delete-btn"
                    @click="openCustomerDeleteDialog(selectedCustomerRecord.id)">
                    VERWIJDEREN
                  </v-btn>
                </div>
              </div>

              <div class="detail-grid">
                <article class="detail-card card card-rounded-xl">
                  <div class="detail-card-head">
                    <div class="panel-kicker">Basisinformatie</div>
                    <h4 class="panel-title">Klantgegevens</h4>
                  </div>
                  <div class="detail-meta-grid">
                    <div class="meta-item">
                      <span class="meta-label">Bedrijf</span>
                      <span class="meta-value">{{ selectedCustomerRecord.companyName }}</span>
                    </div>
                    <div class="meta-item">
                      <span class="meta-label">Contactpersoon</span>
                      <span class="meta-value">{{ selectedCustomerRecord.name }}</span>
                    </div>
                    <div class="meta-item">
                      <span class="meta-label">E-mail</span>
                      <span class="meta-value">{{ selectedCustomerRecord.email }}</span>
                    </div>
                    <div class="meta-item">
                      <span class="meta-label">Telefoon</span>
                      <span class="meta-value">{{ selectedCustomerRecord.tel }}</span>
                    </div>
                    <div class="meta-item">
                      <span class="meta-label">Rol</span>
                      <span class="meta-value">{{ selectedCustomerRecord.role }}</span>
                    </div>
                    <div class="meta-item">
                      <span class="meta-label">Workspaces</span>
                      <span class="meta-value">{{ customerWorkspaceCount(selectedCustomerRecord.id) }}</span>
                    </div>
                  </div>
                </article>

                <article class="detail-card card card-rounded-xl">
                  <div class="detail-card-head">
                    <div class="panel-kicker">Adres</div>
                    <h4 class="panel-title">Vestigingsinformatie</h4>
                  </div>
                  <p class="detail-description">
                    {{ selectedCustomerRecord.address || 'Nog geen adres ingevuld.' }}
                  </p>
                </article>
              </div>

              <article class="detail-card card card-rounded-xl child-list-card">
                <div class="detail-card-head child-list-head">
                  <div>
                    <div class="panel-kicker">Relaties</div>
                    <h4 class="panel-title">Gekoppelde workspaces</h4>
                  </div>
                </div>

                <div v-if="loading" class="empty-state">
                  <v-icon size="30">mdi-alert-outline</v-icon>
                  <p>Relaties zijn aan het laden...</p>
                </div>

                <div v-else-if="OverviewError" class="empty-state">
                  <v-icon size="30">mdi-alert-circle-outline</v-icon>
                  <p>{{ OverviewError }}</p>
                </div>

                <div v-else-if="customerWorkspaces.length" class="child-rows">
                  <div v-for="workspace in customerWorkspaces" :key="workspace.id" class="child-row">
                    <div class="child-row-main">
                      <div class="entity-icon icon-box entity-icon-soft">
                        <v-icon size="18">mdi-view-dashboard-outline</v-icon>
                      </div>
                      <div>
                        <div class="entity-name">{{ workspace.name }}</div>
                        <div class="entity-meta">
                          <span>Workspace</span>
                          <span class="dot">•</span>
                          <span>{{ workspace.categories.length }} categorieën</span>
                        </div>
                      </div>
                    </div>
                    <div class="entity-actions u-flex u-items-center u-wrap u-gap-10">
                      <v-btn size="small" variant="text" @click="selectEntity('workspace', workspace.id)">Openen</v-btn>
                    </div>
                  </div>
                </div>

                <div v-else class="empty-state compact-empty child-empty">
                  <div class="empty-state-icon icon-box">
                    <v-icon size="24">mdi-view-dashboard-outline</v-icon>
                  </div>
                  <h3>Geen gekoppelde workspaces</h3>
                  <p>Deze klant heeft nog geen workspace in de structuur.</p>
                </div>
              </article>
            </template>

            <div v-else class="empty-detail-state">
              <div class="empty-state-icon icon-box">
                <v-icon size="24">mdi-account-box-outline</v-icon>
              </div>
              <h3>Selecteer een klant</h3>
              <p>Kies links een klant om de gegevens te bekijken en te bewerken.</p>
            </div>
          </section>
        </div>
      </section>

      <section v-if="activeTab === 'Reviews'" style="border-radius: 0 0 26px 26px !important"
        class="entity-card card card-elevated card-rounded-2xl studio-card">
        <div class="studio-toolbox">

          <div class="studio-toolbar">
            <v-menu v-model="filterMenu" :close-on-content-click="true" location="bottom start">
              <template #activator="{ props }">
                <button v-bind="props" icon variant="outlined" size="small">
                  <v-badge :model-value="activeFilter !== 'All'" dot color="primary">
                    <v-icon>mdi-filter</v-icon>
                  </v-badge>
                </button>
              </template>

              <v-list density="compact" :selected="[activeFilter]" @update:selected="activeFilter = $event[0]">
                <v-list-item v-for="opt in filterOptions" :key="opt.value" :value="opt.value" :prepend-icon="opt.icon"
                  :title="opt.label" />
              </v-list>
            </v-menu>

            <div class="search-field studio-search-field studio-toolbar-search">
              <v-icon size="18">mdi-magnify</v-icon>
              <input v-model="reviewSearch" type="text" placeholder="Zoek op artikel, klant of feedback..." />
            </div>
            <div class="studio-toolbar-spacer" />
          </div>
        </div>
        <div class="studio-layout">
          <aside class="studio-tree-panel" style="max-height: 120vh; overflow-y: auto;">
            <div class="panel-section-head">
              <div class="panel-kicker">Lijst</div>
              <h3 class="review-title">Beoordelingen</h3>
            </div>

            <div v-if="filteredReviewRecords.length" class="tree-list">
              <article v-for="review in filteredReviewRecords" :key="review.id"
                class="tree-card card card-elevated card-rounded-xl">
                <div class="tree-row tree-row-root" :class="{ 'tree-row--selected': selectedReviewId === review.id }">
                  <button class="tree-row-trigger" @click="selectReview(review.id)">
                    <div class="tree-row-main u-min-w-0">
                      <div class="review-avatar">{{ review.reviewerInitials }}</div>
                      <div class="tree-row-info">
                        <div class="tree-row-title">{{ review.articleTitle }}</div>
                        <div class="tree-row-meta">
                          <span>{{ review.reviewerName }}</span>
                        </div>
                      </div>
                    </div>
                  </button>
                  <div class="tree-row-side">
                    <div class="review-row-side">
                      <v-icon size="20" :class="reviewHelpfulClass(review)">
                        {{ reviewHelpfulIcon(review) }}
                      </v-icon>
                      <v-chip size="small" variant="tonal" class="review-read-chip" :class="reviewReadClass(review)">
                        {{ reviewReadLabel(review) }}
                      </v-chip>
                      <v-chip size="small" class="entity-chip">
                        {{ review.submittedAt || 'Geen datum' }}
                      </v-chip>
                    </div>
                  </div>
                </div>
              </article>
            </div>

            <div v-else class="empty-state compact-empty">
              <div class="empty-state-icon icon-box">
                <v-icon size="24">mdi-comment-outline</v-icon>
              </div>
              <h3>Geen feedback gevonden</h3>
              <p>Pas je zoekopdracht of filter aan om beoordelingen te tonen.</p>
            </div>
          </aside>

          <section class="studio-detail-panel" style="max-height: 120vh; overflow-y: auto;">
            <template v-if="selectedReviewRecord">
              <div class="detail-head">
                <div>
                  <div class="section-kicker">Feedback</div>

                  <h3 class="detail-title">{{ selectedReviewRecord.articleTitle }}</h3>
                  <p class="detail-subtitle">
                    {{ articleReviewRecords.length }} feedback{{ articleReviewRecords.length === 1 ? '' : 's' }} voor
                    dit artikel.
                  </p>
                </div>
                <div class="entity-actions u-flex u-items-center u-wrap u-gap-10 detail-actions">
                  <v-btn size="small" variant="text" class="delete-btn"
                    @click="openReviewDeleteDialog(activeArticleReview.id)">
                    Verwijder Feedback
                  </v-btn>
                </div>
              </div>

              <div class="detail-grid">
                <article class="detail-card card card-rounded-xl">
                  <div class="detail-card-head">
                    <div class="panel-kicker">Artikel</div>
                    <h4 class="panel-title">Artikelinformatie</h4>
                  </div>
                  <div class="detail-meta-grid">
                    <div class="meta-item">
                      <span class="meta-label">Titel</span>
                      <span class="meta-value">{{ selectedReviewRecord.articleTitle }}</span>
                    </div>
                    <div class="meta-item">
                      <span class="meta-label">Workspace</span>
                      <span class="meta-value">{{ selectedReviewRecord.workspaceName || '-' }}</span>
                    </div>
                    <div class="meta-item">
                      <span class="meta-label">Categorie</span>
                      <span class="meta-value">{{ selectedReviewRecord.categoryName || '-' }}</span>
                    </div>
                    <div class="meta-item">
                      <span class="meta-label">Project</span>
                      <span class="meta-value">{{ selectedReviewRecord.projectName || '-' }}</span>
                    </div>
                    <div class="meta-item">
                      <span class="meta-label">Status</span>
                      <span class="meta-value">{{ selectedReviewRecord.articleStatus }}</span>
                    </div>
                    <div class="meta-item">
                      <span class="meta-label">Laatst gewijzigd</span>
                      <span class="meta-value">{{ selectedReviewRecord.articleUpdated_at || '-' }}</span>
                    </div>
                  </div>
                </article>

                <article class="detail-card card card-rounded-xl">
                  <div class="detail-card-head">
                    <div class="panel-kicker">Afzender</div>
                    <h4 class="panel-title">Klantinformatie</h4>
                  </div>
                  <div class="detail-meta-grid">
                    <div class="meta-item">
                      <span class="meta-label">Naam</span>
                      <span class="meta-value">{{ activeArticleReview.reviewerName }}</span>
                    </div>
                    <div class="meta-item">
                      <span class="meta-label">E-mail</span>
                      <span class="meta-value">{{ activeArticleReview.reviewerEmail || '-' }}</span>
                    </div>
                    <div class="meta-item">
                      <span class="meta-label">Beoordeling</span>
                      <div class="meta-value">
                        <span class="review-rating-chip" :class="reviewHelpfulClass(activeArticleReview)">
                          <v-icon size="20">{{ reviewHelpfulIcon(activeArticleReview) }}</v-icon>
                        </span>
                      </div>
                    </div>
                    <div class="meta-item">
                      <span class="meta-label">Verzonden op</span>
                      <span class="meta-value">{{ activeArticleReview.submittedAt || '-' }}</span>
                    </div>
                  </div>
                </article>
              </div>

              <article class="detail-card card card-rounded-xl child-list-card">
                <div class="detail-card-head child-list-head">
                  <div>
                    <div class="panel-kicker">Feedback</div>
                    <h4 class="panel-title">Ingezonden bericht</h4>
                  </div>
                  <div v-if="articleReviewRecords.length > 1" class="review-switcher">
                    <v-btn size="small" variant="text" @click="selectPreviousArticleReview">
                      <v-icon size="16">mdi-chevron-left</v-icon>
                    </v-btn>
                    <span class="review-switcher-count">
                      {{ activeArticleReviewIndex + 1 }} / {{ articleReviewRecords.length }}
                    </span>
                    <v-btn size="small" variant="text" @click="selectNextArticleReview">
                      <v-icon size="16">mdi-chevron-right</v-icon>
                    </v-btn>
                  </div>
                </div>
                <div class="review-feedback-box">
                  <div class="review-header">
                    <div class="entity-meta review-feedback-meta">
                      <div class="feedback-view-feed">
                        <span>{{ reviewReadLabel(activeArticleReview) }}</span>
                      </div>
                      <span class="dot">•</span>
                      <span>{{ activeArticleReview.reviewerName }}</span>
                      <span class="dot">•</span>
                      <span>{{ activeArticleReview.submittedAt || '-' }}</span>
                    </div>
                    <div class="review-rating-chip-row">
                      <span class="review-rating-chip" :class="reviewHelpfulClass(activeArticleReview)">
                        <v-icon size="22">
                          {{ reviewHelpfulIcon(activeArticleReview) }}
                        </v-icon>
                      </span>
                    </div>
                  </div>

                  <p class="detail-description review-feedback-text mb-0">
                    {{ activeArticleReview.hasFeedbackText ? activeArticleReview.feedbackText :
                      'Geen extra feedback ingevuld.' }}
                  </p>
                </div>
              </article>
            </template>

            <div v-else class="empty-detail-state">
              <div class="empty-state-icon icon-box">
                <v-icon size="24">mdi-comment-text-outline</v-icon>
              </div>
              <h3>Selecteer feedback</h3>
              <p>Kies links een feedbackitem om de artikelinformatie en het bericht te bekijken.</p>
            </div>
          </section>
        </div>
      </section>
    </div>

    <v-dialog v-model="editorOpen" max-width="680">
      <v-card class="dialog-card card card-rounded-xl" rounded="xl">
        <div class="dialog-head">
          <div>
            <div class="section-kicker">{{ dialogMode === 'create' ? 'Nieuw item' : 'Bewerken' }}</div>
            <h3 class="dialog-title">
              {{ dialogMode === 'create' ? `Nieuwe ${labelForType(dialogType).toLowerCase()}` : `Bewerk
              ${labelForType(dialogType).toLowerCase()}` }}
            </h3>
          </div>
        </div>
        <div class="dialog-body">
          <v-form ref="formRef" v-model="formValid" @submit.prevent="saveDraft">
            <v-text-field :model-value="dialogType === 'article' ? draft.title : draft.name"
              @update:model-value="updateDraftPrimaryField" :rules="RequireNameRules"
              :label="dialogType === 'article' ? 'Titel' : 'Naam'" variant="solo-filled" flat hide-details="auto"
              class="notion-soft-input mb-4" />
            <v-textarea v-if="dialogType === 'article'" v-model="draft.summary"
              :label="dialogType === 'article' ? 'Samenvatting' : 'Korte Beschrijving'" variant="solo-filled" flat
              hide-details rows="4" class="notion-soft-input mb-4" />
            <v-textarea v-if="dialogType === 'project'" :model-value="draft.description"
              @update:model-value="updateDraftContentField" variant="solo-filled" label="Project beschrijving" flat
              hide-details rows="4" class="notion-soft-input mb-4" />
            <v-select v-if="dialogType === 'article'" :model-value="draft.workspaceId" :items="workspaceSelectOptions"
              item-title="label" item-value="value" label="Workspace" variant="solo-filled" flat hide-details
              class="notion-soft-input mb-4" />
            <v-select v-if="dialogType === 'category'" v-model="draft.workspaceId" :items="workspaceSelectOptions"
              item-title="label" item-value="value" label="Workspace" variant="solo-filled" flat hide-details
              class="notion-soft-input mb-4" />
            <v-select v-if="dialogType === 'project'" :rules="CategoryRules" v-model="draft.categoryId"
              :items="categorySelectOptions" item-title="label" item-value="value" label="Categorie"
              variant="solo-filled" flat hide-details="auto" class="notion-soft-input mb-4" />
            <template v-if="dialogType === 'article'">
              <v-combobox v-model="draft.tags" :items="availableArticleTags" label="Tags" variant="solo-filled" flat
                hide-details multiple chips closable-chips clearable class="notion-soft-input mb-4" />
              <v-select v-model="draft.projectId" :rules="ProjectRules" :items="projectSelectOptions" item-title="label"
                item-value="value" label="Project" variant="solo-filled" flat hide-details="auto"
                class="notion-soft-input mb-4" />
              <v-select v-model="draft.categoryId" :rules="CategoryRules" :items="categorySelectOptions"
                item-title="label" item-value="value" label="Categorie" variant="solo-filled" flat hide-details="auto"
                class="notion-soft-input mb-4" />
              <div class="article-chip-picker mb-4">
                <div class="article-chip-picker__label">Zichtbaarheid</div>
                <v-chip-group v-model="draft.visibility" selected-class="article-choice-chip--selected" mandatory>
                  <v-chip v-for="option in articleVisibilityOptions" :key="option.value" :value="option.value"
                    class="article-choice-chip" filter variant="outlined">
                    {{ option.label }}
                  </v-chip>
                </v-chip-group>
              </div>
              <div class="article-chip-picker mb-4">
                <div class="article-chip-picker__label">Status</div>
                <v-chip-group v-model="draft.status" selected-class="article-choice-chip--selected" mandatory>
                  <v-chip v-for="option in articleStatusOptions" :key="option.value" :value="option.value"
                    class="article-choice-chip" filter variant="outlined">
                    {{ option.label }}
                  </v-chip>
                </v-chip-group>
              </div>
            </template>
          </v-form>
        </div>

        <div class="dialog-actions u-gap-12">
          <v-btn variant="text" @click="editorOpen = false">Annuleren</v-btn>
          <v-btn class="entity-create-btn" @click="saveDraft">Opslaan</v-btn>
        </div>
      </v-card>
    </v-dialog>

    <v-dialog v-model="workspaceDeleteOpen" max-width="396">
      <v-card class="delete-modal" v-if="workspaceDeleteTarget">
        <div class="delete-modal-head">
          <span class="delete-modal-icon" aria-hidden="true">
            <v-icon size="30">mdi-alert-circle-outline</v-icon>
          </span>
        </div>

        <div class="delete-modal-body">
          <h3 class="delete-modal-title">{{ workspaceDeleteTarget.name || workspaceDeleteTarget.title }} verwijderen?
          </h3>
          <p class="delete-modal-content">Weet je zeker dat je {{ workspaceDeleteTarget.name ||
            workspaceDeleteTarget.title
            }}
            wilt verwijderen?</p>
          <p class="delete-modal-content">Dit kan niet ongedaan worden gemaakt.</p>
        </div>

        <div class="delete-modal-footer">
          <button class="delete-modal-btn delete-modal-btn--secondary" type="button"
            @click="workspaceDeleteOpen = false">Annuleren</button>
          <button class="delete-modal-btn delete-modal-btn--warning" type="button"
            @click="confirmWorkspaceDelete">Verwijderen</button>
        </div>
      </v-card>
    </v-dialog>
    <v-dialog v-model="categoryDeleteOpen" max-width="396">
      <v-card class="delete-modal" v-if="categoryDeleteTarget">
        <div class="delete-modal-head">
          <span class="delete-modal-icon" aria-hidden="true">
            <v-icon size="30">mdi-alert-circle-outline</v-icon>
          </span>
        </div>
        <div class="delete-modal-body">
          <h3 class="delete-modal-title">{{ categoryDeleteTarget.name }} verwijderen?</h3>
          <p class="delete-modal-content">Weet je zeker dat je {{ categoryDeleteTarget.name }} wilt verwijderen?</p>
          <p class="delete-modal-content">Dit kan niet ongedaan worden gemaakt.</p>
        </div>
        <div class="delete-modal-footer">
          <button class="delete-modal-btn delete-modal-btn--secondary"
            @click="categoryDeleteOpen = false">Annuleren</button>
          <button class="delete-modal-btn delete-modal-btn--warning" @click="confirmCategoryDelete">Verwijderen</button>
        </div>
      </v-card>
    </v-dialog>

    <v-dialog v-model="projectDeleteOpen" max-width="396">
      <v-card class="delete-modal" v-if="projectDeleteTarget">
        <div class="delete-modal-head">
          <span class="delete-modal-icon" aria-hidden="true">
            <v-icon size="30">mdi-alert-circle-outline</v-icon>
          </span>
        </div>
        <div class="delete-modal-body">
          <h3 class="delete-modal-title">{{ projectDeleteTarget.name }} verwijderen?</h3>
          <p class="delete-modal-content">Weet je zeker dat je {{ projectDeleteTarget.name }} wilt verwijderen?</p>
          <p class="delete-modal-content">Dit kan niet ongedaan worden gemaakt.</p>
        </div>
        <div class="delete-modal-footer">
          <button class="delete-modal-btn delete-modal-btn--secundary"
            @click="projectDeleteOpen = false">Annuleren</button>
          <button class="delete-modal-btn delete-modal-btn--warning" @click="confirmProjectDelete">Verwijderen</button>
        </div>
      </v-card>
    </v-dialog>



    <v-dialog v-model="customerEditorOpen" max-width="680">
      <v-card class="dialog-card card card-rounded-xl" rounded="xl">
        <div class="dialog-head">
          <div>
            <div class="section-kicker">{{ customerDialogMode === 'create' ? 'Nieuwe klant' : 'Klant bewerken' }}</div>
            <h3 class="dialog-title">{{ customerDialogMode ===
              'create' ? 'Nieuwe klant aanmaken' : 'Klantgegevens aanpassen' }}</h3>
          </div>
        </div>
        <div class="dialog-body">
          <v-form ref="formRef" v-model="formValid" @submit.prevent="saveCustomerDraft">
            <v-text-field v-model="customerDraft.name" label="Contactpersoon" :rules="nameRules" variant="solo-filled"
              flat hide-details="auto" class="notion-soft-input mb-4" />
            <v-text-field v-model="customerDraft.companyName" label="Bedrijfsnaam" variant="solo-filled" flat
              hide-details="auto" class="notion-soft-input mb-4" />
            <v-text-field v-model="customerDraft.email" label="E-mail" :rules="emailRules" variant="solo-filled" flat
              hide-details="auto" class="notion-soft-input mb-4" />
            <v-text-field v-model="customerDraft.tel" label="Telefoon" :rules="phoneRules" variant="solo-filled" flat
              hide-details="auto" class="notion-soft-input mb-4" />
            <v-text-field v-model="customerDraft.address" label="Adres" variant="solo-filled" flat hide-details="auto"
              class="notion-soft-input mb-4" />
            <v-select v-model="customerDraft.role" :items="customerRoleOptions" label="Rol" variant="solo-filled" flat
              hide-details class="notion-soft-input mb-4" />
            <v-text-field v-model="customerDraft.password" label="Wachtwoord" autocomplete="password"
              :rules="passwordRules" prepend-inner-icon="mdi-lock-outline"
              :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
              :type="showPassword ? 'text' : 'password'" @click:append-inner="showPassword = !showPassword"
              variant="solo-filled" flat hide-details="auto" />
            <v-text-field ref="confirmFieldRef" v-model="customerDraft.password_confirmation"
              autocomplete="new-password" :type="showConfirm ? 'text' : 'password'"
              prepend-inner-icon="mdi-lock-check-outline"
              :append-inner-icon="showConfirm ? 'mdi-eye-off-outline' : 'mdi-eye-outline'" hide-details="auto"
              class="mt-4" @click:append-inner="showConfirm = !showConfirm" label="Bevestig Wachtwoord"
              :rules="confirmRules" variant="solo-filled" flat />
          </v-form>
        </div>

        <v-alert v-if="error" type="error" variant="tonal" density="comfortable" closable class="auth-alert mx-6"
          @click:close="error = ''">
          {{ error }}
        </v-alert>

        <div class="dialog-actions u-gap-12 mt-5">
          <v-btn variant="text" @click="customerEditorOpen = false">Annuleren</v-btn>
          <v-btn class="entity-create-btn" @click="saveCustomerDraft">Opslaan</v-btn>
        </div>
      </v-card>
    </v-dialog>

    <v-dialog v-model="customerDeleteOpen" max-width="396">
      <v-card class="delete-modal" rounded="xl">
        <div class="delete-modal-head">
          <span class="delete-modal-icon" aria-hidden="true">
            <v-icon size="30">mdi-alert-circle-outline</v-icon>
          </span>
        </div>

        <div class="delete-modal-body">
          <h3 class="delete-modal-title">{{ customerDeleteTarget?.companyName }} verwijderen?</h3>
          <p class="delete-modal-content">Weet je zeker dat je {{ customerDeleteTarget?.companyName }} wilt verwijderen?
          </p>
          <p class="delete-modal-content">Dit kan niet ongedaan worden gemaakt.</p>
        </div>

        <div class="delete-modal-footer">
          <button class="delete-modal-btn delete-modal-btn--secondary" type="button"
            @click="customerDeleteOpen = false">Annuleren</button>
          <button class="delete-modal-btn delete-modal-btn--warning" type="button"
            @click="confirmCustomerDelete">Verwijderen</button>
        </div>
      </v-card>
    </v-dialog>

    <v-dialog v-model="reviewDeleteOpen" max-width="396">
      <v-card class="delete-modal" rounded="xl">
        <div class="delete-modal-head">
          <span class="delete-modal-icon" aria-hidden="true">
            <v-icon size="30">mdi-alert-circle-outline</v-icon>
          </span>
        </div>

        <div class="delete-modal-body">
          <h3 class="delete-modal-title">Feedback verwijderen?</h3>
          <p class="delete-modal-content">Weet je zeker dat je de feedback voor {{ reviewDeleteTarget?.articleTitle }}
            wilt
            verwijderen?</p>
          <p class="delete-modal-content">Dit kan niet ongedaan worden gemaakt.</p>
        </div>

        <div class="delete-modal-footer">
          <button class="delete-modal-btn delete-modal-btn--secondary" type="button"
            @click="reviewDeleteOpen = false">Annuleren</button>
          <button class="delete-modal-btn delete-modal-btn--warning" type="button"
            @click="confirmReviewDelete">Verwijderen</button>
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { onMounted, computed, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { storeCategory, UpdateCategory, DeleteCategory } from '@/services/categoryService'
import { postArticle, updateArticle, deleteFeedback, getFeedbacks, markFeedbackAsRead } from '@/services/articleService'
import { getAdminWorkspaces, postWorkspace, updateWorkspace, deleteWorkspace } from '@/services/workspaceService'
import { storeProject, updateProject, deleteProject } from '@/services/projectService'
import { deleteUser, getAdminUsers, postUser, updateUser } from '@/services/userService'

const activeTab = ref('content')
const formRef = ref(null)
const formValid = ref(false)

const loading = ref(false)
const error = ref('')
const OverviewError = ref('')
const showPassword = ref(false)
const showConfirm = ref(false)

const search = ref('')
const customerSearch = ref('')
const reviewSearch = ref('')
const selectedCustomer = ref(null)
const selectedKind = ref('Alles')
const router = useRouter()

const expandedWorkspaces = ref([])
const expandedCategories = ref([])
const expandedProjects = ref([])

const editorOpen = ref(false)
const workspaceDeleteOpen = ref(false)
const dialogMode = ref('create')
const dialogType = ref('workspace')
const deleteType = ref(null)
const deleteId = ref(null)

const customerEditorOpen = ref(false)
const customerDeleteOpen = ref(false)
const categoryDeleteOpen = ref(false)
const categoryDeleteId = ref(null)
const projectDeleteId = ref(null)
const projectDeleteOpen = ref(false)
const reviewDeleteOpen = ref(false)
const customerDialogMode = ref('create')
const selectedCustomerCrudId = ref(null)
const customerDeleteId = ref(null)
const reviewDeleteId = ref(null)
const selectedReviewId = ref(null)
const workspaceDeleteId = ref(null)

const selectedEntityType = ref('workspace')
const selectedWorkspaceId = ref(null)
const selectedCategoryId = ref(null)
const selectedProjectId = ref(null)
const selectedArticleId = ref(null)

const workspaceId = ref(1)
const categoryId = ref(1)
const projectId = ref(1)
const articleId = ref(1)
const customerId = ref(1)
const snackbar = ref(false)
const snackbarMessage = ref('')
const snackbarColor = ref('success')
const timerColor = ref('success')

const filterMenu = ref(false)
const activeFilter = ref('All')

const filterOptions = [
  { value: 'A-Z', label: 'A-Z', icon: 'mdi-arrow-up' },
  { value: 'Z-A', label: 'Z-A', icon: 'mdi-arrow-down' },
  { value: 'Gelezen', label: 'Gelezen', icon: 'mdi-email-check' },
  { value: 'Ongelezen', label: 'Ongelezen', icon: 'mdi-email-alert' },

]

const getCustomerInitials = (name) => {
  if (!name) return '?'
  const parts = name.trim().split(' ')
  if (parts.length === 1) return parts[0][0].toUpperCase()
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
}

const RequireNameRules = [
  (v) => !!v || 'Invullen van dit veld is verplicht.',
  (v) => (v?.trim()?.length ?? 0) >= 1 || 'De naam moet minstens 1 teken lang zijn',
]


const ProjectRules = [
  (v) => !!v || 'Het toevoegen van een project is verplicht.'
]

const CategoryRules = [
  (v) => !!v || 'Het toevoegen van een categorie is verplicht.'
]


const nameRules = [
  (v) => !!v || 'Uw naam is verplicht.',
  (v) => (v?.trim()?.length ?? 0) >= 2 || 'De naam moet minimaal 2 tekens lang zijn,',
]

const emailRules = [
  (v) => !!v || 'Het invullen van een email is verplicht',
  (v) => /.+@.+\..+/.test(v) || 'Voer een geldige emailadres in.'
]

const phoneRules = [
  (v) => {
    const trimmed = v?.trim();
    if (!trimmed) return true;
    return trimmed.length >= 3 || 'Het telefoonnummer moet minimaal 3 cijfers lang zijn.';
  },
  (v) => {
    const trimmed = v?.trim();
    if (!trimmed) return true;
    return /^\d+$/.test(trimmed) || 'Het telefoonnummer mag alleen uit cijfers bestaan.';
  }
]

const passwordRules = [
  (v) => !!v || 'Het invullen van een wachtwoord is verplicht.',
  (v) => (v?.length ?? 0) >= 8 || 'Het wachtwoord moet minimaal 8 tekens lang zijn.',
  (v) => /[A-Z]/.test(v) || 'Moet een hoofdletter bevatten.',
  (v) => /[a-z]/.test(v) || 'Moet een kleine letter bevatten.',
]

const confirmRules = computed(() => [
  (v) => !!v || 'Bevestig uw wachtwoord.',
  (v) => v === customerDraft.password || 'Wachtwoorden komen niet overeen.'
])

const draft = reactive({
  id: null,
  name: '',
  title: '',
  summary: '',
  content: '',
  description: '',
  visibility: 'public',
  tags: [],
  customer: '',
  workspaceId: null,
  categoryId: null,
  projectId: null,
  status: '',
  slug: '',
  customerAccess: []
})

const customerDraft = reactive({
  id: null,
  companyName: '',
  name: '',
  email: '',
  tel: '',
  address: '',
  role: 'customer',
  password: '',
  password_confirmation: '',
})

const customersData = ref([])
const workspaceData = ref([])
const reviewRecords = ref([])

onMounted(async () => {
  await loadOverviewData()
})

async function reloadWorkspaces() {
  const workspacesResponse = await getAdminWorkspaces()
  workspaceData.value = extractCollection(workspacesResponse).map(normalizeWorkspace)

  if (selectedWorkspaceId.value === 'project' && selectedProjectId.value) {
    selectEntity('project', selectedProjectId.value)
  } else if (selectedEntityType.value === 'category' && selectedCategoryId.value) {
    selectEntity('category', selectedCategoryId.value)
  } else if (selectedWorkspaceId.value) {
    selectEntity('workspace', selectedWorkspaceId.value)
  }
}

const customerOnlyOptions = computed(() =>
  customerOnlyRecords.value.map((customer) => ({
    title: formatCustomerOptionLabel(customer),
    value: customer.id,
  })),
)
const customerOptions = computed(() => [
  { title: 'Alle klanten', value: null },
  ...customerOnlyOptions.value,
])
const kindOptions = ['Alles', 'Workspaces', 'Categorieën', 'Projecten', 'Artikelen']
const customerRoleOptions = ['admin', 'customer']
const articleStatusOptions = [
  { label: 'Gepubliceerd', value: 'Gepubliceerd' },
  { label: 'Concept', value: 'Concept' },
]
const articleVisibilityOptions = [
  { label: 'Privé', value: 'private' },
  { label: 'Openbaar', value: 'public' },

]
const workspaceCustomerSearch = ref('')

const customerOnlyRecords = computed(() =>
  customersData.value.filter((customer) => customer.role !== 'admin')
)

const workspaceCustomerHeaders = [
  { title: '', key: 'access', sortable: false, width: 72 },
  { title: 'Klant', key: 'name', width: 240 },
  { title: 'Bedrijf', key: 'companyName', width: 180 },
  { title: 'Adres', key: 'address', width: 320 },
  { title: 'Tel.', key: 'tel', width: 160 },
]

function formatCustomerOptionLabel(customer) {
  const company = safeText(customer?.companyName, 'Onbekend bedrijf')
  const name = safeText(customer?.name)
  return name ? `${company} (${name})` : company
}

function extractCollection(payload) {
  if (Array.isArray(payload)) return payload
  if (Array.isArray(payload?.data)) return payload.data
  return []
}


function normalizeRole(role) {
  if (role === 'klant') return 'customer'
  return role ?? 'customer'
}

function safeText(value, fallback = '') {
  return typeof value === 'string' ? value : fallback
}

function normalizeArticle(article) {
  return {
    ...article,
    summary: safeText(article.summary),
    content: safeText(article.content),
    slug: safeText(article.slug),
    status: safeText(article.status, 'Concept'),
    visibility: safeText(article.visibility, 'public'),
    tags: Array.isArray(article.tags) ? article.tags.filter((tag) => typeof tag === 'string') : [],
    updated_at: safeText(article.updated_at),
  }
}

function normalizeProject(project) {
  return {
    ...project,
    slug: safeText(project.slug),
    description: safeText(project.description),
    status: safeText(project.status, 'Concept'),
    customerAccess: Array.isArray(project.customer_ids) ? project.customer_ids : [],
    articles: extractCollection(project.articles).map(normalizeArticle),
  }
}

function normalizeCategory(category) {
  return {
    ...category,
    description: safeText(category.description),
    projects: extractCollection(category.projects).map(normalizeProject),
  }
}

function normalizeWorkspace(workspace) {
  const members = extractCollection(workspace.members).map((member) => ({
    id: member.id,
    name: safeText(member.name),
    email: safeText(member.email),
    companyName: safeText(member.company, 'Onbekend bedrijf'),
    address: safeText(member.address),
    tel: safeText(member.phone_number),
    role: normalizeRole(member.role),
  }))

  const customerAccess = [...new Set(
    members
      .filter((member) => member.role !== 'admin')
      .map((member) => member.id)
      .filter(Boolean),
  )]

  return {
    ...workspace,
    description: safeText(workspace.description),
    customer: customerAccess[0] ?? 'Geen klanten',
    customerAccess,
    members,
    categories: extractCollection(workspace.categories).map(normalizeCategory),
  }
}

function normalizeCustomer(user) {
  return {
    id: user.id,
    companyName: safeText(user.company, 'Onbekend bedrijf'),
    name: safeText(user.name),
    email: safeText(user.email),
    tel: safeText(user.phone_number),
    address: safeText(user.address),
    role: normalizeRole(user.role),
  }
}

function flattenArticles(workspaces) {
  return workspaces.flatMap((workspace) =>
    workspace.categories.flatMap((category) =>
      category.projects.flatMap((project) =>
        project.articles.map((article) => ({
          ...article,
          workspaceId: workspace.id,
          workspaceName: workspace.name,
          categoryId: category.id,
          categoryName: category.name,
          projectId: project.id,
          projectName: project.name,
        })),
      ),
    ),
  )
}

function normalizeReviewRecord(feedback, article) {
  const reviewerName = safeText(feedback.user?.name, 'Onbekende klant')
  const reviewerEmail = safeText(feedback.user?.email)
  const feedbackText = safeText(feedback.feedback)
  const isRead = feedback.is_read === true || feedback.is_read === 1 || feedback.is_read === '1'
  const helpfulValue = feedback.helpful === 1 || feedback.helpful === '1' || feedback.helpful === true
    ? 1 : feedback.helpful === 0 || feedback.helpful === '0' || feedback.helpful === false ? 0 : null
  const createdAtRaw = safeText(feedback.created_at)
  const updatedAtRaw = safeText(feedback.updated_at)
  const submittedAtRaw = getLatestTimestamp(createdAtRaw, updatedAtRaw)

  return {
    id: feedback.id,
    articleId: article.id,
    articleTitle: safeText(article.title, 'Ongetiteld artikel'),
    articleStatus: safeText(article.status, 'Concept'),
    articleUpdated_at: safeText(article.updated_at),
    workspaceName: safeText(article.workspaceName),
    categoryName: safeText(article.categoryName),
    projectName: safeText(article.projectName),
    reviewerId: feedback.user_id,
    reviewerName,
    reviewerEmail,
    reviewerInitials: getCustomerInitials(reviewerName),
    helpful: helpfulValue,
    isRead,
    feedbackText,
    hasFeedbackText: Boolean(feedbackText),
    submittedAt: formatRelativeDate(submittedAtRaw),
    submittedAtRaw,
    created_at: formatRelativeDate(createdAtRaw),
    updated_at: formatRelativeDate(updatedAtRaw),
    createdAtRaw,
    updatedAtRaw,
  }
}

function formatRelativeDate(value) {
  if (!value) return ''

  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value

  const diffMs = date.getTime() - Date.now()
  const diffSeconds = Math.round(diffMs / 1000)
  const absSeconds = Math.abs(diffSeconds)
  const formatter = new Intl.RelativeTimeFormat('nl', { numeric: 'auto' })

  if (absSeconds < 45) return 'zojuist'
  if (absSeconds < 2700) return formatter.format(Math.round(diffSeconds / 60), 'minute')
  if (absSeconds < 64800) return formatter.format(Math.round(diffSeconds / 3600), 'hour')
  if (absSeconds < 561600) return formatter.format(Math.round(diffSeconds / 86400), 'day')
  if (absSeconds < 2419200) return formatter.format(Math.round(diffSeconds / 604800), 'week')
  if (absSeconds < 29030400) return formatter.format(Math.round(diffSeconds / 2592000), 'month')

  return formatter.format(Math.round(diffSeconds / 31536000), 'year')
}

function getLatestTimestamp(createdAt, updatedAt) {
  const created = createdAt ? new Date(createdAt) : null
  const updated = updatedAt ? new Date(updatedAt) : null

  if (updated && !Number.isNaN(updated.getTime()) && created && !Number.isNaN(created.getTime())) {
    return updated > created ? updatedAt : createdAt
  }

  if (updated && !Number.isNaN(updated.getTime())) return updatedAt
  if (created && !Number.isNaN(created.getTime())) return createdAt

  return updatedAt || createdAt || ''
}

function isReviewRead(review) {
  return review?.isRead === true
}

function buildUserPayload() {
  const payload = {
    company: customerDraft.companyName,
    name: customerDraft.name,
    email: customerDraft.email,
    phone_number: customerDraft.tel,
    address: customerDraft.address,
    role: customerDraft.role,
  }

  if (customerDraft.password) {
    payload.password = customerDraft.password
    payload.password_confirmation = customerDraft.password_confirmation
  }

  return payload
}

function upsertCustomerRecord(user) {
  const normalizedCustomer = normalizeCustomer(user)
  const existingIndex = customersData.value.findIndex((item) => item.id === normalizedCustomer.id)

  if (existingIndex === -1) {
    customersData.value.unshift(normalizedCustomer)
  } else {
    customersData.value.splice(existingIndex, 1, normalizedCustomer)
  }

  selectedCustomerCrudId.value = normalizedCustomer.id
  syncLocalCounters()
}

function syncLocalCounters() {
  const workspaceIds = workspaceData.value.map((workspace) => workspace.id)
  const categoryIds = workspaceData.value.flatMap((workspace) => workspace.categories.map((category) => category.id))
  const projectIds = workspaceData.value.flatMap((workspace) =>
    workspace.categories.flatMap((category) => category.projects.map((project) => project.id)),
  )
  const articleIds = workspaceData.value.flatMap((workspace) =>
    workspace.categories.flatMap((category) =>
      category.projects.flatMap((project) => project.articles.map((article) => article.id)),
    ),
  )
  const customerIds = customersData.value.map((customer) => customer.id)

  workspaceId.value = Math.max(0, ...workspaceIds) + 1
  categoryId.value = Math.max(0, ...categoryIds) + 1
  projectId.value = Math.max(0, ...projectIds) + 1
  articleId.value = Math.max(0, ...articleIds) + 1
  customerId.value = Math.max(0, ...customerIds) + 1
}

function initializeSelection() {
  const firstWorkspace = workspaceData.value[0] ?? null
  const firstCustomer = customersData.value[0] ?? null
  const firstReview = reviewRecords.value[0] ?? null

  if (firstWorkspace) {
    selectedEntityType.value = 'workspace'
    selectedWorkspaceId.value = firstWorkspace.id
    selectedCategoryId.value = null
    selectedProjectId.value = null
    selectedArticleId.value = null
    expandedWorkspaces.value = [firstWorkspace.id]
  } else {
    selectedWorkspaceId.value = null
  }

  selectedCustomerCrudId.value = firstCustomer?.id ?? null
  selectedReviewId.value = firstReview?.id ?? null
}

async function loadOverviewData() {
  loading.value = true
  OverviewError.value = ''

  try {
    const [workspacesResponse, usersResponse] = await Promise.all([
      getAdminWorkspaces(),
      getAdminUsers(),
    ])

    workspaceData.value = extractCollection(workspacesResponse).map(normalizeWorkspace)
    customersData.value = extractCollection(usersResponse).map(normalizeCustomer)
    reviewRecords.value = await loadReviewRecords(workspaceData.value)

    syncLocalCounters()
    initializeSelection()
  } catch {
    OverviewError.value = 'Kon de overzicht inladen.'
  } finally {
    loading.value = false
  }
}

async function loadReviewRecords(workspaces) {
  const articleMap = new Map(
    flattenArticles(workspaces).map((article) => [article.id, article]),
  )

  const feedbacks = extractCollection(await getFeedbacks())

  return feedbacks
    .map((feedback) => {
      const article = articleMap.get(feedback.article_id)

      if (!article) {
        const apiArticle = feedback.article ?? {}
        return normalizeReviewRecord(feedback, {
          id: feedback.article_id,
          title: safeText(apiArticle.title, 'Ongetiteld artikel'),
          status: safeText(apiArticle.status, 'Concept'),
          updated_at: safeText(apiArticle.updated_at),
          workspaceName: safeText(apiArticle.workspace?.name),
          projectName: safeText(apiArticle.project?.name),
          categoryName: '',
        })
      }

      return normalizeReviewRecord(feedback, article)
    })
    .sort((a, b) => {
      const first = a.submittedAtRaw || ''
      const second = b.submittedAtRaw || ''
      return second.localeCompare(first)
    })
}


function toggleWorkspaceCustomerAccess(entityType, entityId, customerId, enabled) {
  const entity = entityType === 'project'
    ? findProject(entityId)?.project
    : workspaceData.value.find((item) => item.id === entityId)
  if (!entity) return
  const current = entity.customerAccess || []
  entity.customerAccess = enabled
    ? current.includes(customerId) ? current : [...current, customerId]
    : current.filter((id) => id !== customerId)
}

function toggleWorkspaceCustomerAccessFromRow(event, row) {
  const customer = row?.item?.raw ?? row?.item
  const entity = selectedEntity.value
  if (!entity || !customer) return
  const currentlySelected = (entity.customerAccess || []).includes(customer.id)
  toggleWorkspaceCustomerAccess(selectedEntityType.value, entity.id, customer.id, !currentlySelected)
}

function updateWorkspaceCustomerAccess(entityType, entityId, customers) {
  const entity = entityType === 'project'
    ? findProject(entityId)?.project
    : workspaceData.value.find((item) => item.id === entityId)
  if (!entity) return
  entity.customerAccess = customers || []
}

const workspaceSelectOptions = computed(() =>
  workspaceData.value.map((workspace) => ({
    label: `${workspace.name} · ${workspace.customer}`,
    value: workspace.id,
  })),
)

const categorySelectOptions = computed(() =>
  workspaceData.value.flatMap((workspace) =>
    workspace.categories.map((category) => ({
      label: `${category.name} · ${workspace.name}`,
      value: category.id,
    })),
  ),
)

const availableArticleTags = computed(() =>
  [...new Set(
    workspaceData.value.flatMap((workspace) =>
      workspace.categories.flatMap((category) =>
        category.projects.flatMap((project) =>
          project.articles.flatMap((article) => Array.isArray(article.tags) ? article.tags : []),
        ),
      ),
    ),
  )].sort((a, b) => a.localeCompare(b)),
)

const projectSelectOptions = computed(() =>
  workspaceData.value.flatMap((workspace) =>
    workspace.categories.flatMap((category) =>
      category.projects.map((project) => ({
        label: `${project.name} · ${category.name}`,
        value: project.id,
      })),
    ),
  ),
)

const normalizedSearch = computed(() => search.value.trim().toLowerCase())
const normalizedCustomerSearch = computed(() => customerSearch.value.trim().toLowerCase())

const filteredCustomers = computed(() => {
  if (!normalizedCustomerSearch.value) return customersData.value
  return customersData.value.filter((customer) => {
    const q = normalizedCustomerSearch.value
    return (
      safeText(customer.companyName).toLowerCase().includes(q) ||
      safeText(customer.name).toLowerCase().includes(q) ||
      safeText(customer.email).toLowerCase().includes(q) ||
      safeText(customer.tel).toLowerCase().includes(q) ||
      safeText(customer.address).toLowerCase().includes(q) ||
      safeText(customer.role).toLowerCase().includes(q)
    )
  })
})

const filteredReviewRecords = computed(() => {
  const q = reviewSearch.value.trim().toLowerCase()

  let records = reviewRecords.value.filter((review) => {
    if (!q) return true

    return (
      safeText(review.articleTitle).toLowerCase().includes(q) ||
      safeText(review.reviewerName).toLowerCase().includes(q) ||
      safeText(review.reviewerEmail).toLowerCase().includes(q) ||
      safeText(review.projectName).toLowerCase().includes(q) ||
      safeText(review.categoryName).toLowerCase().includes(q) ||
      safeText(review.workspaceName).toLowerCase().includes(q) ||
      safeText(review.feedbackText).toLowerCase().includes(q)
    )
  })

  if (activeFilter.value === 'A-Z') {
    records = [...records].sort((a, b) => a.articleTitle.localeCompare(b.articleTitle))
  } else if (activeFilter.value === 'Z-A') {
    records = [...records].sort((a, b) => b.articleTitle.localeCompare(a.articleTitle))
  } else if (activeFilter.value === 'Gelezen') {
    records = records.filter((review) => isReviewRead(review))
  } else if (activeFilter.value === 'Ongelezen') {
    records = records.filter((review) => !isReviewRead(review))
  }

  return records
})

const selectedCustomerRecord = computed(() =>
  customersData.value.find((customer) => customer.id === selectedCustomerCrudId.value) ?? null
)

const selectedReviewRecord = computed(() =>
  reviewRecords.value.find((review) => review.id === selectedReviewId.value) ?? null
)

const articleReviewRecords = computed(() => {
  if (!selectedReviewRecord.value) return []

  return reviewRecords.value.filter((review) => review.articleId === selectedReviewRecord.value.articleId)
})

const activeArticleReviewIndex = computed(() =>
  articleReviewRecords.value.findIndex((review) => review.id === selectedReviewId.value),
)

const activeArticleReview = computed(() =>
  articleReviewRecords.value[activeArticleReviewIndex.value] ?? selectedReviewRecord.value,
)

const workspaceDeleteTarget = computed(() =>
  workspaceData.value.find((workspace) => workspace.id === workspaceDeleteId.value) ?? null
)

const categoryDeleteTarget = computed(() =>
  workspaceData.value.flatMap(w => w.categories).find(c => c.id === categoryDeleteId.value) ?? null
)

const projectDeleteTarget = computed(() => {
  for (const workspace of workspaceData.value) {
    for (const category of workspace.categories) {
      const project = category.projects.find(p => p.id === projectDeleteId.value)
      if (project) return project
    }
  }
  return null
}
)

const customerDeleteTarget = computed(() =>
  customersData.value.find((customer) => customer.id === customerDeleteId.value) ?? null
)

const reviewDeleteTarget = computed(() =>
  reviewRecords.value.find((review) => review.id === reviewDeleteId.value) ?? null
)

const selectedCustomerId = computed(() => {
  if (selectedCustomer.value === 'Alle klanten') return null
  return customersData.value.find(c => c.companyName === selectedCustomer.value)?.id ?? null
})

const customerWorkspaces = computed(() => {
  if (!selectedCustomerRecord.value) return []
  return workspaceData.value.filter(
    (workspace) => workspace.customerAccess.includes(selectedCustomerRecord.value.id),
  )
})

const filteredWorkspaces = computed(() => {
  return workspaceData.value
    .filter((workspace) => {
      const customerMatch =
        selectedCustomerId.value === null || workspace.customerAccess.includes(selectedCustomerId.value)
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

const totalRecords = computed(() =>
  counts.value.workspaces + counts.value.categories + counts.value.projects + counts.value.articles
)

const selectedEntity = computed(() => {
  if (selectedEntityType.value === 'workspace') return findWorkspace(selectedWorkspaceId.value)
  if (selectedEntityType.value === 'category') return findCategory(selectedCategoryId.value)?.category ?? null
  if (selectedEntityType.value === 'project') return findProject(selectedProjectId.value)?.project ?? null
  if (selectedEntityType.value === 'article') return findArticle(selectedArticleId.value)?.article ?? null
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
  if (selectedEntityType.value === 'category') return findCategory(selectedCategoryId.value)?.workspace.name ?? '-'
  if (selectedEntityType.value === 'project') return findProject(selectedProjectId.value)?.category.name ?? '-'
  if (selectedEntityType.value === 'article') return findArticle(selectedArticleId.value)?.project.name ?? '-'
  return '-'
})

const childRows = computed(() => {
  if (!selectedEntity.value) return []
  if (selectedEntityType.value === 'workspace') return selectedEntity.value.categories.map((category) => ({ ...category, type: 'category' }))
  if (selectedEntityType.value === 'category') return selectedEntity.value.projects.map((project) => ({ ...project, type: 'project' }))
  if (selectedEntityType.value === 'project') return selectedEntity.value.articles.map((article) => ({ ...article, name: article.title, type: 'article' }))
  return []
})

const childSectionTitle = computed(() => {
  if (selectedEntityType.value === 'workspace') return 'Categorieën binnen deze workspace'
  if (selectedEntityType.value === 'category') return 'Projecten binnen deze categorie'
  if (selectedEntityType.value === 'project') return 'Artikelen binnen dit project'
  return 'Dit artikel heeft geen onderliggende items'
})

// function updateWorkspaceCustomerAccess(workspaceId, customers) {
//   const workspace = workspaceData.value.find((item) => item.id === workspaceId)
//   if (!workspace) return
//   workspace.customerAccess = customers || []
// }
//
function formatWorkspaceCustomers(workspace) {
  if (!workspace?.customerAccess?.length) return 'Geen klanten'
  if (workspace.customerAccess.length === 1) {
    return customersData.value.find(c => c.id === workspace.customerAccess[0])?.companyName ?? 'Onbekend'
  } return `${workspace.customerAccess.length} klanten`
}

function workspaceMatchesSearch(workspace) {
  const q = normalizedSearch.value
  if (!q) return true
  return (
    safeText(workspace.name).toLowerCase().includes(q) ||
    safeText(workspace.customer).toLowerCase().includes(q) ||
    safeText(workspace.description).toLowerCase().includes(q) ||
    workspace.categories.some((category) => categoryMatchesSearch(category, workspace))
  )
}

function categoryMatchesSearch(category, workspace) {
  const q = normalizedSearch.value
  if (!q) return true
  return (
    safeText(category.name).toLowerCase().includes(q) ||
    safeText(category.description).toLowerCase().includes(q) ||
    safeText(workspace.name).toLowerCase().includes(q) ||
    category.projects.some((project) => projectMatchesSearch(project, category, workspace))
  )
}

function projectMatchesSearch(project, category, workspace) {
  const q = normalizedSearch.value
  if (!q) return true
  return (
    safeText(project.name).toLowerCase().includes(q) ||
    safeText(project.description).toLowerCase().includes(q) ||
    safeText(project.status).toLowerCase().includes(q) ||
    safeText(category.name).toLowerCase().includes(q) ||
    safeText(workspace.name).toLowerCase().includes(q) ||
    project.articles.some((article) => articleMatchesSearch(article, project, category, workspace))
  )
}

function articleMatchesSearch(article, project, category, workspace) {
  const q = normalizedSearch.value
  if (!q) return true
  return (
    safeText(article.title).toLowerCase().includes(q) ||
    safeText(article.summary).toLowerCase().includes(q) ||
    safeText(article.slug).toLowerCase().includes(q) ||
    safeText(article.status).toLowerCase().includes(q) ||
    safeText(project.name).toLowerCase().includes(q) ||
    safeText(category.name).toLowerCase().includes(q) ||
    safeText(workspace.name).toLowerCase().includes(q)
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

function countArticlesInCategory(category) {
  return category.projects.reduce((sum, project) => sum + project.articles.length, 0)
}

function customerWorkspaceCount(customerId) {
  return workspaceData.value.filter((workspace) => workspace.customerAccess.includes(customerId)).length
}

function isExpanded(list, id) {
  const items = Array.isArray(list) ? list : list?.value
  return Array.isArray(items) ? items.includes(id) : false
}

function toggleExpanded(listRef, id) {
  const items = Array.isArray(listRef?.value) ? listRef.value : []
  listRef.value = items.includes(id) ? items.filter((item) => item !== id) : [...items, id]
}

function toggleWorkspace(id) { toggleExpanded(expandedWorkspaces, id) }
function toggleCategory(id) { toggleExpanded(expandedCategories, id) }
function toggleProject(id) { toggleExpanded(expandedProjects, id) }

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
    if (!isExpanded(expandedWorkspaces, id)) expandedWorkspaces.value = [...expandedWorkspaces.value, id]
    selectedWorkspaceId.value = id
    draft.workspaceId = id
    selectedCategoryId.value = null
    selectedProjectId.value = null
    selectedArticleId.value = null
  }

  if (type === 'category') {
    const result = findCategory(id)
    if (!result) return
    if (!isExpanded(expandedWorkspaces, result.workspace.id)) expandedWorkspaces.value = [...expandedWorkspaces.value, result.workspace.id]
    if (!isExpanded(expandedCategories, id)) expandedCategories.value = [...expandedCategories.value, id]
    selectedWorkspaceId.value = result.workspace.id
    selectedCategoryId.value = id
    draft.categoryId = id
    selectedProjectId.value = null
    selectedArticleId.value = null
  }

  if (type === 'project') {
    const result = findProject(id)
    if (!result) return
    if (!isExpanded(expandedWorkspaces, result.workspace.id)) expandedWorkspaces.value = [...expandedWorkspaces.value, result.workspace.id]
    if (!isExpanded(expandedCategories, result.category.id)) expandedCategories.value = [...expandedCategories.value, result.category.id]
    if (!isExpanded(expandedProjects, id)) expandedProjects.value = [...expandedProjects.value, id]
    selectedWorkspaceId.value = result.workspace.id
    selectedCategoryId.value = result.category.id
    selectedProjectId.value = id
    draft.projectId = id
    selectedArticleId.value = null
  }

  if (type === 'article') {
    const result = findArticle(id)
    if (!result) return
    if (!isExpanded(expandedWorkspaces, result.workspace.id)) expandedWorkspaces.value = [...expandedWorkspaces.value, result.workspace.id]
    if (!isExpanded(expandedCategories, result.category.id)) expandedCategories.value = [...expandedCategories.value, result.category.id]
    if (!isExpanded(expandedProjects, result.project.id)) expandedProjects.value = [...expandedProjects.value, result.project.id]
    selectedWorkspaceId.value = result.workspace.id
    selectedCategoryId.value = result.category.id
    selectedProjectId.value = result.project.id
    selectedArticleId.value = id
    draft.article = id
  }

  // Switch to content tab when navigating from customer workspaces link
  if (activeTab.value === 'customers' && type !== null) {
    activeTab.value = 'content'
  }
}

function labelForType(type) {
  const labels = { workspace: 'Workspace', category: 'Categorie', project: 'Project', article: 'Artikel' }
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
  draft.title = ''
  draft.summary = ''
  draft.content = ''
  draft.visibility = 'public'
  draft.tags = []
  draft.customer = selectedCustomer.value !== 'Alle klanten' ? selectedCustomer.value : customerOnlyOptions.value[0] ?? ''
  draft.workspaceId = selectedWorkspaceId.value
  draft.categoryId = selectedCategoryId.value
  draft.projectId = selectedProjectId.value
  draft.status = ''
  draft.slug = ''
  draft.customerAccess = []
}

function updateDraftPrimaryField(value) {
  if (dialogType.value === 'article') {
    draft.title = value
    return
  }

  draft.name = value
}

function updateDraftContentField(value) {
  if (dialogType.value === 'project') {
    draft.description = value
    return
  }

  draft.content = value
}

function resolveArticleDraftContext() {
  const projectResult = draft.projectId ? findProject(draft.projectId) : null
  const categoryResult = draft.categoryId ? findCategory(draft.categoryId) : null

  if (projectResult) {
    draft.categoryId = projectResult.category.id
    draft.workspaceId = projectResult.workspace.id
    return projectResult
  }

  if (categoryResult) {
    draft.workspaceId = categoryResult.workspace.id
    return {
      workspace: categoryResult.workspace,
      category: categoryResult.category,
      project: null,
    }
  }

  return null
}

function openCreateDialog(type, defaults = null) {
  dialogMode.value = 'create'
  dialogType.value = type
  resetDraft()
  if (type === 'project') draft.status = 'draft'
  if (type === 'article') draft.status = 'Concept'
  if (defaults?.workspaceId) draft.workspaceId = defaults.workspaceId
  if (defaults?.categoryId) draft.categoryId = defaults.categoryId
  if (defaults?.projectId) draft.projectId = defaults.projectId
  if (type === 'article') resolveArticleDraftContext()
  editorOpen.value = true
}

function openEditDialog(type, id) {
  if (type === 'article') {
    const result = findArticle(id)
    if (!result?.article?.slug) return
    router.push({ name: 'article-new', query: { slug: result.article.slug } })
    return
  }

  const entity = getEntity(type, id)
  if (!entity) return

  dialogMode.value = 'edit'
  dialogType.value = type
  resetDraft()

  if (type === 'workspace') {
    draft.id = entity.id
    draft.slug = entity.slug
    draft.name = entity.name
    draft.summary = entity.description ?? ''
    draft.customer = entity.customer
    draft.customerAccess = entity.customerAccess ?? []
  }

  if (type === 'category') {
    const result = findCategory(id)
    draft.slug = entity.slug
    draft.name = entity.name
    draft.summary = entity.description ?? ''
    draft.workspaceId = result?.workspace.id ?? null
  }

  if (type === 'project') {
    const result = findProject(id)
    draft.slug = entity.slug
    draft.name = entity.name
    draft.summary = entity.description ?? ''
    draft.workspaceId = result?.workspace.id ?? null
    draft.categoryId = result?.category.id ?? null
    draft.status = entity.status
    draft.customerAccess = entity.customerAccess ?? []
  }

  if (type === 'article') {
    const result = findArticle(id)
    draft.id = entity.id
    draft.slug = entity.slug
    draft.title = entity.title
    draft.summary = entity.summary ?? ''
    draft.content = entity.content ?? ''
    draft.tags = Array.isArray(entity.tags) ? [...entity.tags] : []
    draft.projectId = result?.project.id ?? null
    draft.categoryId = result?.category.id ?? null
    draft.workspaceId = result?.workspace.id ?? null
    draft.status = entity.status
    draft.visibility = entity.visibility ?? 'public'
  }

  editorOpen.value = true
}

async function saveWorkspaceMembers() {
  error.value = ''
  try {
    const payload = {
      name: selectedEntity.value.name,
      customer_ids: selectedEntity.value.customerAccess ?? [],
    }
    if (selectedEntityType.value === 'project') {
      await updateProject(selectedEntity.value.slug, payload)
    } else {
      await updateWorkspace(selectedEntity.value.slug, payload)
    }
    await reloadWorkspaces()

    snackbarMessage.value = 'Wijzigingen zijn opgeslagen!'
    snackbarColor.value = '#24a1c7'
    timerColor.value = '#24a1c7'
    snackbar.value = true
  } catch (err) {
    error.value = err.response?.data?.message ?? 'Kon de leden niet opslaan.'
    snackbarMessage.value = error.value
    snackbarColor.value = 'error'
    snackbar.value = true
  }
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

async function saveDraft() {
  const { valid } = await formRef.value.validate();
  if (!valid) return;

  error.value = ''
  try {
    if (dialogType.value === 'workspace') {
      const payload = {
        id: draft.id,
        name: draft.name,
        customer_ids: draft.customerAccess ?? [],
      }
      if (dialogMode.value === 'create') {
        const response = await postWorkspace(payload)
        draft.id = response.id
        draft.customerAccess = response.customer_ids ?? []
      } else {
        await updateWorkspace(draft.slug, payload)
        await reloadWorkspaces()

      }
    }

    if (dialogType.value === 'category') {
      const payload = {
        name: draft.name,
        workspace_id: draft.workspaceId,
      }
      if (dialogMode.value === 'create') {
        const response = await storeCategory(payload)
        draft.id = response.id
      } else {
        await UpdateCategory(draft.slug, payload)
        await reloadWorkspaces()
      }
    }

    if (dialogType.value === 'project') {
      const payload = {
        name: draft.name,
        description: draft.description,
        article_id: draft.articleId,
        category_id: draft.categoryId,
        workspace_id: draft.workspaceId,
        user_id: draft.userId,
        customer_ids: draft.customerAccess ?? [],
      }
      if (dialogMode.value === 'create') {
        const response = await storeProject(payload)
        draft.id = response.id
        draft.customerAccess = response.customer_ids ?? []
      } else {
        await updateProject(draft.slug, payload)
        await reloadWorkspaces()
      }
    } if (dialogType.value === 'article') {
      resolveArticleDraftContext()
      const payload = {
        title: draft.title,
        summary: draft.summary,
        status: draft.status,
        project_id: draft.projectId,
        category_id: draft.categoryId,
        workspace_id: draft.workspaceId,
        visibility: draft.visibility,
        tags: draft.tags,
      }
      if (dialogMode.value === 'create') {
        const response = await postArticle(payload)
        editorOpen.value = false
        await router.push({ name: 'article-new', query: { slug: response.slug } })
        return
      } else {
        await updateArticle(draft.slug, payload)
        await reloadWorkspaces()
      }
    }

    if (dialogMode.value === 'create') createEntity()
    else updateEntity()
    editorOpen.value = false
  } catch (err) {
    error.value = err.response?.data?.message ?? 'Kon de workspace niet aanmaken.'
  }
}


function createEntity() {
  if (dialogType.value === 'workspace') {
    const newWorkspace = { id: draft.id, name: draft.name, customer: draft.customer, description: draft.summary, categories: [] }
    workspaceData.value.unshift(newWorkspace)
    selectEntity('workspace', newWorkspace.id)
    return
  }

  if (dialogType.value === 'category') {
    const workspace = findWorkspace(draft.workspaceId)
    if (!workspace) return
    const newCategory = { id: categoryId.value++, name: draft.name, description: draft.summary, projects: [] }
    workspace.categories.unshift(newCategory)
    selectEntity('category', newCategory.id)
    return
  }

  if (dialogType.value === 'project') {
    const result = findCategory(draft.categoryId)
    if (!result) return
    const newProject = { id: projectId.value++, name: draft.name, description: draft.summary, status: draft.status || 'Concept', articles: [] }
    result.category.projects.unshift(newProject)
    selectEntity('project', newProject.id)
    return
  }

  if (dialogType.value === 'article') {
    const result = findProject(draft.projectId)
    if (!result) return
    const articleTitle = draft.title.trim()
    const newArticle = {
      id: draft.id ?? articleId.value++,
      title: articleTitle,
      summary: draft.summary,
      content: draft.content,
      slug: draft.slug || slugify(articleTitle),
      status: draft.status || 'Concept',
      visibility: draft.visibility || 'public',
      tags: [...draft.tags],
      updated_at: '2026-04-13',
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
    result.article.title = draft.title
    result.article.summary = draft.summary
    result.article.content = draft.content
    result.article.slug = draft.slug || slugify(draft.title)
    result.article.status = draft.status
    result.article.visibility = draft.visibility
    result.article.tags = [...draft.tags]
    result.article.updated_at = '2026-04-13'
    selectEntity('article', result.article.id)
  }
}

function openDeleteDialog(type, id) {
  deleteType.value = type
  deleteId.value = id

  if (type === 'workspace') {
    workspaceDeleteId.value = id
    workspaceDeleteOpen.value = true
  } else if (type === 'category') {
    categoryDeleteId.value = id
    categoryDeleteOpen.value = true
  } else if (type === 'project') {
    projectDeleteId.value = id
    projectDeleteOpen.value = true
  }
}

async function confirmWorkspaceDelete() {
  const target = workspaceDeleteTarget.value
  if (!target) return
  error.value = ''

  try {
    await deleteWorkspace(target.slug)
    workspaceData.value = workspaceData.value.filter((w) => w.id !== target.id)
    if (selectedWorkspaceId.value === target.id) {
      selectedWorkspaceId.value = workspaceData.value[0]?.id ?? null
    }
    workspaceDeleteOpen.value = false
    workspaceDeleteId.value = null
    syncLocalCounters()
  } catch (err) {
    error.value = err.response?.data?.message ?? 'Kon workspace niet verwijderen.'
  }
}


async function confirmCategoryDelete() {
  const target = categoryDeleteTarget.value
  if (!target) return
  error.value = ''

  try {
    await DeleteCategory(target.slug)

    for (const workspace of workspaceData.value) {
      workspace.categories = workspace.categories.filter(c => c.id !== target.id)
    }

    if (selectedCategoryId.value === target.id) {
      selectedCategoryId.value = null
      selectedEntityType.value = 'workspace'
    }

    categoryDeleteOpen.value = false
    categoryDeleteId.value = null
    syncLocalCounters()
  } catch (err) {
    error.value = err.response?.data?.message ?? 'Kon categorie niet verwijderen.'
  }
}


async function confirmProjectDelete() {
  const target = projectDeleteTarget.value
  if (!target) return
  error.value = ''

  try {
    await deleteProject(target.slug)

    for (const workspace of workspaceData.value) {
      for (const category of workspace.categories) {
        category.projects = category.projects.filter(p => p.id !== target.id)
      }
    }

    if (selectedProjectId.value === target.id) {
      selectedProjectId.value = null
      selectedEntityType.value = 'workspace'
    }

    syncLocalCounters()
  } catch (err) {
    error.value = err.response?.data?.message ?? 'Kon project niet verwijderen.'
  } finally {
    projectDeleteOpen.value = false
    projectDeleteId.value = null
  }
}

function resetCustomerDraft() {
  customerDraft.id = null
  customerDraft.companyName = ''
  customerDraft.name = ''
  customerDraft.email = ''
  customerDraft.tel = ''
  customerDraft.address = ''
  customerDraft.role = 'customer'
  customerDraft.password = ''
}

function selectCustomer(id) {
  selectedCustomerCrudId.value = id
}

async function updateReviewReadState(id, isRead = true) {
  const review = reviewRecords.value.find((item) => item.id === id)
  if (!review || review.isRead === isRead) return

  const previousReadState = review.isRead
  review.isRead = isRead

  try {
    await markFeedbackAsRead(id, isRead)
  } catch {
    review.isRead = previousReadState
  }
}

async function selectReview(id) {
  selectedReviewId.value = id
  await updateReviewReadState(id, true)
}

function reviewHelpfulClass(review) {
  if (review?.helpful === 1) return 'review-rating-chip--up'
  if (review?.helpful === 0) return 'review-rating-chip--down'
  return 'review-rating-chip--neutral'
}

function reviewHelpfulIcon(review) {
  if (review?.helpful === 1) return 'mdi-thumb-up'
  if (review?.helpful === 0) return 'mdi-thumb-down'
  return 'mdi-comment-outline'
}

function selectPreviousArticleReview() {
  if (!articleReviewRecords.value.length) return

  const currentIndex = activeArticleReviewIndex.value <= 0
    ? articleReviewRecords.value.length - 1
    : activeArticleReviewIndex.value - 1

  selectReview(articleReviewRecords.value[currentIndex].id)
}

function selectNextArticleReview() {
  if (!articleReviewRecords.value.length) return

  const currentIndex = activeArticleReviewIndex.value >= articleReviewRecords.value.length - 1
    ? 0
    : activeArticleReviewIndex.value + 1

  selectReview(articleReviewRecords.value[currentIndex].id)
}

function reviewReadLabel(review) {
  return isReviewRead(review) ? 'Gelezen' : 'Ongelezen'
}

function reviewReadClass(review) {
  return isReviewRead(review) ? 'review-read-chip--read' : 'review-read-chip--unread'
}

function openReviewDeleteDialog(id) {
  reviewDeleteId.value = id
  reviewDeleteOpen.value = true
}

async function confirmReviewDelete() {
  const target = reviewDeleteTarget.value
  if (!target) return

  error.value = ''

  try {
    await deleteFeedback(target.id)

    const articleReviews = reviewRecords.value.filter((review) => review.articleId === target.articleId)
    const deletedIndex = articleReviews.findIndex((review) => review.id === target.id)
    const nextReviewId = articleReviews[deletedIndex + 1]?.id ?? articleReviews[deletedIndex - 1]?.id ?? null

    reviewRecords.value = reviewRecords.value.filter((review) => review.id !== target.id)
    reviewDeleteOpen.value = false
    reviewDeleteId.value = null

    if (!reviewRecords.value.length) {
      selectedReviewId.value = null
      return
    }

    if (selectedReviewId.value === target.id) {
      selectedReviewId.value = nextReviewId && reviewRecords.value.some((review) => review.id === nextReviewId)
        ? nextReviewId
        : reviewRecords.value[0]?.id ?? null
    }
  } catch (err) {
    error.value = err.response?.data?.message ?? 'Kon de feedback niet verwijderen.'
  }
}

function openCustomerCreateDialog() {
  customerDialogMode.value = 'create'
  resetCustomerDraft()
  customerEditorOpen.value = true
}

function openCustomerEditDialog(id) {
  const customer = customersData.value.find((item) => item.id === id)
  if (!customer) return
  customerDialogMode.value = 'edit'
  resetCustomerDraft()
  customerDraft.id = customer.id
  customerDraft.companyName = customer.companyName
  customerDraft.name = customer.name
  customerDraft.email = customer.email
  customerDraft.tel = customer.tel
  customerDraft.address = customer.address
  customerDraft.role = customer.role
  customerEditorOpen.value = true
}

async function saveCustomerDraft() {
  const { valid } = await formRef.value.validate();
  if (!valid) return
  error.value = ''
  try {
    const payload = buildUserPayload()
    const user = customerDialogMode.value === 'create'
      ? await postUser(payload)
      : await updateUser(customerDraft.id, payload)

    upsertCustomerRecord(user)
    customerEditorOpen.value = false
  } catch (err) {
    error.value = err.response?.data?.message ?? 'Kon de klant niet opslaan.'
  }
}

function openCustomerDeleteDialog(id) {
  customerDeleteId.value = id
  customerDeleteOpen.value = true
}

async function confirmCustomerDelete() {
  const target = customerDeleteTarget.value
  if (!target) return

  error.value = ''

  try {
    await deleteUser(target.id)
    customersData.value = customersData.value.filter((customer) => customer.id !== target.id)
    if (selectedCustomerCrudId.value === target.id) selectedCustomerCrudId.value = customersData.value[0]?.id ?? null
    if (selectedCustomer.value === target.companyName) selectedCustomer.value = 'Alle klanten'
    customerDeleteOpen.value = false
    syncLocalCounters()
  } catch (err) {
    error.value = err.response?.data?.message ?? 'Kon de klant niet verwijderen.'
  }
}

function slugify(value) {
  return String(value)
    .toLowerCase()
    .trim()
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
}

</script>
