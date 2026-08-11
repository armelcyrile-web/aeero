<!-- src/layouts/PublicLayout.vue -->

<script setup>
import { ref } from 'vue';
import { RouterLink, RouterView } from 'vue-router';

const isMenuOpen = ref(false);

function toggleMenu() {
    isMenuOpen.value = !isMenuOpen.value;
}

function closeMenu() {
    isMenuOpen.value = false;
}
</script>

<template>
    <div class="public-layout">
        <header class="public-header">
            <div class="header-container">
                <RouterLink to="/" class="logo" @click="closeMenu">
                    AEERO
                </RouterLink>

                <button
                    class="hamburger"
                    :class="{ 'is-active': isMenuOpen }"
                    @click="toggleMenu"
                    aria-label="Menu"
                >
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <nav class="nav" :class="{ 'nav--open': isMenuOpen }">
                    <RouterLink to="/" @click="closeMenu">Accueil</RouterLink>
                    <RouterLink to="/actualites" @click="closeMenu">Actualités</RouterLink>
                    <RouterLink to="/evenements" @click="closeMenu">Événements</RouterLink>
                    <RouterLink to="/programmes" @click="closeMenu">Programmes</RouterLink>
                    <RouterLink to="/partenaires" @click="closeMenu">Partenaires</RouterLink>
                    <RouterLink to="/bureau" @click="closeMenu">Bureau</RouterLink>
                    <RouterLink to="/galerie" @click="closeMenu">Galerie</RouterLink>
                    <RouterLink to="/contact" @click="closeMenu">Contact</RouterLink>
                </nav>
            </div>
        </header>

        <main class="main-content">
            <RouterView />
        </main>

        <footer class="public-footer">
            <div class="footer-container">
                <div class="footer-brand">
                    <h3>AEERO</h3>
                    <p>Association des Étudiants et Élèves Ressortissants de Ouoghi</p>
                </div>
                <div class="footer-links">
                    <h4>Navigation</h4>
                    <RouterLink to="/">Accueil</RouterLink>
                    <RouterLink to="/actualites">Actualités</RouterLink>
                    <RouterLink to="/evenements">Événements</RouterLink>
                    <RouterLink to="/programmes">Programmes</RouterLink>
                    <RouterLink to="/contact">Contact</RouterLink>
                </div>
                <div class="footer-contact">
                    <h4>Contact</h4>
                    <p>Email : contact@aeero.org</p>
                    <p>Tél : +226 XX XX XX XX</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ new Date().getFullYear() }} AEERO - Tous droits réservés</p>
            </div>
        </footer>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.public-layout {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background-color: $color-bg;
}

// =============================================
// HEADER
// =============================================
.public-header {
    background-color: $color-primary;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: $shadow-md;
}

.header-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: $spacing-md $spacing-lg;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.logo {
    font-family: $font-family-base;
    font-size: $font-size-2xl;
    font-weight: $font-weight-bold;
    color: $color-white;
    text-decoration: none;
    letter-spacing: 2px;

    &:hover {
        color: $color-accent;
        transition: color $transition-base;
    }
}

// =============================================
// NAVIGATION
// =============================================
.nav {
    display: flex;
    gap: $spacing-lg;

    a {
        color: $color-white;
        text-decoration: none;
        font-size: $font-size-sm;
        font-weight: $font-weight-medium;
        padding: $spacing-xs $spacing-sm;
        border-radius: $radius-sm;
        transition: color $transition-base, background-color $transition-base;

        &:hover,
        &.router-link-active {
            color: $color-accent;
            background-color: rgba(255, 255, 255, 0.1);
        }
    }
}

// =============================================
// HAMBURGER
// =============================================
.hamburger {
    display: none;
    flex-direction: column;
    gap: 5px;
    background: none;
    border: none;
    cursor: pointer;
    padding: $spacing-xs;

    span {
        display: block;
        width: 24px;
        height: 2px;
        background-color: $color-white;
        transition: transform $transition-fast, opacity $transition-fast;
    }

    &.is-active {
        span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        span:nth-child(2) {
            opacity: 0;
        }
        span:nth-child(3) {
            transform: rotate(-45deg) translate(5px, -5px);
        }
    }
}

// =============================================
// MAIN
// =============================================
.main-content {
    flex: 1;
}

// =============================================
// FOOTER
// =============================================
.public-footer {
    background-color: $color-primary-dark;
    color: $color-white;
    margin-top: $spacing-2xl;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: $spacing-2xl $spacing-lg;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: $spacing-xl;
}

.footer-brand {
    h3 {
        font-size: $font-size-xl;
        font-weight: $font-weight-bold;
        margin-bottom: $spacing-sm;
    }

    p {
        font-size: $font-size-sm;
        color: rgba(255, 255, 255, 0.7);
    }
}

.footer-links {
    h4 {
        font-size: $font-size-base;
        font-weight: $font-weight-semi-bold;
        margin-bottom: $spacing-md;
    }

    a {
        display: block;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        font-size: $font-size-sm;
        margin-bottom: $spacing-sm;
        transition: color $transition-base;

        &:hover {
            color: $color-accent;
        }
    }
}

.footer-contact {
    h4 {
        font-size: $font-size-base;
        font-weight: $font-weight-semi-bold;
        margin-bottom: $spacing-md;
    }

    p {
        font-size: $font-size-sm;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: $spacing-sm;
    }
}

.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
    padding: $spacing-md;

    p {
        font-size: $font-size-sm;
        color: rgba(255, 255, 255, 0.5);
        margin: 0;
    }
}

// =============================================
// RESPONSIVE
// =============================================
@media (max-width: $breakpoint-mobile) {
    .header-container {
        flex-wrap: wrap;
    }

    .hamburger {
        display: flex;
    }

    .nav {
        display: none;
        flex-direction: column;
        width: 100%;
        padding-top: $spacing-md;

        &--open {
            display: flex;
        }

        a {
            padding: $spacing-sm $spacing-md;
            border-radius: $radius-sm;
        }
    }

    .footer-container {
        grid-template-columns: 1fr;
        text-align: center;
    }
}
</style>