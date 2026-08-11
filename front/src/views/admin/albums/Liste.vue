<!-- src/views/admin/albums/Liste.vue -->

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import apiClient from '@/api/axios';

const router = useRouter();
const authStore = useAuthStore();

const albums = ref([]);
const isLoading = ref(true);
const error = ref('');
const filtreStatut = ref('');

const showRejectModal = ref(false);
const rejectAlbum = ref(null);
const motifRejet = ref('');
const rejectError = ref('');
const isRejecting = ref(false);

const statuts = [
    { value: '', label: 'Tous' },
    { value: 'brouillon', label: 'Brouillon' },
    { value: 'en_attente', label: 'En attente' },
    { value: 'publie', label: 'Publié' },
    { value: 'rejete', label: 'Rejeté' },
];

const currentUser = computed(() => authStore.user);

async function fetchAlbums() {
    isLoading.value = true;
    error.value = '';

    try {
        const params = {};
        if (filtreStatut.value) params.statut = filtreStatut.value;

        const response = await apiClient.get('/admin/albums', { params });
        albums.value = response.data.data || [];
    } catch (err) {
        error.value = 'Impossible de charger les albums.';
    } finally {
        isLoading.value = false;
    }
}

function getStatutClass(statut) {
    return `statut--${statut}`;
}

function getStatutLabel(statut) {
    const labels = { brouillon: 'Brouillon', en_attente: 'En attente', publie: 'Publié', rejete: 'Rejeté' };
    return labels[statut] || statut;
}

function getAlbumParent(album) {
    if (album.evenement) return album.evenement.titre;
    if (album.programme) return album.programme.titre;
    return '-';
}

function canEdit(album) {
    if (authStore.isPresident) return true;
    if (authStore.isSecretaire && album.auteur_id === currentUser.value?.id && ['brouillon', 'rejete'].includes(album.statut)) return true;
    return false;
}

function canSubmit(album) {
    return authStore.isSecretaire && album.auteur_id === currentUser.value?.id && ['brouillon', 'rejete'].includes(album.statut);
}

function canValidate(album) {
    return authStore.isPresident && album.statut === 'en_attente';
}

function canReject(album) {
    return authStore.isPresident && album.statut === 'en_attente';
}

function canDelete(album) {
    return authStore.isPresident;
}

async function handleSubmit(album) {
    try {
        await apiClient.post(`/admin/albums/${album.id}/submit`);
        await fetchAlbums();
    } catch (err) {
        alert('Erreur lors de la soumission.');
    }
}

async function handleValidate(album) {
    try {
        await apiClient.post(`/admin/albums/${album.id}/validate`);
        await fetchAlbums();
    } catch (err) {
        alert('Erreur lors de la validation.');
    }
}

function openRejectModal(album) {
    rejectAlbum.value = album;
    motifRejet.value = '';
    rejectError.value = '';
    showRejectModal.value = true;
}

function closeRejectModal() {
    showRejectModal.value = false;
    rejectAlbum.value = null;
    motifRejet.value = '';
    rejectError.value = '';
}

async function handleReject() {
    if (!motifRejet.value.trim()) {
        rejectError.value = 'Le motif de rejet est obligatoire.';
        return;
    }

    isRejecting.value = true;
    rejectError.value = '';

    try {
        await apiClient.post(`/admin/albums/${rejectAlbum.value.id}/reject`, { motif_rejet: motifRejet.value });
        closeRejectModal();
        await fetchAlbums();
    } catch (err) {
        rejectError.value = err.response?.data?.errors?.motif_rejet?.[0] || 'Erreur lors du rejet.';
    } finally {
        isRejecting.value = false;
    }
}

async function handleDelete(album) {
    if (!confirm(`Supprimer l'album "${album.titre}" ?`)) return;

    try {
        await apiClient.delete(`/admin/albums/${album.id}`);
        await fetchAlbums();
    } catch (err) {
        alert('Erreur lors de la suppression.');
    }
}

function getCoverPhoto(album) {
    return album.photos && album.photos.length > 0 ? album.photos[0] : null;
}

onMounted(() => {
    fetchAlbums();
});
</script>

