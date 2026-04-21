<template>
  <div class="entity-page admin-studio-page">
    <div class="entity-shell page-shell admin-studio-shell">
      <section class="entity-hero hero">
        <div class="hero-content u-min-w-0">
          <div class="hero-meta-line u-flex-center u-wrap u-gap-8">
            <span class="hero-pill u-inline-flex u-items-center">Admin</span>
            <span class="hero-meta-separator">•</span>
            <span>{{ totalRecords }} records</span>
            <span class="hero-meta-separator">•</span>
            <span>1 content studio</span>
          </div>
          <h1 class="hero-title">Kyano Knowledgebase Admin</h1>
          <p class="hero-subtitle">Beheer je workspaces, categorieën, projecten en artikelen.</p>
        </div>
      </section>

      <section class="stats-grid">
        <article class="stat-card card card-elevated card-rounded-xl">
          <div class="stat-label">Workspaces</div>
          <div class="stat-value">{{ counts.workspaces }}</div>
        </article>
        <article class="stat-card card card-elevated card-rounded-xl">
          <div class="stat-label">Categorieën</div>
          <div class="stat-value">{{ counts.categories }}</div>
        </article>
        <article class="stat-card card card-elevated card-rounded-xl">
          <div class="stat-label">Projecten</div>
          <div class="stat-value">{{ counts.projects }}</div>
        </article>
        <article class="stat-card card card-elevated card-rounded-xl">
          <div class="stat-label">Artikelen</div>
          <div class="stat-value">{{ counts.articles }}</div>
        </article>
      </section>

      <div class="admin-tabs">
        <button class="admin-tab" :class="{ 'admin-tab--active': activeTab === 'content' }"
          @click="activeTab = 'content'">
          Content structuur
        </button>
        <button class="admin-tab" :class="{ 'admin-tab--active': activeTab === 'customers' }"
          @click="activeTab = 'customers'">
          Klantenbeheer
        </button>
      </div>

      <section v-if="activeTab === 'content'" style="border-radius: 0 0 26px 26px !important;"
        class="entity-card card card-elevated card-rounded-2xl studio-card">

        <div class="studio-toolbar">
          <v-select v-model="selectedCustomer" :items="customerOptions" variant="solo-filled" density="comfortable" flat
            hide-details class="studio-toolbar-select" />
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
          <aside class="studio-tree-panel">
            <div class="panel-section-head">
              <div class="panel-kicker">Navigatie</div>
              <h3 class="panel-title">Structuur</h3>
            </div>

            <div v-if="filteredWorkspaces.length" class="tree-list">
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
                    <v-chip size="small" class="entity-chip">Workspace</v-chip>
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
                                  <span>{{ project.status }}</span>
                                  <span class="dot">•</span>
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
              <div class="empty-state-icon icon-box">
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
                <div class="entity-actions u-flex u-items-center u-wrap u-gap-10 detail-actions">
                  <v-btn v-if="selectedEntityType !== 'article'" size="small" variant="tonal"
                    @click="openCreateChildDialog" class="entity-create-btn">
                    {{ createChildButtonLabel }}
                  </v-btn>
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
                  :class="{ 'detail-card-full': selectedEntityType === 'workspace' }">
                  <div class="detail-card-head">
                    <h4 class="panel-title">
                      {{ selectedEntityType === 'workspace' ? 'Toegang klanten' : 'Eigenschappen' }}
                    </h4>
                  </div>

                  <template v-if="selectedEntityType === 'workspace'">
                    <div class="workspace-access-card">
                      <div class="workspace-access-header">
                        <div class="workspace-access-header-copy">
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
                          <v-btn variant="text" size="small"
                            @click="updateWorkspaceCustomerAccess(selectedEntity.id, customerOnlyRecords.map(c => c.companyName))">
                            Alles selecteren
                          </v-btn>
                          <v-btn variant="text" size="small" color="red"
                            @click="updateWorkspaceCustomerAccess(selectedEntity.id, [])">
                            Alles wissen
                          </v-btn>
                        </div>
                      </div>
                      <div class="workspace-access-table-shell">
                        <v-data-table :headers="workspaceCustomerHeaders" :items="customerOnlyRecords"
                          :search="workspaceCustomerSearch" items-per-page="-1" fixed-header height="360" hover
                          class="workspace-access-table">
                          <template #item.access="{ item }">
                            <div class="workspace-access-check">
                              <v-checkbox-btn
                                :model-value="(selectedEntity.customerAccess || []).includes(item.companyName)"
                                @update:model-value="toggleWorkspaceCustomerAccess(selectedEntity.id, item.companyName, $event)" />
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
              </div>

              <article v-if="showsSummaryCard" class="detail-card card card-rounded-xl">
                <div class="detail-card-head">
                  <div class="panel-kicker">Samenvatting</div>
                  <h4 class="panel-title">Beschrijving</h4>
                </div>
                <p class="detail-description">
                  {{ selectedEntity.description || selectedEntity.summary || defaultEntityDescription }}
                </p>
              </article>

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
                          <span v-if="child.updatedAt" class="dot">•</span>
                          <span v-if="child.updatedAt">{{ child.updatedAt }}</span>
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

            <div v-else class="empty-detail-state">
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
          <v-btn rounded="xl" prepend-icon="mdi-plus" class="entity-create-btn" @click="openCustomerCreateDialog">
            Nieuwe klant
          </v-btn>
        </div>

        <div class="studio-layout">

          <aside class="studio-tree-panel">
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
                      {{ customer.role === 'admin' ? 'Admin' : 'Klant' }}
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

          <section class="studio-detail-panel">
            <template v-if="selectedCustomerRecord">
              <div class="detail-head">
                <div>
                  <div class="section-kicker">Klant</div>
                  <h3 class="detail-title">{{ selectedCustomerRecord.companyName }}</h3>
                  <p class="detail-subtitle">Beheer klantgegevens en contactinformatie.</p>
                </div>
                <div class="entity-actions u-flex u-items-center u-wrap u-gap-10 detail-actions">
                  <v-btn size="small" variant="text"
                    @click="openCustomerEditDialog(selectedCustomerRecord.id)">Bewerken</v-btn>
                  <v-btn size="small" variant="text" class="delete-btn"
                    @click="openCustomerDeleteDialog(selectedCustomerRecord.id)">Verwijderen</v-btn>
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
                      <span class="meta-value">{{ customerWorkspaceCount(selectedCustomerRecord.companyName) }}</span>
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

                <div v-if="customerWorkspaces.length" class="child-rows">
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
          <v-text-field v-model="draft.name" :label="dialogType === 'article' ? 'Titel' : 'Naam'" variant="solo-filled"
            flat hide-details class="notion-soft-input mb-4" />
          <v-textarea v-if="dialogType === 'project' || dialogType === 'article'" v-model="draft.summary"
            :label="dialogType === 'article' ? 'Samenvatting' : 'Beschrijving'" variant="solo-filled" flat hide-details
            rows="4" class="notion-soft-input mb-4" />
          <v-select v-if="dialogType === 'workspace'" v-model="draft.customerAccess" :items="customerOnlyOptions"
            label="Klanten met toegang" multiple chips closable-chips variant="solo-filled" flat hide-details
            class="notion-soft-input mb-4" />
          <v-select v-if="dialogType === 'category'" v-model="draft.workspaceId" :items="workspaceSelectOptions"
            item-title="label" item-value="value" label="Workspace" variant="solo-filled" flat hide-details
            class="notion-soft-input mb-4" />
          <v-select v-if="dialogType === 'project'" v-model="draft.categoryId" :items="categorySelectOptions"
            item-title="label" item-value="value" label="Categorie" variant="solo-filled" flat hide-details
            class="notion-soft-input mb-4" />
          <template v-if="dialogType === 'article'">
            <v-select v-model="draft.projectId" :items="projectSelectOptions" item-title="label" item-value="value"
              label="Project" variant="solo-filled" flat hide-details class="notion-soft-input mb-4" />
            <v-text-field v-model="draft.slug" label="Slug" variant="solo-filled" flat hide-details
              class="notion-soft-input mb-4" />
            <v-select v-model="draft.status" :items="articleStatusOptions" label="Status" variant="solo-filled" flat
              hide-details class="notion-soft-input" />
          </template>
        </div>
        <div class="dialog-actions u-gap-12">
          <v-btn variant="text" @click="editorOpen = false">Annuleren</v-btn>
          <v-btn color="primary" rounded="xl" @click="saveDraft">Opslaan</v-btn>
        </div>
      </v-card>
    </v-dialog>

    <v-dialog v-model="deleteOpen" max-width="520">
      <v-card class="dialog-card card card-rounded-xl" rounded="xl">
        <div class="dialog-head">
          <div>
            <div class="section-kicker">Verwijderen</div>
            <h3 class="dialog-title">Weet je het zeker?</h3>
          </div>
        </div>
        <div class="dialog-body">
          <p class="detail-description mb-0">
            Je staat op het punt om <strong>{{ deleteTarget?.name || deleteTarget?.title }}</strong> te verwijderen.
          </p>
        </div>
        <div class="u-gap-12 dialog-footer">
          <v-btn variant="text" @click="deleteOpen = false">Annuleren</v-btn>
          <v-btn color="error" rounded="xl" @click="confirmDelete">Verwijderen</v-btn>
        </div>
      </v-card>
    </v-dialog>

    <v-dialog v-model="customerEditorOpen" max-width="680">
      <v-card class="dialog-card card card-rounded-xl" rounded="xl">
        <div class="dialog-head">
          <div>
            <div class="section-kicker">{{ customerDialogMode === 'create' ? 'Nieuwe klant' : 'Klant bewerken' }}</div>
            <h3 class="dialog-title">{{ customerDialogMode === 'create' ? 'Nieuwe klant aanmaken' : 'Klantgegevens aanpassen' }}</h3>
          </div>
        </div>
        <div class="dialog-body">
          <v-text-field v-model="customerDraft.companyName" label="Bedrijfsnaam" variant="solo-filled" flat hide-details
            class="notion-soft-input mb-4" />
          <v-text-field v-model="customerDraft.name" label="Contactpersoon" variant="solo-filled" flat hide-details
            class="notion-soft-input mb-4" />
          <v-text-field v-model="customerDraft.email" label="E-mail" variant="solo-filled" flat hide-details
            class="notion-soft-input mb-4" />
          <v-text-field v-model="customerDraft.tel" label="Telefoon" variant="solo-filled" flat hide-details
            class="notion-soft-input mb-4" />
          <v-textarea v-model="customerDraft.address" label="Adres" variant="solo-filled" flat hide-details rows="3"
            class="notion-soft-input mb-4" />
          <v-select v-model="customerDraft.role" :items="customerRoleOptions" label="Rol" variant="solo-filled" flat
            hide-details class="notion-soft-input mb-4" />
          <v-text-field v-model="customerDraft.password" label="Wachtwoord" type="password" variant="solo-filled" flat
            hide-details class="notion-soft-input" />
        </div>
        <div class="dialog-actions u-gap-12">
          <v-btn variant="text" @click="customerEditorOpen = false">Annuleren</v-btn>
          <v-btn color="primary" rounded="xl" @click="saveCustomerDraft">Opslaan</v-btn>
        </div>
      </v-card>
    </v-dialog>

    <v-dialog v-model="customerDeleteOpen" max-width="520">
      <v-card class="dialog-card card card-rounded-xl" rounded="xl">
        <div class="dialog-head">
          <div>
            <div class="section-kicker">Verwijderen</div>
            <h3 class="dialog-title">Deze klant verwijderen?</h3>
          </div>
        </div>
        <div class="dialog-body">
          <p class="detail-description mb-0">
            Je staat op het punt om <strong>{{ customerDeleteTarget?.companyName }}</strong> te verwijderen.
          </p>
        </div>
        <div class="u-gap-12 dialog-footer">
          <v-btn variant="text" @click="customerDeleteOpen = false">Annuleren</v-btn>
          <v-btn color="error" rounded="xl" @click="confirmCustomerDelete">Verwijderen</v-btn>
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'

