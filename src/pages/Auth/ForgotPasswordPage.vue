<template>
    <v-row justify="center" class="login-bg">
        <v-col cols="12" sm="10" md="8" lg="4" xl="3">
            <v-card elevation="10" rounded>
                <v-card-item class="pb-0">
                    <div class="d-flex align-center justify-space-between">
                        <div class="d-flex align-center ga-3">
                            <v-avatar size="44" color="primary" variant="tonal">
                                <v-icon icon="mdi-email-lock-outline" />
                            </v-avatar>

                            <div>
                                <div class="text-h6 font-weight-semibold">Forgot password</div>
                                <div class="text-body-2 text-medium-emphasis">
                                    We’ll send a reset link to your email
                                </div>
                            </div>
                        </div>
                    </div>
                </v-card-item>

                <v-card-text class="pt-6">
                    <template v-if="submitted">
                        <v-alert type="success" variant="tonal" density="comfortable" class="mb-4">
                            If an account exists for <strong>{{ email }}</strong>, you’ll receive an email with a reset
                            link.
                        </v-alert>

                        <v-btn color="primary" size="large" block @click="goToLogin">
                            Back to login
                        </v-btn>

                        <v-btn variant="text" block class="mt-2" @click="submitted = false">
                            Try another email
                        </v-btn>
                    </template>

                    <!-- FORM -->
                    <v-form v-else ref="formRef" v-model="formValid" @submit.prevent="onSubmit">
                        <v-text-field ref="emailField" v-model="email" label="Email" placeholder="name@company.com"
                            autocomplete="email" type="email" variant="outlined" density="comfortable"
                            prepend-inner-icon="mdi-email-outline" :rules="emailRules" hide-details="auto" class="mb-3"
                            @keydown.enter.prevent="onSubmit" />

                        <v-alert v-if="errorMessage" type="error" variant="tonal" class="mt-3" density="comfortable"
                            closable @click:close="errorMessage = ''">
                            {{ errorMessage }}
                        </v-alert>

                        <v-btn class="mt-5" type="submit" color="primary" size="large" block :loading="loading"
                            :disabled="!formValid || loading">
                            Send reset link
                        </v-btn>

                        <div class="text-center mt-6 text-body-2">
                            <span class="text-medium-emphasis">Remembered your password?</span>
                            <v-btn variant="text" class="px-1" @click="goToLogin">
                                Sign in
                            </v-btn>
                        </div>

                        <div class="text-center mt-1 text-body-2">
                            <span class="text-medium-emphasis">No account?</span>
                            <v-btn variant="text" class="px-1" @click="goToRegister">
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
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const formRef = ref(null);
const emailField = ref(null);

const formValid = ref(false);
const loading = ref(false);

const email = ref("");
const submitted = ref(false);
const errorMessage = ref("");

const emailRules = [
    (v) => !!v || "Email is required",
    (v) => /.+@.+\..+/.test(v) || "Enter a valid email",
];

onMounted(() => {
    emailField.value?.focus?.();
});

async function onSubmit() {
    //forgot password logic
}

function goToLogin() {
    router.push({ name: "login" }).catch(() => { });
}

function goToRegister() {
    router.push({ name: "register" }).catch(() => { });
}
</script>
