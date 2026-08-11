<!-- src/views/public/Evenements.vue -->

<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import apiClient from '@/api/axios';

const evenements = ref([]);
const isLoading = ref(true);
const error = ref(null);

async function fetchEvenements() {
    isLoading.value = true;
    error.value = null;

    try {
        const response = await apiClient.get('/evenements');
        evenements.value = response.data.data;
    } catch (err) {
        error.value = 'Impossible de charger les événements. Veuillez réessayer plus tard.';
    } finally {
        isLoading.value = false;
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('fr-FR', {
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

onMounted(() => {
    fetchEvenements();
});
</script>

<template>
    <div class="evenements-page">
        <section class="page-hero">
            <h1>Événements</h1>
            <p>Retrouvez tous les événements organisés par l'AEERO</p>
        </section>

        <section class="page-content">
            <div v-if="isLoading" class="loading">Chargement des événements...</div>

            <div v-else-if="error" class="error">{{ error }}</div>

            <div v-else-if="evenements.length === 0" class="empty">
                Aucun événement pour le moment.
            </div>

            <div v-else class="cards-grid">
                <RouterLink
                    v-for="evenement in evenements"
                    :key="evenement.id"
                    :to="`/evenements/${evenement.slug}`"
                    class="card"
                >
                    <div class="card-image">
                        <img
                            v-if="evenement.image"
                            :src="`http://127.0.0.1:8000/storage/${evenement.image}`"
                            :alt="evenement.titre"
                        />
                        <div v-else class="card-image-placeholder">
                            📅
                        </div>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">{{ evenement.titre }}</h3>
                        <div class="card-meta">
                            <span class="card-date">
                                📅 {{ formatDate(evenement.date_debut) }}
                                à {{ formatTime(evenement.date_debut) }}
                            </span>
                            <span v-if="evenement.date_fin" class="card-date">
                                → {{ formatDate(evenement.date_fin) }}
                                à {{ formatTime(evenement.date_fin) }}
                            </span>
                        </div>
                        <div class="card-lieu">
                            📍 {{ evenement.lieu }}
                        </div>
                    </div>
                </RouterLink>
            </div>
        </section>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.evenements-page {
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

.card-meta {
    display: flex;
    flex-direction: column;
    gap: $spacing-xs;
    margin-bottom: $spacing-sm;
}

.card-date {
    font-size: $font-size-sm;
    color: $color-text-light;
}

.card-lieu {
    font-size: $font-size-sm;
    color: $color-secondary;
    font-weight: $font-weight-medium;
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