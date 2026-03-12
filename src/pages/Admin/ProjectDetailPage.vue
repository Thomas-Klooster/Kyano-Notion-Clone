<template>
  <div>
    <div class="page-header">
      <div>
        <h1 class="page-title">Project detail page</h1>
        <p class="page-subtitle">
          Overzicht van artikelen, categorieën en tags binnen dit project.
        </p>
      </div>

      <v-btn color="primary" rounded="lg" :to="`/admin/projects/${projectId}/edit`">
        Project bewerken
      </v-btn>
    </div>

    <v-card class="notion-card mb-4" flat rounded="xl">
      <div class="pa-6">
        <div class="text-h6 font-weight-bold mb-2">Knowledgebase Portal</div>
        <div class="notion-muted">Customer: Kyano Digital</div>
      </div>
    </v-card>

    <v-card class="notion-card" flat rounded="xl">
      <v-tabs v-model="tab" class="notion-soft-tabs px-2">
        <v-tab value="articles">Articles</v-tab>
        <v-tab value="categories">Categories</v-tab>
        <v-tab value="tags">Tags</v-tab>
      </v-tabs>

      <v-divider />

      <v-window v-model="tab">
        <v-window-item value="articles">
          <div class="notion-tab-panel">
            <div class="notion-empty-state">
              <div class="text-h6 mb-2">Articles</div>
              <p class="page-subtitle mb-0">
                Hier komt later een overzicht van alle artikelen binnen dit project.
              </p>
            </div>
          </div>
        </v-window-item>

        <v-window-item value="categories">
          <div class="notion-tab-panel">
            <div class="notion-empty-state">
              <div class="text-h6 mb-2">Categories</div>
              <p class="page-subtitle mb-0">
                Hier komen later de categorieën die aan dit project gekoppeld zijn.
              </p>
            </div>
          </div>
        </v-window-item>

        <v-window-item value="tags">
          <div class="notion-tab-panel">
            <div class="notion-empty-state">
              <div class="text-h6 mb-2">Tags</div>
              <p class="page-subtitle mb-0">
                Optionele tags kunnen later hier worden beheerd.
              </p>
            </div>
          </div>
        </v-window-item>
      </v-window>
    </v-card>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const projectId = route.params.id
const tab = ref('articles')
</script>