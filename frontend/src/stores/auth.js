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

    
    async logout() {
      await api.post('/logout')
      this.user = null
      localStorage.clear();
      window.location.href = '/auth/login';

    },

    async forgotPassword(email) {
      return api.post('/api/forgot-password', { email })
    },

    async verifyOtp(email, otp) {
      return api.post('/api/verify-otp', { email, otp })
    },

    async setNewPassword(payload) {
      return api.post('/api/newPassword', payload)
    },
  },
})