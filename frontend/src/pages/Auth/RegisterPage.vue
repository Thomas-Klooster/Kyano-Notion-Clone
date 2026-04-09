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

                        <div class="divider my-6">
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

<!-- API CONNECTION -->
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
const password = ref('')
const confirmPassword = ref('')
const acceptTerms = ref(false)
const showPassword = ref(false)
const showConfirm = ref(false)
const router = useRouter()

const nameRules = [
    (v) => !!v || 'Name is required',
    (v) => (v?.trim()?.length ?? 0) >= 2 || 'Name must be at least 2 characters',
]

const emailRules = [
    (v) => !!v || 'Email is required',
    (v) => /.+@.+\..+/.test(v) || 'Enter a valid email',
]

const passwordRules = [
    (v) => !!v || 'Password is required',
    (v) => (v?.length ?? 0) >= 8 || 'Password must be at least 8 characters',
    (v) => /[A-Z]/.test(v) || 'Must contain an uppercase letter',
    (v) => /[a-z]/.test(v) || 'Must contain a lowercase letter',
    (v) => /[\d\W]/.test(v) || 'Must contain a number or special character',
]

const confirmRules = computed(() => [
    (v) => !!v || 'Please confirm your password',
    (v) => v === password.value || 'Passwords do not match',
])
const termsRules = [(v) => v === true || 'You must accept the terms to continue']

function focusEmail() {
    emailField.value?.focus?.()
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

    try {
        const response = await axios.post('/api/register', {
            name: fullName.value,
            email: email.value,
            password: password.value,
            password_confirmation: confirmPassword.value,
        })

        console.log('Registered:', response.data)

        router.push({ name: 'login' })
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {}
            errorMessage.value = 'Please check the fields and try again.'
        } else {
            errorMessage.value = 'An unexpected error occurred.'
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
    router.push({ name: 'login' }).catch(() => { })
}

function onOpenTerms() {
    console.log('Open terms')
}

function onOpenPrivacy() {
    console.log('Open privacy')
}
</script>

<style scoped>
.divider {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    gap: 12px;
}

.divider::before,
.divider::after {
    content: '';
    height: 1px;
    background: rgba(0, 0, 0, 0.12);
}
</style>