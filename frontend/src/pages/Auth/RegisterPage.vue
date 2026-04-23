<template>
  <div class="auth-page">
    <div class="auth-container">
      <div class="auth-card card card-elevated card-rounded-2xl">

        <div class="auth-card-head">
          <div class="auth-card-head-bg" aria-hidden="true" />
          <div class="auth-card-head-content">
            <div class="section-kicker">Nieuw account</div>
            <h1 class="auth-title">Registreren</h1>
            <p class="auth-subtitle">Maak een account aan om te beginnen.</p>
          </div>
        </div>

        <div class="auth-card-body">
          <v-form ref="formRef" v-model="formValid" @submit.prevent="onSubmit">

            <div class="auth-field-group">
              <label class="auth-label">Volledige naam</label>
              <v-text-field
                v-model="fullName"
                placeholder="Jouw naam..."
                autocomplete="name"
                variant="solo-filled"
                flat
                density="comfortable"
                prepend-inner-icon="mdi-account-outline"
                :rules="nameRules"
                hide-details="auto"
                class="notion-soft-input"
                @keydown.enter.prevent="focusEmail"
              />
            </div>

            <div class="auth-field-group">
              <label class="auth-label">E-mailadres</label>
              <v-text-field
                ref="emailField"
                v-model="email"
                placeholder="naam@bedrijf.com"
                autocomplete="email"
                type="email"
                variant="solo-filled"
                flat
                density="comfortable"
                prepend-inner-icon="mdi-email-outline"
                :rules="emailRules"
                hide-details="auto"
                class="notion-soft-input"
                @keydown.enter.prevent="focusPassword"
              />
            </div>

            <div class="auth-field-group">
              <label class="auth-label">Wachtwoord</label>
              <v-text-field
                ref="passwordField"
                v-model="password"
                placeholder="••••••••"
                autocomplete="new-password"
                :type="showPassword ? 'text' : 'password'"
                variant="solo-filled"
                flat
                density="comfortable"
                prepend-inner-icon="mdi-lock-outline"
                :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                :rules="passwordRules"
                hide-details="auto"
                class="notion-soft-input"
                @click:append-inner="showPassword = !showPassword"
                @keydown.enter.prevent="focusConfirm"
              />
            </div>

            <div class="auth-field-group">
              <label class="auth-label">Wachtwoord bevestigen</label>
              <v-text-field
                ref="confirmField"
                v-model="password_confirmation"
                placeholder="••••••••"
                autocomplete="new-password"
                :type="showConfirm ? 'text' : 'password'"
                variant="solo-filled"
                flat
                density="comfortable"
                prepend-inner-icon="mdi-lock-check-outline"
                :append-inner-icon="showConfirm ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                :rules="confirmRules"
                hide-details="auto"
                class="notion-soft-input"
                @click:append-inner="showConfirm = !showConfirm"
                @keydown.enter.prevent="onSubmit"
              />
            </div>

            <div class="auth-terms-row">
              <v-checkbox v-model="acceptTerms" density="compact" hide-details="auto" :rules="termsRules" class="auth-checkbox">
                <template #label>
                  <span class="auth-terms-text">
                    Ik ga akkoord met de
                    <button type="button" class="auth-link-btn" @click.prevent="onOpenTerms">Voorwaarden</button>
                    en
                    <button type="button" class="auth-link-btn" @click.prevent="onOpenPrivacy">Privacybeleid</button>
                  </span>
                </template>
              </v-checkbox>
            </div>

            <v-alert
              v-if="errorMessage"
              type="error"
              variant="tonal"
              density="comfortable"
              closable
              class="auth-alert"
              @click:close="errorMessage = ''"
            >
              {{ errorMessage }}
            </v-alert>

            <button
              type="submit"
              class="auth-submit-btn"
              :class="{ 'auth-submit-btn--loading': loading }"
              :disabled="!formValid || loading"
            >
              <v-progress-circular v-if="loading" size="18" width="2" indeterminate color="white" />
              <span v-else>Account aanmaken</span>
            </button>

            <div class="u-divider auth-divider">
              <span class="auth-divider-text">of ga verder met</span>
            </div>

            <div class="auth-social-btns">
              <button type="button" class="auth-social-btn" @click="onSocial('google')">
                <v-icon size="18">mdi-google</v-icon>
                Google
              </button>
              <button type="button" class="auth-social-btn" @click="onSocial('microsoft')">
                <v-icon size="18">mdi-microsoft</v-icon>
                Microsoft
              </button>
            </div>

            <p class="auth-footer-text">
              Al een account?
              <button type="button" class="auth-link-btn" @click="goToLogin">Inloggen</button>
            </p>

          </v-form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'

