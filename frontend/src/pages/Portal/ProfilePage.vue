<template>
  <div class="dashboard-page profile-page">
    <div class="dashboard-shell page-shell">
      <section class="hero">
        <div class="profile-hero-row u-min-w-0">
          <div class="profile-avatar-large" aria-hidden="true">{{ userInitials }}</div>

          <div class="hero-content u-min-w-0">
            <div class="hero-meta-line u-flex-center u-wrap u-gap-8">
              <span class="hero-pill u-inline-flex u-items-center">Profiel</span>
              <span class="hero-meta-separator">•</span>
              <span>{{ roleLabel }}</span>
              <span v-if="profile.company" class="hero-meta-separator">•</span>
              <span v-if="profile.company">{{ profile.company }}</span>
            </div>

            <h1 class="hero-title">{{ profile.name || 'Mijn profiel' }}</h1>
          </div>
        </div>
      </section>

      <div class="profile-grid">
        <section class="project-list-card card card-elevated card-rounded-2xl">
          <div class="project-list-head card-head">
            <div>
              <div class="section-kicker">Profielgegevens</div>
              <h2 class="section-title">Persoonlijke informatie</h2>
              <p class="page-subtitle">
                Deze gegevens worden gebruikt voor je account en zichtbaarheid binnen het klantportaal.
              </p>
            </div>

            <v-btn
              type="button"
              variant="outlined"
              class="profile-secondary-action"
              :disabled="profileSaving || !hasProfileChanges"
              @click="resetProfileForm"
            >
              <template #prepend>
                <v-icon size="16">mdi-restore</v-icon>
              </template>
              Herstellen
            </v-btn>
          </div>

          <div class="profile-card-body">
            <v-form ref="profileFormRef" v-model="profileFormValid" @submit.prevent="saveProfile">
              <div class="profile-form-grid">
                <div class="profile-field profile-field-full">
                  <label class="notion-meta-label">Volledige naam</label>
                  <v-text-field
                    v-model="profile.name"
                    placeholder="Jouw naam"
                    autocomplete="name"
                    variant="solo-filled"
                    flat
                    density="comfortable"
                    prepend-inner-icon="mdi-account-outline"
                    :rules="nameRules"
                    hide-details="auto"
                    class="notion-soft-input"
                  />
                </div>

                <div class="profile-field profile-field-full">
                  <label class="notion-meta-label">E-mailadres</label>
                  <v-text-field
                    v-model="profile.email"
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
                  />
                </div>

                <div class="profile-field">
                  <label class="notion-meta-label">Bedrijf</label>
                  <v-text-field
                    v-model="profile.company"
                    placeholder="Bedrijfsnaam"
                    autocomplete="organization"
                    variant="solo-filled"
                    flat
                    density="comfortable"
                    prepend-inner-icon="mdi-domain"
                    hide-details="auto"
                    class="notion-soft-input"
                  />
                </div>

                <div class="profile-field">
                  <label class="notion-meta-label">Telefoonnummer</label>
                  <v-text-field
                    v-model="profile.phone_number"
                    placeholder="0612345678"
                    autocomplete="tel"
                    type="tel"
                    variant="solo-filled"
                    flat
                    density="comfortable"
                    prepend-inner-icon="mdi-phone-outline"
                    :rules="phoneRules"
                    hide-details="auto"
                    class="notion-soft-input"
                  />
                </div>

                <div class="profile-field profile-field-full">
                  <label class="notion-meta-label">Adres</label>
                  <v-text-field
                    v-model="profile.address"
                    placeholder="Straatnaam 1, Plaats"
                    autocomplete="street-address"
                    variant="solo-filled"
                    flat
                    density="comfortable"
                    prepend-inner-icon="mdi-map-marker-outline"
                    hide-details="auto"
                    class="notion-soft-input"
                  />
                </div>
              </div>

              <v-alert
                v-if="profileMessage.text"
                :type="profileMessage.type"
                variant="tonal"
                density="comfortable"
                closable
                class="profile-alert"
                @click:close="profileMessage.text = ''"
              >
                {{ profileMessage.text }}
              </v-alert>

              <div class="notion-form-actions profile-form-actions">
                <v-btn
                  type="submit"
                  class="entity-create-btn"
                  :loading="profileSaving"
                  :disabled="!profileFormValid || profileSaving || !hasProfileChanges"
                >
                  Profiel opslaan
                </v-btn>
              </div>
            </v-form>
          </div>
        </section>

        <aside class="profile-side-column">
          <section class="project-list-card card card-elevated card-rounded-2xl">
            <div class="profile-side-card-head">
              <h2 class="section-title">Account info</h2>
            </div>

            <div class="profile-status-stack">
              <div class="notion-stat-card profile-status-card">
                <div class="profile-status-icon icon-box">
                  <v-icon size="18">mdi-account-badge-outline</v-icon>
                </div>
                <div class="u-min-w-0">
                  <div class="notion-stat-label">Rol</div>
                  <div class="profile-stat-value">{{ roleLabel }}</div>
                </div>
              </div>

              <div class="notion-stat-card profile-status-card">
                <div class="profile-status-icon icon-box">
                  <v-icon size="18">mdi-calendar-check-outline</v-icon>
                </div>
                <div class="u-min-w-0">
                  <div class="notion-stat-label">Klant sinds</div>
                  <div class="profile-stat-value">{{ createdAtLabel }}</div>
                </div>
              </div>

              <div class="notion-stat-card profile-status-card">
                <div class="profile-status-icon icon-box">
                  <v-icon size="18">mdi-clock-edit-outline</v-icon>
                </div>
                <div class="u-min-w-0">
                  <div class="notion-stat-label">Laatst bijgewerkt</div>
                  <div class="profile-stat-value">{{ updatedAtLabel }}</div>
                </div>
              </div>
            </div>
          </section>

          <section class="project-list-card card card-elevated card-rounded-2xl">
            <div class="profile-side-card-head">
              <div class="section-kicker">Beveiliging</div>
              <h2 class="section-title">Wachtwoord wijzigen</h2>
              <p class="page-subtitle">Kies een nieuw wachtwoord en bevestig deze nog één keer.</p>
            </div>

            <div class="profile-card-body profile-card-body-compact">
              <v-form ref="passwordFormRef" v-model="passwordFormValid" @submit.prevent="savePassword">
                <div class="profile-field">
                  <label class="notion-meta-label">Nieuw wachtwoord</label>
                  <v-text-field
                    v-model="passwordForm.password"
                    placeholder="••••••••"
                    autocomplete="new-password"
                    :type="showNewPassword ? 'text' : 'password'"
                    variant="solo-filled"
                    flat
                    density="comfortable"
                    prepend-inner-icon="mdi-lock-check-outline"
                    :append-inner-icon="showNewPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                    :rules="newPasswordRules"
                    hide-details="auto"
                    class="notion-soft-input"
                    @click:append-inner="showNewPassword = !showNewPassword"
                  />
                </div>

                <div class="profile-field">
                  <label class="notion-meta-label">Herhaal nieuw wachtwoord</label>
                  <v-text-field
                    v-model="passwordForm.password_confirmation"
                    placeholder="••••••••"
                    autocomplete="new-password"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    variant="solo-filled"
                    flat
                    density="comfortable"
                    prepend-inner-icon="mdi-lock-reset"
                    :append-inner-icon="showConfirmPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                    :rules="confirmPasswordRules"
                    hide-details="auto"
                    class="notion-soft-input"
                    @click:append-inner="showConfirmPassword = !showConfirmPassword"
                  />
                </div>

                <v-alert
                  v-if="passwordMessage.text"
                  :type="passwordMessage.type"
                  variant="tonal"
                  density="comfortable"
                  closable
                  class="profile-alert"
                  @click:close="passwordMessage.text = ''"
                >
                  {{ passwordMessage.text }}
                </v-alert>

                <v-btn
                  type="submit"
                  class="entity-create-btn profile-full-action"
                  :loading="passwordSaving"
                  :disabled="!passwordFormValid || passwordSaving"
                >
                  Wachtwoord opslaan
                </v-btn>
              </v-form>
            </div>
          </section>
        </aside>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { getProfile, updatePassword, updateProfile } from '@/services/profileService'

