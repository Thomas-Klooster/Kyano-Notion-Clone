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

              <v-file-upload v-model="model" clearable multiple show-size :disabled="loading || uploadingAttachments" />

              <div v-if="imagePreviews.length" class="image-preview-grid">
                <div v-for="preview in imagePreviews" :key="preview.key" class="image-preview-card">
                  <div class="delete-preview-box">
                  <v-btn icon size="18" variant="text" color="error" class="delete-preview-button" @click="removePreview(preview)">
                  <v-icon size="16">mdi-window-close</v-icon>
                  </v-btn>
                </div>
                  <img :src="preview.url" @click="openImagePreview" :alt="preview.name" class="image-preview-img" />
                  <div class="image-preview-meta">
                    <span class="image-preview-name">{{ preview.name }}</span>
                    <span v-if="preview.size" class="image-preview-size">{{ formatSize(preview.size) }}</span>
                  </div>
                </div>
              </div>

              <div v-if="uploadingAttachments" class="save-indicator mt-4">
                <span class="save-indicator-dot"></span>
                <span>Bijlagen uploaden...</span>
              </div>

              <div v-if="attachments.length" class="saved-attachments">
                <div v-for="attachment in attachments" :key="attachment.id" type="button"
                 @click="openImagePreview(attachment)" class="attachment-row">
                  <div class="attachment-meta u-flex-center u-gap-8">
                    <template v-if="isImageAttachment(attachment)">
                      <v-icon size="21">mdi-image-outline</v-icon>
                    </template>
                    <v-icon v-else size="21">mdi-file-document-outline</v-icon>
                    <button target="_blank" class="attachment-name">
                      {{ attachment.name || attachment.original_name || 'Bijlage' }}
                    </button>
                  </div>
                  <div class="attachment-actions u-flex-center u-gap-8">
                    <span class="attachment-size">{{ formatSize(attachment.size) }}</span>
                    <v-btn icon size="18" variant="text" color="error" @click="removeAttachment(attachment.id)">
                      <v-icon size="16">mdi-window-close</v-icon>
                    </v-btn>
                  </div>
                </div>
              </div>

                <div v-else class="bookmark-input">
                <v-icon size="18">mdi-bookmark-outline</v-icon>
                <span>Voeg een web bookmark toe</span>
              </div>
            </div>
            <v-alert v-if="error" type="error" variant="tonal" density="comfortable" closable class="mb-4 mt-4">
              {{ error }}
            </v-alert>
          </div>
        </section>  

        <v-dialog v-model="imagePreviewDialog" max-width="1140" class="attacchment-preview-dialog">
        <img v-if="selectedImageUrl" :src="selectedImageUrl" :alt="selectedImageName" class="attachment-preview-image">
        </v-dialog>


        <div class="editor-actions">
          <div class="editor-actions-left">
            <div class="save-indicator">
              <span class="save-indicator-dot"></span>
              <span>{{ saveIndicatorLabel }}</span>
            </div>
          </div>

          <div class="editor-actions-right">
            <div class="status-pill" :class="status === 'Gepubliceerd' ? 'is-published' : 'is-draft'">
              <span class="status-pill-dot"></span>
              {{ statusLabel }}
            </div>


            <v-btn v-if="status !== 'Gepubliceerd'" color="primary" rounded="lg" class="action-btn action-btn-primary" :loading="saving" :disabled="loading || uploadingAttachments" @click="saveArticle('Gepubliceerd')">
              Publiceren
            </v-btn>

            <v-btn v-else rounded="lg" class="action-btn action-btn-danger" :loading="saving" :disabled="loading || uploadingAttachments" @click="saveArticle('Concept')">
              Depubliceren
            </v-btn>
            
            <v-btn variant="tonal" rounded="lg" class="action-btn action-btn-secondary" :loading="saving" :disabled="loading || uploadingAttachments" @click="saveArticle()">
              Opslaan
            </v-btn>
            <v-snackbar v-model="snackbar" timer="bottom" :timer-color="timerColor" :color="snackbarColor"
            :timeout="3000" location="top start">
            {{ snackbarMessage }}
            </v-snackbar>

          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { VFileUpload } from 'vuetify/labs/VFileUpload'
import TipTap from '@/components/TipTap.vue'
import api from '@/api/api'
import { getArticle, updateArticle, uploadArticleAttachments, deleteAttachment } from '@/services/articleService'

const route = useRoute()

const articleSlug = ref('')
const title = ref('')
const summary = ref('')
const content = ref('<p></p>')
const status = ref('Concept')
const visibility = ref('Openbaar')
const projectName = ref('Knowledgebase Portal')
const updatedLabel = ref('Nog niet opgeslagen')
const loading = ref(false)
const saving = ref(false)
const attachments = ref([])
const uploadingAttachments = ref(false)
const error = ref('')
const saveMessage = ref('Klaar om te schrijven')
const snackbar = ref(false)
const snackbarMessage = ref('')
const snackbarColor = ref('success')
const timerColor = ref('success')
const imagePreviewDialog = ref(false)
const selectedImage = ref(null)
const selectedImageUrl = computed(() => selectedImage.value ? getAttachmentUrl(selectedImage.value) : '')

const model = ref([])
const uploads = ref({})
const selectedImagePreviews = ref([])

const previewRoute = computed(() => (
  articleSlug.value
    ? {
      name: 'article',
      params: { slug: articleSlug.value },
    }
    : null
))

