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
                        placeholder="Laat je feedback achter..." rows="1" @input="autoResizeTextarea"></textarea>

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

<style scoped>
.feedback-textarea {
    min-height: 42px;
    overflow: hidden;
    resize: none;
    line-height: 1.5;
}

.useful-button-box {
    display: flex;
    flex-direction: row;
    gap: 0.5rem;
}

.useful-button {
    width: 100%;
    border-radius: 10px;
    border: none;
    color: #FFF;
    padding: 0.5rem;
    cursor: pointer;
}

.thumbs-up {
    border: #24A1C7 1px solid;
    background: #24A1C7;
}

.thumbs-down {
    border: #284e5a 1px solid;
    background: #284e5a;
}

.feedback-card {
    text-align: center;
}

.feedback-input {
    width: 100%;
    min-height: 84px;
    padding: 12px;
    border: 1px solid #e7e5e4;
    border-radius: 12px;
    background: #fbfbfa;
    color: #191919;
    font-size: 0.95rem;
    outline: none;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.feedback-input:focus {
    border-color: #24A1C7;
    box-shadow: 0 0 0 3px rgba(36, 161, 199, 0.12);
}

.feedback-input::placeholder {
    color: #9b9a97;
}

.feedback-submit {
    min-height: 42px;
    width: 100%;
    border: 1px solid #24A1C7;
    border-radius: 12px;
    background: #24A1C7;
    color: #ffffff;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.15s ease, opacity 0.15s ease;
}

.feedback-submit:hover {
    opacity: 0.94;
}

.feedback-submit:active {
    transform: translateY(1px);
}

.article-preview-page {
    min-height: 100%;
    background: #f7f7f5;
}

.preview-topbar {
    position: sticky;
    top: 0;
    z-index: 20;
    border-bottom: 1px solid rgba(55, 53, 47, 0.08);
    backdrop-filter: blur(14px);
    background: rgba(247, 247, 245, 0.82);
}

.preview-topbar-inner {
    max-width: 1440px;
    margin: 0 auto;
    padding: 14px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.preview-topbar-left {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
}

.preview-topbar-right {
    flex-shrink: 0;
}

.preview-badge {
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

.preview-shell {
    max-width: 1440px;
    margin: 0 auto;
    padding: 32px 28px 72px;
    display: grid;
    grid-template-columns: 260px minmax(0, 1fr);
    gap: 28px;
}

.preview-sidebar {
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

.toc-nav {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.toc-link {
    display: block;
    padding: 8px 10px;
    border-radius: 10px;
    color: #5f5e5b;
    text-decoration: none;
    font-size: 0.95rem;
    transition: background 0.15s ease, color 0.15s ease;
}

.toc-link:hover,
.toc-link.active {
    background: #f3f3f1;
    color: #191919;
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

.preview-content {
    min-width: 0;
}

.preview-cover {
    height: 220px;
    border-radius: 26px;
    background: #24A1C7;
    background: linear-gradient(280deg, rgba(36, 161, 199, 1) 0%, rgba(0, 22, 61, 1) 100%);
    margin-bottom: 20px;
}

.article-card {
    border: 1px solid #ececea;
    border-radius: 26px;
    background: #ffffff;
    overflow: hidden;
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

.article-title {
    margin: 0;
    color: #191919;
    font-size: 3.4rem;
    line-height: 1.05;
    font-weight: 800;
    letter-spacing: -0.03em;
}

.article-subtitle {
    max-width: 760px;
    margin: 16px 0 0;
    color: #6b6a67;
    font-size: 1.08rem;
    line-height: 1.75;
}

.article-author-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 24px;
}

.author-avatar {
    width: 40px;
    height: 40px;
    border-radius: 999px;
    background: #6ea43f;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
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

.article-body> :first-child {
    margin-top: 0;
}

.article-body p {
    margin: 0 0 1.15rem;
    font-size: 1.04rem;
    line-height: 1.9;
    color: #37352f;
}

.article-body h2 {
    margin: 2.2rem 0 0.8rem;
    font-size: 1.72rem;
    line-height: 1.2;
    color: #191919;
    font-weight: 700;
    letter-spacing: -0.02em;
}

.article-body ul {
    margin: 0 0 1.15rem;
    padding-left: 1.3rem;
}

.article-body li {
    margin: 0.35rem 0;
    line-height: 1.8;
}

.article-body blockquote {
    margin: 1.4rem 0;
    padding: 12px 0 12px 18px;
    border-left: 3px solid #d9d7d1;
    color: #5f5e5b;
    font-size: 1rem;
    line-height: 1.8;
}

.callout-card {
    display: grid;
    grid-template-columns: 40px minmax(0, 1fr);
    gap: 14px;
    margin: 1.5rem 0;
    padding: 18px;
    border-radius: 18px;
    background: #f7f7f5;
    border: 1px solid #ececea;
}

.callout-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.callout-title {
    margin-bottom: 4px;
    color: #191919;
    font-weight: 700;
}

.callout-text {
    margin: 0;
}

.resource-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
    margin-top: 18px;
}

.resource-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px;
    border: 1px solid #ececea;
    border-radius: 18px;
    background: #fbfbfa;
}

.resource-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid #ececea;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #5f5e5b;
    flex-shrink: 0;
}

.resource-title {
    color: #191919;
    font-weight: 600;
}

.resource-subtitle {
    margin-top: 2px;
    color: #8a8884;
    font-size: 0.92rem;
}

@media (max-width: 1100px) {
    .preview-shell {
        grid-template-columns: 1fr;
    }

    .preview-sidebar {
        order: 2;
    }

    .preview-content {
        order: 1;
    }
}

@media (max-width: 900px) {

    .preview-topbar-inner,
    .preview-shell {
        padding-left: 20px;
        padding-right: 20px;
    }

    .article-head,
    .article-body {
        padding-left: 28px;
        padding-right: 28px;
    }

    .article-title {
        font-size: 2.5rem;
    }

    .resource-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .preview-topbar-inner {
        flex-direction: column;
        align-items: stretch;
    }

    .preview-topbar-left {
        flex-direction: column;
        align-items: flex-start;
    }

    .preview-cover {
        height: 180px;
        border-radius: 20px;
    }

    .article-head,
    .article-body {
        padding-left: 20px;
        padding-right: 20px;
    }

    .article-title {
        font-size: 2rem;
    }

    .article-subtitle,
    .article-body p,
    .article-body li,
    .article-body blockquote {
        font-size: 0.98rem;
    }
}
</style>