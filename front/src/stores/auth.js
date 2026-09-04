import { defineStore } from 'pinia'
import axios from '@/utils/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || null,
    user: JSON.parse(localStorage.getItem('user') || 'null'),
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
  },

  actions: {
    async login(email, password) {
      const response = await axios.post('/login', { email, password })
      
      // Adaptation selon la structure de la réponse (ex: response.data.token ou response.data.access_token)
      this.token = response.data.token || response.data.access_token
      this.user = response.data.user
      
      localStorage.setItem('token', this.token)
      localStorage.setItem('user', JSON.stringify(this.user))
      
      return response.data
    },

    async logout() {
      if (this.token) {
        try {
          await axios.post('/logout')
        } catch (error) {
          // Si le token est expiré ou invalide (401), on ignore l'erreur serveur
          console.warn('Déconnexion échouée sur le serveur ou session déjà expirée')
        }
      }
      
      // Réinitialisation locale garantie
      this.clearAuthData()
    },

    clearAuthData() {
      this.token = null
      this.user = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
    }
  },
})