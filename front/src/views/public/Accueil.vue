<!-- src/views/public/Accueil.vue -->

<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import apiClient from '@/api/axios';

const actualites = ref([]);
const evenements = ref([]);
const isLoadingActus = ref(true);
const isLoadingEvents = ref(true);
const errorActus = ref('');
const errorEvents = ref('');

async function fetchActualites() {
    isLoadingActus.value = true;
    errorActus.value = '';

    try {
        const response = await apiClient.get('/actualites');
        actualites.value = (response.data.data || []).slice(0, 3);
    } catch (err) {
        errorActus.value = 'Impossible de charger les actualités.';
    } finally {
        isLoadingActus.value = false;
    }
}

async function fetchEvenements() {
    isLoadingEvents.value = true;
    errorEvents.value = '';

    try {
        const response = await apiClient.get('/evenements');
        evenements.value = (response.data.data || []).slice(0, 3);
    } catch (err) {
        errorEvents.value = 'Impossible de charger les événements.';
    } finally {
        isLoadingEvents.value = false;
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

function getExcerpt(html, maxLength = 120) {
    const text = html.replace(/<[^>]+>/g, '');
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength).trim() + '...';
}

onMounted(() => {
    fetchActualites();
    fetchEvenements();
});
</script>

<template>
    <div class="accueil-page">
        <!-- Hero -->
        <section class="hero">
            <div class="hero-content">
                <h1>AEERO</h1>
                <p class="hero-subtitle">
                    Association des Étudiants et Élèves Ressortissants de Ouoghi
                </p>
                <p class="hero-description">
                    Promouvoir l'excellence académique, la solidarité et le développement
                    de notre communauté.
                </p>
                <div class="hero-actions">
                    <RouterLink to="/actualites" class="btn-hero btn-hero--primary">
                        Nos actualités
                    </RouterLink>
                    <RouterLink to="/programmes" class="btn-hero btn-hero--secondary">
                        Nos programmes
                    </RouterLink>
                </div>
            </div>
        </section>

        <!-- Dernières actualités -->
        <section class="section">
            <div class="section-header">
                <h2>Dernières actualités</h2>
                <RouterLink to="/actualites" class="section-link">Voir tout →</RouterLink>
            </div>

            <div v-if="isLoadingActus" class="loading">Chargement...</div>
            <div v-else-if="errorActus" class="error">{{ errorActus }}</div>
            <div v-else-if="actualites.length === 0" class="empty">Aucune actualité pour le moment.</div>

            <div v-else class="cards-grid">
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
        </section>

        <!-- Prochains événements -->
        <section class="section section--alt">
            <div class="section-header">
                <h2>Prochains événements</h2>
                <RouterLink to="/evenements" class="section-link">Voir tout →</RouterLink>
            </div>

            <div v-if="isLoadingEvents" class="loading">Chargement...</div>
            <div v-else-if="errorEvents" class="error">{{ errorEvents }}</div>
            <div v-else-if="evenements.length === 0" class="empty">Aucun événement à venir.</div>

            <div v-else class="cards-grid">
                <RouterLink
                    v-for="event in evenements"
                    :key="event.id"
                    :to="`/evenements/${event.slug}`"
                    class="card"
                >
                    <div class="card-image">
                        <img
                            v-if="event.image"
                            :src="`http://127.0.0.1:8000/storage/${event.image}`"
                            :alt="event.titre"
                        />
                        <div v-else class="card-image-placeholder">📅</div>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">{{ event.titre }}</h3>
                        <p class="card-meta">
                            📅 {{ formatDate(event.date_debut) }}
                        </p>
                        <p class="card-meta">
                            📍 {{ event.lieu }}
                        </p>
                    </div>
                </RouterLink>
            </div>
        </section>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.accueil-page {
    min-height: 60vh;
}

// =============================================
// HERO
// =============================================
.hero {
    background: linear-gradient(135deg, $color-primary, $color-secondary);
    color: $color-white;
    text-align: center;
    padding: $spacing-2xl * 2 $spacing-lg;
}

.hero-content {
    max-width: 700px;
    margin: 0 auto;
}

.hero h1 {
    font-size: 3.5rem;
    font-weight: $font-weight-bold;
    letter-spacing: 4px;
    margin-bottom: $spacing-md;
}

.hero-subtitle {
    font-size: $font-size-xl;
    font-weight: $font-weight-semi-bold;
    margin-bottom: $spacing-md;
    opacity: 0.95;
}

.hero-description {
    font-size: $font-size-lg;
    opacity: 0.85;
    margin-bottom: $spacing-xl;
    line-height: 1.6;
}

.hero-actions {
    display: flex;
    gap: $spacing-md;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-hero {
    padding: $spacing-sm $spacing-xl;
    border-radius: $radius-md;
    font-size: $font-size-base;
    font-weight: $font-weight-semi-bold;
    text-decoration: none;
    transition: background-color $transition-base, transform $transition-base;

    &:hover {
        transform: translateY(-2px);
    }

    &--primary {
        background-color: $color-accent;
        color: $color-primary-dark;

        &:hover {
            background-color: $color-accent-light;
        }
    }

    &--secondary {
        background-color: transparent;
        color: $color-white;
        border: 2px solid $color-white;

        &:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
    }
}

// =============================================
// SECTIONS
// =============================================
.section {
    max-width: 1200px;
    margin: 0 auto;
    padding: $spacing-2xl $spacing-lg;

    &--alt {
        background-color: $color-white;
        max-width: 100%;
        padding: $spacing-2xl;

        .cards-grid {
            max-width: 1200px;
            margin: 0 auto;
        }
    }
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: $spacing-lg;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;

    h2 {
        font-size: $font-size-2xl;
        font-weight: $font-weight-bold;
        color: $color-primary;
    }
}

.section-link {
    color: $color-secondary;
    font-size: $font-size-sm;
    font-weight: $font-weight-medium;
    text-decoration: none;

    &:hover {
        color: $color-accent;
    }
}

.loading,
.error,
.empty {
    text-align: center;
    padding: $spacing-xl;
    font-size: $font-size-lg;
    color: $color-text-light;
}

.error {
    color: $color-danger;
}

// =============================================
// CARDS
// =============================================
.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
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

.card-meta {
    font-size: $font-size-sm;
    color: $color-text-light;
    margin-bottom: $spacing-xs;
}

.card-date {
    font-size: 0.8rem;
    color: $color-text-light;
}

// =============================================
// RESPONSIVE
// =============================================
@media (max-width: $breakpoint-mobile) {
    .hero {
        padding: $spacing-2xl $spacing-md;
    }

    .hero h1 {
        font-size: 2.5rem;
    }

    .hero-subtitle {
        font-size: $font-size-lg;
    }

    .hero-description {
        font-size: $font-size-base;
    }

    .hero-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .btn-hero {
        text-align: center;
    }

    .section {
        padding: $spacing-xl $spacing-md;

        &--alt {
            padding: $spacing-xl $spacing-md;
        }
    }

    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: $spacing-sm;
    }

    .cards-grid {
        grid-template-columns: 1fr;
    }
}
</style>