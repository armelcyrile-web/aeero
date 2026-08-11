<!-- src/views/public/Bureau.vue -->

<script setup>
import { ref, onMounted } from 'vue';
import apiClient from '@/api/axios';

const membres = ref([]);
const isLoading = ref(true);
const error = ref(null);

async function fetchMembres() {
    isLoading.value = true;
    error.value = null;

    try {
        const response = await apiClient.get('/membres-bureau');
        // Tri défensif par ordre_affichage
        membres.value = response.data.sort((a, b) => a.ordre_affichage - b.ordre_affichage);
    } catch (err) {
        error.value = 'Impossible de charger les membres du bureau. Veuillez réessayer plus tard.';
    } finally {
        isLoading.value = false;
    }
}

onMounted(() => {
    fetchMembres();
});
</script>

<template>
    <div class="bureau-page">
        <section class="page-hero">
            <h1>Bureau Exécutif</h1>
            <p>Les membres qui font vivre l'association au quotidien</p>
        </section>

        <section class="page-content">
            <div v-if="isLoading" class="loading">Chargement des membres...</div>

            <div v-else-if="error" class="error">{{ error }}</div>

            <div v-else-if="membres.length === 0" class="empty">
                Aucun membre pour le moment.
            </div>

            <div v-else class="membres-grid">
                <div
                    v-for="membre in membres"
                    :key="membre.id"
                    class="membre-card"
                >
                    <div class="membre-photo">
                        <img
                            v-if="membre.photo"
                            :src="`http://127.0.0.1:8000/storage/${membre.photo}`"
                            :alt="membre.nom"
                        />
                        <div v-else class="membre-avatar">
                            {{ membre.nom.charAt(0).toUpperCase() }}
                        </div>
                    </div>
                    <div class="membre-info">
                        <h3 class="membre-nom">{{ membre.nom }}</h3>
                        <span class="membre-poste">{{ membre.poste }}</span>
                        <p v-if="membre.bio" class="membre-bio">{{ membre.bio }}</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.bureau-page {
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
    max-width: 1000px;
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

.membres-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: $spacing-lg;
}

.membre-card {
    background-color: $color-white;
    border-radius: $radius-lg;
    padding: $spacing-lg;
    display: flex;
    gap: $spacing-md;
    box-shadow: $shadow-sm;
    transition: transform $transition-base, box-shadow $transition-base;

    &:hover {
        transform: translateY(-2px);
        box-shadow: $shadow-md;
    }
}

.membre-photo {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    border-radius: $radius-full;
    overflow: hidden;

    img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
}

.membre-avatar {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: $color-secondary;
    color: $color-white;
    font-size: $font-size-2xl;
    font-weight: $font-weight-bold;
}

.membre-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: $spacing-xs;
}

.membre-nom {
    font-size: $font-size-lg;
    font-weight: $font-weight-semi-bold;
    color: $color-primary;
}

.membre-poste {
    font-size: $font-size-sm;
    font-weight: $font-weight-medium;
    color: $color-accent;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.membre-bio {
    font-size: $font-size-sm;
    color: $color-text-light;
    line-height: 1.5;
    margin-top: $spacing-xs;
}

@media (max-width: $breakpoint-mobile) {
    .page-hero {
        padding: $spacing-xl $spacing-md;

        h1 {
            font-size: $font-size-2xl;
        }
    }

    .membres-grid {
        grid-template-columns: 1fr;
    }

    .membre-card {
        padding: $spacing-md;
    }

    .membre-photo {
        width: 60px;
        height: 60px;
    }
}
</style>