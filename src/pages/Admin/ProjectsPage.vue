<template>
  <div>
    <div class="page-header">
      <div>
        <h1 class="page-title">Projects</h1>
        <p class="page-subtitle">
          Beheer projecten en filter deze per customer.
        </p>
      </div>

      <v-btn
        color="primary"
        rounded="lg"
        prepend-icon="mdi-plus"
        to="/admin/projects/new"
      >
        Nieuw project
      </v-btn>
    </div>

    <v-card class="notion-card mb-4" flat rounded="xl">
      <div class="pa-4">
        <v-row>
          <v-col cols="12" md="4">
            <v-select
              class="notion-soft-input"
              label="Filter op customer"
              :items="['Alle customers', 'Kyano Digital', 'Studio North', 'Pixelworks']"
              variant="solo-filled"
              flat
              hide-details
              model-value="Alle customers"
            />
          </v-col>
        </v-row>
      </div>
    </v-card>

    <v-card class="notion-card" flat rounded="xl">
      <v-table class="notion-table">
        <thead>
          <tr>
            <th>Project</th>
            <th>Klant</th>
            <th>Artikelen</th>
            <th>Status</th>
            <th class="text-right">Acties</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="project in projects" :key="project.id">
            <td>{{ project.name }}</td>
            <td>{{ project.customer }}</td>
            <td>{{ project.articles }}</td>
            <td>
              <v-chip size="small" class="notion-chip">
                {{ project.status }}
              </v-chip>
            </td>
            <td class="text-right">
              <v-btn
                size="small"
                variant="text"
                :to="`/admin/projects/${project.id}`"
              >
                Details
              </v-btn>
              <v-btn
                size="small"
                variant="text"
                :to="`/admin/projects/${project.id}/edit`"
              >
                Bewerken
              </v-btn>
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card>
  </div>
</template>

<script setup>
const projects = [
  { id: 1, name: 'Knowledgebase Portal', customer: 'Kyano Digital', articles: 12, status: 'Actief' },
  { id: 2, name: 'Client Onboarding', customer: 'Studio North', articles: 5, status: 'Concept' },
  { id: 3, name: 'Support Docs', customer: 'Pixelworks', articles: 8, status: 'Actief' },
]
</script>