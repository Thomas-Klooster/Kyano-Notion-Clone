import { defineStore } from 'pinia'
import axios from 'axios'

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
    async getCsrfCookie() {
      await axios.get('/sanctum/csrf-cookie')
    },

    async fetchUser() {
      try {
        const { data } = await axios.get('/api/me')
        this.user = data
        return data
      } catch {
        this.user = null
        return null
      } finally {
        this.initialized = true
      }
    },

    async login(payload) {
      this.loading = true
      try {
        await this.getCsrfCookie()
        await axios.post('/api/login', payload)
        await this.fetchUser()
      } finally {
        this.loading = false
      }
    },

    async register(payload) {
      this.loading = true
      try {
        await this.getCsrfCookie()
        await axios.post('/api/register', payload)
        await this.fetchUser()
      } finally {
        this.loading = false
      }
    },

    async logout() {
      await axios.post('/api/logout')
      this.user = null
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