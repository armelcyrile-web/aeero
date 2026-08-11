<!-- src/views/admin/albums/Formulaire.vue -->

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import apiClient from '@/api/axios';

const route = useRoute();
const router = useRouter();

const isEdit = ref(false);
const isLoading = ref(false);
const isSubmitting = ref(false);
const error = ref('');
const formErrors = ref({});

const form = ref({
    titre: '',
    type_parent: '',
    evenement_id: null,
    programme_id: null,
});

const evenements = ref([]);
const programmes = ref([]);
const existingPhotos = ref([]);
const newPhotos = ref([]);
const photoPreviews = ref([]);
const isUploadingPhotos = ref(false);
const uploadError = ref('');

async function fetchEvenements() {
    try {
        const response = await apiClient.get('/admin/evenements');
        evenements.value = response.data.data || [];
    } catch (err) {
        console.error('Erreur chargement événements:', err);
    }
}

async function fetchProgrammes() {
    try {
        const response = await apiClient.get('/admin/programmes');
        programmes.value = response.data.data || [];
    } catch (err) {
        console.error('Erreur chargement programmes:', err);
    }
}

async function loadAlbum() {
    if (!route.params.id) return;

    isEdit.value = true;
    isLoading.value = true;
    error.value = '';

    try {
        const response = await apiClient.get(`/admin/albums/${route.params.id}`);
        const album = response.data;

        form.value.titre = album.titre;
        if (album.evenement_id) {
            form.value.type_parent = 'evenement';
            form.value.evenement_id = album.evenement_id;
            form.value.programme_id = null;
        } else if (album.programme_id) {
            form.value.type_parent = 'programme';
            form.value.programme_id = album.programme_id;
            form.value.evenement_id = null;
        }

        existingPhotos.value = album.photos || [];
    } catch (err) {
        error.value = "Impossible de charger l'album.";
    } finally {
        isLoading.value = false;
    }
}

function handleNewPhotos(event) {
    const files = Array.from(event.target.files);
    newPhotos.value = [...newPhotos.value, ...files];

    files.forEach((file) => {
        photoPreviews.value.push(URL.createObjectURL(file));
    });

    event.target.value = '';
}

function removeNewPhoto(index) {
    URL.revokeObjectURL(photoPreviews.value[index]);
    newPhotos.value.splice(index, 1);
    photoPreviews.value.splice(index, 1);
}

async function handleDeletePhoto(photo) {
    if (!confirm('Supprimer cette photo ?')) return;

    try {
        await apiClient.delete(`/admin/photos/${photo.id}`);
        existingPhotos.value = existingPhotos.value.filter((p) => p.id !== photo.id);
    } catch (err) {
        alert('Erreur lors de la suppression de la photo.');
    }
}

