<!-- src/layouts/AdminLayout.vue -->

<script setup>
import { ref } from 'vue';
import { RouterLink, RouterView, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import {
    LayoutDashboard,
    Newspaper,
    Calendar,
    BookOpen,
    Handshake,
    Users,
    Image,
    Mail,
    LogOut,
    Menu,
} from 'lucide-vue-next';

const authStore = useAuthStore();
const router = useRouter();

const isSidebarOpen = ref(false);

function toggleSidebar() {
    isSidebarOpen.value = !isSidebarOpen.value;
}

function closeSidebar() {
    isSidebarOpen.value = false;
}

async function handleLogout() {
    await authStore.logout();
    router.push({ name: 'AdminLogin' });
}
</script>

<template>
    <div class="admin-layout">
        <!-- Overlay mobile -->
        <div
            class="sidebar-overlay"
            :class="{ 'is-visible': isSidebarOpen }"
            @click="closeSidebar"
        ></div>

        <!-- Sidebar -->
        <aside class="sidebar" :class="{ 'sidebar--open': isSidebarOpen }">
            <div class="sidebar-header">
                <RouterLink to="/admin" class="sidebar-logo" @click="closeSidebar">
                    AEERO
                </RouterLink>
                <span class="sidebar-badge">Admin</span>
            </div>

            <nav class="sidebar-nav">
                <RouterLink
                    to="/admin"
                    class="sidebar-link"
                    :class="{ 'router-link-active': $route.path === '/admin' }"
                    @click="closeSidebar"
                >
                    <LayoutDashboard :size="18" />
                    <span>Dashboard</span>
                </RouterLink>
                <RouterLink
                    to="/admin/actualites"
                    class="sidebar-link"
                    @click="closeSidebar"
                >
                    <Newspaper :size="18" />
                    <span>Actualités</span>
                </RouterLink>
                <RouterLink
                    to="/admin/evenements"
                    class="sidebar-link"
                    @click="closeSidebar"
                >
                    <Calendar :size="18" />
                    <span>Événements</span>
                </RouterLink>
                <RouterLink
                    to="/admin/programmes"
                    class="sidebar-link"
                    @click="closeSidebar"
                >
                    <BookOpen :size="18" />
                    <span>Programmes</span>
                </RouterLink>
                <RouterLink
                    to="/admin/partenaires"
                    class="sidebar-link"
                    @click="closeSidebar"
                >
                    <Handshake :size="18" />
                    <span>Partenaires</span>
                </RouterLink>
                <RouterLink
                    to="/admin/membres-bureau"
                    class="sidebar-link"
                    @click="closeSidebar"
                >
                    <Users :size="18" />
                    <span>Membres bureau</span>
                </RouterLink>
                <RouterLink
                    to="/admin/albums"
                    class="sidebar-link"
                    @click="closeSidebar"
                >
                    <Image :size="18" />
                    <span>Albums</span>
                </RouterLink>
                <RouterLink
                    to="/admin/messages-contact"
                    class="sidebar-link"
                    @click="closeSidebar"
                >
                    <Mail :size="18" />
                    <span>Messages</span>
                </RouterLink>
            </nav>
        </aside>

        <!-- Contenu principal -->
        <div class="admin-main">
            <header class="admin-header">
                <button
                    class="sidebar-toggle"
                    @click="toggleSidebar"
                    aria-label="Menu"
                >
                    <Menu :size="24" />
                </button>

                <div class="admin-header-right">
                    <div class="user-info">
                        <span class="user-name">{{ authStore.user?.name }}</span>
                        <span
                            class="role-badge"
                            :class="{
                                'role-badge--president': authStore.isPresident,
                                'role-badge--secretaire': authStore.isSecretaire,
                            }"
                        >
                            {{ authStore.isPresident ? 'Président' : 'Secrétaire Général' }}
                        </span>
                    </div>
                    <button class="btn-logout" @click="handleLogout">
                        <LogOut :size="18" />
                        <span>Déconnexion</span>
                    </button>
                </div>
            </header>

            <main class="admin-content">
                <RouterView />
            </main>
        </div>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.admin-layout {
    display: flex;
    min-height: 100vh;
    background-color: $color-bg;
}

