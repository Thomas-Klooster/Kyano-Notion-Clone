<template>
  <div class="entity-shell page-shell">
    <div class="page-header">
      <div>
        <h1 class="page-title">Project create/edit</h1>
        <p class="page-subtitle">
          Maak een Project aan.
        </p>
      </div>
    </div>

    <v-card class="notion-card" flat rounded="xl">
      <div class="pa-6">
        <v-row>
          <v-col cols="12" md="6">
            <div class="notion-meta-label">Projectnaam</div>
            <v-text-field class="notion-soft-input" variant="solo-filled" flat hide-details
              placeholder="Bijv. Knowledgebase Portal" />
          </v-col>

          <v-col cols="12" md="6">
            <div class="notion-meta-label">Gekoppelde customer</div>
            <v-select class="notion-soft-input" :items="['Kyano Digital', 'Studio North', 'Pixelworks']"
              variant="solo-filled" flat hide-details placeholder="Selecteer customer" />
          </v-col>

          <v-col cols="12">
            <div class="notion-meta-label">Beschrijving</div>
            <v-textarea class="notion-soft-input" variant="solo-filled" flat rows="5" hide-details
              placeholder="Korte omschrijving van dit project" />
          </v-col>
        </v-row>

        <div class="notion-form-actions">
          <v-btn color="primary" rounded="lg">Opslaan</v-btn>
          <v-btn variant="text" to="/admin/projects">Annuleren</v-btn>
        </div>
      </div>
    </v-card>
  </div>
</template>