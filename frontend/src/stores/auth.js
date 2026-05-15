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

    clearSession() {
      this.user = null;
      this.initialized = true;
    },
    async refresh() {
      try {
        const refreshToken = localStorage.getItem('refreshToken')

        if (!refreshToken) {
          return false
        }

        const { data } = await api.post('/auth/refresh', { refreshToken })
        localStorage.setItem('accessToken', data.accessToken)
        localStorage.setItem('refreshToken', data.refreshToken)
        return true
      } catch {
        return false
      }
    },
    

    async fetchUser() {
      this.loading = true
    
      try {
        const { data } = await api.get('/me')
        this.user = data
        return data
      } catch {
        this.user = null
        return null
      } finally {
        this.loading = false
        this.initialized = true
      }
    },
    
    async logout() {
      try {
        await api.post('/logout')
      } finally {
        this.clearSession()
      }
    },

    async forgotPassword(email) {
      return api.post('/forgot-password', { email })
    },

    async verifyOtp(email, otp) {
      return api.post('/verify-otp', { email, otp })
    },

    async setNewPassword(payload) {
      return api.post('/reset-password', payload)
    },
  },
})