const activeTab = ref('content')

const search = ref('')
const customerSearch = ref('')
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

const customerEditorOpen = ref(false)
const customerDeleteOpen = ref(false)
const customerDialogMode = ref('create')
const selectedCustomerCrudId = ref(1)
const customerDeleteId = ref(null)

const selectedEntityType = ref('workspace')
const selectedWorkspaceId = ref(1)
const selectedCategoryId = ref(null)
const selectedProjectId = ref(null)
const selectedArticleId = ref(null)

const workspaceId = ref(3)
const categoryId = ref(5)
const projectId = ref(6)
const articleId = ref(8)
const customerId = ref(4)

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

const customerDraft = reactive({
  id: null,
  companyName: '',
  name: '',
  email: '',
  tel: '',
  address: '',
  role: 'customer',
  password: '',
})

const customersData = ref([
  {
    id: 1,
    companyName: 'Kyano Digital',
    name: 'Kyano Team',
    email: 'info@kyano.nl',
    tel: '+31 6 12345678',
    address: 'Moermanskweg 2-25, 9723 HM Groningen',
    role: 'admin',
  },
  {
    id: 2,
    companyName: 'Studio North',
    name: 'Mila de Vries',
    email: 'hello@studionorth.nl',
    tel: '+31 50 123 4567',
    address: 'Atoomweg 6, 9743 AK Groningen',
    role: 'customer',
  },
  {
    id: 3,
    companyName: 'Kawasaki',
    name: 'Yasuhiko Hashimoto',
    email: 'hello@kawasaki.jp',
    tel: '+81 3-3435-2111',
    address: '1 Chome-14-5 Kaigan, Minato City, Tokyo 105-0022, Japan',
    role: 'customer',
  },
  {
    id: 4,
    companyName: 'Yamaha',
    name: 'yamaha',
    email: 'info@yamaha.jp',
    tel: '+31 6 12345678',
    address: 'Japan somwhere Tokyo',
    role: 'customer',
  },
  {
    id: 5,
    companyName: 'Honda',
    name: 'Honda',
    email: 'hello@honda.jp',
    tel: '+31 50 123 4567',
    address: 'JAPAN',
    role: 'customer',
  },
  {
    id: 6,
    companyName: 'suzuki',
    name: 'suzuki',
    email: 'hello@suzuki.jp',
    tel: '+81 3-3435-2111',
    address: '1 Chome-14-5 Kaigan, Minato City, Tokyo 105-0022, Japan',
    role: 'customer',
  },
])

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

