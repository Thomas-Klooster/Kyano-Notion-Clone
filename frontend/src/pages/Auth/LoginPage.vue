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
              <v-text-field v-model="email" placeholder="naam@bedrijf.com"  autocomplete="email" type="email"
                variant="solo-filled" flat density="comfortable" prepend-inner-icon="mdi-email-outline"
                :rules="emailRules" hide-details="auto" class="notion-soft-input" @keydown.enter.prevent="focusPassword" />
            </div>

            <div class="auth-field-group">
              <div class="auth-label-row">
                <label class="auth-label">Wachtwoord</label>
                <button type="button" class="auth-link-btn" @click="onForgotPassword">
                  Wachtwoord vergeten?
                </button>
              </div>
              <v-text-field v-model="password" ref="passwordField" placeholder="••••••••" autocomplete="current-password"
                :type="showPassword ? 'text' : 'password'" variant="solo-filled" flat density="comfortable"
                prepend-inner-icon="mdi-lock-outline"
                :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'" hide-details="auto"
                class="notion-soft-input" @click:append-inner="showPassword = !showPassword"
                @keydown.enter="onSubmit" />
            </div>

            <div class="auth-remember-row">
              <label class="auth-checkbox-label">
                <v-checkbox v-model="remember" density="compact" hide-details class="auth-checkbox" />
                <span>Onthoud mij</span>
              </label>
            </div>

            <v-alert v-if="errorMessage" type="error" variant="tonal" density="comfortable" closable class="auth-alert"
              @click:close="errorMessage = ''">
              {{ errorMessage }}
            </v-alert>

            <button type="submit" class="auth-submit-btn" :class="{ 'auth-submit-btn--loading': loading }"
              :disabled="!formValid || loading">
              <v-progress-circular v-if="loading" size="18" width="2" indeterminate color="white" />
              <span v-else>Inloggen</span>
            </button>
            <p class="auth-footer-text">
              Nog geen account?
              <button type="button" class="auth-link-btn" @click="onGoToRegister">Aanmaken</button>
            </p>

          </v-form>
        </div>
      </div>
    </div>
    <div class="auth-hero">
      <h1 class="auth-hero-text">
        <span class="auth-hero-word">Kyano</span> Knowledgebase
      </h1>
    </div>

  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { login } from "@/services/authService";

const auth = useAuthStore();
const formRef = ref(null);
const formValid = ref(false);
const loading = ref(false);
const email = ref("");
const password = ref("");
const passwordField = ref(null)
const remember = ref(true);
const showPassword = ref(false);
const errorMessage = ref("");
const router = useRouter();


function focusPassword() { passwordField.value?.focus?.() }


const emailRules = [
  (v) => !!v || "Het invullen van een email is verplicht.",
  (v) => /.+@.+\..+/.test(v) || "Voer een emailadres in",
];


const onSubmit = async () => {
  const { valid } = await formRef.value.validate();
  if (!valid) return;

  loading.value = true;
  errorMessage.value = '';

  try {
    const user = await login(email.value, password.value);
    auth.setUser(user);
    router.push({ name: 'Dashboard' });
  } catch (err) {
    // errorMessage.value = err.response?.data?.message
    errorMessage.value = 'Ongeldige inloggegevens.';
  } finally {
    loading.value = false;
  }
};


function onForgotPassword() {
  router.push({ name: "forgot-password" }).catch(() => { });
}

function onGoToRegister() {
  router.push({ name: "register" }).catch(() => { });
}

</script>