<template>
    <v-row justify="center" class="login-bg">
        <v-col cols="12" sm="10" md="8" lg="4" xl="3">
            <v-card elevation="10" rounded>
                <v-card-item class="pb-0">
                    <div class="d-flex align-center ga-3">
                        <v-avatar size="44" color="primary" variant="tonal">
                            <v-icon
                                :icon="stage === STAGES.VERIFY ? 'mdi-shield-key-outline' : 'mdi-email-lock-outline'" />
                        </v-avatar>

                        <div>
                            <div class="text-h6 font-weight-semibold">
                                {{ stageTitle }}
                            </div>

                            <div class="text-body-2 text-medium-emphasis">
                                {{ stageSubtitle }}
                            </div>
                        </div>
                    </div>
                </v-card-item>

                <v-card-text class="pt-6">
                    <v-form ref="formRef" v-model="formValid" @submit.prevent="onSubmit">
                        <template v-if="stage === STAGES.REQUEST || stage === STAGES.CODE_SENT">
                            <v-text-field ref="emailField" v-model.trim="email" label="Email"
                                placeholder="name@company.com" autocomplete="email" type="email" variant="outlined"
                                density="comfortable" prepend-inner-icon="mdi-email-outline" :rules="emailRules"
                                hide-details="auto" class="mb-3" :readonly="stage === STAGES.CODE_SENT"
                                @keydown.enter.prevent="onSubmit" />

                            <v-alert v-if="successMessage" type="success" variant="tonal" density="comfortable"
                                class="mb-3">
                                {{ successMessage }}
                            </v-alert>

                            <v-alert v-if="errorMessage" type="error" variant="tonal" density="comfortable" class="mb-3"
                                closable @click:close="clearMessages">
                                {{ errorMessage }}
                            </v-alert>

                            <v-btn class="mt-2" type="submit" color="primary" size="large" block :loading="loading"
                                :disabled="loading">
                                {{ stage === STAGES.CODE_SENT ? "Send code again" : "Send reset code" }}
                            </v-btn>

                            <v-btn v-if="stage === STAGES.CODE_SENT" class="mt-3" variant="outlined" color="primary"
                                size="large" block :disabled="loading" @click="goToVerifyStage">
                                I've received my code
                            </v-btn>

                            <v-btn v-if="stage === STAGES.CODE_SENT" variant="text" block class="mt-2"
                                :disabled="loading" @click="useAnotherEmail">
                                Use another email
                            </v-btn>
                        </template>

                        <template v-else-if="stage === STAGES.VERIFY">
                            <v-text-field :model-value="email" label="Email" variant="outlined" density="comfortable"
                                prepend-inner-icon="mdi-email-outline" readonly class="mb-3" />

                            <div class="text-subtitle-2 mb-2">Verification code</div>

                            <div class="d-flex ga-2 mb-4">
                                <v-text-field v-for="(char, index) in otp" :key="index"
                                    :ref="(el) => setOtpRef(el, index)" :model-value="otp[index]" variant="outlined"
                                    density="comfortable" maxlength="1" hide-details class="otp-input" inputmode="text"
                                    autocomplete="one-time-code"
                                    @update:model-value="(value) => onOtpInput(index, value)"
                                    @keydown.backspace="onOtpBackspace(index)" @paste="onOtpPaste" />
                            </div>

                            <v-alert v-if="errorMessage" type="error" variant="tonal" density="comfortable" class="mb-3"
                                closable @click:close="clearMessages">
                                {{ errorMessage }}
                            </v-alert>

                            <v-btn color="primary" size="large" block :loading="loading"
                                :disabled="loading || otpCode.length !== OTP_LENGTH" @click="verifyOtp">
                                Verify
                            </v-btn>

                            <v-btn variant="outlined" color="primary" block class="mt-2" :disabled="loading"
                                @click="backToCodeSent">
                                Back
                            </v-btn>

                            <div class="text-center mt-3">
                                <v-btn variant="text" :disabled="loading" @click="resendCodeFromVerify">
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

const STAGES = {
    REQUEST: "request",
    CODE_SENT: "codeSent",
    VERIFY: "verify",
};

const OTP_LENGTH = 6;

const formRef = ref(null);
const emailField = ref(null);

const formValid = ref(false);
const loading = ref(false);
const email = ref("");
const errorMessage = ref("");
const successMessage = ref("");
const stage = ref(STAGES.REQUEST);

const otp = ref(Array.from({ length: OTP_LENGTH }, () => ""));
const otpRefs = ref([]);

const otpCode = computed(() => otp.value.join(""));

const stageTitle = computed(() => {
    if (stage.value === STAGES.VERIFY) return "Verify code";
    return "Forgot password";
});