<template>
    <div class="liste-page">
        <div class="page-top">
            <h1>Albums photo</h1>
            <div class="page-actions">
                <select v-model="filtreStatut" class="filter-select" @change="fetchAlbums">
                    <option v-for="s in statuts" :key="s.value" :value="s.value">{{ s.label }}</option>
                </select>
                <button class="btn-primary" @click="router.push('/admin/albums/nouveau')">+ Nouvel album</button>
            </div>
        </div>

        <div v-if="isLoading" class="loading">Chargement...</div>
        <div v-else-if="error" class="error">{{ error }}</div>
        <div v-else-if="albums.length === 0" class="empty">Aucun album trouvé.</div>

        <div v-else class="cards-grid">
            <div v-for="album in albums" :key="album.id" class="album-card">
                <div class="album-cover">
                    <img
                        v-if="getCoverPhoto(album)"
                        :src="`http://127.0.0.1:8000/storage/${getCoverPhoto(album).chemin}`"
                        :alt="album.titre"
                    />
                    <div v-else class="album-cover-placeholder">🖼️</div>
                    <div class="album-photo-count">
                        {{ album.photos?.length || 0 }} photo{{ album.photos?.length !== 1 ? 's' : '' }}
                    </div>
                </div>
                <div class="album-body">
                    <h3 class="album-title">{{ album.titre }}</h3>
                    <p class="album-parent">{{ getAlbumParent(album) }}</p>
                    <span class="statut-badge" :class="getStatutClass(album.statut)">
                        {{ getStatutLabel(album.statut) }}
                    </span>
                    <div v-if="album.statut === 'rejete' && album.motif_rejet" class="motif-rejet">
                        Motif : {{ album.motif_rejet }}
                    </div>
                </div>
                <div class="album-actions">
                    <button v-if="canEdit(album)" class="btn-action btn-action--edit" @click="router.push(`/admin/albums/${album.id}/modifier`)" title="Modifier">✏️</button>
                    <button v-if="canSubmit(album)" class="btn-action btn-action--submit" @click="handleSubmit(album)" title="Soumettre">📤</button>
                    <button v-if="canValidate(album)" class="btn-action btn-action--validate" @click="handleValidate(album)" title="Valider">✅</button>
                    <button v-if="canReject(album)" class="btn-action btn-action--reject" @click="openRejectModal(album)" title="Rejeter">❌</button>
                    <button v-if="canDelete(album)" class="btn-action btn-action--delete" @click="handleDelete(album)" title="Supprimer">🗑️</button>
                </div>
            </div>
        </div>

        <!-- Modal rejet -->
        <Teleport to="body">
            <div v-if="showRejectModal" class="modal-overlay" @click.self="closeRejectModal">
                <div class="modal">
                    <h3>Rejeter l'album</h3>
                    <p class="modal-subtitle">{{ rejectAlbum?.titre }}</p>
                    <div class="form-group">
                        <label for="motif">Motif du rejet *</label>
                        <textarea id="motif" v-model="motifRejet" rows="4" placeholder="Expliquez la raison du rejet..."></textarea>
                        <span v-if="rejectError" class="field-error">{{ rejectError }}</span>
                    </div>
                    <div class="modal-actions">
                        <button class="btn-cancel" @click="closeRejectModal" :disabled="isRejecting">Annuler</button>
                        <button class="btn-reject" @click="handleReject" :disabled="isRejecting">{{ isRejecting ? 'Rejet...' : 'Confirmer le rejet' }}</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.liste-page {
    max-width: 1200px;
}

.page-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: $spacing-lg;
    flex-wrap: wrap;
    gap: $spacing-md;

    h1 {
        font-size: $font-size-2xl;
        font-weight: $font-weight-bold;
        color: $color-text;
    }
}

.page-actions {
    display: flex;
    gap: $spacing-sm;
}

.filter-select {
    padding: $spacing-sm $spacing-md;
    border: 1px solid $color-border;
    border-radius: $radius-md;
    font-family: $font-family-base;
    font-size: $font-size-sm;
    color: $color-text;
    background-color: $color-white;
    cursor: pointer;
    outline: none;

    &:focus {
        border-color: $color-secondary;
    }
}

.btn-primary {
    background-color: $color-secondary;
    color: $color-white;
    border: none;
    padding: $spacing-sm $spacing-lg;
    border-radius: $radius-md;
    font-size: $font-size-sm;
    font-weight: $font-weight-semi-bold;
    cursor: pointer;

    &:hover {
        background-color: $color-secondary-dark;
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
    color: $color-text-light;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: $spacing-lg;
}

.album-card {
    background-color: $color-white;
    border-radius: $radius-lg;
    overflow: hidden;
    box-shadow: $shadow-sm;
    transition: transform $transition-base, box-shadow $transition-base;
    display: flex;
    flex-direction: column;

    &:hover {
        transform: translateY(-2px);
        box-shadow: $shadow-md;
    }
}

.album-cover {
    width: 100%;
    height: 180px;
    position: relative;
    overflow: hidden;

    img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
}

.album-cover-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, $color-primary-light, $color-secondary-light);
    font-size: 3rem;
}

