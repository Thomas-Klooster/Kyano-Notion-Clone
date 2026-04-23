<template>
  <div class="auth-page">
    <div class="auth-container">

      <div class="auth-card card card-elevated card-rounded-2xl">

        <div class="auth-card-head">
          <div class="auth-card-head-bg" aria-hidden="true" />
          <div class="auth-card-head-content">
            <div class="section-kicker">Accountherstel</div>
            <h1 class="auth-title">{{ stageTitle }}</h1>
            <p class="auth-subtitle">{{ stageSubtitle }}</p>
          </div>
        </div>

        <div class="auth-card-body">
          <v-form ref="formRef" v-model="formValid" @submit.prevent="onSubmit">

            <template v-if="stage === STAGES.REQUEST || stage === STAGES.CODE_SENT">
              <div class="auth-field-group">
                <label class="auth-label">E-mailadres</label>
                <v-text-field
                  ref="emailField"
                  v-model.trim="email"
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
                  :readonly="stage === STAGES.CODE_SENT"
                  @keydown.enter.prevent="onSubmit"
                />
              </div>

              <v-alert v-if="successMessage" type="success" variant="tonal" density="comfortable" class="auth-alert">
                {{ successMessage }}
              </v-alert>
              <v-alert v-if="errorMessage" type="error" variant="tonal" density="comfortable" closable class="auth-alert" @click:close="clearMessages">
                {{ errorMessage }}
              </v-alert>

              <button
                type="submit"
                class="auth-submit-btn"
                :class="{ 'auth-submit-btn--loading': loading }"
                :disabled="loading"
              >
                <v-progress-circular v-if="loading" size="18" width="2" indeterminate color="white" />
                <span v-else>{{ stage === STAGES.CODE_SENT ? 'Code opnieuw sturen' : 'Resetcode versturen' }}</span>
              </button>

              <button
                v-if="stage === STAGES.CODE_SENT"
                type="button"
                class="auth-outline-btn"
                :disabled="loading"
                @click="goToVerifyStage"
              >
                Ik heb mijn code ontvangen
              </button>

              <button
                v-if="stage === STAGES.CODE_SENT"
                type="button"
                class="auth-ghost-btn"
                :disabled="loading"
                @click="useAnotherEmail"
              >
                Ander e-mailadres gebruiken
              </button>
            </template>

            <template v-else-if="stage === STAGES.VERIFY">
              <div class="auth-field-group">
                <label class="auth-label">E-mailadres</label>
                <v-text-field
                  :model-value="email"
                  variant="solo-filled"
                  flat
                  density="comfortable"
                  prepend-inner-icon="mdi-email-outline"
                  readonly
                  hide-details
                  class="notion-soft-input"
                />
              </div>

              <div class="auth-field-group">
                <label class="auth-label">Verificatiecode</label>
                <div class="auth-otp-row">
                  <v-text-field
                    v-for="(char, index) in otp"
                    :key="index"
                    :ref="(el) => setOtpRef(el, index)"
                    :model-value="otp[index]"
                    variant="solo-filled"
                    flat
                    density="comfortable"
                    maxlength="1"
                    hide-details
                    class="notion-soft-input otp-input"
                    inputmode="text"
                    autocomplete="one-time-code"
                    @update:model-value="(value) => onOtpInput(index, value)"
                    @keydown.backspace="onOtpBackspace(index)"
                    @paste="onOtpPaste"
                  />
                </div>
              </div>

              <v-alert v-if="errorMessage" type="error" variant="tonal" density="comfortable" closable class="auth-alert" @click:close="clearMessages">
                {{ errorMessage }}
              </v-alert>

              <button
                type="button"
                class="auth-submit-btn"
                :class="{ 'auth-submit-btn--loading': loading }"
                :disabled="loading || otpCode.length !== OTP_LENGTH"
                @click="verifyOtp"
              >
                <v-progress-circular v-if="loading" size="18" width="2" indeterminate color="white" />
                <span v-else>Verifiëren</span>
              </button>

              <button type="button" class="auth-outline-btn" :disabled="loading" @click="backToCodeSent">
                Terug
              </button>

              <p class="auth-footer-text" style="margin-top: 12px;">
                <button type="button" class="auth-link-btn" :disabled="loading" @click="resendCodeFromVerify">
                  Code opnieuw sturen
                </button>
              </p>
            </template>

            <p class="auth-footer-text">
              Wachtwoord onthouden?
              <button type="button" class="auth-link-btn" @click="goToLogin">Inloggen</button>
            </p>

          </v-form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { computed, nextTick, onMounted, ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const STAGES = { REQUEST: "request", CODE_SENT: "codeSent", VERIFY: "verify" };
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
  if (stage.value === STAGES.VERIFY) return "Code verifiëren";
  return "Wachtwoord vergeten";
});

