<template>
    <v-row justify="center">
        <v-col cols="12" sm="10" md="8" lg="4" xl="3">
            <v-card elevation="10" rounded>
                <v-card-item class="pb-0">
                    <div class="d-flex align-center justify-space-between">
                        <div class="d-flex align-center ga-3">
                            <v-avatar size="44" color="primary" variant="tonal">
                                <v-icon icon="mdi-account-plus-outline" />
                            </v-avatar>

                            <div>
                                <div class="text-h6 font-weight-semibold">Create account</div>

                                <div class="text-body-2 text-medium-emphasis">Sign up to get started</div>
                            </div>
                        </div>
                    </div>
                </v-card-item>

                <v-card-text class="pt-6">
                    <v-form ref="formRef" v-model="formValid" @submit.prevent="onSubmit">
                        <v-text-field v-model="fullName" label="Full name" placeholder="Your name..."
                            autocomplete="name" variant="outlined" density="comfortable"
                            prepend-inner-icon="mdi-account-outline" :rules="nameRules" hide-details="auto" class="mb-3"
                            @keydown.enter.prevent="focusEmail" /><v-text-field ref="emailField" v-model="email"
                            label="Email" placeholder="name@company.com" autocomplete="email" type="email"
                            variant="outlined" density="comfortable" prepend-inner-icon="mdi-email-outline"
                            :rules="emailRules" hide-details="auto" class="mb-3"
                            @keydown.enter.prevent="focusPassword" />

                            <v-text-field ref="companyField" v-model="company" label="Company name" placeholder="optioneel"
                            autocomplete="company" variant="outlined" density="comfortable" 
                            prepend-inner-icon="mdi-briefcase-outline" hide-details="auto" class="mb-3"
                            @keydown.enter.prevent="focusCompany" />

                            <v-text-field ref="phoneNumberField" v-model="phone_number" label="Phone number" placeholder="optioneel"
                            autocomplete="phonenumber" variant="outlined" density="comfortable"
                            prepend-inner-icon="mdi-phone-outline" :rules="phoneRules" hide-details="auto" class="mb-3"
                            @keydown.enter.prevent="focusPhoneNumber" />

                        <v-text-field ref="passwordField" v-model="password" label="Password" placeholder="••••••••"
                            autocomplete="new-password" :type="showPassword ? 'text' : 'password'" variant="outlined"
                            density="comfortable" prepend-inner-icon="mdi-lock-outline"
                            :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                            :rules="passwordRules" hide-details="auto" class="mb-3"
                            @click:append-inner="showPassword = !showPassword" @keydown.enter.prevent="focusConfirm" />

                        <v-text-field ref="confirmField" v-model="password_confirmation" label="Confirm password"
                            placeholder="••••••••" autocomplete="new-password" :type="showConfirm ? 'text' : 'password'"
                            variant="outlined" density="comfortable" prepend-inner-icon="mdi-lock-check-outline"
                            :append-inner-icon="showConfirm ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                            :rules="confirmRules" hide-details="auto" @click:append-inner="showConfirm = !showConfirm"
                            @keydown.enter.prevent="onSubmit" />


                        <div class="mt-3">
                            <v-checkbox v-model="acceptTerms" density="compact" hide-details="auto" :rules="termsRules">
                                <template #label>
                                    <span>
                                        I agree to the
                                        <v-btn variant="text" density="compact" class="px-1"
                                            @click.prevent="onOpenTerms">
                                            Terms
                                        </v-btn>
                                        and
                                        <v-btn variant="text" density="compact" class="px-1"
                                            @click.prevent="onOpenPrivacy">
                                            Privacy Policy
                                        </v-btn>
                                    </span>
                                </template>
                            </v-checkbox>
                        </div>

                        <v-alert v-if="errorMessage" type="error" variant="tonal" class="mt-4" density="comfortable"
                            closable @click:close="errorMessage = ''">
                            {{ errorMessage }}
                        </v-alert>

                        <v-btn class="mt-5" type="submit" color="primary" size="large" block :loading="loading"
                            :disabled="!formValid || loading">
                            Create account
                        </v-btn>

                        <div class="u-divider my-6">
                            <span class="text-body-2 text-medium-emphasis">or continue with</span>
                        </div>

                        <div style="display: flex; gap: 0.5rem; flex-direction: column">
                            <v-btn block variant="outlined" prepend-icon="mdi-google" @click="onSocial('google')">
                                Google
                            </v-btn>

                            <v-btn block variant="outlined" prepend-icon="mdi-microsoft" @click="onSocial('microsoft')">
                                Microsoft
                            </v-btn>
                        </div>

                        <div class="text-center mt-6 text-body-2">
                            <span class="text-medium-emphasis">Already have an account?</span>

                            <v-btn variant="text" class="px-1" @click="goToLogin"> Sign in </v-btn>
                        </div>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script setup>
import axios from 'axios'
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
const formRef = ref(null)
const emailField = ref(null)
const passwordField = ref(null)
const companyField = ref(null)
const phoneNumberField = ref(null)
const confirmField = ref(null)
const formValid = ref(false)
const loading = ref(false)
const errorMessage = ref('')
const errors = ref({})
const fullName = ref('')
const email = ref('')
const company = ref('')
const phone_number = ref('')
const password = ref('')
const confirmPassword = ref('')
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

const phoneRules = [
  (v) => (v?.trim()?.length ?? 0) >= 3 || 'Het telefoonnummer moet minimaal 3 cijfers lang zijn.',
  (v) => /^\d+$/.test(v?.trim() ?? '') || 'Het telefoonnummer mag alleen uit cijfers bestaan.'
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

function focusEmail() {
  emailField.value?.focus?.()
}

function focusCompany() {
  companyField.value?.focus?.()
}

function focusPhoneNumber() {
  phoneNumberField.value?.focus?.()
}

function focusPassword() {
  passwordField.value?.focus?.()
}

function focusConfirm() {
  confirmField.value?.focus?.()
}



async function onSubmit() {
  const { valid } = await formRef.value.validate()
  if (!valid) return
  loading.value = true
  errors.value = {}
  errorMessage.value = ''

  await axios.get('/sanctum/csrf-cookie')

  try {
    await axios.get('/sanctum/csrf-cookie', { withCredentials: true })

    const response = await axios.post(
      '/api/register',
      {
        name: fullName.value,
        email: email.value,
        company: company.value,
        phone_number: phone_number.value,
        password: password.value,
        password_confirmation: confirmPassword.value,
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

function onOpenTerms() {
  console.log('Open terms')
}

function onOpenPrivacy() {
  console.log('Open privacy')
}
</script>

