<!-- src/views/public/EvenementDetail.vue -->

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import apiClient from '@/api/axios';

const route = useRoute();
const router = useRouter();

const evenement = ref(null);
const isLoading = ref(true);
const error = ref(null);

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('fr-FR', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function formatTime(dateString) {
    return new Date(dateString).toLocaleTimeString('fr-FR', {
        hour: '2-digit',
        minute: '2-digit',
    });
}

async function fetchEvenement() {
    isLoading.value = true;
    error.value = null;

    try {
        const response = await apiClient.get(`/evenements/${route.params.slug}`);
        evenement.value = response.data;
    } catch (err) {
        if (err.response && err.response.status === 404) {
            error.value = 'Événement introuvable.';
        } else {
            error.value = 'Une erreur est survenue lors du chargement.';
        }
    } finally {
        isLoading.value = false;
    }
}

function goBack() {
    router.push('/evenements');
}

onMounted(() => {
    fetchEvenement();
});
</script>

<template>
    <div class="evenement-detail-page">
        <div v-if="isLoading" class="loading">Chargement...</div>

        <div v-else-if="error" class="error-container">
            <p class="error">{{ error }}</p>
            <button class="btn-back" @click="goBack">← Retour aux événements</button>
        </div>

        <article v-else-if="evenement" class="evenement-article">
            <button class="btn-back" @click="goBack">← Retour aux événements</button>

            <div v-if="evenement.image" class="article-image">
                <img
                    :src="`http://127.0.0.1:8000/storage/${evenement.image}`"
                    :alt="evenement.titre"
                />
            </div>

            <header class="article-header">
                <h1>{{ evenement.titre }}</h1>
                <div class="article-meta">
                    <div class="meta-item">
                        📅 {{ formatDate(evenement.date_debut) }}
                        à {{ formatTime(evenement.date_debut) }}
                    </div>
                    <div v-if="evenement.date_fin" class="meta-item">
                        → {{ formatDate(evenement.date_fin) }}
                        à {{ formatTime(evenement.date_fin) }}
                    </div>
                    <div class="meta-item">
                        📍 {{ evenement.lieu }}
                    </div>
                </div>
            </header>

            <div class="article-body" v-html="evenement.description"></div>
        </article>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.evenement-detail-page {
    min-height: 60vh;
    max-width: 900px;
    margin: 0 auto;
    padding: $spacing-xl $spacing-lg;
}

.loading {
    text-align: center;
    padding: $spacing-2xl;
    font-size: $font-size-lg;
}

.error-container {
    text-align: center;
    padding: $spacing-2xl;
}

.error {
    color: $color-danger;
    font-size: $font-size-lg;
    margin-bottom: $spacing-md;
}

.btn-back {
    display: inline-block;
    background: none;
    border: none;
    color: $color-secondary;
    font-size: $font-size-base;
    cursor: pointer;
    padding: $spacing-sm 0;
    margin-bottom: $spacing-md;
    transition: color $transition-base;

    &:hover {
        color: $color-accent;
    }
}

.evenement-article {
    background-color: $color-white;
    border-radius: $radius-lg;
    overflow: hidden;
    box-shadow: $shadow-md;
}

.article-image {
    width: 100%;
    max-height: 400px;
    overflow: hidden;

    img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
}

.article-header {
    padding: $spacing-xl $spacing-lg $spacing-md;
    border-bottom: 1px solid $color-border;
}

.article-header h1 {
    font-size: $font-size-3xl;
    font-weight: $font-weight-bold;
    color: $color-primary;
    margin-bottom: $spacing-md;
}

.article-meta {
    display: flex;
    flex-direction: column;
    gap: $spacing-xs;
}

.meta-item {
    font-size: $font-size-base;
    color: $color-text-light;
}

.article-body {
    padding: $spacing-lg;
    font-size: $font-size-base;
    line-height: 1.8;

    :deep(p) {
        margin-bottom: $spacing-md;
    }

    :deep(img) {
        max-width: 100%;
        border-radius: $radius-md;
        margin: $spacing-md 0;
    }
}

@media (max-width: $breakpoint-mobile) {
    .evenement-detail-page {
        padding: $spacing-md;
    }

    .article-header h1 {
        font-size: $font-size-2xl;
    }

    .article-header {
        padding: $spacing-md;
    }

    .article-body {
        padding: $spacing-md;
    }
}
</style>