const stageSubtitle = computed(() => {
  if (stage.value === STAGES.REQUEST) return "We sturen een resetcode naar je e-mailadres.";
  if (stage.value === STAGES.CODE_SENT) return `We hebben een code verstuurd naar ${email.value}.`;
  return "Voer de ontvangen code in om verder te gaan.";
});

const emailRules = [
  (v) => !!v || "Het invullen van een email is verplicht.",
  (v) => /.+@.+\..+/.test(v) || "Voer een geldige email in.",
];

onMounted(() => { emailField.value?.focus?.(); });

function clearMessages() { errorMessage.value = ""; successMessage.value = ""; }
function resetOtp() { otp.value = Array.from({ length: OTP_LENGTH }, () => ""); }
function setOtpRef(el, index) { if (el) otpRefs.value[index] = el; }
function focusOtp(index) { nextTick(() => { otpRefs.value[index]?.focus?.(); }); }

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
    await axios.post("http://localhost:8000/api/forgot-password", { email: email.value }, { withCredentials: true });
    stage.value = STAGES.CODE_SENT;
    successMessage.value = "Een verificatiecode is verstuurd naar je e-mailadres.";
  } catch (error) {
    handleRequestError(error, { defaultMessage: "Er is een onverwachte fout opgetreden.", notFoundMessage: "Email niet gevonden." });
  } finally {
    loading.value = false;
  }
}

function goToVerifyStage() { clearMessages(); resetOtp(); stage.value = STAGES.VERIFY; focusOtp(0); }
function backToCodeSent() { clearMessages(); resetOtp(); stage.value = STAGES.CODE_SENT; }
function useAnotherEmail() {
  clearMessages(); resetOtp(); email.value = ""; stage.value = STAGES.REQUEST;
  nextTick(() => { emailField.value?.focus?.(); });
}

function normalizeOtpCharacter(value) { return String(value ?? "").trim().slice(0, 1).toUpperCase(); }

function onOtpInput(index, value) {
  const normalized = normalizeOtpCharacter(value);
  otp.value[index] = normalized;
  if (normalized && index < OTP_LENGTH - 1) focusOtp(index + 1);
}

function onOtpBackspace(index) {
  if (otp.value[index]) { otp.value[index] = ""; return; }
  if (index > 0) { otp.value[index - 1] = ""; focusOtp(index - 1); }
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
  if (stage.value === STAGES.CODE_SENT) goToVerifyStage();
}

async function verifyOtp() {
  clearMessages();
  if (otpCode.value.length !== OTP_LENGTH) { errorMessage.value = "Voer de volledige verificatiecode in."; return; }
  loading.value = true;
  try {
    await axios.post("http://localhost:8000/api/verify-otp", { email: email.value, otp: otpCode.value }, { withCredentials: true });
    router.push({ path: "/auth/reset-password" });
  } catch (error) {
    handleRequestError(error, { defaultMessage: "Er is een onverwachte fout opgetreden.", invalidCodeMessage: "De verificatiecode is ongeldig of verlopen." });
  } finally {
    loading.value = false;
  }
}

function handleRequestError(error, messages = {}) {
  const status = error?.response?.status;
  if (status === 422) { errorMessage.value = messages.notFoundMessage || messages.invalidCodeMessage || "De ingevoerde gegevens zijn ongeldig."; return; }
  if (status === 400 || status === 401) { errorMessage.value = messages.invalidCodeMessage || "De verificatiecode is ongeldig of verlopen."; return; }
  if (status === 429) { errorMessage.value = "Te veel pogingen. Probeer het later opnieuw."; return; }
  errorMessage.value = messages.defaultMessage || "Er is een onverwachte fout opgetreden.";
  console.error(error);
}

function goToLogin() { router.push({ name: "login" }).catch(() => {}); }
</script>
