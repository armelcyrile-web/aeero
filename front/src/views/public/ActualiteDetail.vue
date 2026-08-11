<!-- src/views/public/ActualiteDetail.vue -->

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import apiClient from '@/api/axios';

const route = useRoute();
const router = useRouter();

const actualite = ref(null);
const isLoading = ref(true);
const error = ref('');

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('fr-FR', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

async function fetchActualite() {
    isLoading.value = true;
    error.value = '';

    try {
        const response = await apiClient.get(`/actualites/${route.params.slug}`);
        actualite.value = response.data;
    } catch (err) {
        if (err.response && err.response.status === 404) {
            error.value = 'Actualité introuvable ou non publiée.';
        } else {
            error.value = 'Une erreur est survenue lors du chargement.';
        }
    } finally {
        isLoading.value = false;
    }
}

function goBack() {
    router.push('/actualites');
}

onMounted(() => {
    fetchActualite();
});
</script>

<template>
    <div class="actualite-detail-page">
        <div v-if="isLoading" class="loading">Chargement...</div>

        <div v-else-if="error" class="error-container">
            <p class="error">{{ error }}</p>
            <button class="btn-back" @click="goBack">← Retour aux actualités</button>
        </div>

        <article v-else-if="actualite" class="actualite-article">
            <button class="btn-back" @click="goBack">← Retour aux actualités</button>

            <div v-if="actualite.image" class="article-image">
                <img
                    :src="`http://127.0.0.1:8000/storage/${actualite.image}`"
                    :alt="actualite.titre"
                />
            </div>

            <header class="article-header">
                <h1>{{ actualite.titre }}</h1>
                <p class="article-date">
                    Publié le {{ formatDate(actualite.published_at) }}
                </p>
            </header>

            <div class="article-body" v-html="actualite.contenu"></div>
        </article>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.actualite-detail-page {
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

.actualite-article {
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
    margin-bottom: $spacing-sm;
}

.article-date {
    font-size: $font-size-sm;
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

    :deep(h2),
    :deep(h3) {
        margin-top: $spacing-lg;
        margin-bottom: $spacing-sm;
        color: $color-primary;
    }

    :deep(ul),
    :deep(ol) {
        margin-bottom: $spacing-md;
        padding-left: $spacing-lg;
    }

    :deep(li) {
        margin-bottom: $spacing-xs;
    }
}

@media (max-width: $breakpoint-mobile) {
    .actualite-detail-page {
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