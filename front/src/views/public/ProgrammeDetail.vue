<!-- src/views/public/ProgrammeDetail.vue -->

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import apiClient from '@/api/axios';

const route = useRoute();
const router = useRouter();

const programme = ref(null);
const isLoading = ref(true);
const error = ref(null);

async function fetchProgramme() {
    isLoading.value = true;
    error.value = null;

    try {
        const response = await apiClient.get(`/programmes/${route.params.slug}`);
        programme.value = response.data;
    } catch (err) {
        if (err.response && err.response.status === 404) {
            error.value = 'Programme introuvable.';
        } else {
            error.value = 'Une erreur est survenue lors du chargement.';
        }
    } finally {
        isLoading.value = false;
    }
}

function goBack() {
    router.push('/programmes');
}

onMounted(() => {
    fetchProgramme();
});
</script>

<template>
    <div class="programme-detail-page">
        <div v-if="isLoading" class="loading">Chargement...</div>

        <div v-else-if="error" class="error-container">
            <p class="error">{{ error }}</p>
            <button class="btn-back" @click="goBack">← Retour aux programmes</button>
        </div>

        <article v-else-if="programme" class="programme-article">
            <button class="btn-back" @click="goBack">← Retour aux programmes</button>

            <div v-if="programme.image" class="article-image">
                <img
                    :src="`http://127.0.0.1:8000/storage/${programme.image}`"
                    :alt="programme.titre"
                />
            </div>

            <header class="article-header">
                <h1>{{ programme.titre }}</h1>
            </header>

            <div class="article-body" v-html="programme.description"></div>
        </article>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.programme-detail-page {
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

.programme-article {
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
    .programme-detail-page {
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