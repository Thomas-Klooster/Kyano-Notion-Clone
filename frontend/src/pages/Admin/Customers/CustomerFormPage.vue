<template>
  <div class="entity-page">
    <div class="entity-shell">
      <section class="entity-hero">
        <div class="hero-content">
          <div class="hero-meta-line">
            <span class="hero-pill">Admin</span>
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

      <section class="entity-card">
        <div class="entity-card-head">
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

          <div class="entity-form-actions" style="display: flex; align-items: center;">
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

<style scoped>
@import '../shared-admin-list.css';

.entity-form {
  padding: 28px 28px 22px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(12, minmax(0, 1fr));
  gap: 18px;
}

.form-field {
  grid-column: span 12;
}

.form-field-full {
  grid-column: span 12;
}

.form-label {
  display: block;
  margin-bottom: 10px;
  font-size: 0.86rem;
  font-weight: 600;
  color: #6b7280;
}

.entity-input {
  border: 1px solid #ebe7e2;
  background: rgba(245, 245, 245, 0.69420);
  border-radius: 18px;
  transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
  overflow: hidden;
}

.entity-input:hover {
  border-color: #ddd6ce;
  background: #ffffff;
}

.entity-input:focus-within {
  border-color: rgba(var(--v-theme-primary), 0.35);
  background: #ffffff;
  box-shadow: 0 0 0 4px rgba(var(--v-theme-primary), 0.08);
}

:deep(.entity-input .v-field) {
  background: transparent !important;
  box-shadow: none !important;
  border: none !important;
  border-radius: 18px !important;
}

:deep(.entity-input .v-field__overlay) {
  display: none !important;
}

:deep(.entity-input .v-field__input) {
  padding: 18px 18px !important;
  min-height: 56px !important;
  font-size: 0.96rem;
  color: #1f2937;
  display: flex;
  align-items: center;
}

:deep(.entity-input .v-field__append-inner) {
  display: flex;
  align-items: center;
  justify-content: center;
  padding-inline-start: 8px;
  padding-inline-end: 14px;
  align-self: center;
  height: 100%;
}

:deep(.entity-input .v-field__append-inner .v-icon) {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  line-height: 1;
  margin: 0;
}

:deep(.entity-input .v-field__clearable) {
  align-self: center;
}

:deep(.entity-textarea .v-field__input) {
  padding-top: 16px !important;
  padding-bottom: 16px !important;
}

.entity-form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 28px;
  padding-top: 22px;
  border-top: 1px solid #f0ece6;
}

@media (min-width: 960px) {
  .form-field {
    grid-column: span 6;
  }

  .form-field-full {
    grid-column: span 12;
  }
}

@media (max-width: 640px) {
  .entity-form-actions {
    flex-direction: column-reverse;
    align-items: stretch;
  }
}
</style>