const customerOnlyOptions = computed(() => customersData.value.map((c) => c.companyName))
const customerOptions = computed(() => ['Alle klanten', ...customerOnlyOptions.value])
const kindOptions = ['Alles', 'Workspaces', 'Categorieën', 'Projecten', 'Artikelen']
const customerRoleOptions = ['admin', 'customer']
const articleStatusOptions = ['Draft', 'Published', 'Archived']

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

function toggleWorkspaceCustomerAccess(workspaceId, companyName, enabled) {
  const workspace = workspaceData.value.find((item) => item.id === workspaceId)
  if (!workspace) return
  const currentAccess = workspace.customerAccess || []
  workspace.customerAccess = enabled
    ? currentAccess.includes(companyName) ? currentAccess : [...currentAccess, companyName]
    : currentAccess.filter((name) => name !== companyName)
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
      customer.companyName.toLowerCase().includes(q) ||
      customer.name.toLowerCase().includes(q) ||
      customer.email.toLowerCase().includes(q) ||
      customer.tel.toLowerCase().includes(q) ||
      customer.address.toLowerCase().includes(q) ||
      customer.role.toLowerCase().includes(q)
    )
  })
})

const selectedCustomerRecord = computed(() =>
  customersData.value.find((customer) => customer.id === selectedCustomerCrudId.value) ?? null
)

