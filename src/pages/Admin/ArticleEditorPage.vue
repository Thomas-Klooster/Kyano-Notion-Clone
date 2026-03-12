<template>
  <div class="article-editor-page">
    <div class="d-flex justify-space-between align-center mb-6">
      <div class="d-flex align-center ga-3">
        <v-btn
          variant="text"
          prepend-icon="mdi-arrow-left"
          to="/admin/articles"
        >
          Terug
        </v-btn>

        <v-chip size="small" class="notion-chip">
          {{ status }}
        </v-chip>
      </div>

      <div class="d-flex ga-2">
        <v-btn variant="text">Opslaan</v-btn>
        <v-btn color="primary" rounded="lg">
          {{ status === 'Published' ? 'Bijwerken' : 'Publiceren' }}
        </v-btn>
      </div>
    </div>

    <div class="article-editor-shell">
      <div class="article-editor-content">
        <div class="article-top mb-6">
          <input
            v-model="title"
            class="article-title-input"
            placeholder="Untitled"
          />

          <textarea
            v-model="summary"
            class="article-summary-input"
            placeholder="Voeg een korte samenvatting toe..."
          />
        </div>

        <v-card class="notion-card mb-5" flat rounded="xl">
          <div class="pa-5">
            <div class="text-subtitle-2 mb-4 notion-muted">Eigenschappen</div>

            <v-row>
              <v-col cols="12" md="6">
                <div class="notion-meta-label">Slug</div>
                <v-text-field
                  v-model="slug"
                  class="notion-soft-input"
                  variant="solo-filled"
                  flat
                  hide-details
                  placeholder="artikel-slug"
                />
              </v-col>

              <v-col cols="12" md="6">
                <div class="notion-meta-label">Status</div>
                <v-select
                  v-model="status"
                  class="notion-soft-input"
                  :items="statuses"
                  variant="solo-filled"
                  flat
                  hide-details
                />
              </v-col>

              <v-col cols="12" md="6">
                <div class="notion-meta-label">Project</div>
                <v-select
                  v-model="project"
                  class="notion-soft-input"
                  :items="projects"
                  variant="solo-filled"
                  flat
                  hide-details
                />
              </v-col>

              <v-col cols="12" md="6">
                <div class="notion-meta-label">Categorie</div>
                <v-select
                  v-model="category"
                  class="notion-soft-input"
                  :items="categories"
                  variant="solo-filled"
                  flat
                  hide-details
                />
              </v-col>
            </v-row>
          </div>
        </v-card>

        <v-card class="notion-card mb-5" flat rounded="xl">
          <div class="pa-5">
            <div class="text-subtitle-2 mb-4 notion-muted">Content</div>

            <div class="editor-toolbar mb-4">
              <v-btn size="small" variant="text">Text</v-btn>
              <v-btn size="small" variant="text">H1</v-btn>
              <v-btn size="small" variant="text">H2</v-btn>
              <v-btn size="small" variant="text">Bullets</v-btn>
              <v-btn size="small" variant="text">Quote</v-btn>
              <v-btn size="small" variant="text">Code</v-btn>
            </div>

            <div class="editor-surface">
              <textarea
                v-model="content"
                class="editor-textarea"
                placeholder="Begin hier met schrijven..."
              />
            </div>
          </div>
        </v-card>

        <v-card class="notion-card" flat rounded="xl">
          <div class="pa-5">
            <div class="text-subtitle-2 mb-4 notion-muted">Bijlagen</div>

            <div class="attachment-dropzone mb-4">
              <v-icon icon="mdi-paperclip" size="20" class="mb-2" />
              <div class="font-weight-medium mb-1">Sleep bestanden hierheen</div>
              <div class="page-subtitle mb-0">
                Of voeg later uploads toe aan dit artikel.
              </div>
            </div>

            <div class="attachment-row" v-for="file in attachments" :key="file.name">
              <div>
                <div class="font-weight-medium">{{ file.name }}</div>
                <div class="text-caption notion-muted">{{ file.size }}</div>
              </div>

              <v-btn size="small" variant="text">Verwijderen</v-btn>
            </div>
          </div>
        </v-card>
      </div>

      <div class="article-editor-sidebar">
        <v-card class="notion-card mb-4" flat rounded="xl">
          <div class="pa-5">
            <div class="text-subtitle-2 mb-4 notion-muted">Publicatie</div>

            <div class="sidebar-meta-row">
              <span class="notion-muted">Status</span>
              <strong>{{ status }}</strong>
            </div>

            <div class="sidebar-meta-row">
              <span class="notion-muted">Laatst gewijzigd</span>
              <strong>Zojuist</strong>
            </div>

            <div class="sidebar-meta-row">
              <span class="notion-muted">Auteur</span>
              <strong>Admin</strong>
            </div>
          </div>
        </v-card>

        <v-card class="notion-card" flat rounded="xl">
          <div class="pa-5">
            <div class="text-subtitle-2 mb-4 notion-muted">Acties</div>

            <div class="d-flex flex-column ga-2">
              <v-btn variant="text" justify="start">1</v-btn>
              <v-btn variant="text" justify="start">2</v-btn>
              <v-btn variant="text" justify="start">3</v-btn>
              <v-btn variant="text" justify="start" color="error">4</v-btn>
            </div>
          </div>
        </v-card>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const title = ref('Installatie handleiding')
