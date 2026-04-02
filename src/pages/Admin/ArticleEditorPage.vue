<template>
  <div class="article-editor-page">
    <div class="editor-topbar">
      <div class="editor-topbar-inner">
        <div class="editor-topbar-left">
          <div class="editor-badge">
            <v-icon size="16">mdi-file-document-edit-outline</v-icon>
            <span>Artikel editor</span>
          </div>
        </div>

        <div class="editor-topbar-right">
          <v-btn variant="text" rounded="lg" prepend-icon="mdi-open-in-new" :to="previewRoute">
            Preview
          </v-btn>
        </div>
      </div>
    </div>

    <div class="editor-shell">
      <aside class="editor-sidebar">
        <div class="sidebar-card">
          <div class="sidebar-label">Pagina-instellingen</div>

          <div class="sidebar-meta-list">

            <div class="sidebar-meta-row">
              <span>Project</span>
              <strong>Knowledgebase Portal</strong>
            </div>

            <div class="sidebar-meta-row">
              <span>Status</span>
              <strong>{{ status }}</strong>
            </div>

            <div class="sidebar-meta-row">
              <span>Bijgewerkt</span>
              <strong>Vandaag</strong>
            </div>
          </div>
        </div>

        <div class="sidebar-card">
          <div class="sidebar-label">Reacties</div>

          <div class="comment-row">
            <div class="comment-avatar">K</div>
            <div>
              <div class="comment-meta">
                Klooster Thomas <span>Zojuist</span>
              </div>
              <div class="comment-text">Opmerkingen komen hier</div>
            </div>
          </div>

          <div class="comment-row muted add-comment-row">
            <div class="comment-avatar">K</div>
            <div class="comment-placeholder">Voeg een reactie toe...</div>
          </div>
        </div>
      </aside>

      <main class="editor-content">
        <div class="editor-cover">
          <div class="cover-actions">
            <v-btn size="small">Change</v-btn>
          </div>
        </div>

        <section class="article-card">
          <div class="article-head">

            <div class="article-meta-line">
              <span class="article-pill">Handleiding</span>
              <span class="article-meta-separator">•</span>
              <span>Concept artikel</span>
            </div>

            <input v-model="title" class="article-title-input" placeholder="Paginatitel" />

            <textarea v-model="summary" class="article-summary-input" placeholder="Voeg een korte samenvatting toe..."
              rows="2" />

            <div class="article-author-row">
              <div class="author-avatar">K</div>

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

<style scoped>
.article-editor-page {
  min-height: 100%;
  background: #f7f7f5;
}

.editor-topbar {
  position: sticky;
  top: 0;
  z-index: 20;
  border-bottom: 1px solid rgba(55, 53, 47, 0.08);
  backdrop-filter: blur(14px);
  background: rgba(247, 247, 245, 0.82);
}

.editor-topbar-inner {
  max-width: 1440px;
  margin: 0 auto;
  padding: 14px 28px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.editor-topbar-left {
  display: flex;
  align-items: center;
  gap: 14px;
  min-width: 0;
}

.editor-topbar-right {
  flex-shrink: 0;
}

.editor-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  min-height: 34px;
  padding: 0 12px;
  border-radius: 999px;
  background: #ffffff;
  border: 1px solid #e8e7e4;
  color: #37352f;
  font-size: 0.88rem;
  font-weight: 600;
}

.editor-shell {
  max-width: 1440px;
  margin: 0 auto;
  padding: 32px 28px 72px;
  display: grid;
  grid-template-columns: 260px minmax(0, 1fr);
  gap: 28px;
}

.editor-sidebar {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.sidebar-card {
  border: 1px solid #ececea;
  border-radius: 18px;
  background: #ffffff;
  padding: 18px;
  box-shadow: 0 1px 2px rgba(15, 15, 15, 0.03);
}

.sidebar-label {
  margin-bottom: 12px;
  color: #787774;
  font-size: 0.86rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.sidebar-meta-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.sidebar-meta-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  color: #5f5e5b;
  font-size: 0.92rem;
}

.sidebar-meta-row strong {
  color: #191919;
  font-weight: 600;
}

.editor-content {
  min-width: 0;
}

.editor-cover {
  height: 220px;
  border-radius: 26px;
  background: linear-gradient(135deg, rgba(220, 47, 255, 0.95), rgba(87, 121, 255, 0.9));
  margin-bottom: 20px;
  position: relative;
}

.cover-actions {
  position: absolute;
  top: 18px;
  right: 18px;
  background-color: white;
  border-radius: 4px;
}

.article-card {
  border: 1px solid #ececea;
  border-radius: 26px;
  background: #ffffff;
  overflow: visible;
  box-shadow: 0 2px 10px rgba(15, 15, 15, 0.03);
}

.article-head {
  padding: 40px 56px 28px;
  border-bottom: 1px solid #f0efec;
}

.article-icon {
  margin-bottom: 16px;
  font-size: 4rem;
  line-height: 1;
  border: none;
  background: transparent;
  padding: 0;
  cursor: pointer;
}

.article-meta-line {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 14px;
  color: #787774;
  font-size: 0.92rem;
}

.article-pill {
  display: inline-flex;
  align-items: center;
  min-height: 28px;
  padding: 0 10px;
  border-radius: 999px;
  background: #edf4ff;
  color: #1d4ed8;
  font-size: 0.82rem;
  font-weight: 600;
}

.article-meta-separator {
  color: #b6b4af;
}

.article-title-input {
  width: 100%;
  border: none;
  outline: none;
  background: transparent;
  margin: 0;
  color: #191919;
  font-size: 3.4rem;
  line-height: 1.05;
  font-weight: 800;
  letter-spacing: -0.03em;
}

.article-title-input::placeholder {
  color: #9b9a97;
}

.article-summary-input {
  width: 100%;
  margin-top: 16px;
  border: none;
  outline: none;
  resize: none;
  background: transparent;
  color: #6b6a67;
  font-size: 1.08rem;
  line-height: 1.75;
}

.article-summary-input::placeholder {
  color: #9b9a97;
}

.article-author-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 24px;
}

