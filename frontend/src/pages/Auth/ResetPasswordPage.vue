<template>
    <v-row justify="center">
        <v-col cols="12" sm="10" md="8" lg="4" xl="3">
            <v-card elevation="10" rounded>
                <v-card-item class="pb-0">
                    <div class="d-flex align-center ga-3">
                        <v-avatar size="44" color="primary" variant="tonal">
                            <v-icon icon="mdi-lock-reset" />
                        </v-avatar>

                        <div>
                            <div class="text-h6 font-weight-semibold">Reset password</div>
                            <div class="text-body-2 text-medium-emphasis">
                                Choose a new password for your account
                            </div>
                        </div>
                    </div>
                </v-card-item>

                <v-card-text class="pt-6">
                    <template v-if="checkingSession">
                        <v-alert type="info" variant="tonal" density="comfortable">
                            Checking your reset session...
                        </v-alert>
                    </template>

                    <template v-else-if="submitted">
                        <v-alert type="success" variant="tonal" density="comfortable" class="mb-4">
                            Your password has been reset. You can now sign in.
                        </v-alert>

                        <v-btn color="primary" size="large" block @click="goToLogin">
                            Go to login
                        </v-btn>
                    </template>

                    <v-form v-else ref="formRef" v-model="formValid" @submit.prevent="onSubmit">
                        <v-text-field
                            ref="passwordField"
                            v-model="password"
                            label="New password"
                            placeholder="••••••••"
                            autocomplete="new-password"
                            :type="showPassword ? 'text' : 'password'"
                            variant="outlined"
                            density="comfortable"
                            prepend-inner-icon="mdi-lock-outline"
                            :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                            :rules="passwordRules"
                            hide-details="auto"
                            class="mb-3"
                            @click:append-inner="showPassword = !showPassword"
                            @keydown.enter.prevent="focusConfirm"
                        />

                        <v-text-field
                            ref="confirmField"
                            v-model="confirmPassword"
                            label="Confirm new password"
                            placeholder="••••••••"
                            autocomplete="new-password"
                            :type="showConfirm ? 'text' : 'password'"
                            variant="outlined"
                            density="comfortable"
                            prepend-inner-icon="mdi-lock-check-outline"
                            :append-inner-icon="showConfirm ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                            :rules="confirmRules"
                            hide-details="auto"
                            @click:append-inner="showConfirm = !showConfirm"
                            @keydown.enter.prevent="onSubmit"
                        />

                        <v-alert
                            v-if="errorMessage"
                            type="error"
                            variant="tonal"
                            class="mt-4"
                            density="comfortable"
                            closable
                            @click:close="errorMessage = ''"
                        >
                            {{ errorMessage }}
                        </v-alert>

                        <v-btn
                            class="mt-5"
                            type="submit"
                            color="primary"
                            size="large"
                            block
                            :loading="loading"
                            :disabled="!formValid || loading"
                        >
                            Reset password
                        </v-btn>

                        <div class="text-center mt-6 text-body-2">
                            <span class="text-medium-emphasis">Back to</span>
                            <v-btn variant="text" class="px-1" @click="goToLogin">
                                Sign in
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
import { computed, nextTick, onMounted, ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const formRef = ref(null);
const passwordField = ref(null);
const confirmField = ref(null);

const formValid = ref(false);
const loading = ref(false);
const checkingSession = ref(true);

const password = ref("");
const confirmPassword = ref("");

const showPassword = ref(false);
const showConfirm = ref(false);

const submitted = ref(false);
const errorMessage = ref("");

const passwordRules = [
    (v) => !!v || "Password is required",
    (v) => (v?.length ?? 0) >= 8 || "Password must be at least 8 characters",
    (v) => /[A-Z]/.test(v) || "Password must contain at least one uppercase letter",
    (v) => /[a-z]/.test(v) || "Password must contain at least one lowercase letter",
    (v) => /[\d\W]/.test(v) || "Password must contain at least one number or special character",
];

const confirmRules = computed(() => [
    (v) => !!v || "Please confirm your password",
    (v) => v === password.value || "Passwords do not match",
]);

onMounted(async () => {
    await validateResetSession();
});

async function validateResetSession() {
    errorMessage.value = "";

    try {
        await axios.get("http://localhost:8000/api/reset-password/session", {
            withCredentials: true,
        });

        checkingSession.value = false;

        await nextTick();
        passwordField.value?.focus?.();
    } catch (error) {
        checkingSession.value = false;
        router.push({ name: "forgot-password" }).catch(() => {});
    }
}

function focusConfirm() {
    confirmField.value?.focus?.();
}

async function onSubmit() {
    errorMessage.value = "";

    const result = await formRef.value?.validate?.();
    if (result?.valid === false) return;

    loading.value = true;

    try {
        await axios.post(
            "http://localhost:8000/api/reset-password",
            {
                password: password.value,
                password_confirmation: confirmPassword.value,
            },
            {
                withCredentials: true,
            }
        );

        submitted.value = true;
    } catch (error) {
        const status = error?.response?.status;

        if (status === 401 || status === 403) {
            errorMessage.value = "Your reset session is invalid or expired. Please start again.";
        } else if (status === 422) {
            errorMessage.value = "Please check your password and try again.";
        } else {
            errorMessage.value = "Could not reset password. Please try again.";
        }
    } finally {
        loading.value = false;
    }
}

function goToLogin() {
    router.push({ name: "login" }).catch(() => {});
}
</script>