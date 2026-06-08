    <template>
        <div v-if="loading" class="empty-state">
            <v-icon size="24">mdi-loading mdi-spin</v-icon>
            <p>Artikel is aan het laden...</p>
        </div>

        <div v-else-if="error" class="empty-state">
            <div style="margin-top: 300px;">
                <v-icon size="40">mdi-alert-circle-outline</v-icon>
                <h2>{{ error }}</h2>
            </div>
        </div>

        <div v-else-if="article" class="article-page">
            <div class="article-topbar">
                <div class="article-topbar-inner">
                    <div class="article-topbar-left">
                        <div class="article-badge">
                            <v-icon size="16">mdi-eye-outline</v-icon>
                            <span>Preview</span>
                        </div>
                    </div>

                    <div class="article-topbar-right">
                        <v-btn variant="text" rounded="lg" prepend-icon="mdi-arrow-left" :to="backToEditorRoute">
                            Terug naar editor
                        </v-btn>
                    </div>
                </div>
            </div>

            <div class="entity-shell page-shell article-grid">
                <aside class="article-sidebar">
                    <div class="infoside-card card card-soft card-rounded-lg">
                        <div class="sidebar-label">In dit artikel</div>

                        <nav class="toc-nav">
                            <a href="#summary" class="toc-link active">Introductie</a>
                            <a href="#content" class="toc-link">Content</a>
                            <a href="#attachments" class="toc-link">Bijlagen</a>
                        </nav>
                    </div>

                    <div class="sidebar-card card card-soft card-rounded-lg">
                        <div class="sidebar-label">Artikelinformatie</div>

                        <div class="sidebar-meta-list u-flex-col u-gap-12">
                            <div class="sidebar-meta-row u-flex-between u-gap-12">
                                <span>Project</span>
                                <strong>{{ project.name }}</strong>
                            </div>

                            <div class="sidebar-meta-row u-flex-between u-gap-12">
                                <span>Status</span>
                                <strong>{{ article.status }}</strong>
                            </div>

                            <div class="sidebar-meta-row u-flex-between u-gap-12">
                                <span>Bijgewerkt</span>
                                <strong>{{ article.updated_at }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar-card card card-soft card-rounded-lg">
                        <div class="sidebar-label">Tags</div>
                        <div v-if="articleTags.length" class="tag-grid flex-wrap">
                            <span v-for="tag in articleTags" :key="tag"
                                class="article-pill u-inline-flex u-items-center">{{ tag }}</span>
                        </div>
                        <span v-else class="article-pill u-inline-flex u-items-center">Geen tags</span>
                    </div>

                    <div v-if="feedbackSent" class="sidebar-card card card-soft card-rounded-lg feedback-card">
                        <div class="sidebar-label">Bedankt voor je feedback!</div>
                        <v-icon size="32" color="light-blue">mdi-check-circle-outline</v-icon>
                    </div>
                    <form v-else class="sidebar-card card card-soft card-rounded-lg feedback-card"
                        @submit.prevent="submitFeedback">
                        <div class="sidebar-label">Was dit artikel nuttig?</div>
                
                <v-dialog v-model="feedbackDialog" max-width="596">
                <v-card class="dialog-card card card-rounded-xl" rounded="xl">
                <div class="feedback-modal">
                    <div class="feedback-modal-head">
                  <span class="feedback-modal-icon" aria-hidden="true">
                <v-icon size="30">mdi-message-outline</v-icon>
                  </span>
                    </div>
                    <div class="feedback-modal-title">Geef ons een beoordeling!</div>

                        <div class="useful-button-box">
                            <button class="useful-button thumbs-up" type="button" :class="{ active: helpful === true }"
                            @click="setHelpful(true)">
                            <v-icon>mdi-thumb-up</v-icon></button>
                            <button class="useful-button thumbs-down" type="button"
                            :class="{ active: helpful === false }" @click="setHelpful(false)">
                            <v-icon>mdi-thumb-down</v-icon>
                            </button> 
                         </div> 
                        <v-alert v-if="feedbackError" type="error" variant="tonal" density="comfortable" closable
                        class="mt-2" @click:close="feedbackError = ''"> {{ feedbackError }} </v-alert>

                    <div class="feedback-divider" />
                    <div class="sidebar-label" style="margin-top: 12px;">Extra feedback</div> 
                         
                    <textarea ref="feedbackTextarea" v-model="feedbackTitle"
                    
                    class="feedback-input feedback-textarea" placeholder="Laat je feedback achter..." rows="1"
                    @input="autoResizeTextarea" maxlength="500" /> 

                <div class="feedback-modal-footer">
                    <button class="feedback-cancel" type="button"
                    @click="feedbackDialog = false">Annuleren</button>
                    <button class="feedback-submit" type="button"
                    @click="submitFeedback"
                    :disabled="helpful === null && !feedbackTitle.trim()">Versturen</button> 
                </div>
                </div>

                                        
                </v-card>
                </v-dialog>
                    <button class="feedback-submit" type="button" @click="feedbackDialog = true" >Geef feedback!</button>
                    </form>
                </aside>
                <main class="article-content">
                    <div class="article-cover"></div>
                    <article class="article-card card card-elevated card-rounded-2xl">
                        <div class="article-head card-head">
                            <div class="article-meta-line u-flex-center u-wrap u-gap-8" />
                            <h1 class="article-title-input">{{ article.title }}</h1>
                            <p class="article-subtitle" id="summary" >
                                {{ article.summary }}
                            </p>
                        </div>


                        <div class="article-body" id="content" v-html="article.content" 
                        @mouseover="showLinkPreview" @mousemove="moveLinkPreview"
                        @mouseout="hideLinkPreview" @focusin="showLinkPreview"
                        @focusout="hideLinkPreview" />

                        <div v-if="linkPreview.visible" class="link-preview-tooltip" :style="linkPreviewStyle">
                            <img v-if="linkPreview.thumbnail" :src="linkPreview.thumbnail" :alt="linkPreview.title" class="link-preview-image" />
                            <div class="link-preview-content">
                                <div class="link-preview-kicker">{{ linkPreview.type }}</div>
                                <div class="link-preview-title">{{ linkPreview.title }}</div>
                                <div class="link-preview-url">{{ linkPreview.displayUrl }}</div>
                            </div>
                        </div>

                        <div v-if="articleAttachments.length" id="attachments" class="resource-grid">
                            <button
                                v-for="attachment in imageAttachments"
                                :key="attachment.id"
                                type="button"
                                class="resource-card resource-card-image"
                                @click="openImagePreview(attachment)">
                                <img :src="getAttachmentUrl(attachment)" :alt="getAttachmentName(attachment)" class="resource-image-preview">

                                <span class="resource-copy">
                                    <span class="resource-title">{{ getAttachmentName(attachment) }}</span>
                                    <span class="resource-subtitle">{{ formatAttachmentSize(attachment.size) }}</span>
                                </span>
                            </button>

                            <a v-for="attachment in downloadableAttachments" :key="attachment.id" class="resource-card resource-card-download"
                            :href="getAttachmentUrl(attachment)" :download="getAttachmentName(attachment)">
                                <span class="resource-icon">
                                    <v-icon size="18">{{ isPdfAttachment(attachment) ? 'mdi-file-pdf-box' : 'mdi-download-outline' }}</v-icon>
                                </span>
                                <span class="resource-copy">
                                    <span class="resource-title">{{ getAttachmentName(attachment) }}</span>
                                    <span class="resource-subtitle">{{ isPdfAttachment(attachment) ? 'PDF downloaden' : 'Downloaden' }} · {{ formatAttachmentSize(attachment.size) }}</span>
                                </span>
                            </a>
                        </div>
                    </article>
                </main>
            </div>
            
            <v-dialog v-model="imagePreviewDialog" max-width="1140" class="attachment-preview-dialog">
                <!-- <v-card class="attachment-preview-card"> -->
                    <!-- <div class="attachment-preview-head"> -->
                        <!-- <div class="attachment-preview-title">{{  selectedImageName }} </div> -->
                        <!-- <v-btn icon variant="text" aria-label="Sluiten" @click="imagePreviewDialog = false">
                            <v-icon>mdi-close</v-icon> -->
                        <!-- </v-btn>     -->
                    <!-- </div> -->
                    <img v-if="selectedImageUrl" :src="selectedImageUrl" :alt="selectedImageName" class="attachment-preview-image">
                <!-- </v-card> -->
            </v-dialog>
        </div>

        <div v-else class="empty-state">
            <div style="margin-top: 300px;">
                <v-icon size="40">mdi-file-document-outline</v-icon>
                <h2>Artikel niet gevonden.</h2>
            </div>
        </div>
    </template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { getArticle, postFeedback } from '@/services/articleService'
import api from '@/api/api'
const feedbackTitle = ref('')
const feedbackTextarea = ref(null)
const helpful = ref(null)
const feedbackSent = ref(false)
const feedbackError = ref(false)
const feedbackDialog = ref(false)
const imagePreviewDialog = ref(false)
const selectedImage = ref(null)
const linkPreview = ref({visible: false, x: 0, y: 0,
    title: '',
    displayUrl: '',
    type: '',
    thumbnail: '',
})

const route = useRoute()
const article = ref(null)
const error = ref(false)
const loading = ref(false)
const project = computed(() => article.value?.project ?? null)
const articleTags = computed(() => Array.isArray(article.value?.tags) ? article.value.tags : [])
const articleAttachments = computed(() => Array.isArray(article.value?.attachments) ? article.value.attachments : [])
const imageAttachments = computed(() => articleAttachments.value.filter(isImageAttachment))
const downloadableAttachments = computed(() => articleAttachments.value.filter((attachment) => !isImageAttachment(attachment)))
const linkPreviewStyle = computed(() => ({ left: `${linkPreview.value.x}px`, top: `${linkPreview.value.y}px`, }))
const selectedImageUrl = computed(() => selectedImage.value ? getAttachmentUrl(selectedImage.value) : '')
const selectedImageName = computed(() => selectedImage.value ? getAttachmentName(selectedImage.value) : '')


onMounted(loadArticles)

async function loadArticles() {
    loading.value = true
    error.value = false
    try {
        const data = await getArticle(route.params.slug)
        article.value = data
    } catch (err) {
        error.value = 'Artikel is niet gevonden...'
    } finally {
        loading.value = false
    }
}

function setHelpful(value) {
    helpful.value = helpful.value === value ? null : value
}

async function submitFeedback() {

    if (helpful.value === null && !feedbackTitle.value.trim()) return
    feedbackError.value = false
    try {
        await postFeedback(route.params.slug, {
            helpful: helpful.value,
            feedback: feedbackTitle.value.trim()
        })
        feedbackDialog.value = false
        feedbackSent.value = true
        feedbackTitle.value = ''
        helpful.value = null
    } catch (err) {
        feedbackError.value = 'Een beoordeling is nodig om te versturen!'
    }
}

function autoResizeTextarea() {
    const el = feedbackTextarea.value
    if (!el) return

    el.style.height = 'auto'
    el.style.height = `${el.scrollHeight}px`
}


function findLinkTarget(target) {
    if (!(target instanceof Element)) return null
    return target.closest('.article-body a[href]')
}

function showLinkPreview(event) {
    const link = findLinkTarget(event.target)
    if (!link) return

    const preview = createLinkPreview(link.href, link.textContent)

    linkPreview.value = {
        ...linkPreview.value,
        ...preview,
        visible: true,
    }

    positionLinkPreview(event, link)
}

function moveLinkPreview(event) {
    if(!linkPreview.value.visible) return
    const link = findLinkTarget(event.target)
    if (!link) return

    positionLinkPreview(event, link)
}


function hideLinkPreview(event) {
    const link = findLinkTarget(event.target)
    if (link && event.relatedTarget instanceof Node && link.contains(event.relatedTarget)) return

    linkPreview.value.visible = false
}

function positionLinkPreview(event, link) {
    const viewportPadding = 16
    const tooltipWidth = 320
    const tooltipHeight = linkPreview.value.thumbnail ? 276 : 132
    const sourceRect = link.getBoundingClientRect()
    const pointerX = 'clientX' in event ? event.clientX : sourceRect.left
    const pointerY = 'clientY' in event ? event.clientY : sourceRect.bottom

    const preferredX = pointerX + 14
    const preferredY = pointerY + 18
    const fallbackY = sourceRect.top - tooltipHeight - 12

    linkPreview.value.x = Math.min(Math.max(preferredX, viewportPadding),
        window.innerWidth - tooltipWidth - viewportPadding,
    )
    linkPreview.value.y = preferredY + tooltipHeight > window.innerHeight
        ? Math.max(fallbackY, viewportPadding) : preferredY
}

function createLinkPreview(href, label = '') {
    const url = new URL(href, window.location.origin)
    const youtubeId = getYouTubeVideoId(url)
    const displayUrl = url.hostname.replace(/^www\./, '') + url.pathname

    if (youtubeId) {
        return {
            title: label?.trim() || 'Youtube Video',
            displayUrl,
            thumbnail: `https://img.youtube.com/vi/${youtubeId}/mqdefault.jpg`,
        }
    }

    return {
        title: label?.trim() || url.hostname.replace(/^www\./, ''),
        displayUrl,
        thumbnail: '',
    }
}

function getYouTubeVideoId(url) {
    const hostname = url.hostname.replace(/^www\./, '')
    if (hostname === 'youtu.be') {
        return url.pathname.split('/').filter(Boolean)[0] || ''
    }
    if (!['youtube.com', 'm.youtube.com', 'music.youtube.com', 'youtube-nocookie.com'].includes(hostname)) {
        return ''
    }

    if (url.pathname === '/watch') return url.searchParams.get('v') || ''

    const parts = url.pathname.split('/').filter(Boolean)
    if (['embed', 'shorts', 'live'].includes(parts[0])) return parts[1] || ''
    return ''
}

function getAttachmentName(attachment) {
    return attachment.name || attachment.original_name || 'Bijlage'
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

function getApiOrigin() {
    return new URL(api.defaults.baseURL, window.location.origin).origin
}

function isImageAttachment(attachment) {
    const name = `${getAttachmentName(attachment)} ${attachment.path || attachment.url || ''}`.toLocaleLowerCase()
    const mimeType = `${attachment.mime || attachment.mime_type || attachment.type || ''}`.toLocaleLowerCase()
    return mimeType.startsWith('image/') || /\.(avif|gif|jpe?g|webp|png|svg)$/i.test(name)
}

function isPdfAttachment(attachment) {
    const name = `${getAttachmentName(attachment)} ${attachment.path || attachment.url || ''}`.toLocaleLowerCase()
    const mimeType = `${attachment.mime || attachment.mime_type || attachment.type || ''}`.toLowerCase()
    return mimeType === 'application/pdf' || /\.pdf$/i.test(name)
}

function openImagePreview(attachment) {
     selectedImage.value = attachment
     imagePreviewDialog.value = true
}

const backToEditorRoute = computed(() => {
    if (!route.params.slug) return { name: 'admin-overview'}
    return { name: 'article-new', query: { slug: route.params.slug }}
})

function formatAttachmentSize(size) {
    const value = Number(size)

    if (!Number.isFinite(value) || value <= 0) return 'Onbekende grootte'
    if (value < 1024) return `${value} B`
    if (value < 1024 * 1024) return `${(value / 1024).toFixed(1)} KB`

    return `${(value / (1024 * 1024)).toFixed(1)} MB`
}
</script>

<style scoped>
#summary,
#content,
#attachments {
    scroll-margin-top: 200px;
}
</style>