const auth = useAuthStore()

const profileFormRef = ref(null)
const passwordFormRef = ref(null)
const profileFormValid = ref(false)
const passwordFormValid = ref(false)
const profileSaving = ref(false)
const passwordSaving = ref(false)

const profile = reactive({
  name: '',
  email: '',
  company: '',
  phone_number: '',
  address: '',
  role: '',
  created_at: '',
  updated_at: '',
})

const originalProfile = ref({})

const passwordForm = reactive({
  password: '',
  password_confirmation: '',
})

const profileMessage = reactive({ text: '', type: 'success' })
const passwordMessage = reactive({ text: '', type: 'success' })

const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

const editableKeys = ['name', 'email', 'company', 'phone_number', 'address']

const nameRules = [
  (v) => !!v || 'Naam is verplicht.',
  (v) => (v?.trim()?.length ?? 0) >= 2 || 'Naam moet minimaal 2 tekens lang zijn.',
]

const emailRules = [
  (v) => !!v || 'E-mailadres is verplicht.',
  (v) => /.+@.+\..+/.test(v) || 'Voer een geldig e-mailadres in.',
]

const phoneRules = [
  (v) => !v || /^\+?[0-9\s-]{6,20}$/.test(v.trim()) || 'Voer een geldig telefoonnummer in.',
]

