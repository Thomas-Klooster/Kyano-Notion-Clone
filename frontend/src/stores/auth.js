import { defineStore } from 'pinia'
import api from '@/api/api'
export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    loading: false,
    initialized: false,
  }),

  getters: {
    isAuthenticated: (state) => !!state.user,
    isAdmin: (state) => state.user?.role === 'admin',
    isCustomer: (state) => state.user?.role === 'customer',
  },

  actions: {
    setUser(user) {
      this.user = user;
    },
  

    async fetchUser() {
      try {
        const { data } = await api.get('/me');
        this.user = data;
        return data;
      } catch {
        this.user = null;
        return null;
      } finally {
        this.initialized = true;
      }
    },

    async login(payload) {
      this.loading = true
      try {
        await this.getCsrfCookie()
        await api.post('/login', payload)
        await this.fetchUser()
      } finally {
        this.loading = false
      }
    },

    async register(payload) {
      this.loading = true
      try {
        await this.getCsrfCookie()
        await api.post('/register', payload)
        await this.fetchUser()
      } finally {
        this.loading = false
      }
    },

    async logout() {
      await api.post('/logout')
      this.user = null
      localStorage.clear();
      window.location.href = '/login';

    },

    async forgotPassword(email) {
      return axios.post('/api/forgot-password', { email })
    },

    async verifyOtp(email, otp) {
      return axios.post('/api/verify-otp', { email, otp })
    },

    async setNewPassword(payload) {
      return axios.post('/api/newPassword', payload)
    },
  },
})