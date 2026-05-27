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
          <v-btn variant="text" rounded="lg" prepend-icon="mdi-open-in-new" :disabled="loading || !previewRoute" :to="previewRoute || undefined">
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
              <strong>{{ projectName }}</strong>
            </div>

            <div class="sidebar-meta-row u-flex-between u-gap-12">
              <span>Status</span>
              <strong>{{ statusLabel }}</strong>
            </div>

            <div class="sidebar-meta-row u-flex-between u-gap-12">
              <span>Bijgewerkt</span>
              <strong>{{ updatedLabel }}</strong>
            </div>
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
              <span>{{ statusLabel }}</span>
            </div>

            <input v-model="title" class="article-title-input" placeholder="Paginatitel" :disabled="loading" />

            <textarea v-model="summary" class="article-summary-input" placeholder="Voeg een korte samenvatting toe..."
              rows="2" :disabled="loading" />

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
              <TipTap v-model="content" />
            </div>

            <div class="attachments-section">
              <div class="section-label">Bijlagen & links</div>

              <v-file-upload v-model="model" clearable multiple show-size :disabled="loading || uploadingAttachments">
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

                          <v-progress-linear v-if="uploads[fileUploadKey(file)]" :buffer-value="uploads[fileUploadKey(file)].buffer"
                            :color="uploads[fileUploadKey(file)].progress >= 100 ? 'success' : 'primary'"
                            :model-value="uploads[fileUploadKey(file)].progress" location="bottom" absolute />
                        </template>
                      </v-file-upload-item>
                    </template>
                  </v-file-upload-list>
                </template>
              </v-file-upload>

              <div v-if="uploadingAttachments" class="save-indicator mt-4">
                <span class="save-indicator-dot"></span>
                <span>Bijlagen uploaden...</span>
              </div>

              <div class="bookmark-link-row">
                <v-icon size="18">mdi-link-variant</v-icon>
                <span>TEST PAGINA</span>
              </div>

              <div class="bookmark-input">
                <v-icon size="18">mdi-bookmark-outline</v-icon>
                <span>Voeg een web bookmark toe</span>
              </div>
            </div>
            <v-alert v-if="error" type="error" variant="tonal" density="comfortable" class="mb-4 mt-4">
              {{ error }}
            </v-alert>
          </div>
        </section>

        <div class="editor-actions">
          <div class="editor-actions-left">
            <div class="save-indicator">
              <span class="save-indicator-dot"></span>
              <span>{{ saveIndicatorLabel }}</span>
            </div>
          </div>

          <div class="editor-actions-right">
            <div class="status-pill" :class="status === 'published' ? 'is-published' : 'is-draft'">
              <span class="status-pill-dot"></span>
              {{ statusLabel }}
            </div>

            <v-btn variant="tonal" rounded="lg" class="action-btn action-btn-secondary" :loading="saving" :disabled="loading || uploadingAttachments" @click="saveArticle()">
              Opslaan
            </v-btn>

            <v-btn v-if="status !== 'published'" color="primary" rounded="lg" class="action-btn action-btn-primary" :loading="saving" :disabled="loading || uploadingAttachments" @click="saveArticle('published')">
              Publiceren
            </v-btn>

            <v-btn v-else rounded="lg" class="action-btn action-btn-danger" :loading="saving" :disabled="loading || uploadingAttachments" @click="saveArticle('draft')">
              Depubliceren
            </v-btn>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { VFileUpload } from 'vuetify/labs/VFileUpload'
import TipTap from '@/components/TipTap.vue'
import { getArticle, updateArticle, uploadArticleAttachments } from '@/services/articleService'

const route = useRoute()

const articleSlug = ref('')
const title = ref('')
const summary = ref('')
const content = ref('<p></p>')
const status = ref('draft')
const visibility = ref('public')
const projectName = ref('Knowledgebase Portal')
const updatedLabel = ref('Nog niet opgeslagen')
const loading = ref(false)
const saving = ref(false)
const uploadingAttachments = ref(false)
const error = ref('')
const saveMessage = ref('Klaar om te schrijven')

