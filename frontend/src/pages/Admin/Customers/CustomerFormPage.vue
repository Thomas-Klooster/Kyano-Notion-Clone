<template>
  <div class="entity-page">
    <div class="entity-shell page-shell">
      <section class="entity-hero hero">
        <div class="hero-content u-min-w-0">
          <div class="hero-meta-line u-flex-center u-wrap u-gap-8">
            <span class="hero-pill u-inline-flex u-items-center">Admin</span>
            <span class="hero-meta-separator">•</span>
            <span>{{ isEditMode ? 'Klant bewerken' : 'Nieuwe klant' }}</span>
          </div>

          <h1 class="hero-title">
            {{ isEditMode ? 'Klant bewerken' : 'Nieuwe klant' }}
          </h1>

          <p class="hero-subtitle">
            {{ isEditMode
              ? 'Werk bedrijfsgegevens, accountinformatie en toegangsrechten bij.'
              : 'Voeg een nieuwe klant toe met bedrijfsgegevens, accountinformatie en toegangsrechten.' }}
          </p>
        </div>
      </section>

      <section class="entity-card card card-elevated card-rounded-2xl">
        <div class="entity-card-head card-head">
          <div>
            <div class="section-kicker">Klantaccount</div>
            <h2 class="section-title">
              {{ isEditMode ? 'Gegevens aanpassen' : 'Gegevens invullen' }}
            </h2>
          </div>
        </div>

        <div class="entity-form">
          <div class="form-grid">
            <div class="form-field">
              <label class="form-label">Bedrijfsnaam</label>
              <v-text-field
                v-model="form.companyName"
                class="entity-input"
                variant="plain"
                hide-details
                placeholder="Kyano Digital"
              />
            </div>

            <div class="form-field">
              <label class="form-label">Contactpersoon</label>
              <v-text-field
                v-model="form.name"
                class="entity-input"
                variant="plain"
                hide-details
                placeholder="Jan Jansen"
              />
            </div>

            <div class="form-field">
              <label class="form-label">E-mail</label>
              <v-text-field
                v-model="form.email"
                class="entity-input"
                variant="plain"
                hide-details
                placeholder="info@bedrijf.nl"
                type="email"
              />
            </div>

            <div class="form-field">
              <label class="form-label">Telefoonnummer</label>
              <v-text-field
                v-model="form.tel"
                class="entity-input"
                variant="plain"
                hide-details
                placeholder="+31 6 12345678"
              />
            </div>

            <div class="form-field form-field-full">
              <label class="form-label">Adres</label>
              <v-textarea
                v-model="form.address"
                class="entity-input entity-textarea"
                variant="plain"
                rows="3"
                auto-grow
                hide-details
                placeholder="Straatnaam 12, 1234 AB Plaats, Nederland"
              />
            </div>

            <div class="form-field">
              <label class="form-label">
                {{ isEditMode ? 'Nieuw wachtwoord' : 'Wachtwoord' }}
              </label>
              <v-text-field
                v-model="form.password"
                class="entity-input"
                variant="plain"
                hide-details
                :placeholder="isEditMode ? 'Leeg laten om huidig wachtwoord te behouden' : 'Voer een wachtwoord in'"
                :type="showPassword ? 'text' : 'password'"
                :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                @click:append-inner="showPassword = !showPassword"
              />
            </div>

            <div class="form-field">
              <label class="form-label">Rol</label>
              <v-select
                v-model="form.role"
                class="entity-input"
                variant="plain"
                hide-details
                :items="roles"
                placeholder="Selecteer een rol"
              />
            </div>
          </div>

          <div class="entity-form-actions u-gap-12" style="display: flex; align-items: center;">
            <v-btn
              class="entity-create-btn"
              rounded="lg"
              variant="text"
            >
              {{ isEditMode ? 'Wijzigingen opslaan' : 'Klant opslaan' }}
            </v-btn>

            <v-btn
              variant="text"
              rounded="lg"
              to="/admin/customers"
            >
              Annuleren
            </v-btn>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const isEditMode = computed(() => Boolean(route.params.id))
const showPassword = ref(false)

const roles = ['admin', 'customer']

const form = ref({
  companyName: '',
  name: '',
  email: '',
  tel: '',
  address: '',
  password: '',
  role: 'customer',
})
</script>

