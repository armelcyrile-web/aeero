<!-- src/views/admin/partenaires/Formulaire.vue -->

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
    nom: '',
    logo: null,
    lien_site: '',
});

const logoPreview = ref(null);

async function loadPartenaire() {
    if (!route.params.id) return;
    isEdit.value = true; isLoading.value = true; error.value = '';
    try {
        const response = await apiClient.get(`/admin/partenaires/${route.params.id}`);
        form.value.nom = response.data.nom;
        form.value.lien_site = response.data.lien_site || '';
    } catch (err) { error.value = 'Impossible de charger le partenaire.'; }
    finally { isLoading.value = false; }
}

function handleLogoChange(event) {
    const file = event.target.files[0];
    if (file) { form.value.logo = file; logoPreview.value = URL.createObjectURL(file); }
}
function removeLogo() {
    form.value.logo = null;
    if (logoPreview.value) { URL.revokeObjectURL(logoPreview.value); logoPreview.value = null; }
}

async function handleSubmit() {
    isSubmitting.value = true; error.value = ''; formErrors.value = {};
    const formData = new FormData();
    formData.append('nom', form.value.nom);
    if (form.value.logo) formData.append('logo', form.value.logo);
    if (form.value.lien_site) formData.append('lien_site', form.value.lien_site);
    try {
        if (isEdit.value) {
            formData.append('_method', 'PUT');
            await apiClient.post(`/admin/partenaires/${route.params.id}`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
        } else {
            await apiClient.post('/admin/partenaires', formData, { headers: { 'Content-Type': 'multipart/form-data' } });
        }
        router.push('/admin/partenaires');
    } catch (err) {
        if (err.response?.status === 422) formErrors.value = err.response.data.errors || {};
        else error.value = "Une erreur est survenue lors de l'enregistrement.";
    } finally { isSubmitting.value = false; }
}
function getFieldError(field) { return formErrors.value[field]?.[0] || ''; }

onMounted(() => loadPartenaire());
</script>

<template>
    <div class="formulaire-page">
        <div class="page-top">
            <h1>{{ isEdit ? 'Modifier le partenaire' : 'Nouveau partenaire' }}</h1>
            <button class="btn-back" @click="router.push('/admin/partenaires')">← Retour à la liste</button>
        </div>
        <div v-if="isLoading" class="loading">Chargement...</div>
        <div v-else-if="error" class="error">{{ error }}</div>
        <form v-else class="form" @submit.prevent="handleSubmit">
            <div v-if="error && !isLoading" class="alert alert-error">{{ error }}</div>
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input id="nom" v-model="form.nom" type="text" placeholder="Nom du partenaire" :class="{ 'input-error': getFieldError('nom') }" />
                <span v-if="getFieldError('nom')" class="field-error">{{ getFieldError('nom') }}</span>
            </div>
            <div class="form-group">
                <label for="logo">Logo {{ isEdit ? '' : '*' }}</label>
                <input id="logo" type="file" accept="image/*" @change="handleLogoChange" />
                <span v-if="getFieldError('logo')" class="field-error">{{ getFieldError('logo') }}</span>
                <div v-if="logoPreview" class="image-preview">
                    <img :src="logoPreview" alt="Aperçu" />
                    <button type="button" class="btn-remove-image" @click="removeLogo">✕</button>
                </div>
            </div>
            <div class="form-group">
                <label for="lien_site">Lien du site</label>
                <input id="lien_site" v-model="form.lien_site" type="url" placeholder="https://..." />
                <span v-if="getFieldError('lien_site')" class="field-error">{{ getFieldError('lien_site') }}</span>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-cancel" @click="router.push('/admin/partenaires')" :disabled="isSubmitting">Annuler</button>
                <button type="submit" class="btn-submit" :disabled="isSubmitting">{{ isSubmitting ? 'Enregistrement...' : (isEdit ? 'Mettre à jour' : 'Créer le partenaire') }}</button>
            </div>
        </form>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;
.formulaire-page { max-width: 700px; }
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
    input[type="text"], input[type="url"] { padding: $spacing-sm $spacing-md; border: 1px solid $color-border; border-radius: $radius-md; font-family: $font-family-base; font-size: $font-size-base; color: $color-text; outline: none;
        &:focus { border-color: $color-secondary; box-shadow: 0 0 0 3px rgba($color-secondary, 0.1); }
        &.input-error { border-color: $color-danger; }
    }
}
.field-error { font-size: 0.8rem; color: $color-danger; }
.image-preview { position: relative; display: inline-block; margin-top: $spacing-sm;
    img { max-width: 200px; max-height: 150px; border-radius: $radius-md; object-fit: contain; background-color: $color-bg; }
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
    .form-actions { flex-direction: column; }
    .btn-cancel, .btn-submit { width: 100%; text-align: center; }
}
</style>