const customerDeleteTarget = computed(() =>
  customersData.value.find((customer) => customer.id === customerDeleteId.value) ?? null
)

const customerWorkspaces = computed(() => {
  if (!selectedCustomerRecord.value) return []
  return workspaceData.value.filter(
    (workspace) => workspace.customer === selectedCustomerRecord.value.companyName,
  )
})

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

const deleteTarget = computed(() => {
  if (!deleteType.value || deleteId.value == null) return null
  return getEntity(deleteType.value, deleteId.value)
})

function updateWorkspaceCustomerAccess(workspaceId, customers) {
  const workspace = workspaceData.value.find((item) => item.id === workspaceId)
  if (!workspace) return
  workspace.customerAccess = customers || []
}

function formatWorkspaceCustomers(workspace) {
  if (!workspace?.customerAccess?.length) return 'Geen klanten'
  if (workspace.customerAccess.length === 1) return workspace.customerAccess[0]
  return `${workspace.customerAccess.length} klanten`
}

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

function countArticlesInCategory(category) {
  return category.projects.reduce((sum, project) => sum + project.articles.length, 0)
}

function customerWorkspaceCount(companyName) {
  return workspaceData.value.filter((workspace) => workspace.customer === companyName).length
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
  if (type === 'project') draft.status = 'Concept'
  if (type === 'article') draft.status = 'Draft'
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
  if (dialogMode.value === 'create') createEntity()
  else updateEntity()
  editorOpen.value = false
}

