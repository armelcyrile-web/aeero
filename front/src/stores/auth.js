// src/stores/auth.js

import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import apiClient from '@/api/axios';

export const useAuthStore = defineStore('auth', () => {
    // =============================================
    // STATE
    // =============================================
    const user = ref(null);
    const token = ref(localStorage.getItem('token') || sessionStorage.getItem('token'));

    // =============================================
    // GETTERS
    // =============================================
    const isAuthenticated = computed(() => !!token.value);

    const isPresident = computed(() => {
        return user.value?.roles?.some(r => r.name === 'president') ?? false;
    });

    const isSecretaire = computed(() => {
        return user.value?.roles?.some(r => r.name === 'secretaire_general') ?? false;
    });

    // =============================================
    // ACTIONS
    // =============================================
    async function login(email, password, rememberMe = true) {
        try {
            const response = await apiClient.post('/login', {
                email,
                password,
            });

            user.value = response.data.user;
            token.value = response.data.token;

            if (rememberMe) {
                localStorage.setItem('token', token.value);
                sessionStorage.removeItem('token');
            } else {
                sessionStorage.setItem('token', token.value);
                localStorage.removeItem('token');
            }
        } catch (error) {
            throw error;
        }
    }

    async function logout() {
        try {
            await apiClient.post('/admin/logout');
        } catch (error) {
            console.error('Erreur lors de la déconnexion:', error);
        } finally {
            user.value = null;
            token.value = null;
            localStorage.removeItem('token');
            sessionStorage.removeItem('token');
        }
    }

    async function fetchMe() {
        try {
            const response = await apiClient.get('/admin/me');
            user.value = response.data;
        } catch (error) {
            user.value = null;
            token.value = null;
            localStorage.removeItem('token');
            sessionStorage.removeItem('token');
            throw error;
        }
    }

    return {
        // State
        user,
        token,
        // Getters
        isAuthenticated,
        isPresident,
        isSecretaire,
        // Actions
        login,
        logout,
        fetchMe,
    };
});