.author-avatar,
.comment-avatar {
  width: 40px;
  height: 40px;
  border-radius: 999px;
  background: #6ea43f;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  flex-shrink: 0;
}

.author-name {
  color: #191919;
  font-weight: 600;
}

.author-role {
  color: #8a8884;
  font-size: 0.92rem;
}

.article-body {
  padding: 36px 56px 52px;
  color: #2f3437;
}

.editor-block {
  /* width: 100%; */
  margin-bottom: 28px;
}

.attachments-section {
  margin-top: 24px;
  padding-top: 24px;
  border-top: 1px solid #ececea;
}

.section-label {
  margin-bottom: 12px;
  color: #787774;
  font-size: 0.86rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.upload-dropzone {
  border-radius: 18px;
  background: #fbfbfa;
}

.upload-list {
  margin-top: 12px;
}

.comment-row {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.add-comment-row {
  margin-top: 12px;
}

.comment-meta {
  color: #37352f;
  font-size: 0.95rem;
}

.comment-meta span,
.comment-text,
.comment-placeholder {
  color: #9b9a97;
  font-size: 0.9rem;
}

.muted {
  opacity: 0.92;
}

.bookmark-link-row {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #37352f;
  margin-block: 12px;
}

.bookmark-input {
  min-height: 44px;
  border-radius: 10px;
  background: #efefed;
  color: #9b9a97;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 0 14px;
  width: 100%;
}

.editor-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding-top: 18px;
  margin-top: 16px;
  width: 100%;
}

.editor-actions-left,
.editor-actions-right {
  display: flex;
  align-items: center;
  gap: 10px;
}

.save-indicator {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  min-height: 40px;
  padding: 0 12px;
  border-radius: 999px;
  background: #f1f1ef;
  color: #787774;
  font-size: 0.92rem;
  font-weight: 500;
}

.save-indicator-dot {
  width: 8px;
  height: 8px;
  border-radius: 999px;
  background: #22c55e;
  flex-shrink: 0;
}

.status-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  min-height: 40px;
  padding: 0 14px;
  border-radius: 999px;
  font-size: 0.92rem;
  font-weight: 600;
  border: 1px solid transparent;
}

.status-pill-dot {
  width: 8px;
  height: 8px;
  border-radius: 999px;
  flex-shrink: 0;
}

.status-pill.is-draft {
  background: #f6f6f4;
  color: #6b7280;
  border-color: #e7e5e4;
}

.status-pill.is-draft .status-pill-dot {
  background: #9b9a97;
}

.status-pill.is-published {
  background: #edfdf3;
  color: #15803d;
  border-color: #ccefd8;
}

.status-pill.is-published .status-pill-dot {
  background: #22c55e;
}

.action-btn {
  min-width: 108px;
  height: 40px !important;
  padding-inline: 16px !important;
  text-transform: none !important;
  font-weight: 600 !important;
  letter-spacing: 0 !important;
  box-shadow: none !important;
}

.action-btn-secondary {
  border: 1px solid #e7e5e4;
  background: #fff;
  color: #37352f;
}

.action-btn-danger {
  background: #fff1f2 !important;
  color: #be123c !important;
  border: 1px solid #fecdd3 !important;
}

.action-btn-danger:hover {
  background: #ffe4e6 !important;
}

.action-btn-primary {
  box-shadow: 0 1px 2px rgba(16, 24, 40, 0.08) !important;
}

/* TipTap wrapper styling */
:deep(.editor-wrapper) {
  border: 1px solid #ececea;
  border-radius: 18px;
  background: #ffffff;
  overflow: visible;
  position: relative;
}

:deep(.menu-bar),
:deep(.tiptap-toolbar),
:deep(.bubble-menu),
:deep(.floating-menu) {
  border-color: #ececea;
}

:deep(.ProseMirror) {
  min-height: 420px;
  padding: 24px;
  color: #37352f;
  font-size: 1.04rem;
  line-height: 1.9;
  outline: none;
}

:deep(.ProseMirror h1),
:deep(.ProseMirror h2),
:deep(.ProseMirror h3) {
  color: #191919;
  letter-spacing: -0.02em;
}

:deep(.ProseMirror pre) {
  border-radius: 16px;
  border: 1px solid #ececea;
  background: #f7f7f5;
}

@media (max-width: 1100px) {
  .editor-shell {
    grid-template-columns: 1fr;
  }

  .editor-sidebar {
    order: 2;
  }

  .editor-content {
    order: 1;
  }
}

@media (max-width: 900px) {

  .editor-topbar-inner,
  .editor-shell {
    padding-left: 20px;
    padding-right: 20px;
  }

  .article-head,
  .article-body {
    padding-left: 28px;
    padding-right: 28px;
  }

  .article-title-input {
    font-size: 2.5rem;
  }

  .editor-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .editor-actions-left,
  .editor-actions-right {
    width: 100%;
  }

  .editor-actions-right {
    flex-wrap: wrap;
    justify-content: flex-end;
  }
}

@media (max-width: 640px) {
  .editor-topbar-inner {
    flex-direction: column;
    align-items: stretch;
  }

  .editor-cover {
    height: 180px;
    border-radius: 20px;
  }

  .article-head,
  .article-body {
    padding-left: 20px;
    padding-right: 20px;
  }

  .article-title-input {
    font-size: 2rem;
  }

  .article-summary-input {
    font-size: 0.98rem;
  }
}
</style>