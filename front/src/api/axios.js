// src/api/axios.js

import axios from 'axios';
import { useAuthStore } from '@/stores/auth';

const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api',
    headers: {
        'Accept': 'application/json',
    },
});

// Intercepteur de requête : ajoute le token Bearer si disponible
apiClient.interceptors.request.use(
    (config) => {
        const authStore = useAuthStore();

        if (authStore.token) {
            config.headers.Authorization = `Bearer ${authStore.token}`;
        }

        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Intercepteur de réponse : gère les erreurs 401
apiClient.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        if (error.response && error.response.status === 401) {
            const authStore = useAuthStore();

            // Vide le state du store
            authStore.user = null;
            authStore.token = null;

            // Supprime le token du localStorage
            localStorage.removeItem('token');

            // Redirige vers /admin/login seulement si on n'y est pas déjà
            if (!window.location.pathname.includes('/admin/login')) {
                window.location.href = '/admin/login';
            }
        }

        return Promise.reject(error);
    }
);

export default apiClient;