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
                        <!-- Delete? -->
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
                <div class="sidebar-card card card-soft card-rounded-lg">
                    <div class="sidebar-label">In dit artikel</div>

                    <!-- Kopjes bij id geen slug -->
                    <nav class="toc-nav">
                        <a href="#" class="toc-link active">Introductie</a>
                        <a href="#" class="toc-link">Meer Introductie</a>
                        <a href="#" class="toc-link">Nog meer introductie</a>
                        <a href="#" class="toc-link">Geen introductie meer</a>
                        <a href="#" class="toc-link">Bijlagen</a>
                    </nav>
                </div>

                <div class="sidebar-card card card-soft card-rounded-lg">
                    <div class="sidebar-label">Artikelinformatie</div>

                    <div class="sidebar-meta-list u-flex-col u-gap-12">
                        <div class="sidebar-meta-row u-flex-between u-gap-12">
                            <span>Project</span>
                            <!-- {{ Project }} bij titel-->
                    <strong>{{ project?.name }}</strong>
                        </div>

                        <div class="sidebar-meta-row u-flex-between u-gap-12">
                            <span>Status</span>
                            <!-- {{ status }}-->
                            <strong>{{ article?.status }}</strong>
                        </div>

                        <div class="sidebar-meta-row u-flex-between u-gap-12">
                            <span>Bijgewerkt</span>
                            <!-- {{ timestamp }} -->
                            <strong>{{article?.updated_at}}</strong>
                        </div>
                    </div>
                </div>

                <div class="sidebar-card card card-soft card-rounded-lg">
                <div class="sidebar-label">Tags</div>
                <div v-if="articleTags.length" class="tag-grid flex-wrap">
                                    <span
                                        v-for="tag in articleTags"
                                        :key="tag"
                                        class="article-pill u-inline-flex u-items-center">
                                        {{ tag }}
                                    </span>
                                </div>
                            
                                <span v-else class="article-pill u-inline-flex u-items-center">Geen tags</span>
                
                </div>

                <div class="sidebar-card card card-soft card-rounded-lg feedback-card">
                    <div class="sidebar-label">Was Dit Artikel Nuttig?</div>

                    <div class="useful-button-box">
                        <button class="useful-button thumbs-up" type="button">
                        <!-- Feedback - helpful boolean-->
                            <v-icon>mdi-thumb-up</v-icon>
                        </button>
                        <button class="useful-button thumbs-down" type="button">
                            <v-icon>mdi-thumb-down</v-icon>
                        </button>
                    </div>
                </div>
                <form class="sidebar-card card card-soft card-rounded-lg feedback-card" @submit.prevent="submitFeedback">
                    <div class="sidebar-label">Extra feedback</div>

                    <!-- {{ comment }} -->
                    <textarea ref="feedbackTextarea" v-model="feedbackTitle" class="feedback-input feedback-textarea"
                        placeholder="Laat je feedback achter..." rows="1" @input="autoResizeTextarea" maxlength="500"></textarea>

                        <!-- {{ OnSubmit }}-->
                    <button class="feedback-submit" type="submit">
                        Versturen
                    </button>
                </form>
            </aside>

            <main class="article-content">
                <div class="article-cover"></div>

                <article class="article-card card card-elevated card-rounded-2xl">
                    <div class="article-head card-head">
                        <div class="article-meta-line u-flex-center u-wrap u-gap-8" />

                        <h1 class="article-title-input">{{article.title}}</h1>

                        <p class="article-subtitle">
                            {{ article.summary }}
                        </p>
                    </div>


                <div class="article-body" v-html="article.content" />
                        <div v-if="articleAttachments.length" class="resource-grid">
                            <div v-for="attachment in articleAttachments" :key="attachment.id" class="resource-card">
                                <div class="resource-icon">
                                    <v-icon size="18">mdi-file-document-outline</v-icon>
                                </div>
                                <button>
                                    <div class="resource-title">
                                        {{ attachment.original_name }}
                                    </div>
                                    <div class="resource-subtitle">
                                        {{ formatAttachmentSize(attachment.size) }}</div>
                                    </button>
                            </div>
                     </div>
                </article>
            </main>
        </div>
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
import { getArticle } from '@/services/articleService'
const feedbackTitle = ref('')
const feedbackTextarea = ref(null)

const route = useRoute()
const article = ref(null)
const error = ref(false)
const loading = ref(false)
const project = computed(() => article.value?.project ?? null)
const articleTags = computed(() => Array.isArray(article.value?.tags) ? article.value.tags : [])
const articleAttachments = computed(() => Array.isArray(article.value?.attachments) ? article.value.attachments : [])

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

function autoResizeTextarea() {
    const el = feedbackTextarea.value
    if (!el) return

    el.style.height = 'auto'
    el.style.height = `${el.scrollHeight}px`
}
 // remove
// onMounted(() => {
//     nextTick(() => {
//         autoResizeTextarea()
//     })
// })

const backToEditorRoute = computed(() => {
    const articleId = route.params.id

    if (articleId && articleId !== 'preview') {
        return `/admin/articles/${articleId}/edit`
    }

    return '/admin/articles/new'
})

function submitFeedback() {
    console.log('Feedback verzonden:', feedbackTitle.value)

    feedbackTitle.value = ''
}

function formatAttachmentSize(size) {
    const value = Number(size)

    if (!Number.isFinite(value) || value <= 0) return 'Onbekende grootte'
    if (value < 1024) return `${value} B`
    if (value < 1024 * 1024) return `${(value / 1024).toFixed(1)} KB`

    return `${(value / (1024 * 1024)).toFixed(1)} MB`
}
</script>
