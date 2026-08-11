<!-- src/views/admin/actualites/Formulaire.vue -->

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
    contenu: '',
    image: null,
});

const imagePreview = ref(null);

async function loadActualite() {
    if (!route.params.id) return;

    isEdit.value = true;
    isLoading.value = true;
    error.value = '';

    try {
        const response = await apiClient.get(`/admin/actualites/${route.params.id}`);
        form.value.titre = response.data.titre;
        form.value.contenu = response.data.contenu;
    } catch (err) {
        error.value = 'Impossible de charger l\'actualité.';
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
    formData.append('contenu', form.value.contenu);

    if (form.value.image) {
        formData.append('image', form.value.image);
    }

    try {
        if (isEdit.value) {
            formData.append('_method', 'PUT');
            await apiClient.post(`/admin/actualites/${route.params.id}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
        } else {
            await apiClient.post('/admin/actualites', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
        }

        router.push('/admin/actualites');
    } catch (err) {
        if (err.response && err.response.status === 422) {
            formErrors.value = err.response.data.errors || {};
        } else {
            error.value = 'Une erreur est survenue lors de l\'enregistrement.';
        }
    } finally {
        isSubmitting.value = false;
    }
}

function getFieldError(field) {
    return formErrors.value[field]?.[0] || '';
}

onMounted(() => {
    loadActualite();
});
</script>

<template>
    <div class="formulaire-page">
        <div class="page-top">
            <h1>{{ isEdit ? 'Modifier l\'actualité' : 'Nouvelle actualité' }}</h1>
            <button class="btn-back" @click="router.push('/admin/actualites')">
                ← Retour à la liste
            </button>
        </div>

        <div v-if="isLoading" class="loading">Chargement...</div>

        <div v-else-if="error" class="error">{{ error }}</div>

        <form v-else class="form" @submit.prevent="handleSubmit">
            <div v-if="error && !isLoading" class="alert alert-error">
                {{ error }}
            </div>

            <div class="form-group">
                <label for="titre">Titre *</label>
                <input
                    id="titre"
                    v-model="form.titre"
                    type="text"
                    placeholder="Titre de l'actualité"
                    :class="{ 'input-error': getFieldError('titre') }"
                />
                <span v-if="getFieldError('titre')" class="field-error">{{ getFieldError('titre') }}</span>
            </div>

            <div class="form-group">
                <label for="contenu">Contenu *</label>
                <textarea
                    id="contenu"
                    v-model="form.contenu"
                    rows="12"
                    placeholder="Contenu de l'actualité..."
                    :class="{ 'input-error': getFieldError('contenu') }"
                ></textarea>
                <span v-if="getFieldError('contenu')" class="field-error">{{ getFieldError('contenu') }}</span>
            </div>

            <div class="form-group">
                <label for="image">Image (optionnelle)</label>
                <input
                    id="image"
                    type="file"
                    accept="image/*"
                    @change="handleImageChange"
                />
                <span v-if="getFieldError('image')" class="field-error">{{ getFieldError('image') }}</span>

                <div v-if="imagePreview" class="image-preview">
                    <img :src="imagePreview" alt="Aperçu" />
                    <button type="button" class="btn-remove-image" @click="removeImage">
                        ✕
                    </button>
                </div>
            </div>

            <div class="form-actions">
                <button
                    type="button"
                    class="btn-cancel"
                    @click="router.push('/admin/actualites')"
                    :disabled="isSubmitting"
                >
                    Annuler
                </button>
                <button type="submit" class="btn-submit" :disabled="isSubmitting">
                    {{ isSubmitting ? 'Enregistrement...' : (isEdit ? 'Mettre à jour' : 'Créer l\'actualité') }}
                </button>
            </div>
        </form>
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
    transition: color $transition-base;

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

    label {
        font-size: $font-size-sm;
        font-weight: $font-weight-medium;
        color: $color-text;
    }

    input[type="text"],
    textarea {
        padding: $spacing-sm $spacing-md;
        border: 1px solid $color-border;
        border-radius: $radius-md;
        font-family: $font-family-base;
        font-size: $font-size-base;
        color: $color-text;
        outline: none;
        transition: border-color $transition-base;

        &:focus {
            border-color: $color-secondary;
            box-shadow: 0 0 0 3px rgba($color-secondary, 0.1);
        }

        &.input-error {
            border-color: $color-danger;
        }
    }

    textarea {
        resize: vertical;
        min-height: 200px;
    }

    input[type="file"] {
        font-size: $font-size-sm;
    }
}

.field-error {
    font-size: 0.8rem;
    color: $color-danger;
}

.image-preview {
    position: relative;
    display: inline-block;
    margin-top: $spacing-sm;

    img {
        max-width: 300px;
        max-height: 200px;
        border-radius: $radius-md;
        object-fit: cover;
    }
}

.btn-remove-image {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: $color-danger;
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
    transition: background-color $transition-base;

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
    transition: background-color $transition-base;

    &:hover:not(:disabled) {
        background-color: $color-secondary-dark;
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

    .form {
        padding: $spacing-md;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn-cancel,
    .btn-submit {
        width: 100%;
        text-align: center;
    }
}
</style>