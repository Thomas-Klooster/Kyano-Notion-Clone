<template>
  <div class="article-page">
    <div class="editor-article-topbar">
      <v-btn variant="text" rounded="lg" prepend-icon="mdi-open-in-new" :disabled="loading || !previewRoute"
        :to="previewRoute || undefined">
        Preview
      </v-btn>
    </div>

    <div class="entity-shell page-shell">
      <main class="article-content">
        <div class="editor-cover" :class="{ 'editor-cover--image': isCoverImage }" :style="editorCoverStyle">
          <div class="cover-actions">
            <v-menu v-model="colorMenu" :close-on-content-click="false" location="bottom start">
              <template #activator="{ props }">
                <v-btn v-bind="props" size="small" prepend-icon="mdi-palette-outline">
                  Kleur
                </v-btn>
              </template>
              <v-color-picker v-model="selectedColour" :swatches="swatches" show-swatches mode="hex"
                @update:model-value="selectColourCover"></v-color-picker>
            </v-menu>

            <v-menu location="bottom start" :disabled="!coverAttachmentOptions.length">
              <template #activator="{ props }">
                <v-btn v-bind="props" size="small" prepend-icon="mdi-image-outline"
                  :disabled="!coverAttachmentOptions.length">
                  Bijlage
                </v-btn>
              </template>

              <v-list density="compact" class="cover-attachment-menu">
                <v-list-item v-for="attachment in coverAttachmentOptions" :key="attachment.id"
                  @click="selectAttachmentCover(attachment)">
                  <template #prepend>
                    <v-avatar rounded="sm" size="38">
                      <v-img :src="getAttachmentUrl(attachment)" :alt="getAttachmentName(attachment)" cover />
                    </v-avatar>
                  </template>
                  <v-list-item-title>{{ getAttachmentName(attachment) }}</v-list-item-title>
                  <template #append>
                    <v-icon v-if="isAttachmentCover(attachment.id)" color="primary" size="18">mdi-check</v-icon>
                  </template>
                </v-list-item>
              </v-list>
            </v-menu>

            <v-btn size="small" prepend-icon="mdi-upload-outline">
              Uploaden
            </v-btn>


            <v-btn v-if="isCoverImage" icon size="small" variant="text" aria-label="Gebruik kleur als cover"
              @click="selectColourCover(selectedColour)">
              <v-icon style="color: darkred;" size="16">mdi-close</v-icon>
            </v-btn>
          </div>
        </div>

        <section class="article-card card card-elevated card-rounded-2xl">
          <div class="article-head card-head">

            <div class="article-pill article-meta-line u-flex-center u-wrap u-gap-8">
              <span>{{ statusLabel }}</span>
            </div>

            <input v-model="title" class="article-title-input" placeholder="Paginatitel" :disabled="loading" />

            <textarea v-model="summary" class="article-summary-input" placeholder="Voeg een korte samenvatting toe..."
              rows="2" :disabled="loading" />
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
                    <v-btn v-if="preview.attachmentId" icon size="18" variant="text"
                      :color="isAttachmentCover(preview.attachmentId) ? 'primary' : undefined" :aria-label="isAttachmentCover(preview.attachmentId) ? 'Kleur Gelecteerd' :
                        'Gebruik als cover'" @click.stop="selectAttachmentCoverById(preview.attachmentId)">
                      <v-icon size="16">{{ isAttachmentCover(preview.attachmentId) ?
                        'mdi-image-check-outline' : 'mdi-image-plus-outline' }}</v-icon>
                    </v-btn>
                    <v-btn icon size="18" variant="text" color="error" class="delete-preview-button"
                      @click.stop="removePreview(preview)">
                      <v-icon size="16">mdi-window-close</v-icon>
                    </v-btn>
                  </div>
                  <img :src="preview.url" :alt="preview.name" class="image-preview-img" />
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
                <div v-for="attachment in attachments" :key="attachment.id"
                  :type="isImageAttachment(attachment) ? 'button' : undefined"
                  :class="{ clickable: isImageAttachment(attachment) }"
                  @click="isImageAttachment(attachment) ? openImagePreview(attachment) : null" class="attachment-row">
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
                    <v-btn icon size="18" variant="text" color="error" @click.stop="removeAttachment(attachment.id)">
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

        <v-dialog v-model="imagePreviewDialog" max-width="1140" class="attachment-preview-dialog">
          <img v-if="selectedImageUrl" :src="selectedImageUrl" :alt="selectedImageName"
            class="attachment-preview-image">
        </v-dialog>


        <div class="editor-actions">
          <div class="editor-actions-left">
            <div class="save-indicator">
              <span class="save-indicator-dot"></span>
              <span>{{ saveIndicatorLabel }}</span>
            </div>
          </div>

          <div class="editor-actions-right">
            <div class="status-pill" style="width: 200px;"
              :class="status === 'Gepubliceerd' ? 'is-published' : 'is-draft'">
              <span class="status-pill-dot"></span>
              {{ statusLabel }}
            </div>


            <v-btn v-if="status !== 'Gepubliceerd'" color="primary" rounded="lg" class="action-btn action-btn-primary"
              style="width: 200px;" :loading="saving" :disabled="loading || uploadingAttachments"
              @click="saveArticle('Gepubliceerd')">
              Publiceren
            </v-btn>

            <v-btn v-else rounded="lg" class="action-btn action-btn-danger" style="width: 200px;" :loading="saving"
              :disabled="loading || uploadingAttachments" @click="saveArticle('Concept')">
              Depubliceren
            </v-btn>

            <v-btn variant="tonal" rounded="lg" class="save-article-btn" :loading="saving"
              :disabled="loading || uploadingAttachments" @click="saveArticle()">
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
import { VColorPicker } from 'vuetify/components'
import TipTap from '@/components/TipTap.vue'
import api from '@/api/api'
import { getArticle, updateArticle, uploadArticleAttachments, deleteAttachment } from '@/services/articleService'

