<template>
  <div class="admin-layout">
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <h2>AEERO Admin</h2>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/admin" class="nav-link" exact-active-class="active" @click="closeSidebar">
          <LayoutDashboard class="nav-icon" />
          Tableau de bord
        </router-link>
        <router-link to="/admin/publications" class="nav-link" active-class="active" @click="closeSidebar">
          <Newspaper class="nav-icon" />
          Publications
        </router-link>
        <router-link to="/admin/albums" class="nav-link" active-class="active" @click="closeSidebar">
          <Images class="nav-icon" />
          Albums
        </router-link>
        <router-link to="/admin/bureau" class="nav-link" active-class="active" @click="closeSidebar">
          <Users class="nav-icon" />
          Bureau
        </router-link>
      </nav>

      <div class="sidebar-footer">
        <button class="logout-btn" @click="handleLogout">
          <LogOut class="nav-icon" />
          Déconnexion
        </button>
      </div>
    </aside>

    <div class="main-content">
      <header class="content-header">
        <button class="menu-toggle" @click="sidebarOpen = !sidebarOpen" aria-label="Menu">
          <Menu class="menu-icon" />
        </button>
      </header>
      <slot />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { LayoutDashboard, Newspaper, Images, Users, LogOut, Menu } from 'lucide-vue-next'

const router = useRouter()
const authStore = useAuthStore()
const sidebarOpen = ref(false)

const closeSidebar = () => {
  if (window.innerWidth <= 768) {
    sidebarOpen.value = false
  }
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/admin/login')
}
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background-color: var(--bg);
}

.sidebar {
  width: 250px;
  background-color: var(--surface);
  color: var(--text);
  display: flex;
  flex-direction: column;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease;
  z-index: 100;
}

.sidebar-header {
  padding: 2rem 1.5rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.sidebar-header h2 {
  font-size: 1.5rem;
  color: var(--primary);
}

.sidebar-nav {
  flex: 1;
  padding: 1.5rem 0;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.5rem;
  color: var(--text);
  text-decoration: none;
  font-weight: 500;
  transition: background-color 0.3s, color 0.3s;
}

.nav-link:hover,
.nav-link.active {
  background-color: rgba(139, 111, 122, 0.15);
  color: var(--primary);
}

.nav-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

.sidebar-footer {
  padding: 1.5rem;
  border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.75rem 0;
  background: none;
  border: none;
  color: var(--text);
  font-weight: 500;
  cursor: pointer;
  font-size: 1rem;
  transition: color 0.3s;
}

.logout-btn:hover {
  color: var(--accent);
}

.main-content {
  flex: 1;
  padding: 2rem;
}

.content-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
}

.menu-toggle {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  color: var(--text);
  padding: 0.5rem;
}

.menu-icon {
  width: 24px;
  height: 24px;
}

@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    transform: translateX(-100%);
  }

  .sidebar.open {
    transform: translateX(0);
  }

  .main-content {
    padding: 1rem;
  }

  .menu-toggle {
    display: flex;
  }
}
</style>