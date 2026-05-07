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
                        <div class="sidebar-meta-row u-flex-between u-gap-12">
                            <span>Tag</span>
                            <strong>{{ articleTags }}</strong>
                        </div>
                    </div>
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
                        <div class="article-meta-line u-flex-center u-wrap u-gap-8">
                            <span class="article-pill u-inline-flex u-items-center">{{ articleTags }}</span>
                            <span class="article-meta-separator">•</span>
                            <span>{{ article.updated_at }}</span>
                        </div>

                        <h1 class="article-title-input">{{article.title}}</h1>

                        <p class="article-subtitle">
                            {{ article.summary }}
                        </p>
                    </div>


                <div class="article-body" v-html="article?.content" />
                        <div class="resource-grid">
                            <div class="resource-card">
                                <div class="resource-icon">
                                    <v-icon size="18">mdi-file-document-outline</v-icon>
                                </div>
                                <div class="resource-body">
                                    <div class="resource-title">
                                        {{ article.original_name }}
                                    </div>
                                    <div class="resource-subtitle">
                                        {{  article.size  }}</div>
                                </div>
                            </div> 

                            <div class="resource-card">
                                <div class="resource-icon">
                                    <v-icon size="18">mdi-link-variant</v-icon>
                                </div>
                                <div class="resource-body">
                                    <div class="resource-title">{{ article.mime }}</div>
                                    <div class="resource-subtitle">{{ article.mime }}</div>
                                </div>
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
const articleTags = computed(() => { const tags = article.value?.tags ?? []
    return Array.isArray(tags) && tags.length ? tags.join(', ') : 'Geen tags'
})

onMounted(loadArticles)

async function loadArticles() {
    loading.value = true
    error.value = false
    try {
        const data = await getArticle(route.params.slug)
        article.value = data
    } catch (err) {
        error.value = 'Artikel kon niet worden geladen.'
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
</script>
