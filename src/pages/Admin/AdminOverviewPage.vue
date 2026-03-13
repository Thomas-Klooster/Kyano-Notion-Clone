<template>
  <div class="admin-page-container">
    <div class="page-header">
      <div>
        <h1 class="page-title">Admin omgeving</h1>
        <p class="page-subtitle">
          Overzicht van de admin modules voor klanten en projecten.
        </p>
      </div>
    </div>

    <v-row class="mb-2">
      <v-col cols="12" sm="6" md="3">
        <div class="notion-stat-card">
          <div class="notion-stat-label">Klanten</div>
          <div class="notion-stat-value">12</div>
        </div>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <div class="notion-stat-card">
          <div class="notion-stat-label">Projecten</div>
          <div class="notion-stat-value">28</div>
        </div>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <div class="notion-stat-card">
          <div class="notion-stat-label">Artikelen</div>
          <div class="notion-stat-value">143</div>
        </div>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <div class="notion-stat-card">
          <div class="notion-stat-label">Drafts</div>
          <div class="notion-stat-value">19</div>
        </div>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12" md="6">
        <v-card class="notion-card" flat rounded="xl" to="/admin/customers">
          <div class="pa-6">
            <div class="d-flex align-center justify-space-between mb-4">
              <div class="text-h6 font-weight-bold">Klanten</div>
              <v-icon icon="mdi-account-box-multiple-outline" />
            </div>

            <p class="page-subtitle">
              Klanten beheren, bewerken en structureren binnen de admin omgeving.
            </p>

            <div class="mt-6">
              <v-btn variant="text" color="primary">
                Open klanten
              </v-btn>
            </div>
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" md="6">
        <v-card class="notion-card" flat rounded="xl" to="/admin/projects">
          <div class="pa-6">
            <div class="d-flex align-center justify-space-between mb-4">
              <div class="text-h6 font-weight-bold">Projecten</div>
              <v-icon icon="mdi-folder-outline" />
            </div>

            <p class="page-subtitle">
              Projecten koppelen aan klanten en detailpagina’s opzetten met tabs.
            </p>

            <div class="mt-6">
              <v-btn variant="text" color="primary">
                Open projecten
              </v-btn>
            </div>
          </div>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" md="6">
        <v-card class="notion-card" flat rounded="xl" to="/admin/articles">
          <div class="pa-6">
            <div class="d-flex align-center justify-space-between mb-4">
              <div class="text-h6 font-weight-bold">Artikelen</div>
              <v-icon icon="mdi-account-box-multiple-outline" />
            </div>

            <p class="page-subtitle">
              Artikelen beheren, bewerken en structureren binnen de admin omgeving.
            </p>

            <div class="mt-6">
              <v-btn variant="text" color="primary">
                Open Artikelen
              </v-btn>
            </div>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>