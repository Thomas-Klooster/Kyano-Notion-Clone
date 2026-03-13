<template>
  <div class="admin-page-container">
    <div class="page-header">
      <div>
        <h1 class="page-title">Customer create/edit form</h1>
        <p class="page-subtitle">
          Maak een klant aan.
        </p>
      </div>
    </div>

    <v-card class="notion-card" flat rounded="xl">
      <div class="pa-6">
        <v-row>
          <v-col cols="12" md="6">
            <div class="notion-meta-label">Naam klant</div>
            <v-text-field class="notion-soft-input" variant="solo-filled" flat hide-details
              placeholder="Kyano Digital" />
          </v-col>

          <v-col cols="12" md="6">
            <div class="notion-meta-label">Contact e-mail</div>
            <v-text-field class="notion-soft-input" variant="solo-filled" flat hide-details
              placeholder="kyano@kyano.digital" />
          </v-col>

          <v-col cols="12">
            <div class="notion-meta-label">Metadata / notities</div>
            <v-textarea class="notion-soft-input" variant="solo-filled" flat rows="5" hide-details
              placeholder="Metadata / notities" />
          </v-col>
        </v-row>

        <div class="notion-form-actions">
          <v-btn color="primary" rounded="lg">Opslaan</v-btn>
          <v-btn variant="text" to="/admin/customers">Annuleren</v-btn>
        </div>
      </div>
    </v-card>
  </div>
</template>