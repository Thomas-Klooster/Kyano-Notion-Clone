<template>
  <div class="entity-page">
    <div class="entity-shell">
      <section class="entity-hero">
        <div class="hero-content">
          <div class="hero-meta-line">
            <span class="hero-pill">Admin</span>
            <span class="hero-meta-separator">•</span>
            <span>{{ filteredCustomers.length }} klanten</span>
          </div>

          <h1 class="hero-title">Klanten</h1>

          <p class="hero-subtitle">
            Beheer klantaccounts, bedrijfsgegevens en gekoppelde contactinformatie.
          </p>
        </div>
      </section>

      <section class="entity-card">
        <div class="entity-card-head">
          <div>
            <div class="section-kicker">Overzicht</div>
            <h2 class="section-title">Alle klanten</h2>
          </div>

          <div class="entity-controls">
            <div class="search-field">
              <v-icon size="18">mdi-magnify</v-icon>
              <input
                v-model="search"
                type="text"
                placeholder="Zoek klanten..."
              />
            </div>

            <v-btn
              class="entity-create-btn"
              variant="text"
              prepend-icon="mdi-plus"
              to="/admin/customers/new"
            >
              Nieuwe klant
            </v-btn>
          </div>
        </div>

        <div v-if="filteredCustomers.length" class="entity-list">
          <div
            v-for="customer in filteredCustomers"
            :key="customer.id"
            class="entity-row entity-row-rich"
          >
            <div class="entity-row-main">
              <div class="entity-icon entity-icon-soft">
                <v-icon size="18">mdi-domain</v-icon>
              </div>

              <div class="entity-info">
                <div class="entity-name-row">
                  <div class="entity-name">{{ customer.companyName }}</div>

                  <v-chip
                    size="small"
                    class="entity-chip"
                    :class="{
                      'entity-chip-admin': customer.role === 'admin',
                      'entity-chip-customer': customer.role === 'customer'
                    }"
                  >
                    {{ customer.role }}
                  </v-chip>
                </div>

                <div class="entity-meta entity-meta-stack">
                  <span>
                    <strong>Contact:</strong> {{ customer.name }}
                  </span>
                  <span class="dot">•</span>
                  <span>{{ customer.email }}</span>
                  <span class="dot">•</span>
                  <span>{{ customer.tel }}</span>
                </div>

                <div class="entity-submeta">
                  <v-icon size="15">mdi-map-marker-outline</v-icon>
                  <span>{{ customer.address }}</span>
                </div>
              </div>
            </div>

            <div class="entity-actions">
              <v-btn
                size="small"
                variant="text"
                :to="`/admin/customers/${customer.id}/edit`"
              >
                Bewerken
              </v-btn>

              <v-btn
                size="small"
                variant="text"
                class="delete-btn"
                @click="openDeleteDialog(customer)"
              >
                Verwijderen
              </v-btn>
            </div>
          </div>
        </div>

        <div v-else class="empty-state">
          <div class="empty-state-icon">
            <v-icon size="24">mdi-domain-off</v-icon>
          </div>
          <h3>Geen klanten gevonden</h3>
          <p>Probeer een andere zoekterm of voeg een nieuwe klant toe.</p>
        </div>
      </section>
    </div>

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

              <p class="page-subtitle mb-0 dialog-copy">
                Je staat op het punt om
                <strong>{{ customerToDelete?.companyName }}</strong>
                te verwijderen. Deze actie kan niet ongedaan worden gemaakt.
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
  {
    id: 1,
    companyName: 'Kyano Digital',
    name: 'Kyano Team',
    email: 'info@kyano.nl',
    tel: '+31 6 12345678',
    address: 'Moermanskweg 2-25, 9723 HM Groningen',
    role: 'admin',
  },
  {
    id: 2,
    companyName: 'Kawasaki',
    name: 'Yasuhiko Hashimoto',
    email: 'hello@kawasaki.jp',
    tel: '+81 3-3435-2111',
    address: '1 Chome-14-5 Kaigan, Minato City, Tokyo 105-0022, Japan',
    role: 'customer',
  },
  {
    id: 3,
    companyName: 'Yamaha',
    name: 'Motofumi Shitara',
    email: 'team@yamaha.jp',
    tel: '+81 538-32-1115',
    address: '2500 Shingai, Iwata, Shizuoka 438-0025, Japan',
    role: 'customer',
  },
])

const filteredCustomers = computed(() => {
  const query = search.value.trim().toLowerCase()

  if (!query) return customers.value

  return customers.value.filter((customer) =>
    customer.companyName.toLowerCase().includes(query) ||
    customer.name.toLowerCase().includes(query) ||
    customer.email.toLowerCase().includes(query) ||
    customer.tel.toLowerCase().includes(query) ||
    customer.address.toLowerCase().includes(query) ||
    customer.role.toLowerCase().includes(query)
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

