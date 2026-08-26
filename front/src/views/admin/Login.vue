<template>
  <div class="login-page">
    <div class="login-card">
      <h1 class="logo">AEERO</h1>
      <p class="subtitle">Espace administrateur</p>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="input-group">
          <Mail class="input-icon" />
          <input
            type="email"
            v-model="email"
            placeholder="Adresse email"
            required
            autocomplete="email"
          />
        </div>

        <div class="input-group">
          <Lock class="input-icon" />
          <input
            type="password"
            v-model="password"
            placeholder="Mot de passe"
            required
            autocomplete="current-password"
          />
        </div>

        <button type="submit" class="btn-login" :disabled="loading">
          {{ loading ? 'Connexion...' : 'Se connecter' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Swal from 'sweetalert2'
import { Mail, Lock } from 'lucide-vue-next'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)

const handleLogin = async () => {
  loading.value = true
  try {
    await authStore.login(email.value, password.value)
    router.push('/admin')
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Erreur de connexion',
      text: error.response?.data?.message || 'Identifiants incorrects',
    })
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--bg);
  padding: 1.5rem;
}

.login-card {
  background-color: var(--surface);
  padding: 3rem 2rem;
  border-radius: 16px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
  text-align: center;
}

.logo {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--primary);
  margin-bottom: 0.5rem;
}

.subtitle {
  color: var(--text);
  margin-bottom: 2rem;
  font-size: 1.1rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 1rem;
  color: var(--primary);
  width: 20px;
  height: 20px;
}

.input-group input {
  width: 100%;
  padding: 0.85rem 1rem 0.85rem 3rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-family: inherit;
  font-size: 1rem;
  background-color: var(--bg);
  color: var(--text);
  transition: border-color 0.3s;
}

.input-group input:focus {
  outline: none;
  border-color: var(--primary);
}

.btn-login {
  padding: 0.85rem;
  background-color: var(--accent);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-login:hover:not(:disabled) {
  background-color: #d95f3c;
}

.btn-login:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>