const stageSubtitle = computed(() => {
    if (stage.value === STAGES.REQUEST) {
        return "We’ll send a reset code to your email";
    }

    if (stage.value === STAGES.CODE_SENT) {
        return `We sent a code to ${email.value}`;
    }

    return "Enter the code you received to continue";
});

const emailRules = [
    (v) => !!v || "Het invullen van een email is verplicht.",
    (v) => /.+@.+\..+/.test(v) || "Voer een geldige email in.",
];

onMounted(() => {
    emailField.value?.focus?.();
});

function clearMessages() {
    errorMessage.value = "";
    successMessage.value = "";
}

function resetOtp() {
    otp.value = Array.from({ length: OTP_LENGTH }, () => "");
}

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

async function validateEmailForm() {
    if (!formRef.value) return false;
    const result = await formRef.value.validate();
    return !!result.valid;
}

async function onSubmit() {
    if (stage.value === STAGES.REQUEST || stage.value === STAGES.CODE_SENT) {
        await sendResetCode();
    }
}

async function sendResetCode() {
    clearMessages();

    const valid = await validateEmailForm();
    if (!valid) return;

    loading.value = true;

    try {
        await axios.post(
            "http://localhost:8000/api/forgot-password",
            { email: email.value },
            { withCredentials: true }
        );

        stage.value = STAGES.CODE_SENT;
        successMessage.value = "A verification code has been sent to your email address.";
    } catch (error) {
        handleRequestError(error, {
            defaultMessage: "Er is een onverwachte fout opgetreden.",
            notFoundMessage: "Email niet gevonden.",
        });
    } finally {
        loading.value = false;
    }
}

function goToVerifyStage() {
    clearMessages();
    resetOtp();
    stage.value = STAGES.VERIFY;
    focusOtp(0);
}

function backToCodeSent() {
    clearMessages();
    resetOtp();
    stage.value = STAGES.CODE_SENT;
}

function useAnotherEmail() {
    clearMessages();
    resetOtp();
    email.value = "";
    stage.value = STAGES.REQUEST;

    nextTick(() => {
        emailField.value?.focus?.();
    });
}

function normalizeOtpCharacter(value) {
    return String(value ?? "")
        .trim()
        .slice(0, 1)
        .toUpperCase();
}

function onOtpInput(index, value) {
    const normalized = normalizeOtpCharacter(value);
    otp.value[index] = normalized;

    if (normalized && index < OTP_LENGTH - 1) {
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
    const pasted = event.clipboardData?.getData("text") ?? "";
    const normalized = pasted.trim().toUpperCase().slice(0, OTP_LENGTH);

    if (!normalized) return;

    event.preventDefault();

    const chars = normalized.split("");
    otp.value = Array.from({ length: OTP_LENGTH }, (_, index) => chars[index] ?? "");

    const nextEmptyIndex = otp.value.findIndex((char) => !char);
    focusOtp(nextEmptyIndex === -1 ? OTP_LENGTH - 1 : nextEmptyIndex);
}

async function resendCodeFromVerify() {
    stage.value = STAGES.CODE_SENT;
    await sendResetCode();
    if (stage.value === STAGES.CODE_SENT) {
        goToVerifyStage();
    }
}

async function verifyOtp() {
    clearMessages();

    if (otpCode.value.length !== OTP_LENGTH) {
        errorMessage.value = "Voer de volledige verificatiecode in.";
        return;
    }

    loading.value = true;

    try {
        await axios.post(
            "http://localhost:8000/api/verify-otp",
            {
                email: email.value,
                otp: otpCode.value,
            },
            {
                withCredentials: true,
            }
        );

        router.push({ path: "/auth/reset-password" });
    } catch (error) {
        handleRequestError(error, {
            defaultMessage: "Er is een onverwachte fout opgetreden.",
            invalidCodeMessage: "De verificatiecode is ongeldig of verlopen.",
        });
    } finally {
        loading.value = false;
    }
}

function handleRequestError(error, messages = {}) {
    const status = error?.response?.status;

    if (status === 422) {
        errorMessage.value =
            messages.notFoundMessage ||
            messages.invalidCodeMessage ||
            "De ingevoerde gegevens zijn ongeldig.";
        return;
    }

    if (status === 400 || status === 401) {
        errorMessage.value =
            messages.invalidCodeMessage ||
            "De verificatiecode is ongeldig of verlopen.";
        return;
    }

    if (status === 429) {
        errorMessage.value = "Te veel pogingen. Probeer het later opnieuw.";
        return;
    }

    errorMessage.value = messages.defaultMessage || "Er is een onverwachte fout opgetreden.";
    console.error(error);
}

function goToLogin() {
    router.push({ name: "login" }).catch(() => { });
}
</script>

<style scoped>
.otp-input {
    max-width: 56px;
}

.otp-input :deep(input) {
    text-align: center;
    font-size: 1.25rem;
    font-weight: 600;
    text-transform: uppercase;
}
</style>