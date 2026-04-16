<template>
  <div class="article-page">
    <div class="article-topbar">
      <div class="article-topbar-inner">
        <div class="article-topbar-left">
          <div class="article-badge">
            <v-icon size="16">mdi-file-document-edit-outline</v-icon>
            <span>Artikel editor</span>
          </div>
        </div>

        <div class="article-topbar-right">
          <v-btn variant="text" rounded="lg" prepend-icon="mdi-open-in-new" :to="previewRoute">
            Preview
          </v-btn>
        </div>
      </div>
    </div>

    <div class="article-grid entity-shell page-shell">
      <aside class="article-sidebar">
        <div class="sidebar-card card card-soft card-rounded-lg">
          <div class="sidebar-label">Pagina-instellingen</div>

          <div class="sidebar-meta-list u-flex-col u-gap-12">

            <div class="sidebar-meta-row u-flex-between u-gap-12">
              <span>Project</span>
              <strong>Knowledgebase Portal</strong>
            </div>

            <div class="sidebar-meta-row u-flex-between u-gap-12">
              <span>Status</span>
              <strong>{{ status }}</strong>
            </div>

            <div class="sidebar-meta-row u-flex-between u-gap-12">
              <span>Bijgewerkt</span>
              <strong>Vandaag</strong>
            </div>
          </div>
        </div>

        <div class="sidebar-card card card-soft card-rounded-lg">
          <div class="sidebar-label">Reacties</div>

          <div class="comment-row">
            <div class="comment-avatar icon-box">K</div>
            <div>
              <div class="comment-meta">
                Klooster Thomas <span>Zojuist</span>
              </div>
              <div class="comment-text">Opmerkingen komen hier</div>
            </div>
          </div>

          <div class="comment-row muted add-comment-row">
            <div class="comment-avatar icon-box">K</div>
            <div class="comment-placeholder">Voeg een reactie toe...</div>
          </div>
        </div>
      </aside>

      <main class="article-content">
        <div class="article-cover">
          <div class="cover-actions">
            <v-btn size="small">Change</v-btn>
          </div>
        </div>

        <section class="article-card card card-elevated card-rounded-2xl">
          <div class="article-head card-head">

            <div class="article-meta-line u-flex-center u-wrap u-gap-8">
              <span class="article-pill u-inline-flex u-items-center">Handleiding</span>
              <span class="article-meta-separator">•</span>
              <span>Concept artikel</span>
            </div>

            <input v-model="title" class="article-title-input" placeholder="Paginatitel" />

            <textarea v-model="summary" class="article-summary-input" placeholder="Voeg een korte samenvatting toe..."
              rows="2" />

            <div class="article-author-row u-flex-center u-gap-12">
              <div class="author-avatar icon-box">K</div>

              <div>
                <div class="author-name">Kyano Team</div>
                <div class="author-role">Knowledgebase Portal</div>
              </div>
            </div>
          </div>

          <div class="article-body">
            <div class="editor-block">
              <TipTap />
            </div>

            <div class="attachments-section">
              <div class="section-label">Bijlagen & links</div>

              <v-file-upload v-model="model" clearable multiple show-size>
                <template v-slot:default>
                  <v-file-upload-dropzone density="comfortable" class="upload-dropzone" />

                  <v-file-upload-list class="upload-list">
                    <template v-slot:default="{ files, onClickRemove }">
                      <v-file-upload-item v-for="(file, index) in files" :key="`${file.name}-${index}`" :file="file"
                        clearable show-size @click:remove="onClickRemove(index)">
                        <template v-slot:prepend>
                          <VAvatar rounded="circle" size="36">
                            <v-icon size="18">mdi-file-outline</v-icon>
                          </VAvatar>

                          <v-progress-linear v-if="uploads.has(file)" :buffer-value="uploads.get(file).buffer"
                            :color="uploads.get(file).progress >= 100 ? 'success' : 'primary'"
                            :model-value="uploads.get(file).progress" location="bottom" absolute />
                        </template>
                      </v-file-upload-item>
                    </template>
                  </v-file-upload-list>
                </template>
              </v-file-upload>

              <div class="bookmark-link-row">
                <v-icon size="18">mdi-link-variant</v-icon>
                <span>TEST PAGINA</span>
              </div>

              <div class="bookmark-input">
                <v-icon size="18">mdi-bookmark-outline</v-icon>
                <span>Voeg een web bookmark toe</span>
              </div>
            </div>
          </div>
        </section>

        <div class="editor-actions">
          <div class="editor-actions-left">
            <div class="save-indicator">
              <span class="save-indicator-dot"></span>
              <span>Zojuist opgeslagen</span>
            </div>
          </div>

          <div class="editor-actions-right">
            <div class="status-pill" :class="status === 'Published' ? 'is-published' : 'is-draft'">
              <span class="status-pill-dot"></span>
              {{ status }}
            </div>

            <v-btn variant="tonal" rounded="lg" class="action-btn action-btn-secondary">
              Opslaan
            </v-btn>

            <v-btn v-if="status !== 'Published'" color="primary" rounded="lg" class="action-btn action-btn-primary">
              Publiceren
            </v-btn>

            <v-btn v-else rounded="lg" class="action-btn action-btn-danger">
              Depubliceren
            </v-btn>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'
import { VFileUpload } from 'vuetify/labs/VFileUpload'
import TipTap from '@/components/TipTap.vue'

const route = useRoute()

const title = ref('PAGINATITEL')
const summary = ref('Dit is de editor van een artikel. Hier kan de admin content schrijven en aanpassen voordat deze gepubliceerd wordt.')
const status = ref('Draft')

const model = ref([])
const uploads = ref(new Map())

const previewRoute = computed(() => ({
  name: 'article-preview',
  params: {
    id: route.params.id || 'preview',
  },
}))
</script>