const model = ref([])
const uploads = ref({})

const previewRoute = computed(() => (
  articleSlug.value
    ? {
      name: 'article',
      params: { slug: articleSlug.value },
    }
    : null
))

const statusLabel = computed(() => status.value === 'published' ? 'Published' : 'Draft')
const saveIndicatorLabel = computed(() => {
  if (uploadingAttachments.value) return 'Bijlagen uploaden...'
  if (saving.value) return 'Opslaan...'
  return saveMessage.value
})

watch(
  () => route.query.slug,
  (slug) => {
    const nextSlug = typeof slug === 'string' ? slug : ''
    if (!nextSlug) {
      error.value = 'Geen artikel geselecteerd om te bewerken.'
      return
    }

    loadArticle(nextSlug)
  },
  { immediate: true },
)

watch(
  model,
  (files) => {
    if (!articleSlug.value || uploadingAttachments.value || !Array.isArray(files) || !files.length) return
    uploadSelectedAttachments(files)
  },
  { deep: true },
)

function fileUploadKey(file) {
  return `${file.name}-${file.size}-${file.lastModified}`
}

function updateUploadProgress(files, progress) {
  const nextUploads = { ...uploads.value }

  files.forEach((file) => {
    nextUploads[fileUploadKey(file)] = {
      progress,
      buffer: 100,
    }
  })

  uploads.value = nextUploads
}

function clearUploadProgress(files) {
  const nextUploads = { ...uploads.value }

  files.forEach((file) => {
    delete nextUploads[fileUploadKey(file)]
  })

  uploads.value = nextUploads
}

function hydrateArticle(article) {
  articleSlug.value = article.slug ?? articleSlug.value
  title.value = article.title ?? ''
  summary.value = article.summary ?? ''
  content.value = typeof article.content === 'string' && article.content.length ? article.content : '<p></p>'
  status.value = article.status ?? 'draft'
  visibility.value = article.visibility ?? 'public'
  projectName.value = article.project?.name ?? 'Knowledgebase Portal'
  updatedLabel.value = article.updated_at ?? 'Zojuist bijgewerkt'
}

async function loadArticle(slug) {
  loading.value = true
  error.value = ''

  try {
    const article = await getArticle(slug)
    hydrateArticle(article)
    saveMessage.value = 'Klaar om te schrijven'
  } catch (err) {
    error.value = err.response?.data?.message ?? 'Kon het artikel niet laden.'
  } finally {
    loading.value = false
  }
}

async function uploadSelectedAttachments(files) {
  uploadingAttachments.value = true
  error.value = ''

  const selectedFiles = files.filter((file) => file instanceof File)

  if (!selectedFiles.length) {
    uploadingAttachments.value = false
    return
  }

  updateUploadProgress(selectedFiles, 0)

  try {
    const article = await uploadArticleAttachments(articleSlug.value, selectedFiles, {
      onUploadProgress: (event) => {
        if (!event.total) return
        const progress = Math.round((event.loaded / event.total) * 100)
        updateUploadProgress(selectedFiles, progress)
      },
    })

    hydrateArticle(article)
    saveMessage.value = 'Bijlagen geupload'
    model.value = []
  } catch (err) {
    error.value = err.response?.data?.message ?? 'Kon de bijlagen niet uploaden.'
  } finally {
    clearUploadProgress(selectedFiles)
    uploadingAttachments.value = false
  }
}

async function saveArticle(nextStatus = status.value) {
  if (!articleSlug.value) return

  saving.value = true
  error.value = ''

  try {
    const article = await updateArticle(articleSlug.value, {
      title: title.value.trim() || 'Onbekende artikelnaam',
      summary: summary.value.trim(),
      content: content.value,
      status: nextStatus,
      visibility: visibility.value,
    })

    hydrateArticle(article)
    saveMessage.value = nextStatus === 'published' ? 'Artikel gepubliceerd' : 'Zojuist opgeslagen'
  } catch (err) {
    error.value = err.response?.data?.message ?? 'Kon het artikel niet opslaan.'
  } finally {
    saving.value = false
  }
}
</script>
