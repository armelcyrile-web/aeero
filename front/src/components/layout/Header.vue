<template>
  <header class="header">
    <div class="header-container">
      <router-link to="/" class="logo">AEERO</router-link>

      <nav class="desktop-nav">
        <router-link to="/" exact-active-class="active">Accueil</router-link>
        <router-link to="/presentation" exact-active-class="active">Présentation</router-link>
        <router-link to="/bureau" exact-active-class="active">Bureau</router-link>
        <router-link to="/galerie" exact-active-class="active">Galerie</router-link>
        <router-link to="/adhesion" exact-active-class="active">Adhésion</router-link>
        <router-link to="/dons" exact-active-class="active">Dons</router-link>
        <router-link to="/contact" exact-active-class="active">Contact</router-link>
      </nav>

      <div class="header-actions">
        <button class="theme-toggle" @click="toggleTheme" :aria-label="isDark ? 'Activer le mode clair' : 'Activer le mode sombre'">
          <Sun v-if="isDark" class="theme-icon" />
          <Moon v-else class="theme-icon" />
        </button>
        <button class="hamburger" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Menu">
          <X v-if="mobileMenuOpen" class="menu-icon" />
          <Menu v-else class="menu-icon" />
        </button>
      </div>
    </div>

    <transition name="slide">
      <nav v-if="mobileMenuOpen" class="mobile-nav">
        <router-link to="/" @click="mobileMenuOpen = false">Accueil</router-link>
        <router-link to="/presentation" @click="mobileMenuOpen = false">Présentation</router-link>
        <router-link to="/bureau" @click="mobileMenuOpen = false">Bureau</router-link>
        <router-link to="/galerie" @click="mobileMenuOpen = false">Galerie</router-link>
        <router-link to="/adhesion" @click="mobileMenuOpen = false">Adhésion</router-link>
        <router-link to="/dons" @click="mobileMenuOpen = false">Dons</router-link>
        <router-link to="/contact" @click="mobileMenuOpen = false">Contact</router-link>
      </nav>
    </transition>
  </header>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Menu, X, Sun, Moon } from 'lucide-vue-next'

const isDark = ref(false)
const mobileMenuOpen = ref(false)

const toggleTheme = () => {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark', isDark.value)
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
}

onMounted(() => {
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    isDark.value = true
    document.documentElement.classList.add('dark')
  }
})
</script>

<style scoped>
.header {
  position: sticky;
  top: 0;
  z-index: 1000;
  background-color: var(--surface);
  border-bottom: 1px solid var(--border);
  transition: background-color 0.3s ease, border-color 0.3s ease;
}

.header-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
  height: 70px;
}

.logo {
  font-family: 'Inter', sans-serif;
  font-weight: 700;
  font-size: 1.8rem;
  color: var(--primary);
  text-decoration: none;
}

.desktop-nav {
  display: flex;
  gap: 2rem;
}

.desktop-nav a {
  position: relative;
  color: var(--text);
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}

.desktop-nav a:hover {
  color: var(--primary);
}

.desktop-nav a.active {
  color: var(--primary);
}

/* Soulignement animé */
.desktop-nav a::after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -8px;
  width: 100%;
  height: 3px;
  background-color: var(--accent);
  border-radius: 2px;
  transform: scaleX(0);
  transform-origin: center;
  transition: transform 0.3s ease;
}

.desktop-nav a:hover::after {
  transform: scaleX(1);
}

.desktop-nav a.active::after {
  transform: scaleX(1);
  transform-origin: left;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.theme-toggle,
.hamburger {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text);
  transition: background-color 0.3s ease, color 0.3s ease;
}

.theme-toggle:hover,
.hamburger:hover {
  background-color: var(--bg-secondary);
  color: var(--primary);
}

.theme-icon,
.menu-icon {
  width: 20px;
  height: 20px;
}

.hamburger {
  display: none;
}

.mobile-nav {
  display: none;
}

@media (max-width: 768px) {
  .desktop-nav {
    display: none;
  }
  .hamburger {
    display: flex;
  }
  .mobile-nav {
    display: flex;
    flex-direction: column;
    background-color: var(--surface);
    border-top: 1px solid var(--border);
    padding: 1rem 1.5rem;
  }
  .mobile-nav a {
    padding: 0.75rem 0;
    color: var(--text);
    text-decoration: none;
    font-weight: 500;
    border-bottom: 1px solid var(--border);
  }
  .mobile-nav a:last-child {
    border-bottom: none;
  }
}

.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
}
.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>