const formRef = ref(null)
const emailField = ref(null)
const passwordField = ref(null)
const confirmField = ref(null)
const formValid = ref(false)
const loading = ref(false)
const errorMessage = ref('')
const errors = ref({})
const fullName = ref('')
const email = ref('')
const company = ref('')
const phone_number = ref('')
const address = ref('')
const password = ref('')
const password_confirmation = ref('')
const acceptTerms = ref(false)
const showPassword = ref(false)
const showConfirm = ref(false)
const router = useRouter()

const nameRules = [
  (v) => !!v || 'Uw naam is verplicht.',
  (v) => (v?.trim()?.length ?? 0) >= 2 || 'De naam moet minimaal 2 tekens lang zijn.',
]

const emailRules = [
  (v) => !!v || 'Het invullen van een email is verplicht.',
  (v) => /.+@.+\..+/.test(v) || 'Voer een geldig emailadres in',
]

const passwordRules = [
  (v) => !!v || 'Het invullen van een wachtwoord is verplicht.',
  (v) => (v?.length ?? 0) >= 8 || 'Het wachtwoord moet minimaal 8 tekens lang zijn.',
  (v) => /[A-Z]/.test(v) || 'Moet een hoofdletter bevatten',
  (v) => /[a-z]/.test(v) || 'Moet een kleine letter bevatten',
  (v) => /[\d\W]/.test(v) || 'Moet een getal of speciaal teken bevatten.',
]

const confirmRules = computed(() => [
  (v) => !!v || 'Bevestig uw wachtwoord',
  (v) => v === password.value || 'Wachtwoorden komen niet overeen',
])

const termsRules = [(v) => v === true || 'U moet de voorwaarden accepteren om verder te gaan.']

function focusEmail() { emailField.value?.focus?.() }
function focusPassword() { passwordField.value?.focus?.() }
function focusConfirm() { confirmField.value?.focus?.() }

async function onSubmit() {
  const { valid } = await formRef.value.validate()
  if (!valid) return
  loading.value = true
  errors.value = {}
  errorMessage.value = ''

  try {
    await axios.get('/sanctum/csrf-cookie', { withCredentials: true })
    const response = await axios.post(
      '/api/register',
      {
        name: fullName.value,
        email: email.value,
        company: company.value,
        phone_number: phone_number.value,
        address: address.value,
        password: password.value,
        password_confirmation: password_confirmation.value,
      },
      { withCredentials: true },
    )
    console.log('Geregistreerd:', response.data)
    router.push({ name: 'login' })
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors ?? {}
      errorMessage.value = 'Controleer ur velden en probeer opnieuw.'
    } else {
      errorMessage.value = 'Er is een onverwachte fout opgetreden'
      console.error(error)
    }
  } finally {
    loading.value = false
  }
}

function onSocial(provider) {
  errorMessage.value = `Social signup (${provider}) is not connected yet.`
}

function goToLogin() {
  router.push({ name: 'login' }).catch(() => {})
}

function onOpenTerms() { console.log('Open terms') }
function onOpenPrivacy() { console.log('Open privacy') }
</script>