const DEFAULT_COVER_COLOUR = '#24a1c7'
const COVER_ATTACHMENT_PREFIX = 'attachment:'
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
const selectedImageName = computed(() => selectedImage.value ? getAttachmentName(selectedImage.value) : '')
const selectedColour = ref(DEFAULT_COVER_COLOUR)
const articleCover = ref(DEFAULT_COVER_COLOUR)
const colorMenu = ref(false)
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

const swatches = [
  ['#24a1c7', '#1b7e9a', '#0fc0fc'],
  ['#5d8aa8', '#9966cc', '#ffbf00'],
  ['#ff9966', '#8e2311', '#f4c2c2'],
  ['#ff0038', '#ffef00', '#b2ffff'],
]

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
      attachmentId: attachment.id,
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

const coverAttachmentOptions = computed(() => attachments.value.filter(isImageAttachment))
const activeCoverAttachment = computed(() => {
  const attachmentId = getCoverAttachmentId(articleCover.value)

  if (!attachmentId) return null


  return coverAttachmentOptions.value.find((attachment) => attachment.id === attachmentId) ?? null
})
const isCoverImage = computed(() => Boolean(activeCoverAttachment.value))
const editorCoverStyle = computed(() => {
  const coverAttachment = activeCoverAttachment.value

  if (coverAttachment) {
    const coverUrl = getAttachmentUrl(coverAttachment)

    if (coverUrl) {
      return {
        backgroundColor: selectedColour.value,
        backgroundImage: `url("${coverUrl}")`,
      }
    }
  }

  return {
    backgroundColor: selectedColour.value,
    backgroundImage: 'none',
  }
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


function isHexColour(value) {
  return typeof value === 'string' && /^#[0-9A-F]{6}$/i.test(value)
}

function normalizeColourInput(value) {
  if (isHexColour(value)) return value

  if (value && typeof value === 'object' && isHexColour(value.hex)) {
    return value.hex
  }

  return ''
}

function attachmentCoverValue(attachmentId) {
  return `${COVER_ATTACHMENT_PREFIX}${attachmentId}`
}


function getCoverAttachmentId(value) {
  const match = String(value ?? '').match(/^attachment:(\d+)$/)
  return match ? Number(match[1]) : null
}

function normalizeCoverValue(value) {
  const colour = normalizeColourInput(value)
  if (colour) return colour

  const attachmentId = getCoverAttachmentId(value)
  if (attachmentId) return attachmentCoverValue(attachmentId)

  return DEFAULT_COVER_COLOUR
}

function isAttachmentCover(attachmentId) {
  return articleCover.value === attachmentCoverValue(attachmentId)
}

function selectColourCover(colour) {
  const nextColour = normalizeColourInput(colour) || DEFAULT_COVER_COLOUR

  selectedColour.value = nextColour
  articleCover.value = nextColour
}

function selectAttachmentCover(attachment) {
  if (!attachment || !isImageAttachment(attachment)) return

  articleCover.value = attachmentCoverValue(attachment.id)
}

function selectAttachmentCoverById(attachmentId) {
  const attachment = attachments.value.find((item) => item.id === attachmentId)
  selectAttachmentCover(attachment)
}


function getArticleCoverForSave() {
  if (activeCoverAttachment.value) {
    return attachmentCoverValue(activeCoverAttachment.value.id)
  }

  return normalizeColourInput(selectedColour.value) || DEFAULT_COVER_COLOUR
}

function isImageFile(file) {
  return file instanceof File && file.type.startsWith('image/')
}

function getAttachmentName(attachment) {
  return attachment.name || attachment.original_name || 'Bijlage'
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
  const nextCover = normalizeCoverValue(article.article_cover)
  articleSlug.value = article.slug ?? articleSlug.value
  title.value = article.title ?? ''
  summary.value = article.summary ?? ''
  content.value = typeof article.content === 'string' && article.content.length ? article.content : '<p></p>'
  status.value = article.status ?? 'Concept'
  visibility.value = article.visibility ?? 'Openbaar'
  projectName.value = article.project?.name ?? 'Knowledgebase Portal'
  updatedLabel.value = article.updated_at ?? 'Zojuist bijgewerkt'
  attachments.value = article.attachments ?? []
  articleCover.value = nextCover
  selectedColour.value = normalizeColourInput(nextCover) || DEFAULT_COVER_COLOUR
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
    attachments.value = article.attachments ?? []
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
  const wasCoverAttachment = isAttachmentCover(attachmentId)

  try {
    await deleteAttachment(articleSlug.value, attachmentId)
    attachments.value = attachments.value.filter(a => a.id !== attachmentId)
    if (wasCoverAttachment) {
      articleCover.value = normalizeColourInput(selectedColour.value) || DEFAULT_COVER_COLOUR
    }
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
      article_cover: getArticleCoverForSave(),
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