function createEntity() {
  if (dialogType.value === 'workspace') {
    const newWorkspace = { id: workspaceId.value++, name: draft.name, customer: draft.customer, description: draft.summary, categories: [] }
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
    const newArticle = { id: articleId.value++, title: draft.name, summary: draft.summary, slug: draft.slug || slugify(draft.name), status: draft.status || 'Draft', updatedAt: '2026-04-13' }
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
    if (result) result.workspace.categories = result.workspace.categories.filter((category) => category.id !== deleteId.value)
  }
  if (deleteType.value === 'project') {
    const result = findProject(deleteId.value)
    if (result) result.category.projects = result.category.projects.filter((project) => project.id !== deleteId.value)
  }
  if (deleteType.value === 'article') {
    const result = findArticle(deleteId.value)
    if (result) result.project.articles = result.project.articles.filter((article) => article.id !== deleteId.value)
  }
  if (selectedEntityType.value === deleteType.value) {
    selectedEntityType.value = 'workspace'
    selectedCategoryId.value = null
    selectedProjectId.value = null
    selectedArticleId.value = null
  }
  deleteOpen.value = false
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

function saveCustomerDraft() {
  if (customerDialogMode.value === 'create') {
    const newCustomer = {
      id: customerId.value++,
      companyName: customerDraft.companyName,
      name: customerDraft.name,
      email: customerDraft.email,
      tel: customerDraft.tel,
      address: customerDraft.address,
      role: customerDraft.role,
    }
    customersData.value.unshift(newCustomer)
    selectedCustomerCrudId.value = newCustomer.id
  } else {
    const customer = customersData.value.find((item) => item.id === customerDraft.id)
    if (!customer) return
    const previousCompanyName = customer.companyName
    customer.companyName = customerDraft.companyName
    customer.name = customerDraft.name
    customer.email = customerDraft.email
    customer.tel = customerDraft.tel
    customer.address = customerDraft.address
    customer.role = customerDraft.role
    workspaceData.value.forEach((workspace) => {
      if (workspace.customer === previousCompanyName) workspace.customer = customer.companyName
    })
    if (selectedCustomer.value === previousCompanyName) selectedCustomer.value = customer.companyName
    selectedCustomerCrudId.value = customer.id
  }
  customerEditorOpen.value = false
}

function openCustomerDeleteDialog(id) {
  customerDeleteId.value = id
  customerDeleteOpen.value = true
}

function confirmCustomerDelete() {
  const target = customerDeleteTarget.value
  if (!target) return
  customersData.value = customersData.value.filter((customer) => customer.id !== target.id)
  if (selectedCustomerCrudId.value === target.id) selectedCustomerCrudId.value = customersData.value[0]?.id ?? null
  if (selectedCustomer.value === target.companyName) selectedCustomer.value = 'Alle klanten'
  customerDeleteOpen.value = false
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
selectCustomer(1)
</script>