// =============================================
// OVERLAY MOBILE
// =============================================
.sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 90;

    &.is-visible {
        display: block;
    }
}

// =============================================
// SIDEBAR
// =============================================
.sidebar {
    width: 260px;
    background-color: $color-primary;
    color: $color-white;
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    z-index: 100;
    transition: transform $transition-base;
}

.sidebar-header {
    padding: $spacing-lg;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-logo {
    font-family: $font-family-base;
    font-size: $font-size-xl;
    font-weight: $font-weight-bold;
    color: $color-white;
    text-decoration: none;
    letter-spacing: 2px;
}

.sidebar-badge {
    font-size: $font-size-sm;
    background-color: $color-accent;
    color: $color-primary-dark;
    padding: 2px $spacing-sm;
    border-radius: $radius-sm;
    font-weight: $font-weight-semi-bold;
}

.sidebar-nav {
    flex: 1;
    padding: $spacing-md;
    display: flex;
    flex-direction: column;
    gap: $spacing-xs;
    overflow-y: auto;
}

.sidebar-link {
    display: flex;
    align-items: center;
    gap: $spacing-sm;
    padding: $spacing-sm $spacing-md;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    border-radius: $radius-md;
    font-size: $font-size-sm;
    transition: background-color $transition-fast, color $transition-fast;

    &:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: $color-white;
    }

    &.router-link-active {
        background-color: $color-secondary;
        color: $color-white;
        font-weight: $font-weight-semi-bold;
    }
}

// =============================================
// MAIN CONTENT
// =============================================
.admin-main {
    flex: 1;
    margin-left: 260px;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.admin-header {
    background-color: $color-white;
    padding: $spacing-md $spacing-lg;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: $shadow-sm;
    position: sticky;
    top: 0;
    z-index: 50;
}

.sidebar-toggle {
    display: none;
    background: none;
    border: none;
    cursor: pointer;
    padding: $spacing-xs;
    color: $color-primary;

    &:hover {
        color: $color-secondary;
    }
}

.admin-header-right {
    display: flex;
    align-items: center;
    gap: $spacing-lg;
    margin-left: auto;
}

.user-info {
    display: flex;
    align-items: center;
    gap: $spacing-sm;
}

.user-name {
    font-size: $font-size-sm;
    font-weight: $font-weight-medium;
    color: $color-text;
}

.role-badge {
    font-size: 0.75rem;
    padding: 2px $spacing-sm;
    border-radius: $radius-full;
    font-weight: $font-weight-semi-bold;

    &--president {
        background-color: $color-accent;
        color: $color-primary-dark;
    }

    &--secretaire {
        background-color: $color-secondary-light;
        color: $color-white;
    }
}

.btn-logout {
    display: flex;
    align-items: center;
    gap: $spacing-xs;
    background-color: transparent;
    color: $color-danger;
    border: 1px solid $color-danger;
    padding: $spacing-xs $spacing-md;
    border-radius: $radius-sm;
    font-size: $font-size-sm;
    cursor: pointer;
    transition: background-color $transition-base, color $transition-base;

    &:hover {
        background-color: $color-danger;
        color: $color-white;
    }
}

.admin-content {
    flex: 1;
    padding: $spacing-lg;
}

// =============================================
// RESPONSIVE
// =============================================
@media (max-width: $breakpoint-mobile) {
    .sidebar {
        transform: translateX(-100%);

        &--open {
            transform: translateX(0);
        }
    }

    .admin-main {
        margin-left: 0;
    }

    .sidebar-toggle {
        display: flex;
    }

    .admin-header-right {
        gap: $spacing-md;
    }

    .user-name {
        display: none;
    }

    .admin-content {
        padding: $spacing-md;
    }
}
</style>