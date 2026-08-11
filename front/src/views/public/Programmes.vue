<!-- src/views/public/Programmes.vue -->

<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import apiClient from '@/api/axios';

const programmes = ref([]);
const isLoading = ref(true);
const error = ref(null);

async function fetchProgrammes() {
    isLoading.value = true;
    error.value = null;

    try {
        const response = await apiClient.get('/programmes');
        programmes.value = response.data.data;
    } catch (err) {
        error.value = 'Impossible de charger les programmes. Veuillez réessayer plus tard.';
    } finally {
        isLoading.value = false;
    }
}

function getExcerpt(description, maxLength = 150) {
    const text = description.replace(/<[^>]+>/g, '');
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength).trim() + '...';
}

onMounted(() => {
    fetchProgrammes();
});
</script>

<template>
    <div class="programmes-page">
        <section class="page-hero">
            <h1>Programmes</h1>
            <p>
                Découvrez les activités récurrentes de l'association : cours de vacances,
                séances de renforcement, ateliers de formation et bien plus encore.
            </p>
        </section>

        <section class="page-content">
            <div v-if="isLoading" class="loading">Chargement des programmes...</div>

            <div v-else-if="error" class="error">{{ error }}</div>

            <div v-else-if="programmes.length === 0" class="empty">
                Aucun programme pour le moment.
            </div>

            <div v-else class="cards-grid">
                <RouterLink
                    v-for="programme in programmes"
                    :key="programme.id"
                    :to="`/programmes/${programme.slug}`"
                    class="card"
                >
                    <div class="card-image">
                        <img
                            v-if="programme.image"
                            :src="`http://127.0.0.1:8000/storage/${programme.image}`"
                            :alt="programme.titre"
                        />
                        <div v-else class="card-image-placeholder">
                            📋
                        </div>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">{{ programme.titre }}</h3>
                        <p class="card-excerpt">{{ getExcerpt(programme.description) }}</p>
                    </div>
                </RouterLink>
            </div>
        </section>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.programmes-page {
    min-height: 60vh;
}

.page-hero {
    background: linear-gradient(135deg, $color-primary, $color-secondary);
    color: $color-white;
    text-align: center;
    padding: $spacing-2xl $spacing-lg;

    h1 {
        font-size: $font-size-3xl;
        font-weight: $font-weight-bold;
        margin-bottom: $spacing-sm;
    }

    p {
        font-size: $font-size-lg;
        opacity: 0.9;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.6;
    }
}

.page-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: $spacing-xl $spacing-lg;
}

.loading,
.error,
.empty {
    text-align: center;
    padding: $spacing-2xl;
    font-size: $font-size-lg;
}

.error {
    color: $color-danger;
}

.empty {
    color: $color-text-light;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: $spacing-lg;
}

.card {
    background-color: $color-white;
    border-radius: $radius-lg;
    overflow: hidden;
    box-shadow: $shadow-sm;
    text-decoration: none;
    color: $color-text;
    transition: transform $transition-base, box-shadow $transition-base;

    &:hover {
        transform: translateY(-4px);
        box-shadow: $shadow-lg;
    }
}

.card-image {
    width: 100%;
    height: 200px;
    overflow: hidden;

    img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
}

.card-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, $color-primary-light, $color-secondary-light);
    font-size: 3rem;
}

.card-body {
    padding: $spacing-md;
}

.card-title {
    font-size: $font-size-lg;
    font-weight: $font-weight-semi-bold;
    margin-bottom: $spacing-sm;
    color: $color-primary;
}

.card-excerpt {
    font-size: $font-size-sm;
    color: $color-text-light;
    line-height: 1.5;
}

@media (max-width: $breakpoint-mobile) {
    .page-hero {
        padding: $spacing-xl $spacing-md;

        h1 {
            font-size: $font-size-2xl;
        }

        p {
            font-size: $font-size-base;
        }
    }

    .cards-grid {
        grid-template-columns: 1fr;
    }
}
</style>