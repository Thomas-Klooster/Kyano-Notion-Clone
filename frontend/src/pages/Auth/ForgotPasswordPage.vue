<template>
    <v-row justify="center" class="login-bg">
        <v-col cols="12" sm="10" md="8" lg="4" xl="3">
            <v-card elevation="10" rounded>
                <v-card-item class="pb-0">
                    <div class="d-flex align-center ga-3">
                        <v-avatar size="44" color="primary" variant="tonal">
                            <v-icon
                                :icon="stage === 'verify' ? 'mdi-shield-key-outline' : 'mdi-email-lock-outline'" />
                        </v-avatar>

                        <div>
                            <div class="text-h6 font-weight-semibold">
                                {{ stage === 'verify' ? 'Verify code' : 'Forgot password' }}
                            </div>

                            <div class="text-body-2 text-medium-emphasis">
                                <template v-if="stage === 'request'">
                                    We’ll send a reset code to your email
                                </template>

                                <template v-else-if="stage === 'codeSent'">
                                    We sent a code to {{ email }}
                                </template>

                                <template v-else>
                                    Enter the 6-digit code you received
                                </template>
                            </div>
                        </div>
                    </div>
                </v-card-item>

                <v-card-text class="pt-6">
                    <v-form ref="formRef" v-model="formValid" @submit.prevent="handlePrimaryAction">
                        <!-- STAGE 1 + 2 -->
                        <template v-if="stage === 'request' || stage === 'codeSent'">
                            <v-text-field ref="emailField" v-model="email" label="Email"
                                placeholder="name@company.com" autocomplete="email" type="email"
                                variant="outlined" density="comfortable" prepend-inner-icon="mdi-email-outline"
                                :rules="emailRules" hide-details="auto" class="mb-3"
                                :readonly="stage === 'codeSent'" @keydown.enter.prevent="handlePrimaryAction" />

                            <v-alert v-if="successMessage" type="success" variant="tonal" density="comfortable"
                                class="mb-3">
                                {{ successMessage }}
                            </v-alert>

                            <v-alert v-if="errorMessage" type="error" variant="tonal" density="comfortable"
                                class="mb-3" closable @click:close="errorMessage = ''">
                                {{ errorMessage }}
                            </v-alert>

                            <v-btn class="mt-2" type="submit" color="primary" size="large" block :loading="loading"
                                :disabled="loading || !formValid">
                                {{ stage === 'codeSent' ? 'Send code again' : 'Send reset code' }}
                            </v-btn>

                            <v-btn v-if="stage === 'codeSent'" class="mt-3" variant="outlined" color="primary"
                                size="large" block @click="goToVerifyStage">
                                I've received my code
                            </v-btn>

                            <v-btn v-if="stage === 'codeSent'" variant="text" block class="mt-2"
                                @click="useAnotherEmail">
                                Use another email
                            </v-btn>
                        </template>

                        <!-- STAGE 3 -->
                        <template v-else-if="stage === 'verify'">
                            <v-text-field v-model="email" label="Email" variant="outlined" density="comfortable"
                                prepend-inner-icon="mdi-email-outline" readonly class="mb-3" />

                            <div class="text-subtitle-2 mb-2">Verification code</div>

                            <div class="d-flex ga-2 mb-4">
                                <v-text-field v-for="(digit, index) in otp" :key="index"
                                    :ref="(el) => setOtpRef(el, index)" v-model="otp[index]" variant="outlined"
                                    density="comfortable" maxlength="1" hide-details class="otp-input" @input="onOtpInput(index, $event)"
                                    @keydown.backspace="onOtpBackspace(index, $event)"
                                    @paste="onOtpPaste" />
                            </div>

                            <v-alert v-if="errorMessage" type="error" variant="tonal" density="comfortable"
                                class="mb-3" closable @click:close="errorMessage = ''">
                                {{ errorMessage }}
                            </v-alert>

                            <v-btn color="primary" size="large" block :loading="loading"
                                :disabled="loading || otpCode.length !== 6" @click="verifyOtp">
                                Verify
                            </v-btn>

                            <v-btn variant="outlined" color="primary" block class="mt-2" :disabled="loading" @click="stage = 'codeSent'">
                                Back
                            </v-btn>

                            <div class="text-center mt-3">
                                <v-btn variant="text" :disabled="loading" @click="sendResetCode">
                                    Send code again
                                </v-btn>
                            </div>
                        </template>

                        <div class="text-center mt-6 text-body-2">
                            <span class="text-medium-emphasis">Remembered your password?</span>
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
const emailField = ref(null);

