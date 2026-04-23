<template>
  <div class="auth-page">
    <div class="auth-container">
      <div class="auth-card card card-elevated card-rounded-2xl">

        <div class="auth-card-head">
          <div class="auth-card-head-bg" aria-hidden="true" />
          <div class="auth-card-head-content">
            <div class="section-kicker">Welkom terug</div>
            <h1 class="auth-title">Inloggen</h1>
            <p class="auth-subtitle">Voer je gegevens in om verder te gaan.</p>
          </div>
        </div>

        <div class="auth-card-body">
          <v-form ref="formRef" v-model="formValid" @submit.prevent="onSubmit">

            <div class="auth-field-group">
              <label class="auth-label">E-mailadres</label>
              <v-text-field
                v-model="email"
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

            <div class="auth-field-group">
              <div class="auth-label-row">
                <label class="auth-label">Wachtwoord</label>
                <button type="button" class="auth-link-btn" @click="onForgotPassword">
                  Wachtwoord vergeten?
                </button>
              </div>
              <v-text-field
                ref="passwordField"
                v-model="password"
                placeholder="••••••••"
                autocomplete="current-password"
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
                @keydown.enter="onSubmit"
              />
            </div>

            <div class="auth-remember-row">
              <label class="auth-checkbox-label">
                <v-checkbox v-model="remember" density="compact" hide-details class="auth-checkbox" />
                <span>Onthoud mij</span>
              </label>
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
              <span v-else>Inloggen</span>
            </button>

            <div class="u-divider auth-divider">
              <span class="auth-divider-text">of ga verder met</span>
            </div>

            <div class="auth-social-btns">
              <button type="button" class="auth-social-btn" @click="onSocial('google')">
                <v-icon size="18">mdi-google</v-icon>
                Google
              </button>
              <button type="button" class="auth-social-btn" @click="onSocial('microsoft')">
                <v-icon size="18">mdi-microsoft</v-icon>
                Microsoft
              </button>
            </div>

            <p class="auth-footer-text">
              Nog geen account?
              <button type="button" class="auth-link-btn" @click="onGoToRegister">Aanmaken</button>
            </p>

          </v-form>
        </div>
      </div>
    </div>
  </div>
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
  (v) => !!v || "Het invullen van een email is verplicht.",
  (v) => /.+@.+\..+/.test(v) || "Voer een emailadres in",
];

const passwordRules = [
  (v) => !!v || "Password is required",
  (v) => (v?.length ?? 0) >= 8 || "Het wachtwoord moet minimaal 8 tekens lang zijn.",
  (v) => /[A-Z]/.test(v) || "Het wachtwoord moet ten minste één hoofdletter bevatten.",
  (v) => /[a-z]/.test(v) || "Het wachtwoord moet ten minste één kleine letter bevatten.",
  (v) => /[\d\W]/.test(v) || "Het wachtwoord moet ten minste één cijfer of speciaal teken bevatten.",
];

async function onSubmit() {
  const { valid } = await formRef.value.validate();
  if (!valid) return;

  loading.value = true;
  errors.value = {};
  errorMessage.value = "";

  try {
    await axios.get("/sanctum/csrf-cookie");
    const response = await axios.post("/api/login", {
      email: email.value,
      password: password.value,
      remember: remember.value,
    });
    console.log("Ingelogd:", response.data);
    router.push({ name: "Dashboard" });
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors ?? {};
      errorMessage.value = "Oeps, er is iets verkeerds gegaan..";
    } else {
      errorMessage.value = "Onjuiste gegevens.";
      console.error(error);
    }
  } finally {
    loading.value = false;
  }
}

function onForgotPassword() {
  router.push({ name: "forgot-password" }).catch(() => {});
}

function onGoToRegister() {
  router.push({ name: "register" }).catch(() => {});
}

function onSocial(provider) {
  // social login logic
}
</script>