async function uploadPhotos(albumId) {
    if (newPhotos.value.length === 0) return;

    isUploadingPhotos.value = true;
    uploadError.value = '';

    const formData = new FormData();
    newPhotos.value.forEach((file, index) => {
        formData.append(`photos[${index}][image]`, file);
        formData.append(`photos[${index}][legende]`, '');
    });

    try {
        await apiClient.post(`/admin/albums/${albumId}/photos`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
    } catch (err) {
        uploadError.value = "Erreur lors de l'upload des photos.";
        throw err;
    } finally {
        isUploadingPhotos.value = false;
    }
}

async function handleSubmit() {
    isSubmitting.value = true;
    error.value = '';
    formErrors.value = {};

    const albumData = {
        titre: form.value.titre,
        evenement_id: form.value.type_parent === 'evenement' ? form.value.evenement_id : null,
        programme_id: form.value.type_parent === 'programme' ? form.value.programme_id : null,
    };

    try {
        let albumId;

        if (isEdit.value) {
            const response = await apiClient.put(`/admin/albums/${route.params.id}`, albumData);
            albumId = route.params.id;
        } else {
            const response = await apiClient.post('/admin/albums', albumData);
            albumId = response.data.id;
        }

        if (newPhotos.value.length > 0) {
            await uploadPhotos(albumId);
        }

        router.push('/admin/albums');
    } catch (err) {
        if (err.response?.status === 422) {
            formErrors.value = err.response.data.errors || {};
        } else if (!uploadError.value) {
            error.value = "Une erreur est survenue lors de l'enregistrement.";
        }
    } finally {
        isSubmitting.value = false;
    }
}

function getFieldError(field) {
    return formErrors.value[field]?.[0] || '';
}

watch(
    () => form.value.type_parent,
    () => {
        form.value.evenement_id = null;
        form.value.programme_id = null;
    }
);

onMounted(async () => {
    await Promise.all([fetchEvenements(), fetchProgrammes()]);
    await loadAlbum();
});
</script>

<template>
    <div class="formulaire-page">
        <div class="page-top">
            <h1>{{ isEdit ? "Modifier l'album" : 'Nouvel album' }}</h1>
            <button class="btn-back" @click="router.push('/admin/albums')">← Retour à la liste</button>
        </div>

        <div v-if="isLoading" class="loading">Chargement...</div>
        <div v-else-if="error" class="error">{{ error }}</div>

        <form v-else class="form" @submit.prevent="handleSubmit">
            <div v-if="error && !isLoading" class="alert alert-error">{{ error }}</div>
            <div v-if="uploadError" class="alert alert-error">{{ uploadError }}</div>

            <div class="form-group">
                <label for="titre">Titre de l'album *</label>
                <input
                    id="titre"
                    v-model="form.titre"
                    type="text"
                    placeholder="Titre de l'album"
                    :class="{ 'input-error': getFieldError('titre') }"
                />
                <span v-if="getFieldError('titre')" class="field-error">{{ getFieldError('titre') }}</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="type_parent">Type de parent *</label>
                    <select id="type_parent" v-model="form.type_parent" class="form-select">
                        <option value="">-- Choisir --</option>
                        <option value="evenement">Événement</option>
                        <option value="programme">Programme</option>
                    </select>
                </div>

                <div class="form-group" v-if="form.type_parent === 'evenement'">
                    <label for="evenement_id">Événement *</label>
                    <select id="evenement_id" v-model="form.evenement_id" class="form-select">
                        <option :value="null">-- Choisir un événement --</option>
                        <option
                            v-for="ev in evenements"
                            :key="ev.id"
                            :value="ev.id"
                        >
                            {{ ev.titre }}
                        </option>
                    </select>
                </div>

                <div class="form-group" v-if="form.type_parent === 'programme'">
                    <label for="programme_id">Programme *</label>
                    <select id="programme_id" v-model="form.programme_id" class="form-select">
                        <option :value="null">-- Choisir un programme --</option>
                        <option
                            v-for="pr in programmes"
                            :key="pr.id"
                            :value="pr.id"
                        >
                            {{ pr.titre }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" @click="router.push('/admin/albums')" :disabled="isSubmitting">Annuler</button>
                <button type="submit" class="btn-submit" :disabled="isSubmitting || isUploadingPhotos">
                    {{ isSubmitting ? 'Enregistrement...' : (isEdit ? 'Mettre à jour' : "Créer l'album") }}
                </button>
            </div>
        </form>

        <!-- Section photos (visible seulement en modification) -->
        <div v-if="isEdit && !isLoading" class="photos-section">
            <h2>Photos de l'album</h2>

            <!-- Photos existantes -->
            <div v-if="existingPhotos.length > 0" class="photos-grid">
                <div v-for="photo in existingPhotos" :key="photo.id" class="photo-card">
                    <img
                        :src="`http://127.0.0.1:8000/storage/${photo.chemin}`"
                        :alt="photo.legende || 'Photo'"
                    />
                    <button class="btn-delete-photo" @click="handleDeletePhoto(photo)" title="Supprimer">✕</button>
                    <p v-if="photo.legende" class="photo-legende">{{ photo.legende }}</p>
                </div>
            </div>
            <div v-else class="empty-photos">Aucune photo dans cet album.</div>

            <!-- Nouvelles photos (preview) -->
            <div v-if="photoPreviews.length > 0" class="photos-grid">
                <div v-for="(preview, index) in photoPreviews" :key="'new-' + index" class="photo-card photo-card--new">
                    <img :src="preview" alt="Nouvelle photo" />
                    <button class="btn-delete-photo" @click="removeNewPhoto(index)" title="Retirer">✕</button>
                </div>
            </div>

            <!-- Upload -->
            <div class="upload-zone">
                <label for="new-photos" class="btn-upload">
                    + Ajouter des photos
                </label>
                <input
                    id="new-photos"
                    type="file"
                    accept="image/*"
                    multiple
                    @change="handleNewPhotos"
                    class="input-hidden"
                />
                <span class="upload-info">Sélectionnez une ou plusieurs images</span>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.formulaire-page {
    max-width: 900px;
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

.btn-back {
    background: none;
    border: none;
    color: $color-secondary;
    font-size: $font-size-sm;
    cursor: pointer;

    &:hover {
        color: $color-accent;
    }
}

.loading,
.error {
    text-align: center;
    padding: $spacing-2xl;
    font-size: $font-size-lg;
}

.error {
    color: $color-danger;
}

.form {
    background-color: $color-white;
    border-radius: $radius-lg;
    padding: $spacing-xl;
    box-shadow: $shadow-sm;
    display: flex;
    flex-direction: column;
    gap: $spacing-md;
    margin-bottom: $spacing-xl;
}

.alert {
    padding: $spacing-sm $spacing-md;
    border-radius: $radius-md;
    font-size: $font-size-sm;

    &-error {
        background-color: lighten($color-danger, 40%);
        color: $color-danger;
        border: 1px solid lighten($color-danger, 30%);
    }
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: $spacing-xs;
    flex: 1;

    label {
        font-size: $font-size-sm;
        font-weight: $font-weight-medium;
        color: $color-text;
    }

    input[type="text"],
    select {
        padding: $spacing-sm $spacing-md;
        border: 1px solid $color-border;
        border-radius: $radius-md;
        font-family: $font-family-base;
        font-size: $font-size-base;
        color: $color-text;
        outline: none;

        &:focus {
            border-color: $color-secondary;
            box-shadow: 0 0 0 3px rgba($color-secondary, 0.1);
        }

        &.input-error {
            border-color: $color-danger;
        }
    }
}

.form-select {
    background-color: $color-white;
    cursor: pointer;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: $spacing-md;
}

.field-error {
    font-size: 0.8rem;
    color: $color-danger;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: $spacing-sm;
    margin-top: $spacing-md;
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

.btn-submit {
    background-color: $color-secondary;
    color: $color-white;
    border: none;
    padding: $spacing-sm $spacing-lg;
    border-radius: $radius-md;
    font-size: $font-size-sm;
    font-weight: $font-weight-semi-bold;
    cursor: pointer;

    &:hover:not(:disabled) {
        background-color: $color-secondary-dark;
    }

    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
}

// =============================================
// SECTION PHOTOS
// =============================================
.photos-section {
    background-color: $color-white;
    border-radius: $radius-lg;
    padding: $spacing-xl;
    box-shadow: $shadow-sm;

    h2 {
        font-size: $font-size-lg;
        font-weight: $font-weight-semi-bold;
        color: $color-text;
        margin-bottom: $spacing-md;
    }
}

.photos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: $spacing-md;
    margin-bottom: $spacing-md;
}

.photo-card {
    position: relative;
    border-radius: $radius-md;
    overflow: hidden;
    aspect-ratio: 1;
    background-color: $color-bg;

    img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    &--new {
        border: 2px dashed $color-secondary;
    }
}

.btn-delete-photo {
    position: absolute;
    top: 4px;
    right: 4px;
    background-color: rgba($color-danger, 0.8);
    color: $color-white;
    border: none;
    width: 24px;
    height: 24px;
    border-radius: $radius-full;
    font-size: 0.8rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;

    &:hover {
        background-color: $color-danger;
    }
}

.photo-legende {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.6);
    color: $color-white;
    padding: 2px $spacing-sm;
    font-size: 0.75rem;
}

.empty-photos {
    text-align: center;
    color: $color-text-light;
    padding: $spacing-lg;
    font-size: $font-size-sm;
}

.upload-zone {
    display: flex;
    align-items: center;
    gap: $spacing-md;
    padding: $spacing-md;
    border: 2px dashed $color-border;
    border-radius: $radius-md;
    background-color: $color-bg;
}

.btn-upload {
    background-color: $color-secondary;
    color: $color-white;
    padding: $spacing-sm $spacing-lg;
    border-radius: $radius-md;
    font-size: $font-size-sm;
    font-weight: $font-weight-semi-bold;
    cursor: pointer;
    white-space: nowrap;

    &:hover {
        background-color: $color-secondary-dark;
    }
}

.input-hidden {
    display: none;
}

.upload-info {
    font-size: $font-size-sm;
    color: $color-text-light;
}

@media (max-width: $breakpoint-mobile) {
    .page-top {
        flex-direction: column;
        align-items: stretch;
    }

    .form {
        padding: $spacing-md;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn-cancel,
    .btn-submit {
        width: 100%;
        text-align: center;
    }

    .photos-grid {
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    }

    .upload-zone {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
    }
}
</style>