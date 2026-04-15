<template>
    <div class="article-preview-page">
        <div class="preview-topbar">
            <div class="preview-topbar-inner">
                <div class="preview-topbar-left">
                    <div class="preview-badge">
                        <v-icon size="16">mdi-eye-outline</v-icon>
                        <span>Preview</span>
                    </div>
                </div>

                <div class="preview-topbar-right">
                    <v-btn variant="text" rounded="lg" prepend-icon="mdi-arrow-left" :to="backToEditorRoute">
                        Terug naar editor
                    </v-btn>
                </div>
            </div>
        </div>

        <div class="preview-shell">
            <aside class="preview-sidebar">
                <div class="sidebar-card">
                    <div class="sidebar-label">In dit artikel</div>

                    <nav class="toc-nav">
                        <a href="#" class="toc-link active">Introductie</a>
                        <a href="#" class="toc-link">Meer Introductie</a>
                        <a href="#" class="toc-link">Nog meer introductie</a>
                        <a href="#" class="toc-link">Geen introductie meer</a>
                        <a href="#" class="toc-link">Bijlagen</a>
                    </nav>
                </div>

                <div class="sidebar-card">
                    <div class="sidebar-label">Artikelinformatie</div>

                    <div class="sidebar-meta-list">
                        <div class="sidebar-meta-row">
                            <span>Project</span>
                            <strong>Knowledgebase Portal</strong>
                        </div>

                        <div class="sidebar-meta-row">
                            <span>Status</span>
                            <strong>Gepubliceerd</strong>
                        </div>

                        <div class="sidebar-meta-row">
                            <span>Bijgewerkt</span>
                            <strong>Vandaag</strong>
                        </div>
                    </div>
                </div>

                <div class="sidebar-card feedback-card">
                    <div class="sidebar-label">Was Dit Artikel Nuttig?</div>

                    <div class="useful-button-box">
                        <button class="useful-button thumbs-up" type="button">
                            <v-icon>mdi-thumb-up</v-icon>
                        </button>
                        <button class="useful-button thumbs-down" type="button">
                            <v-icon>mdi-thumb-down</v-icon>
                        </button>
                    </div>
                </div>
                <form class="sidebar-card feedback-card" @submit.prevent="submitFeedback">
                    <div class="sidebar-label">Extra feedback</div>

                    <textarea ref="feedbackTextarea" v-model="feedbackTitle" class="feedback-input feedback-textarea"
                        placeholder="Laat je feedback achter..." rows="1" @input="autoResizeTextarea" maxlength="500"></textarea>

                    <button class="feedback-submit" type="submit">
                        Versturen
                    </button>
                </form>
            </aside>

            <main class="preview-content">
                <div class="preview-cover"></div>

                <article class="article-card">
                    <div class="article-head">
                        <div class="article-meta-line">
                            <span class="article-pill">Handleiding</span>
                            <span class="article-meta-separator">•</span>
                            <span>Vandaag bijgewerkt</span>
                        </div>

                        <h1 class="article-title">PAGINATITEL</h1>

                        <p class="article-subtitle">
                            Dit is de preview van een artikel. deze pagina is de pagina die klanten in het portaal zien.
                        </p>

                        <div class="article-author-row">
                            <div class="author-avatar">K</div>

                            <div>
                                <div class="author-name">Kyano Team</div>
                                <div class="author-role">Kennisbankartikel</div>
                            </div>
                        </div>
                    </div>

                    <div class="article-body">
                        <p>
                            Cras quis commodo turpis, eu rhoncus ligula. Phasellus viverra mi nec elit tempus vulputate.
                            Cras lacinia tincidunt eros sit amet varius. Nam quis purus quis nulla mollis mollis nec sed
                            nulla. Phasellus in facilisis diam. Aenean sit amet pharetra quam.
                        </p>

                        <h2>Aenean sapien nisi</h2>
                        <p>
                            Curabitur egestas
                            vulputate est at egestas. Aenean sapien nisi, pulvinar ut nisi in, commodo tristique erat.
                            Donec laoreet, mi sed mattis maximus, neque ante malesuada erat, a vulputate lacus dui ac
                            ante.
                        </p>

                        <blockquote>
                            Nunc eros leo, efficitur eu orci quis, feugiat tempor eros. Fusce feugiat lacinia cursus.
                            Morbi dolor enim, varius ut mauris ut, mattis aliquet nibh. Vestibulum ultricies tincidunt
                            lacinia. Sed at nisl libero.
                        </blockquote>

                        <h2>Cras quis commodo</h2>
                        <p>
                            Cras quis commodo turpis, eu rhoncus ligula. Phasellus viverra mi nec elit tempus vulputate.
                            Cras lacinia tincidunt eros sit amet varius. Nam quis purus quis nulla mollis mollis nec sed
                            nulla. Phasellus in facilisis diam. Aenean sit amet pharetra quam.
                        </p>

                        <ul>
                            <li>Cras quis commodo turpis, eu rhoncus ligula.</li>
                            <li>Phasellus viverra mi nec elit tempus vulputate.</li>
                            <li>Nam quis purus quis nulla mollis mollis nec sed nulla.</li>
                            <li>Phasellus in facilisis diam. Aenean sit amet pharetra quam.</li>
                        </ul>

                        <h2>tempus vulputate</h2>
                        <p>
                            Sed sollicitudin egestas neque, nec laoreet odio maximus vitae. Vestibulum accumsan, libero
                            ornare faucibus varius, urna tortor cursus turpis, non euismod augue magna non nunc. Etiam
                            lobortis augue lectus, quis laoreet lorem imperdiet et. Ut auctor magna sit amet dolor
                            luctus efficitur.
                        </p>

                        <div class="callout-card">
                            <div class="callout-icon">!</div>
                            <div>
                                <div class="callout-title">Nam quis purus</div>
                                <p class="callout-text">
                                    Vestibulum nec augue bibendum risus malesuada pretium. Vivamus vel nunc sed ipsum
                                    varius sollicitudin. Donec rutrum interdum orci ut rutrum.
                                </p>
                            </div>
                        </div>

                        <h2>commodo turpis</h2>
                        <p>
                            Cras quis commodo turpis, eu rhoncus ligula. Phasellus viverra mi nec elit tempus vulputate.
                            Cras lacinia tincidunt eros sit amet varius. Nam quis purus quis nulla mollis mollis nec sed
                            nulla. Phasellus in facilisis diam. Aenean sit amet pharetra quam. Curabitur egestas
                            vulputate est at egestas. Aenean sapien nisi, pulvinar ut nisi in, commodo tristique erat.
                            Donec laoreet, mi sed mattis maximus, neque ante malesuada erat, a vulputate lacus dui ac
                            ante.
                        </p>

                        <div class="resource-grid">
                            <div class="resource-card">
                                <div class="resource-icon">
                                    <v-icon size="18">mdi-file-document-outline</v-icon>
                                </div>
                                <div class="resource-body">
                                    <div class="resource-title">Portal.pdf</div>
                                    <div class="resource-subtitle">PDF-document · 6.9 MB</div>
                                </div>
                            </div>

                            <div class="resource-card">
                                <div class="resource-icon">
                                    <v-icon size="18">mdi-link-variant</v-icon>
                                </div>
                                <div class="resource-body">
                                    <div class="resource-title">youtube.com</div>
                                    <div class="resource-subtitle">bookmark</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </main>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, nextTick, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const feedbackTitle = ref('')
const feedbackTextarea = ref(null)

function autoResizeTextarea() {
    const el = feedbackTextarea.value
    if (!el) return

    el.style.height = 'auto'
    el.style.height = `${el.scrollHeight}px`
}

onMounted(() => {
    nextTick(() => {
        autoResizeTextarea()
    })
})

const route = useRoute()

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

