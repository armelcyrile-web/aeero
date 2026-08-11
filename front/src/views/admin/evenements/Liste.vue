<!-- src/views/admin/evenements/Liste.vue -->

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import apiClient from '@/api/axios';

const router = useRouter();
const authStore = useAuthStore();

const evenements = ref([]);
const isLoading = ref(true);
const error = ref('');
const filtreStatut = ref('');

const showRejectModal = ref(false);
const rejectEvenement = ref(null);
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

async function fetchEvenements() {
    isLoading.value = true;
    error.value = '';

    try {
        const params = {};
        if (filtreStatut.value) params.statut = filtreStatut.value;

        const response = await apiClient.get('/admin/evenements', { params });
        evenements.value = response.data.data || [];
    } catch (err) {
        error.value = 'Impossible de charger les événements.';
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

function canEdit(evenement) {
    if (authStore.isPresident) return true;
    if (authStore.isSecretaire && evenement.auteur_id === currentUser.value?.id && ['brouillon', 'rejete'].includes(evenement.statut)) return true;
    return false;
}

function canSubmit(evenement) {
    return authStore.isSecretaire && evenement.auteur_id === currentUser.value?.id && ['brouillon', 'rejete'].includes(evenement.statut);
}

function canValidate(evenement) {
    return authStore.isPresident && evenement.statut === 'en_attente';
}

function canReject(evenement) {
    return authStore.isPresident && evenement.statut === 'en_attente';
}

function canDelete(evenement) {
    return authStore.isPresident;
}

async function handleSubmit(evenement) {
    try {
        await apiClient.post(`/admin/evenements/${evenement.id}/submit`);
        await fetchEvenements();
    } catch (err) {
        alert('Erreur lors de la soumission.');
    }
}

async function handleValidate(evenement) {
    try {
        await apiClient.post(`/admin/evenements/${evenement.id}/validate`);
        await fetchEvenements();
    } catch (err) {
        alert('Erreur lors de la validation.');
    }
}

function openRejectModal(evenement) {
    rejectEvenement.value = evenement;
    motifRejet.value = '';
    rejectError.value = '';
    showRejectModal.value = true;
}

function closeRejectModal() {
    showRejectModal.value = false;
    rejectEvenement.value = null;
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
        await apiClient.post(`/admin/evenements/${rejectEvenement.value.id}/reject`, { motif_rejet: motifRejet.value });
        closeRejectModal();
        await fetchEvenements();
    } catch (err) {
        rejectError.value = err.response?.data?.errors?.motif_rejet?.[0] || 'Erreur lors du rejet.';
    } finally {
        isRejecting.value = false;
    }
}

async function handleDelete(evenement) {
    if (!confirm(`Supprimer l'événement "${evenement.titre}" ?`)) return;
    try {
        await apiClient.delete(`/admin/evenements/${evenement.id}`);
        await fetchEvenements();
    } catch (err) {
        alert('Erreur lors de la suppression.');
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
}

onMounted(() => fetchEvenements());
</script>

<template>
    <div class="liste-page">
        <div class="page-top">
            <h1>Événements</h1>
            <div class="page-actions">
                <select v-model="filtreStatut" class="filter-select" @change="fetchEvenements">
                    <option v-for="s in statuts" :key="s.value" :value="s.value">{{ s.label }}</option>
                </select>
                <button class="btn-primary" @click="router.push('/admin/evenements/nouveau')">+ Nouvel événement</button>
            </div>
        </div>

        <div v-if="isLoading" class="loading">Chargement...</div>
        <div v-else-if="error" class="error">{{ error }}</div>
        <div v-else-if="evenements.length === 0" class="empty">Aucun événement trouvé.</div>

        <div v-else class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Date</th>
                        <th>Lieu</th>
                        <th>Statut</th>
                        <th>Auteur</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="evenement in evenements" :key="evenement.id">
                        <td class="cell-title">
                            {{ evenement.titre }}
                            <div v-if="evenement.statut === 'rejete' && evenement.motif_rejet" class="motif-rejet">Motif : {{ evenement.motif_rejet }}</div>
                        </td>
                        <td>{{ formatDate(evenement.date_debut) }}</td>
                        <td>{{ evenement.lieu }}</td>
                        <td><span class="statut-badge" :class="getStatutClass(evenement.statut)">{{ getStatutLabel(evenement.statut) }}</span></td>
                        <td>{{ evenement.auteur?.name || '-' }}</td>
                        <td class="cell-actions">
                            <button v-if="canEdit(evenement)" class="btn-action btn-action--edit" @click="router.push(`/admin/evenements/${evenement.id}/modifier`)" title="Modifier">✏️</button>
                            <button v-if="canSubmit(evenement)" class="btn-action btn-action--submit" @click="handleSubmit(evenement)" title="Soumettre">📤</button>
                            <button v-if="canValidate(evenement)" class="btn-action btn-action--validate" @click="handleValidate(evenement)" title="Valider">✅</button>
                            <button v-if="canReject(evenement)" class="btn-action btn-action--reject" @click="openRejectModal(evenement)" title="Rejeter">❌</button>
                            <button v-if="canDelete(evenement)" class="btn-action btn-action--delete" @click="handleDelete(evenement)" title="Supprimer">🗑️</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Teleport to="body">
            <div v-if="showRejectModal" class="modal-overlay" @click.self="closeRejectModal">
                <div class="modal">
                    <h3>Rejeter l'événement</h3>
                    <p class="modal-subtitle">{{ rejectEvenement?.titre }}</p>
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

.liste-page { max-width: 1200px; }
.page-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: $spacing-lg; flex-wrap: wrap; gap: $spacing-md;
    h1 { font-size: $font-size-2xl; font-weight: $font-weight-bold; color: $color-text; }
}
.page-actions { display: flex; gap: $spacing-sm; }
.filter-select { padding: $spacing-sm $spacing-md; border: 1px solid $color-border; border-radius: $radius-md; font-family: $font-family-base; font-size: $font-size-sm; color: $color-text; background-color: $color-white; cursor: pointer; outline: none;
    &:focus { border-color: $color-secondary; }
}
.btn-primary { background-color: $color-secondary; color: $color-white; border: none; padding: $spacing-sm $spacing-lg; border-radius: $radius-md; font-size: $font-size-sm; font-weight: $font-weight-semi-bold; cursor: pointer;
    &:hover { background-color: $color-secondary-dark; }
}
.loading, .error, .empty { text-align: center; padding: $spacing-2xl; font-size: $font-size-lg; }
.error { color: $color-danger; }
.empty { color: $color-text-light; }
.table-wrapper { background-color: $color-white; border-radius: $radius-lg; box-shadow: $shadow-sm; overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; font-size: $font-size-sm;
    thead { background-color: $color-bg; border-bottom: 2px solid $color-border;
        th { padding: $spacing-md; text-align: left; font-weight: $font-weight-semi-bold; color: $color-text; white-space: nowrap; }
    }
    tbody tr { border-bottom: 1px solid $color-border;
        &:hover { background-color: lighten($color-bg, 2%); }
        &:last-child { border-bottom: none; }
    }
    tbody td { padding: $spacing-md; vertical-align: top; }
}
.cell-title { max-width: 250px; font-weight: $font-weight-medium; }
.motif-rejet { font-size: 0.75rem; color: $color-danger; margin-top: $spacing-xs; font-style: italic; font-weight: $font-weight-regular; }
.statut-badge { display: inline-block; padding: 2px $spacing-sm; border-radius: $radius-full; font-size: 0.75rem; font-weight: $font-weight-semi-bold; white-space: nowrap;
    &--brouillon { background-color: $color-bg; color: $color-text-light; border: 1px solid $color-border; }
    &--en_attente { background-color: lighten($color-warning, 40%); color: darken($color-warning, 10%); }
    &--publie { background-color: lighten($color-success, 45%); color: darken($color-success, 10%); }
    &--rejete { background-color: lighten($color-danger, 40%); color: darken($color-danger, 10%); }
}
.cell-actions { display: flex; gap: $spacing-xs; flex-wrap: nowrap; }
.btn-action { background: none; border: 1px solid $color-border; border-radius: $radius-sm; padding: 4px 6px; cursor: pointer; font-size: 0.9rem;
    &:hover { background-color: $color-bg; }
    &--edit:hover { border-color: $color-secondary; }
    &--submit:hover { border-color: $color-warning; }
    &--validate:hover { border-color: $color-success; }
    &--reject:hover { border-color: $color-danger; }
    &--delete:hover { border-color: $color-danger; background-color: lighten($color-danger, 40%); }
}
.modal-overlay { position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 200; display: flex; align-items: center; justify-content: center; padding: $spacing-md; }
.modal { background-color: $color-white; border-radius: $radius-lg; padding: $spacing-xl; width: 100%; max-width: 500px; box-shadow: $shadow-lg;
    h3 { font-size: $font-size-lg; font-weight: $font-weight-semi-bold; margin-bottom: $spacing-xs; }
}
.modal-subtitle { font-size: $font-size-sm; color: $color-text-light; margin-bottom: $spacing-md; }
.form-group { margin-bottom: $spacing-md;
    label { display: block; font-size: $font-size-sm; font-weight: $font-weight-medium; margin-bottom: $spacing-xs; }
    textarea { width: 100%; padding: $spacing-sm; border: 1px solid $color-border; border-radius: $radius-md; font-family: $font-family-base; font-size: $font-size-sm; resize: vertical; outline: none;
        &:focus { border-color: $color-secondary; box-shadow: 0 0 0 2px rgba($color-secondary, 0.1); }
    }
}
.field-error { font-size: 0.8rem; color: $color-danger; margin-top: $spacing-xs; display: block; }
.modal-actions { display: flex; justify-content: flex-end; gap: $spacing-sm; }
.btn-cancel { background-color: $color-bg; color: $color-text; border: 1px solid $color-border; padding: $spacing-sm $spacing-lg; border-radius: $radius-md; font-size: $font-size-sm; cursor: pointer;
    &:hover:not(:disabled) { background-color: darken($color-bg, 5%); }
}
.btn-reject { background-color: $color-danger; color: $color-white; border: none; padding: $spacing-sm $spacing-lg; border-radius: $radius-md; font-size: $font-size-sm; font-weight: $font-weight-semi-bold; cursor: pointer;
    &:hover:not(:disabled) { background-color: darken($color-danger, 10%); }
    &:disabled { opacity: 0.6; cursor: not-allowed; }
}
@media (max-width: $breakpoint-mobile) {
    .page-top { flex-direction: column; align-items: stretch; }
    .page-actions { flex-direction: column; }
    .cell-actions { flex-wrap: wrap; }
    .data-table { font-size: 0.8rem; thead th, tbody td { padding: $spacing-sm; } }
}
</style>