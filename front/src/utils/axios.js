import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import router from '@/router'

const instance = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
})

instance.interceptors.request.use((config) => {
  const authStore = useAuthStore()
  if (authStore.token) {
    config.headers.Authorization = `Bearer ${authStore.token}`
  }
  return config
})

instance.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      const authStore = useAuthStore()

      // 1. Vérifier si l'URL qui a échoué est /login ou /logout
      const requestUrl = error.config?.url || ''
      const isAuthEndpoint = requestUrl.includes('/login') || requestUrl.includes('/logout')

      // 2. Si l'erreur provient de la tentative de login, on ne fait rien (on laisse le composant afficher "Identifiants incorrects")
      if (requestUrl.includes('/login')) {
        return Promise.reject(error)
      }

      // 3. Si c'est /logout qui échoue en 401, on réinitialise directement le store sans rappeler logout() pour éviter la boucle
      if (requestUrl.includes('/logout')) {
        authStore.token = null
        authStore.user = null
        localStorage.removeItem('token')
      } else {
        // Pour n'importe quelle autre route protégée (ex: /publications), on réinitialise la session
        authStore.token = null
        authStore.user = null
        localStorage.removeItem('token')
      }

      // 4. Redirection vers la page de login si on n'y est pas déjà
      if (router.currentRoute.value.path !== '/admin/login') {
        router.push('/admin/login')
      }
    }

    return Promise.reject(error)
  }
)

export default instance