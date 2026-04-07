<template>
  <div class="admin-page-container">
    <div class="page-header">
      <div>
        <h1 class="page-title">Klanten</h1>
        <p class="page-subtitle">
          Beheer klanten, basisgegevens en gekoppelde projecten.
        </p>
      </div>

      <v-btn color="primary" rounded="lg" prepend-icon="mdi-plus" to="/admin/customers/new">
        Nieuwe klant
      </v-btn>
    </div>

    <v-card class="notion-card mb-4" flat rounded="xl">
      <div class="pa-4">
        <v-row>
          <v-col cols="12" md="6">
            <v-text-field v-model="search" class="notion-soft-input" label="Zoek klant" prepend-inner-icon="mdi-magnify"
              variant="solo-filled" flat hide-details />
          </v-col>
        </v-row>
      </div>
    </v-card>

    <v-card class="notion-card" flat rounded="xl">
      <v-table class="notion-table">
        <thead>
          <tr>
            <th>Naam</th>
            <th>Contactpersoon</th>
            <th>Projecten</th>
            <th>Status</th>
            <th class="text-right">Acties</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="customer in filteredCustomers" :key="customer.id">
            <td>{{ customer.name }}</td>
            <td>{{ customer.contact }}</td>
            <td>{{ customer.projects }}</td>
            <td>
              <v-chip size="small" class="notion-chip">
                {{ customer.status }}
              </v-chip>
            </td>
            <td class="text-right">
              <v-btn size="small" variant="text" :to="`/admin/customers/${customer.id}/edit`">
                Bewerken
              </v-btn>

              <v-btn size="small" variant="text" color="error" @click="openDeleteDialog(customer)">
                Verwijderen
              </v-btn>
            </td>
          </tr>

          <tr v-if="filteredCustomers.length === 0">
            <td colspan="5">
              <div class="notion-empty-state ma-4">
                <div class="text-subtitle-1 font-weight-medium mb-1">
                  Geen klanten gevonden
                </div>
                <p class="page-subtitle mb-0">
                  Probeer een andere zoekterm of voeg een nieuwe klant toe.
                </p>
              </div>
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card>

    <v-dialog v-model="deleteDialog" max-width="480">
      <v-card class="notion-card" flat rounded="xl">
        <div class="pa-6">
          <div class="d-flex align-start ga-4">
            <div class="dialog-icon-wrapper">
              <v-icon icon="mdi-alert-outline" />
            </div>

            <div class="flex-1">
              <h2 class="text-h6 font-weight-bold mb-2">
                Klant verwijderen?
              </h2>

              <p class="page-subtitle mb-0">
                Je staat op het punt om
                <strong>{{ customerToDelete?.name }}</strong>
                te verwijderen. Deze kan niet ongedaan worden.
              </p>
            </div>
          </div>

          <div class="d-flex justify-end ga-3 mt-6">
            <v-btn variant="text" @click="closeDeleteDialog">
              Annuleren
            </v-btn>

            <v-btn color="error" rounded="lg" @click="confirmDelete">
              Verwijderen
            </v-btn>
          </div>
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const search = ref('')
const deleteDialog = ref(false)
const customerToDelete = ref(null)

const customers = ref([
  { id: 1, name: 'Kyano Digital', contact: 'info@kyano.nl', projects: 3, status: 'Actief' },
  { id: 2, name: 'Studio North', contact: 'hello@studionorth.nl', projects: 2, status: 'Actief' },
  { id: 3, name: 'Pixelworks', contact: 'team@pixelworks.nl', projects: 1, status: 'Concept' },
])

const filteredCustomers = computed(() => {
  const query = search.value.toLowerCase().trim()

  if (!query) return customers.value

  return customers.value.filter((customer) =>
    customer.name.toLowerCase().includes(query) ||
    customer.contact.toLowerCase().includes(query) ||
    customer.status.toLowerCase().includes(query)
  )
})

function openDeleteDialog(customer) {
  customerToDelete.value = customer
  deleteDialog.value = true
}

function closeDeleteDialog() {
  deleteDialog.value = false
  customerToDelete.value = null
}

function confirmDelete() {
  if (!customerToDelete.value) return

  customers.value = customers.value.filter(
    (customer) => customer.id !== customerToDelete.value.id
  )

  closeDeleteDialog()
}
</script>

<style scoped>
.dialog-icon-wrapper {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff4f4;
  border: 1px solid #f3d6d6;
  color: #b42318;
  flex-shrink: 0;
}
</style>