const formValid = ref(false);
const loading = ref(false);
const errors = ref({});
const email = ref("");
const errorMessage = ref("");
const successMessage = ref("");

const stage = ref("request"); // request | codeSent | verify

const otp = ref(["", "", "", "", "", ""]);
const otpRefs = ref([]);

const otpCode = computed(() => otp.value.join(""));

const emailRules = [
    (v) => !!v || "Het invullen van een email is verplicht.",
    (v) => /.+@.+\..+/.test(v) || "Voer een geldige email in.",
];

onMounted(() => {
    emailField.value?.focus?.();
});

function setOtpRef(el, index) {
    if (el) {
        otpRefs.value[index] = el;
    }
}

function focusOtp(index) {
    nextTick(() => {
        otpRefs.value[index]?.focus?.();
    });
}

function clearMessages() {
    errors.value = {};
    errorMessage.value = "";
    successMessage.value = "";
}

function resetOtp() {
    otp.value = ["", "", "", "", "", ""];
}

async function handlePrimaryAction() {
    if (stage.value === "request" || stage.value === "codeSent") {
        await sendResetCode();
    }
}

async function sendResetCode() {
    clearMessages();

    const { valid } = await formRef.value.validate();
    if (!valid) return;

    loading.value = true;

    try {
        await axios.post("http://localhost:8000/api/forgot-password", {
            email: email.value,
        });

        stage.value = "codeSent";
        successMessage.value = "A verification code has been sent to your email address.";
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {};
            errorMessage.value = "Email niet gevonden.";
        } else {
            errorMessage.value = "Er is een onverwachte fout opgetreden.";
            console.error(error);
        }
    } finally {
        loading.value = false;
    }
}

function goToVerifyStage() {
    clearMessages();
    stage.value = "verify";
    resetOtp();
    focusOtp(0);
}

function useAnotherEmail() {
    clearMessages();
    stage.value = "request";
    resetOtp();
    nextTick(() => {
        emailField.value?.focus?.();
    });
}

function onOtpInput(index, event) {
    const rawValue = event?.target?.value ?? otp.value[index] ?? "";
    const value = rawValue.slice(0, 1).toUpperCase();

    otp.value[index] = value;

    if (value && index < 5) {
        focusOtp(index + 1);
    }
}

function onOtpBackspace(index) {
    if (otp.value[index]) {
        otp.value[index] = "";
        return;
    }

    if (index > 0) {
        otp.value[index - 1] = "";
        focusOtp(index - 1);
    }
}

function onOtpPaste(event) {
    const pasted = event.clipboardData?.getData("text")?.trim().slice(0, 6).toUpperCase() ?? "";
    if (!pasted) return;

    event.preventDefault();

    const chars = pasted.split("");
    otp.value = [
        chars[0] ?? "",
        chars[1] ?? "",
        chars[2] ?? "",
        chars[3] ?? "",
        chars[4] ?? "",
        chars[5] ?? "",
    ];

    const nextEmptyIndex = otp.value.findIndex((digit) => !digit);
    focusOtp(nextEmptyIndex === -1 ? 5 : nextEmptyIndex);
}

async function verifyOtp() {
    clearMessages();

    if (otpCode.value.length !== 6) {
        errorMessage.value = "Voer de volledige 6-cijferige code in.";
        return;
    }

    loading.value = true;

    try {
        const response = await axios.post("http://localhost:8000/api/verify-reset-otp", {
            email: email.value,
            otp: otpCode.value,
        });

        router.push({
            path: "/reset-password",
            query: {
                email: email.value,
                otp: otpCode.value,
                token: response.data?.token ?? "",
            },
        });
    } catch (error) {
        if (error.response?.status === 422 || error.response?.status === 400) {
            errorMessage.value = "De verificatiecode is ongeldig of verlopen.";
        } else {
            errorMessage.value = "Er is een onverwachte fout opgetreden.";
            console.error(error);
        }
    } finally {
        loading.value = false;
    }
}

function goToLogin() {
    router.push({ name: "login" }).catch(() => {});
}

function goToRegister() {
    router.push({ name: "register" }).catch(() => {});
}
</script>

<style scoped>
.otp-input {
    max-width: 56px;
    margin-inline: auto;
}

.otp-input :deep(input) {
    text-align: center;
    font-size: 1.25rem;
    font-weight: 600;
}
</style>