.album-photo-count {
    position: absolute;
    bottom: $spacing-sm;
    right: $spacing-sm;
    background-color: rgba(0, 0, 0, 0.7);
    color: $color-white;
    padding: 2px $spacing-sm;
    border-radius: $radius-sm;
    font-size: 0.75rem;
}

.album-body {
    padding: $spacing-md;
    flex: 1;
}

.album-title {
    font-size: $font-size-base;
    font-weight: $font-weight-semi-bold;
    color: $color-primary;
    margin-bottom: $spacing-xs;
}

.album-parent {
    font-size: $font-size-sm;
    color: $color-text-light;
    margin-bottom: $spacing-sm;
}

.statut-badge {
    display: inline-block;
    padding: 2px $spacing-sm;
    border-radius: $radius-full;
    font-size: 0.75rem;
    font-weight: $font-weight-semi-bold;
    white-space: nowrap;

    &--brouillon {
        background-color: $color-bg;
        color: $color-text-light;
        border: 1px solid $color-border;
    }

    &--en_attente {
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

.motif-rejet {
    font-size: 0.75rem;
    color: $color-danger;
    margin-top: $spacing-xs;
    font-style: italic;
}

.album-actions {
    display: flex;
    gap: $spacing-xs;
    padding: $spacing-sm $spacing-md;
    border-top: 1px solid $color-border;
    justify-content: flex-end;
}

.btn-action {
    background: none;
    border: 1px solid $color-border;
    border-radius: $radius-sm;
    padding: 4px 6px;
    cursor: pointer;
    font-size: 0.9rem;

    &:hover {
        background-color: $color-bg;
    }

    &--edit:hover { border-color: $color-secondary; }
    &--submit:hover { border-color: $color-warning; }
    &--validate:hover { border-color: $color-success; }
    &--reject:hover { border-color: $color-danger; }
    &--delete:hover { border-color: $color-danger; background-color: lighten($color-danger, 40%); }
}

// Modal
.modal-overlay {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 200;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: $spacing-md;
}

.modal {
    background-color: $color-white;
    border-radius: $radius-lg;
    padding: $spacing-xl;
    width: 100%;
    max-width: 500px;
    box-shadow: $shadow-lg;

    h3 {
        font-size: $font-size-lg;
        font-weight: $font-weight-semi-bold;
        margin-bottom: $spacing-xs;
    }
}

.modal-subtitle {
    font-size: $font-size-sm;
    color: $color-text-light;
    margin-bottom: $spacing-md;
}

.form-group {
    margin-bottom: $spacing-md;

    label {
        display: block;
        font-size: $font-size-sm;
        font-weight: $font-weight-medium;
        margin-bottom: $spacing-xs;
    }

    textarea {
        width: 100%;
        padding: $spacing-sm;
        border: 1px solid $color-border;
        border-radius: $radius-md;
        font-family: $font-family-base;
        font-size: $font-size-sm;
        resize: vertical;
        outline: none;

        &:focus {
            border-color: $color-secondary;
            box-shadow: 0 0 0 2px rgba($color-secondary, 0.1);
        }
    }
}

.field-error {
    font-size: 0.8rem;
    color: $color-danger;
    margin-top: $spacing-xs;
    display: block;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: $spacing-sm;
}

.btn-cancel {
    background-color: $color-bg;
    color: $color-text;
    border: 1px solid $color-border;
    padding: $spacing-sm $spacing-lg;
    border-radius: $radius-md;
    font-size: $font-size-sm;
    cursor: pointer;

    &:hover:not(:disabled) {
        background-color: darken($color-bg, 5%);
    }
}

.btn-reject {
    background-color: $color-danger;
    color: $color-white;
    border: none;
    padding: $spacing-sm $spacing-lg;
    border-radius: $radius-md;
    font-size: $font-size-sm;
    font-weight: $font-weight-semi-bold;
    cursor: pointer;

    &:hover:not(:disabled) {
        background-color: darken($color-danger, 10%);
    }

    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
}

@media (max-width: $breakpoint-mobile) {
    .page-top {
        flex-direction: column;
        align-items: stretch;
    }

    .page-actions {
        flex-direction: column;
    }

    .cards-grid {
        grid-template-columns: 1fr;
    }
}
</style>