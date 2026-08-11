// src/router/index.js

import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        // =============================================
        // ROUTES PUBLIQUES
        // =============================================
        {
            path: '/',
            name: 'Accueil',
            component: () => import('@/views/public/Accueil.vue'),
            meta: { layout: 'public' },
        },
        {
            path: '/actualites',
            name: 'Actualites',
            component: () => import('@/views/public/Actualites.vue'),
            meta: { layout: 'public' },
        },
        {
            path: '/actualites/:slug',
            name: 'ActualiteDetail',
            component: () => import('@/views/public/ActualiteDetail.vue'),
            meta: { layout: 'public' },
        },
        {
            path: '/evenements',
            name: 'Evenements',
            component: () => import('@/views/public/Evenements.vue'),
            meta: { layout: 'public' },
        },
        {
            path: '/evenements/:slug',
            name: 'EvenementDetail',
            component: () => import('@/views/public/EvenementDetail.vue'),
            meta: { layout: 'public' },
        },
        {
            path: '/programmes',
            name: 'Programmes',
            component: () => import('@/views/public/Programmes.vue'),
            meta: { layout: 'public' },
        },
        {
            path: '/programmes/:slug',
            name: 'ProgrammeDetail',
            component: () => import('@/views/public/ProgrammeDetail.vue'),
            meta: { layout: 'public' },
        },
        {
            path: '/partenaires',
            name: 'Partenaires',
            component: () => import('@/views/public/Partenaires.vue'),
            meta: { layout: 'public' },
        },
        {
            path: '/bureau',
            name: 'Bureau',
            component: () => import('@/views/public/Bureau.vue'),
            meta: { layout: 'public' },
        },
        {
            path: '/galerie',
            name: 'Galerie',
            component: () => import('@/views/public/Galerie.vue'),
            meta: { layout: 'public' },
        },
        {
            path: '/contact',
            name: 'Contact',
            component: () => import('@/views/public/Contact.vue'),
            meta: { layout: 'public' },
        },

        // =============================================
        // ROUTES ADMIN - AUTH
        // =============================================
        {
            path: '/admin/login',
            name: 'AdminLogin',
            component: () => import('@/views/admin/Login.vue'),
            meta: { layout: 'auth' },
        },

        // =============================================
        // ROUTES ADMIN - PROTÉGÉES
        // =============================================
        {
            path: '/admin',
            name: 'AdminDashboard',
            component: () => import('@/views/admin/Dashboard.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },

        // Actualités
        {
            path: '/admin/actualites',
            name: 'AdminActualites',
            component: () => import('@/views/admin/actualites/Liste.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },
        {
            path: '/admin/actualites/nouveau',
            name: 'AdminActualiteNouveau',
            component: () => import('@/views/admin/actualites/Formulaire.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },
        {
            path: '/admin/actualites/:id/modifier',
            name: 'AdminActualiteModifier',
            component: () => import('@/views/admin/actualites/Formulaire.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },

        // Événements
        {
            path: '/admin/evenements',
            name: 'AdminEvenements',
            component: () => import('@/views/admin/evenements/Liste.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },
        {
            path: '/admin/evenements/nouveau',
            name: 'AdminEvenementNouveau',
            component: () => import('@/views/admin/evenements/Formulaire.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },
        {
            path: '/admin/evenements/:id/modifier',
            name: 'AdminEvenementModifier',
            component: () => import('@/views/admin/evenements/Formulaire.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },

        // Programmes
        {
            path: '/admin/programmes',
            name: 'AdminProgrammes',
            component: () => import('@/views/admin/programmes/Liste.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },
        {
            path: '/admin/programmes/nouveau',
            name: 'AdminProgrammeNouveau',
            component: () => import('@/views/admin/programmes/Formulaire.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },
        {
            path: '/admin/programmes/:id/modifier',
            name: 'AdminProgrammeModifier',
            component: () => import('@/views/admin/programmes/Formulaire.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },

        // Partenaires
        {
            path: '/admin/partenaires',
            name: 'AdminPartenaires',
            component: () => import('@/views/admin/partenaires/Liste.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },
        {
            path: '/admin/partenaires/nouveau',
            name: 'AdminPartenaireNouveau',
            component: () => import('@/views/admin/partenaires/Formulaire.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },
        {
            path: '/admin/partenaires/:id/modifier',
            name: 'AdminPartenaireModifier',
            component: () => import('@/views/admin/partenaires/Formulaire.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },

        // Membres du bureau
        {
            path: '/admin/membres-bureau',
            name: 'AdminMembresBureau',
            component: () => import('@/views/admin/membres-bureau/Liste.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },
        {
            path: '/admin/membres-bureau/nouveau',
            name: 'AdminMembreBureauNouveau',
            component: () => import('@/views/admin/membres-bureau/Formulaire.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },
        {
            path: '/admin/membres-bureau/:id/modifier',
            name: 'AdminMembreBureauModifier',
            component: () => import('@/views/admin/membres-bureau/Formulaire.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },

        // Albums
        {
            path: '/admin/albums',
            name: 'AdminAlbums',
            component: () => import('@/views/admin/albums/Liste.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },

        // Messages contact
        {
            path: '/admin/messages-contact',
            name: 'AdminMessagesContact',
            component: () => import('@/views/admin/MessagesContact.vue'),
            meta: { layout: 'admin', requiresAuth: true },
        },
    ],
});

// =============================================
// NAVIGATION GUARD
// =============================================
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    // Redirige vers login si la route nécessite auth et que l'utilisateur n'est pas connecté
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        return next({ name: 'AdminLogin' });
    }

    // Redirige vers dashboard si l'utilisateur connecté tente d'accéder à la page login
    if (to.name === 'AdminLogin' && authStore.isAuthenticated) {
        return next({ name: 'AdminDashboard' });
    }

    next();
});

export default router;