const newPasswordRules = [
  (v) => !!v || 'Nieuw wachtwoord is verplicht.',
  (v) => (v?.length ?? 0) >= 8 || 'Het wachtwoord moet minimaal 8 tekens lang zijn.',
  (v) => /[A-Z]/.test(v) || 'Moet een hoofdletter bevatten.',
  (v) => /[a-z]/.test(v) || 'Moet een kleine letter bevatten.',
  (v) => /[\d\W]/.test(v) || 'Moet een getal of speciaal teken bevatten.',
]

const confirmPasswordRules = computed(() => [
  (v) => !!v || 'Herhaal je nieuwe wachtwoord.',
  (v) => v === passwordForm.password || 'Wachtwoorden komen niet overeen.',
])

const userInitials = computed(() => {
  const name = profile.name || auth.user?.name
  if (!name) return '?'
  const parts = name.trim().split(' ').filter(Boolean)
  if (parts.length === 1) return parts[0][0].toUpperCase()
  return `${parts[0][0]}${parts[parts.length - 1][0]}`.toUpperCase()
})

const roleLabel = computed(() => {
  const role = profile.role || auth.user?.role
  if (role === 'admin' || role === 'owner') return 'Admin'
  if (role === 'customer') return 'klant'
  return 'Klant'
})

const createdAtLabel = computed(() => formatDate(profile.created_at || auth.user?.created_at))
const updatedAtLabel = computed(() => formatDate(profile.updated_at || auth.user?.updated_at))

const hasProfileChanges = computed(() => {
  return editableKeys.some((key) => normalize(profile[key]) !== normalize(originalProfile.value?.[key]))
})

watch(
  () => auth.user,
  (user) => {
    hydrateProfile(user)
  },
  { immediate: true },
)

function normalize(value) {
  return value ?? ''
}

function formatDate(value) {
  if (!value) return 'Onbekend'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  return new Intl.DateTimeFormat('nl-NL', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  }).format(date)
}

function hydrateProfile(user = {}) {
  Object.assign(profile, {
    name: user?.name ?? '',
    email: user?.email ?? '',
    company: user?.company ?? '',
    phone_number: user?.phone_number ?? '',
    address: user?.address ?? '',
    role: user?.role ?? '',
    created_at: user?.created_at ?? '',
    updated_at: user?.updated_at ?? '',
  })
  originalProfile.value = editableKeys.reduce((carry, key) => {
    carry[key] = profile[key]
    return carry
  }, {})
}

function resetProfileForm() {
  editableKeys.forEach((key) => {
    profile[key] = originalProfile.value?.[key] ?? ''
  })
  profileMessage.text = ''
}

function resetPasswordForm() {
  passwordForm.password = ''
  passwordForm.password_confirmation = ''
  passwordFormRef.value?.resetValidation?.()
}

function friendlyError(error, fallback) {
  return error?.response?.data?.message || error?.response?.data?.error || fallback
}

async function saveProfile() {
  const { valid } = await profileFormRef.value.validate()
  if (!valid || !hasProfileChanges.value) return

  profileSaving.value = true
  profileMessage.text = ''

  try {
    const payload = editableKeys.reduce((carry, key) => {
      carry[key] = profile[key] || null
      return carry
    }, {})

    const updatedProfile = await updateProfile(payload)
    const user = updatedProfile.user ?? updatedProfile
    auth.setUser({ ...auth.user, ...user })
    hydrateProfile({ ...auth.user, ...user })
    profileMessage.type = 'success'
    profileMessage.text = 'Je profiel is opgeslagen.'
  } catch (error) {
    profileMessage.type = 'error'
    profileMessage.text = friendlyError(error, 'Profiel opslaan is nog niet verbonden met de backend.')
  } finally {
    profileSaving.value = false
  }
}

async function savePassword() {
  const { valid } = await passwordFormRef.value.validate()
  if (!valid) return

  passwordSaving.value = true
  passwordMessage.text = ''

  try {
    await updatePassword({ ...passwordForm })
    resetPasswordForm()
    passwordMessage.type = 'success'
    passwordMessage.text = 'Je wachtwoord is gewijzigd.'
  } catch (error) {
    passwordMessage.type = 'error'
    passwordMessage.text = friendlyError(error, 'Het wachtwoord kon niet worden gewijzigd.')
  } finally {
    passwordSaving.value = false
  }
}

async function refreshProfile() {
  try {
    const user = await getProfile()
    auth.setUser(user)
    hydrateProfile(user)
  } catch {
    hydrateProfile(auth.user)
  }
}

refreshProfile()
</script>