const statusLabel = computed(() => status.value === 'Gepubliceerd' ? 'Gepubliceerd' : 'Concept')
const saveIndicatorLabel = computed(() => {
  if (uploadingAttachments.value) return 'Bijlagen uploaden...'
  if (saving.value) return 'Opslaan...'
  return saveMessage.value
})
const savedImagePreviews = computed(() => (
  attachments.value
    .filter(isImageAttachment)
    .map((attachment) => ({
      key: `attachment-${attachment.id}`,
      name: attachment.name || attachment.original_name || 'Bijlage',
      size: attachment.size,
      url: getAttachmentUrl(attachment),
    }))
    .filter((preview) => preview.url)
))
const imagePreviews = computed(() => [
  ...selectedImagePreviews.value,
  ...savedImagePreviews.value,
])

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
    updateSelectedImagePreviews(files)
    if (!articleSlug.value || uploadingAttachments.value || !Array.isArray(files) || !files.length) return
    uploadSelectedAttachments(files)
  },
  { deep: true },
)

onBeforeUnmount(() => {
  revokeSelectedImagePreviews()
})

function fileUploadKey(file) {
  return `${file.name}-${file.size}-${file.lastModified}`
}

function isImageFile(file) {
  return file instanceof File && file.type.startsWith('image/')
}

function isImageAttachment(attachment) {
  const name = `${attachment.name || attachment.original_name || attachment.path || attachment.url || ''}`.toLowerCase()
  const mimeType = `${attachment.mime || attachment.mime_type || attachment.type || ''}`.toLowerCase()

  return mimeType.startsWith('image/') || /\.(avif|gif|jpe?g|png|svg|webp)$/i.test(name)
}

function getApiOrigin() {
  return new URL(api.defaults.baseURL, window.location.origin).origin
}

function getAttachmentUrl(attachment) {
  const url = attachment.url || attachment.path || ''

  if (!url) return ''

  if (url.startsWith('/storage/')) {
    return `${getApiOrigin()}${url}`
  }

  if (/^https?:\/\//i.test(url)) {
    const parsedUrl = new URL(url)

    if (parsedUrl.pathname.startsWith('/storage/')) {
      return `${getApiOrigin()}${parsedUrl.pathname}${parsedUrl.search}${parsedUrl.hash}`
    }
    return url
  }

  return `${getApiOrigin()}/storage/${url.replace(/^\/+/, '')}`
}

function revokeSelectedImagePreviews() {
  selectedImagePreviews.value.forEach((preview) => {
    URL.revokeObjectURL(preview.url)
  })

  selectedImagePreviews.value = []
}


function openImagePreview(attachment) {
  selectedImage.value = attachment
  imagePreviewDialog.value = true
}

function updateSelectedImagePreviews(files) {
  revokeSelectedImagePreviews()

  if (!Array.isArray(files) || !files.length) return

  selectedImagePreviews.value = files
    .filter(isImageFile)
    .map((file) => ({
      key: `selected-${fileUploadKey(file)}`,
      name: file.name,
      size: file.size,
      url: URL.createObjectURL(file),
    }))
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
  status.value = article.status ?? 'Concept'
  visibility.value = article.visibility ?? 'Openbaar'
  projectName.value = article.project?.name ?? 'Knowledgebase Portal'
  updatedLabel.value = article.updated_at ?? 'Zojuist bijgewerkt'
  attachments.value = article.attachments ?? []
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
    revokeSelectedImagePreviews()
  } catch (err) {
    error.value = err.response?.data?.message ?? 'Kon de bijlagen niet uploaden.'
  } finally {
    clearUploadProgress(selectedFiles)
    uploadingAttachments.value = false
  }
}

    async function removeAttachment(attachmentId) {
      try {
        await deleteAttachment(articleSlug.value, attachmentId)
        attachments.value = attachments.value.filter(a => a.id !== attachmentId)
      } catch (err) {
        error.value = err.response?.data?.message ?? 'Kon de bijlage niet verwijderen.'
      }
    }

    async function removePreview(preview) {
      if (preview.key.startsWith('attachment-')) {
        const attachmentId = Number(
          preview.key.replace('attachment-', '')
        )
        await removeAttachment(attachmentId)
        return
      }

      const fileName = preview.name
      model.value = model.value.filter(
        file => file.name !== fileName
      )
      selectedImagePreviews.value = selectedImagePreviews.value.filter(
        p => p.key !== preview.key
      )
      URL.revokeObjectURL(preview.url)
    }

    function formatSize(bytes) {
      if (!bytes) return ''
      if (bytes < 1024) return `${bytes} B`
      if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`
      return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
    }


async function saveArticle(nextStatus = status.value) {
  if (!articleSlug.value) return

  saving.value = true
  error.value = ''

  try {
    const article = await updateArticle(articleSlug.value, {
      title: title.value.trim() || 'Onbekende titel',
      summary: summary.value.trim(),
      content: content.value,
      status: nextStatus,
      visibility: visibility.value,
    })
    snackbarMessage.value = 'Wijzigingen zijn opgeslagen!'
    snackbarColor.value = '#24a1c7'
    timerColor.value = '#24a1c7'
    snackbar.value = true

    hydrateArticle(article)
    saveMessage.value = nextStatus === 'Gepubliceerd' ? 'Artikel gepubliceerd' : 'Zojuist opgeslagen'
  } catch (err) {
    snackbarMessage.value = error.value
    snackbarColor.value = 'error'
    snackbar.value = true
    error.value = err.response?.data?.message ?? 'Kon het artikel niet opslaan.'
  } finally {
    saving.value = false
  }
}
</script>
