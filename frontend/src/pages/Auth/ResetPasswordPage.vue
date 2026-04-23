<template>
  <div class="auth-page">
    <div class="auth-container">
      <div class="auth-card card card-elevated card-rounded-2xl">

        <div class="auth-card-head">
          <div class="auth-card-head-bg" aria-hidden="true" />
          <div class="auth-card-head-content">
            <div class="section-kicker">Accountherstel</div>
            <h1 class="auth-title">Nieuw wachtwoord</h1>
            <p class="auth-subtitle">Kies een nieuw wachtwoord voor je account.</p>
          </div>
        </div>

        <div class="auth-card-body">

          <template v-if="checkingSession">
            <v-alert type="info" variant="tonal" density="comfortable" class="auth-alert">
              Je herstel-sessie wordt gecontroleerd...
            </v-alert>
          </template>

          <template v-else-if="submitted">
            <v-alert type="success" variant="tonal" density="comfortable" class="auth-alert">
              Je wachtwoord is opnieuw ingesteld. Je kunt nu inloggen.
            </v-alert>
            <button type="button" class="auth-submit-btn" @click="goToLogin">
              Naar inloggen
            </button>
          </template>

          <v-form v-else ref="formRef" v-model="formValid" @submit.prevent="onSubmit">

            <div class="auth-field-group">
              <label class="auth-label">Nieuw wachtwoord</label>
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
                v-model="confirmPassword"
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
              <span v-else>Wachtwoord opslaan</span>
            </button>

            <p class="auth-footer-text">
              Terug naar
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

onMounted(async () => { await validateResetSession(); });

async function validateResetSession() {
  errorMessage.value = "";
  try {
    await axios.get("http://localhost:8000/api/reset-password/session", { withCredentials: true });
    checkingSession.value = false;
    await nextTick();
    passwordField.value?.focus?.();
  } catch (error) {
    checkingSession.value = false;
    router.push({ name: "forgot-password" }).catch(() => {});
  }
}

function focusConfirm() { confirmField.value?.focus?.(); }

async function onSubmit() {
  errorMessage.value = "";
  const result = await formRef.value?.validate?.();
  if (result?.valid === false) return;
  loading.value = true;
  try {
    await axios.post(
      "http://localhost:8000/api/reset-password",
      { password: password.value, password_confirmation: confirmPassword.value },
      { withCredentials: true }
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

function goToLogin() { router.push({ name: "login" }).catch(() => {}); }
</script>
