<!-- src/views/admin/evenements/Formulaire.vue -->

<script setup>
import { ref, onMounted } from 'vue';
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
    description: '',
    date_debut: '',
    date_fin: '',
    lieu: '',
    image: null,
});

const imagePreview = ref(null);

async function loadEvenement() {
    if (!route.params.id) return;
    isEdit.value = true;
    isLoading.value = true;
    error.value = '';
    try {
        const response = await apiClient.get(`/admin/evenements/${route.params.id}`);
        form.value.titre = response.data.titre;
        form.value.description = response.data.description;
        form.value.date_debut = response.data.date_debut?.slice(0, 16) || '';
        form.value.date_fin = response.data.date_fin?.slice(0, 16) || '';
        form.value.lieu = response.data.lieu;
    } catch (err) {
        error.value = "Impossible de charger l'événement.";
    } finally {
        isLoading.value = false;
    }
}

function handleImageChange(event) {
    const file = event.target.files[0];
    if (file) {
        form.value.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
}

function removeImage() {
    form.value.image = null;
    if (imagePreview.value) {
        URL.revokeObjectURL(imagePreview.value);
        imagePreview.value = null;
    }
}

async function handleSubmit() {
    isSubmitting.value = true;
    error.value = '';
    formErrors.value = {};

    const formData = new FormData();
    formData.append('titre', form.value.titre);
    formData.append('description', form.value.description);
    formData.append('date_debut', form.value.date_debut);
    if (form.value.date_fin) formData.append('date_fin', form.value.date_fin);
    formData.append('lieu', form.value.lieu);
    if (form.value.image) formData.append('image', form.value.image);

    try {
        if (isEdit.value) {
            formData.append('_method', 'PUT');
            await apiClient.post(`/admin/evenements/${route.params.id}`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
        } else {
            await apiClient.post('/admin/evenements', formData, { headers: { 'Content-Type': 'multipart/form-data' } });
        }
        router.push('/admin/evenements');
    } catch (err) {
        if (err.response?.status === 422) formErrors.value = err.response.data.errors || {};
        else error.value = "Une erreur est survenue lors de l'enregistrement.";
    } finally {
        isSubmitting.value = false;
    }
}

function getFieldError(field) {
    return formErrors.value[field]?.[0] || '';
}

onMounted(() => loadEvenement());
</script>

<template>
    <div class="formulaire-page">
        <div class="page-top">
            <h1>{{ isEdit ? "Modifier l'événement" : 'Nouvel événement' }}</h1>
            <button class="btn-back" @click="router.push('/admin/evenements')">← Retour à la liste</button>
        </div>

        <div v-if="isLoading" class="loading">Chargement...</div>
        <div v-else-if="error" class="error">{{ error }}</div>

        <form v-else class="form" @submit.prevent="handleSubmit">
            <div v-if="error && !isLoading" class="alert alert-error">{{ error }}</div>

            <div class="form-group">
                <label for="titre">Titre *</label>
                <input id="titre" v-model="form.titre" type="text" placeholder="Titre de l'événement" :class="{ 'input-error': getFieldError('titre') }" />
                <span v-if="getFieldError('titre')" class="field-error">{{ getFieldError('titre') }}</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_debut">Date de début *</label>
                    <input id="date_debut" v-model="form.date_debut" type="datetime-local" :class="{ 'input-error': getFieldError('date_debut') }" />
                    <span v-if="getFieldError('date_debut')" class="field-error">{{ getFieldError('date_debut') }}</span>
                </div>
                <div class="form-group">
                    <label for="date_fin">Date de fin</label>
                    <input id="date_fin" v-model="form.date_fin" type="datetime-local" />
                    <span v-if="getFieldError('date_fin')" class="field-error">{{ getFieldError('date_fin') }}</span>
                </div>
            </div>

            <div class="form-group">
                <label for="lieu">Lieu *</label>
                <input id="lieu" v-model="form.lieu" type="text" placeholder="Lieu de l'événement" :class="{ 'input-error': getFieldError('lieu') }" />
                <span v-if="getFieldError('lieu')" class="field-error">{{ getFieldError('lieu') }}</span>
            </div>

            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" v-model="form.description" rows="10" placeholder="Description de l'événement..." :class="{ 'input-error': getFieldError('description') }"></textarea>
                <span v-if="getFieldError('description')" class="field-error">{{ getFieldError('description') }}</span>
            </div>

            <div class="form-group">
                <label for="image">Image (optionnelle)</label>
                <input id="image" type="file" accept="image/*" @change="handleImageChange" />
                <span v-if="getFieldError('image')" class="field-error">{{ getFieldError('image') }}</span>
                <div v-if="imagePreview" class="image-preview">
                    <img :src="imagePreview" alt="Aperçu" />
                    <button type="button" class="btn-remove-image" @click="removeImage">✕</button>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" @click="router.push('/admin/evenements')" :disabled="isSubmitting">Annuler</button>
                <button type="submit" class="btn-submit" :disabled="isSubmitting">{{ isSubmitting ? 'Enregistrement...' : (isEdit ? "Mettre à jour" : "Créer l'événement") }}</button>
            </div>
        </form>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.formulaire-page { max-width: 900px; }
.page-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: $spacing-lg; flex-wrap: wrap; gap: $spacing-md;
    h1 { font-size: $font-size-2xl; font-weight: $font-weight-bold; color: $color-text; }
}
.btn-back { background: none; border: none; color: $color-secondary; font-size: $font-size-sm; cursor: pointer;
    &:hover { color: $color-accent; }
}
.loading, .error { text-align: center; padding: $spacing-2xl; font-size: $font-size-lg; }
.error { color: $color-danger; }
.form { background-color: $color-white; border-radius: $radius-lg; padding: $spacing-xl; box-shadow: $shadow-sm; display: flex; flex-direction: column; gap: $spacing-md; }
.alert { padding: $spacing-sm $spacing-md; border-radius: $radius-md; font-size: $font-size-sm;
    &-error { background-color: lighten($color-danger, 40%); color: $color-danger; border: 1px solid lighten($color-danger, 30%); }
}
.form-group { display: flex; flex-direction: column; gap: $spacing-xs;
    label { font-size: $font-size-sm; font-weight: $font-weight-medium; color: $color-text; }
    input[type="text"], input[type="datetime-local"], textarea { padding: $spacing-sm $spacing-md; border: 1px solid $color-border; border-radius: $radius-md; font-family: $font-family-base; font-size: $font-size-base; color: $color-text; outline: none;
        &:focus { border-color: $color-secondary; box-shadow: 0 0 0 3px rgba($color-secondary, 0.1); }
        &.input-error { border-color: $color-danger; }
    }
    textarea { resize: vertical; min-height: 150px; }
}
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: $spacing-md; }
.field-error { font-size: 0.8rem; color: $color-danger; }
.image-preview { position: relative; display: inline-block; margin-top: $spacing-sm;
    img { max-width: 300px; max-height: 200px; border-radius: $radius-md; object-fit: cover; }
}
.btn-remove-image { position: absolute; top: -8px; right: -8px; background-color: $color-danger; color: $color-white; border: none; width: 24px; height: 24px; border-radius: $radius-full; font-size: 0.8rem; cursor: pointer; }
.form-actions { display: flex; justify-content: flex-end; gap: $spacing-sm; margin-top: $spacing-md; }
.btn-cancel { background-color: $color-bg; color: $color-text; border: 1px solid $color-border; padding: $spacing-sm $spacing-lg; border-radius: $radius-md; font-size: $font-size-sm; cursor: pointer;
    &:hover:not(:disabled) { background-color: darken($color-bg, 5%); }
}
.btn-submit { background-color: $color-secondary; color: $color-white; border: none; padding: $spacing-sm $spacing-lg; border-radius: $radius-md; font-size: $font-size-sm; font-weight: $font-weight-semi-bold; cursor: pointer;
    &:hover:not(:disabled) { background-color: $color-secondary-dark; }
    &:disabled { opacity: 0.6; cursor: not-allowed; }
}
@media (max-width: $breakpoint-mobile) {
    .page-top { flex-direction: column; align-items: stretch; }
    .form { padding: $spacing-md; }
    .form-row { grid-template-columns: 1fr; }
    .form-actions { flex-direction: column; }
    .btn-cancel, .btn-submit { width: 100%; text-align: center; }
}
</style>