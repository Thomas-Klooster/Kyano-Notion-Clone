<template>
    <v-row justify="center">
        <v-col cols="12" sm="10" md="8" lg="4" xl="3">
            <v-card elevation="10" rounded>
                <v-card-item class="pb-0">
                    <div class="d-flex align-center justify-space-between">
                        <div class="d-flex align-center ga-3">
                            <v-avatar size="44" color="primary" variant="tonal">
                                <v-icon icon="mdi-shield-lock-outline" />
                            </v-avatar>

                            <div>
                                <div class="text-h6 font-weight-semibold">Welcome back</div>
                                <div class="text-body-2 text-medium-emphasis">
                                    Sign in to continue
                                </div>
                            </div>
                        </div>
                    </div>
                </v-card-item>

                <v-card-text class="pt-6">
                    <v-form ref="formRef" v-model="formValid" @submit.prevent="onSubmit">
                        <v-text-field v-model="email" label="Email" placeholder="name@company.com" autocomplete="email"
                            type="email" variant="outlined" density="comfortable" prepend-inner-icon="mdi-email-outline"
                            :rules="emailRules" class="mb-3" hide-details="auto" @keydown.enter="focusPassword" />

                        <v-text-field ref="passwordField" v-model="password" label="Password" placeholder="••••••••"
                            autocomplete="current-password" :type="showPassword ? 'text' : 'password'"
                            variant="outlined" density="comfortable" prepend-inner-icon="mdi-lock-outline"
                            :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                            :rules="passwordRules" hide-details="auto"
                            @click:append-inner="showPassword = !showPassword" @keydown.enter="onSubmit" />

                        <div class="d-flex align-center justify-space-between mt-3">
                            <v-checkbox v-model="remember" label="Remember me" density="compact" hide-details />
                            <v-btn variant="text" density="comfortable" class="px-1" @click="onForgotPassword">
                                Forgot password?
                            </v-btn>
                        </div>

                        <v-alert v-if="errorMessage" type="error" variant="tonal" class="mt-4" density="comfortable"
                            closable @click:close="errorMessage = ''">
                            {{ errorMessage }}
                        </v-alert>

                        <v-btn class="mt-5" type="submit" color="primary" size="large" block :loading="loading"
                            :disabled="!formValid || loading">
                            Sign in
                        </v-btn>

                        <div class="divider my-6">
                            <span class="text-body-2 text-medium-emphasis">or continue with</span>
                        </div>

                        <div style="display: flex; gap: .5rem; flex-direction: column;">
                            <v-btn block variant="outlined" prepend-icon="mdi-google" @click="onSocial('google')">
                                Google
                            </v-btn>
                            <v-btn block variant="outlined" prepend-icon="mdi-microsoft" @click="onSocial('microsoft')">
                                Microsoft
                            </v-btn>
                        </div>

                        <div class="text-center mt-6 text-body-2">
                            <span class="text-medium-emphasis">No account?</span>
                            <v-btn variant="text" class="px-1" @click="onGoToRegister">
                                Create one
                            </v-btn>
                        </div>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script setup>
import axios from "axios";
import { ref } from "vue";
import { useRouter } from "vue-router";

const passwordField = ref(null);
const formRef = ref(null);
const formValid = ref(false);
const loading = ref(false);
const errors = ref({});
const email = ref("");
const password = ref("");
const remember = ref(true);
const showPassword = ref(false);
const errorMessage = ref("");
const router = useRouter();

const emailRules = [
    (v) => !!v || "Email is required",
    (v) => /.+@.+\..+/.test(v) || "Enter a valid email",
];

const passwordRules = [
    (v) => !!v || "Password is required",
    (v) => (v?.length ?? 0) >= 8 || "Password must be at least 8 characters",
    (v) => /[A-Z]/.test(v) || "Password must contain at least one uppercase letter",
    (v) => /[a-z]/.test(v) || "Password must contain at least one lowercase letter",
    (v) =>
        /[\d\W]/.test(v) ||
        "Password must contain at least one number or special character",
];

async function onSubmit() {
    const { valid } = await
    formRef.value.validate()

    if (!valid) return

    loading.value = true 
    errors.value = {}
    errorMessage.value = ''

        try {
        await axios.get('http://localhost:8000/sanctum/csrf-cookie');
            const response =

        await axios.post('http://localhost:8000/api/login', {
            email: email.value,
            password: password.value,
            remember: remember.value,
        })

        console.log('Ingelogd:', response.data)

        router.push({ name: 'Dashboard' })
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {}
            errorMessage.value = 'Invalid Credentials.'
        } else {
            errorMessage.value = 'An unexpected error occurred.'
            console.error(error)
        } 
        } finally {
        loading.value = false
    }


}

function focusPassword() {
    passwordField.value?.focus?.();
}

function onForgotPassword() {
    router.push({ name: "forgot-password" }).catch(() => { });
}

function onGoToRegister() {
    router.push({ name: "register" }).catch(() => { });
}

function onSocial(provider) {
    // social login logic
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
    content: "";
    height: 1px;
    background: rgba(0, 0, 0, 0.12);
}
</style>