const slug = ref('installatie-handleiding')
const summary = ref('Stapsgewijze uitleg voor het opzetten van het project.')
const status = ref('Draft')
const project = ref('Knowledgebase Portal')
const category = ref('Installatie')
const content = ref(`# Installatie

Welkom in de handleiding.

## Stap 1
Maak een project aan.

## Stap 2
Koppel het project aan een klant.

## Stap 3
Publiceer het artikel zodra de inhoud klaar is.
`)

const projects = ['Knowledgebase Portal', 'Client Onboarding', 'Support Docs']
const categories = ['Installatie', 'Toegang', 'Troubleshooting']
const statuses = ['Draft', 'Published', 'Archived']

const attachments = ref([
  { name: 'installatie-checklist.pdf', size: '240 KB' },
  { name: 'technische-specificaties.docx', size: '88 KB' },
])
</script>

<style scoped>
.article-editor-page {
  max-width: 1320px;
}

.article-editor-shell {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 300px;
  gap: 20px;
  align-items: start;
}

.article-editor-content {
  min-width: 0;
}

.article-title-input {
  width: 100%;
  border: none;
  outline: none;
  font-size: 2.6rem;
  line-height: 1.1;
  font-weight: 700;
  color: #191919;
  background: transparent;
  margin-bottom: 14px;
}

.article-title-input::placeholder {
  color: #9b9a97;
}

.article-summary-input {
  width: 100%;
  min-height: 70px;
  border: none;
  outline: none;
  resize: none;
  font-size: 1rem;
  line-height: 1.6;
  color: #5f5e5b;
  background: transparent;
}

.article-summary-input::placeholder {
  color: #9b9a97;
}

.editor-toolbar {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  padding-bottom: 8px;
  border-bottom: 1px solid #f0efed;
}

.editor-surface {
  min-height: 520px;
  border: 1px solid #efefee;
  border-radius: 18px;
  background: #fff;
  padding: 20px;
}

.editor-textarea {
  width: 100%;
  min-height: 460px;
  border: none;
  outline: none;
  resize: none;
  font-size: 1rem;
  line-height: 1.75;
  color: #2f3437;
  background: transparent;
}

.editor-textarea::placeholder {
  color: #9b9a97;
}

.attachment-dropzone {
  border: 1px dashed #dddcd8;
  border-radius: 16px;
  padding: 28px;
  text-align: center;
  background: #fcfcfb;
  color: #5f5e5b;
}

.attachment-row {
  min-height: 56px;
  border: 1px solid #efefee;
  border-radius: 14px;
  padding: 0 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 10px;
  background: #fff;
}

.sidebar-meta-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid #f1f1ef;
}

.sidebar-meta-row:last-child {
  border-bottom: none;
}

@media (max-width: 960px) {
  .article-editor-shell {
    grid-template-columns: 1fr;
  }

  .article-title-input {
    font-size: 2rem;
  }
}
</style>