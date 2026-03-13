<template>
  <div class="article-editor">
    <div class="article-cover">
      <div class="cover-actions">
        <v-btn size="small" variant="tonal">Change</v-btn>
      </div>
    </div>

    <div class="article-container">
      <div class="article-header">
        <button class="article-icon" type="button">🗼</button>
        <input v-model="title" class="article-title-input" placeholder="Untitled" />
      </div>
      <div class="article-comments">
        <div class="comment-row">
          <div class="comment-avatar">K</div>
          <div>
            <div class="comment-meta">
              Klooster Thomas <span>Just now</span>
            </div>
            <div class="comment-text">Comments go here</div>
          </div>
        </div>

        <div class="comment-row muted">
          <div class="comment-avatar">K</div>
          <div class="comment-placeholder">Add a comment...</div>
        </div>
      </div>

      <div class="editor-block">
        <QuillEditor v-model:content="content" contentType="html" theme="snow" toolbar="minimal" class="notion-quill" />
      </div>

      <div class="bookmark-section">
        <div class="bookmark-link-row">
          <v-icon size="18">mdi-link-variant</v-icon>
          <span>TEST PAGE</span>
        </div>

        <div class="bookmark-input">
          <v-icon size="18">mdi-bookmark-outline</v-icon>
          <span>Add a web bookmark</span>
        </div>
      </div>

      <div class="editor-actions">
        <v-btn variant="text">Save</v-btn>
        <v-btn color="primary" rounded="lg">
          {{ status === 'Published' ? 'Update' : 'Publish' }}
        </v-btn>
      </div>
    </div>
  </div>
</template>

<script setup>
import { QuillEditor } from '@vueup/vue-quill'
import { ref } from 'vue'

const title = ref('PAGE TITLE')
const slug = ref('page-title')
const summary = ref('')
const status = ref('Draft')
const project = ref('Knowledgebase Portal')
const category = ref('Installatie')

const projects = ['Knowledgebase Portal', 'Client Onboarding', 'Support Docs']
const categories = ['Installatie', 'Toegang', 'Troubleshooting']
const statuses = ['Draft', 'Published', 'Archived']

const toolbarOptions = [
  [{ header: [1, 2, false] }],
  ['bold', 'italic', 'underline'],
  [{ list: 'ordered' }, { list: 'bullet' }],
  ['blockquote', 'code-block', 'link'],
  ['clean'],
]

const content = ref(`
  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse pretium rutrum diam tempor lacinia. Donec dictum felis sed nisl mollis porta.</p>
  <p>Etiam pulvinar, eros at auctor accumsan, ex nibh fermentum libero, eu ullamcorper ligula odio sed est. Nunc ac enim sapien.</p>
  <p>Nulla viverra erat eget diam pretium, vitae pharetra sapien aliquet.</p>
`)
</script>

<style scoped>
.article-editor {
  background: #f7f7f5;
  min-height: 100%;
  width: 100%;
}

.article-cover {
  height: 260px;
  width: 100%;
  background: #ff2f68;
  position: relative;
}

.cover-actions {
  position: absolute;
  top: 16px;
  right: 16px;
  display: flex;
  gap: 8px;
}

.article-container {
  width: 100%;
  max-width: 1000px !important;
  margin: 0 auto;
  padding: 0 56px 72px;
}

.article-icon-row {
  margin-top: -42px;
  margin-bottom: 8px;
}

.article-icon {
  border: none;
  background: transparent;
  font-size: 4rem;
  line-height: 1;
  cursor: pointer;
  padding: 0;
}

.article-title-input {
  width: 100%;
  border: none;
  outline: none;
  background: transparent;
  font-size: 3.4rem;
  line-height: 1.08;
  font-weight: 700;
  color: #2f3437;
}

.article-title-input::placeholder {
  color: #9b9a97;
}

.article-comments {
  display: flex;
  flex-direction: column;
  gap: 14px;
  margin-bottom: 28px;
  max-width: 900px;
}

.comment-row {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.comment-avatar {
  width: 24px;
  height: 24px;
  border-radius: 999px;
  background: #6ea43f;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 600;
  flex-shrink: 0;
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
  opacity: 0.9;
}


.editor-block {
  width: 100%;
  margin-bottom: 28px;
}

.article-header {
  display: flex;
  align-items: center;
  justify-content: start;
  padding-block: 1.5rem;
}

/* :deep(.notion-quill) {
  width: 100%;
}

:deep(.notion-quill .ql-toolbar.ql-snow) {
  border: none;
  border-bottom: 1px solid #ececea;
  padding: 10px 0 12px;
  background: transparent;
}

:deep(.notion-quill .ql-container.ql-snow) {
  border: none;
  min-height: 420px;
  font-size: 1.05rem;
  line-height: 1.8;
  color: #37352f;
}

:deep(.notion-quill .ql-editor) {
  padding: 18px;
  min-height: 420px;
} */

:deep(.notion-quill .ql-editor.ql-blank::before) {
  color: #9b9a97;
  font-style: normal;
  left: 0;
}

.bookmark-section {
  border-top: 1px solid #ececea;
  padding-top: 12px;
  margin-bottom: 32px;
  width: 100%;
}

.bookmark-link-row {
  display: flex;
  align-items: center;
  gap: 8px;
  color: #37352f;
  margin-bottom: 12px;
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
  justify-content: flex-end;
  gap: 10px;
  padding-top: 12px;
  width: 100%;
}

@media (max-width: 1200px) {
  .article-container {
    padding: 0 40px 64px;
  }
}

@media (max-width: 900px) {
  .article-container {
    padding: 0 24px 56px;
  }

  .article-title-input {
    font-size: 2.4rem;
  }

  .property-row {
    grid-template-columns: 1fr;
    gap: 4px;
  }

  .property-label {
    padding-top: 0;
  }
}

@media (max-width: 600px) {
  .article-container {
    padding: 0 16px 48px;
  }

  .article-cover {
    height: 200px;
  }

  .article-title-input {
    font-size: 2rem;
  }
}
</style>