<!-- src/views/public/Partenaires.vue -->

<script setup>
import { ref, onMounted } from 'vue';
import apiClient from '@/api/axios';

const partenaires = ref([]);
const isLoading = ref(true);
const error = ref(null);

async function fetchPartenaires() {
    isLoading.value = true;
    error.value = null;

    try {
        const response = await apiClient.get('/partenaires');
        partenaires.value = response.data.data;
    } catch (err) {
        error.value = 'Impossible de charger les partenaires. Veuillez réessayer plus tard.';
    } finally {
        isLoading.value = false;
    }
}

onMounted(() => {
    fetchPartenaires();
});
</script>

<template>
    <div class="partenaires-page">
        <section class="page-hero">
            <h1>Partenaires</h1>
            <p>Ils nous font confiance et soutiennent nos actions</p>
        </section>

        <section class="page-content">
            <div v-if="isLoading" class="loading">Chargement des partenaires...</div>

            <div v-else-if="error" class="error">{{ error }}</div>

            <div v-else-if="partenaires.length === 0" class="empty">
                Aucun partenaire pour le moment.
            </div>

            <div v-else class="partenaires-grid">
                <a
                    v-for="partenaire in partenaires"
                    :key="partenaire.id"
                    :href="partenaire.lien_site || '#'"
                    :target="partenaire.lien_site ? '_blank' : '_self'"
                    :rel="partenaire.lien_site ? 'noopener noreferrer' : undefined"
                    class="partenaire-card"
                    :class="{ 'partenaire-card--link': partenaire.lien_site }"
                >
                    <div class="partenaire-logo">
                        <img
                            :src="`http://127.0.0.1:8000/storage/${partenaire.logo}`"
                            :alt="partenaire.nom"
                        />
                    </div>
                    <p class="partenaire-nom">{{ partenaire.nom }}</p>
                </a>
            </div>
        </section>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.partenaires-page {
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

.partenaires-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: $spacing-lg;
}

.partenaire-card {
    background-color: $color-white;
    border-radius: $radius-lg;
    padding: $spacing-xl;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: $spacing-md;
    text-decoration: none;
    color: $color-text;
    box-shadow: $shadow-sm;
    transition: transform $transition-base, box-shadow $transition-base;

    &--link {
        cursor: pointer;

        &:hover {
            transform: translateY(-4px);
            box-shadow: $shadow-lg;
        }
    }
}

.partenaire-logo {
    width: 120px;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;

    img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
}

.partenaire-nom {
    font-size: $font-size-sm;
    font-weight: $font-weight-medium;
    text-align: center;
    color: $color-text;
}

@media (max-width: $breakpoint-mobile) {
    .page-hero {
        padding: $spacing-xl $spacing-md;

        h1 {
            font-size: $font-size-2xl;
        }
    }

    .partenaires-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: $spacing-md;
    }

    .partenaire-card {
        padding: $spacing-md;
    }

    .partenaire-logo {
        width: 100px;
        height: 100px;
    }
}
</style>