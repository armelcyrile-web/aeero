<!-- src/views/admin/Dashboard.vue -->

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import apiClient from '@/api/axios';

const authStore = useAuthStore();

const stats = ref({
    actualites: { en_attente: 0, brouillon: 0, publie: 0, rejete: 0 },
    evenements: { en_attente: 0, brouillon: 0, publie: 0, rejete: 0 },
    programmes: { en_attente: 0, brouillon: 0, publie: 0, rejete: 0 },
    partenaires: { en_attente: 0, brouillon: 0, publie: 0, rejete: 0 },
    membres_bureau: { en_attente: 0, brouillon: 0, publie: 0, rejete: 0 },
});

const isLoading = ref(true);
const error = ref('');

const totalEnAttente = computed(() => {
    let total = 0;
    for (const module of Object.values(stats.value)) {
        total += module.en_attente;
    }
    return total;
});

const modules = [
    { key: 'actualites', label: 'Actualités', route: '/admin/actualites' },
    { key: 'evenements', label: 'Événements', route: '/admin/evenements' },
    { key: 'programmes', label: 'Programmes', route: '/admin/programmes' },
    { key: 'partenaires', label: 'Partenaires', route: '/admin/partenaires' },
    { key: 'membres_bureau', label: 'Membres bureau', route: '/admin/membres-bureau' },
];

async function fetchPresidentStats() {
    const promises = modules.map(async (mod) => {
        const response = await apiClient.get(`/admin/${mod.key}?statut=en_attente`);
        stats.value[mod.key].en_attente = response.data.meta?.total || response.data.data?.length || 0;
    });

    await Promise.all(promises);
}

async function fetchSecretaireStats() {
    for (const mod of modules) {
        try {
            const response = await apiClient.get(`/admin/${mod.key}`);
            const items = response.data.data || [];

            stats.value[mod.key] = {
                brouillon: items.filter(i => i.statut === 'brouillon').length,
                en_attente: items.filter(i => i.statut === 'en_attente').length,
                publie: items.filter(i => i.statut === 'publie').length,
                rejete: items.filter(i => i.statut === 'rejete').length,
            };
        } catch (err) {
            console.error(`Erreur chargement stats ${mod.key}:`, err);
        }
    }
}

async function fetchStats() {
    isLoading.value = true;
    error.value = '';

    try {
        if (authStore.isPresident) {
            await fetchPresidentStats();
        } else {
            await fetchSecretaireStats();
        }
    } catch (err) {
        error.value = 'Impossible de charger les statistiques.';
    } finally {
        isLoading.value = false;
    }
}

onMounted(() => {
    fetchStats();
});
</script>

<template>
    <div class="dashboard">
        <div class="dashboard-header">
            <h1>Bonjour, {{ authStore.user?.name }}</h1>
            <span class="role-tag" :class="authStore.isPresident ? 'role-tag--president' : 'role-tag--secretaire'">
                {{ authStore.isPresident ? 'Président' : 'Secrétaire Général' }}
            </span>
        </div>

        <div v-if="isLoading" class="loading">Chargement du tableau de bord...</div>

        <div v-else-if="error" class="error">{{ error }}</div>

        <template v-else>
            <!-- Vue Président -->
            <div v-if="authStore.isPresident" class="stats-section">
                <h2>Publications en attente de validation</h2>

                <div v-if="totalEnAttente === 0" class="empty">
                    ✅ Aucune publication en attente pour le moment.
                </div>

                <div v-else class="stats-grid">
                    <router-link
                        v-for="mod in modules"
                        :key="mod.key"
                        :to="`${mod.route}?statut=en_attente`"
                        class="stat-card"
                        :class="{ 'stat-card--highlight': stats[mod.key].en_attente > 0 }"
                    >
                        <span class="stat-number">{{ stats[mod.key].en_attente }}</span>
                        <span class="stat-label">{{ mod.label }}</span>
                    </router-link>
                </div>
            </div>

            <!-- Vue Secrétaire Général -->
            <div v-if="authStore.isSecretaire" class="stats-section">
                <h2>Résumé de mes publications</h2>

                <div class="stats-grid stats-grid--wide">
                    <router-link
                        v-for="mod in modules"
                        :key="mod.key"
                        :to="mod.route"
                        class="stat-card stat-card--detailed"
                    >
                        <span class="stat-label">{{ mod.label }}</span>
                        <div class="stat-details">
                            <span class="stat-detail stat-detail--brouillon">
                                {{ stats[mod.key].brouillon }} brouillons
                            </span>
                            <span class="stat-detail stat-detail--attente">
                                {{ stats[mod.key].en_attente }} en attente
                            </span>
                            <span class="stat-detail stat-detail--publie">
                                {{ stats[mod.key].publie }} publiés
                            </span>
                            <span class="stat-detail stat-detail--rejete">
                                {{ stats[mod.key].rejete }} rejetés
                            </span>
                        </div>
                    </router-link>
                </div>
            </div>
        </template>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.dashboard {
    max-width: 1200px;
}

.dashboard-header {
    display: flex;
    align-items: center;
    gap: $spacing-md;
    margin-bottom: $spacing-xl;

    h1 {
        font-size: $font-size-2xl;
        font-weight: $font-weight-bold;
        color: $color-text;
    }
}

.role-tag {
    font-size: $font-size-sm;
    padding: 2px $spacing-sm;
    border-radius: $radius-full;
    font-weight: $font-weight-semi-bold;

    &--president {
        background-color: $color-accent;
        color: $color-primary-dark;
    }

    &--secretaire {
        background-color: $color-secondary-light;
        color: $color-white;
    }
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
    color: $color-success;
}

.stats-section {
    h2 {
        font-size: $font-size-lg;
        font-weight: $font-weight-semi-bold;
        color: $color-text;
        margin-bottom: $spacing-lg;
    }
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: $spacing-md;

    &--wide {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    }
}

.stat-card {
    background-color: $color-white;
    border-radius: $radius-lg;
    padding: $spacing-lg;
    text-align: center;
    text-decoration: none;
    color: $color-text;
    box-shadow: $shadow-sm;
    transition: transform $transition-base, box-shadow $transition-base;

    &:hover {
        transform: translateY(-2px);
        box-shadow: $shadow-md;
    }

    &--highlight {
        border: 2px solid $color-warning;
    }
}

.stat-number {
    display: block;
    font-size: $font-size-3xl;
    font-weight: $font-weight-bold;
    color: $color-primary;
}

.stat-label {
    display: block;
    font-size: $font-size-sm;
    color: $color-text-light;
    margin-top: $spacing-xs;
}

.stat-card--detailed {
    text-align: left;
    padding: $spacing-md $spacing-lg;
}

.stat-details {
    display: flex;
    flex-wrap: wrap;
    gap: $spacing-sm;
    margin-top: $spacing-sm;
}

.stat-detail {
    font-size: 0.75rem;
    padding: 2px $spacing-sm;
    border-radius: $radius-sm;
    font-weight: $font-weight-medium;

    &--brouillon {
        background-color: $color-bg;
        color: $color-text-light;
    }

    &--attente {
        background-color: lighten($color-warning, 40%);
        color: darken($color-warning, 10%);
    }

    &--publie {
        background-color: lighten($color-success, 45%);
        color: darken($color-success, 10%);
    }

    &--rejete {
        background-color: lighten($color-danger, 40%);
        color: darken($color-danger, 10%);
    }
}

@media (max-width: $breakpoint-mobile) {
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
        gap: $spacing-sm;
    }

    .stats-grid {
        grid-template-columns: 1fr;

        &--wide {
            grid-template-columns: 1fr;
        }
    }
}
</style>