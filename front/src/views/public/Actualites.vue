<!-- src/views/public/Actualites.vue -->

<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import apiClient from '@/api/axios';

const actualites = ref([]);
const isLoading = ref(true);
const error = ref('');
const currentPage = ref(1);
const lastPage = ref(1);

async function fetchActualites(page = 1) {
    isLoading.value = true;
    error.value = '';

    try {
        const response = await apiClient.get('/actualites', {
            params: { page },
        });

        if (page === 1) {
            actualites.value = response.data.data || [];
        } else {
            actualites.value = [...actualites.value, ...(response.data.data || [])];
        }

        currentPage.value = response.data.current_page || 1;
        lastPage.value = response.data.last_page || 1;
    } catch (err) {
        error.value = 'Impossible de charger les actualités.';
    } finally {
        isLoading.value = false;
    }
}

function loadMore() {
    if (currentPage.value < lastPage.value) {
        fetchActualites(currentPage.value + 1);
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function getExcerpt(html, maxLength = 150) {
    const text = html.replace(/<[^>]+>/g, '');
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength).trim() + '...';
}

onMounted(() => {
    fetchActualites();
});
</script>

<template>
    <div class="actualites-page">
        <section class="page-hero">
            <h1>Actualités</h1>
            <p>Toute l'actualité de l'association</p>
        </section>

        <section class="page-content">
            <div v-if="isLoading && actualites.length === 0" class="loading">
                Chargement des actualités...
            </div>

            <div v-else-if="error" class="error">{{ error }}</div>

            <div v-else-if="actualites.length === 0" class="empty">
                Aucune actualité pour le moment.
            </div>

            <template v-else>
                <div class="cards-grid">
                    <RouterLink
                        v-for="actu in actualites"
                        :key="actu.id"
                        :to="`/actualites/${actu.slug}`"
                        class="card"
                    >
                        <div class="card-image">
                            <img
                                v-if="actu.image"
                                :src="`http://127.0.0.1:8000/storage/${actu.image}`"
                                :alt="actu.titre"
                            />
                            <div v-else class="card-image-placeholder">📰</div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">{{ actu.titre }}</h3>
                            <p class="card-excerpt">{{ getExcerpt(actu.contenu) }}</p>
                            <span class="card-date">{{ formatDate(actu.published_at) }}</span>
                        </div>
                    </RouterLink>
                </div>

                <div v-if="currentPage < lastPage" class="pagination">
                    <button
                        class="btn-load-more"
                        :disabled="isLoading"
                        @click="loadMore"
                    >
                        {{ isLoading ? 'Chargement...' : 'Charger plus d\'actualités' }}
                    </button>
                </div>
            </template>
        </section>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.actualites-page {
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
    margin-bottom: $spacing-sm;
}

.card-date {
    font-size: 0.8rem;
    color: $color-text-light;
}

.pagination {
    text-align: center;
    margin-top: $spacing-xl;
}

.btn-load-more {
    background-color: $color-secondary;
    color: $color-white;
    border: none;
    padding: $spacing-sm $spacing-xl;
    border-radius: $radius-md;
    font-size: $font-size-base;
    font-weight: $font-weight-semi-bold;
    cursor: pointer;
    transition: background-color $transition-base;

    &:hover:not(:disabled) {
        background-color: $color-secondary-dark;
    }

    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
}

@media (max-width: $breakpoint-mobile) {
    .page-hero {
        padding: $spacing-xl $spacing-md;

        h1 {
            font-size: $font-size-2xl;
        }
    }

    .cards-grid {
        grid-template-columns: 1fr;
    }
}
</style>