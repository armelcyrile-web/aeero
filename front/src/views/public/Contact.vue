<!-- src/views/public/Contact.vue -->

<script setup>
import { ref, reactive } from 'vue';
import apiClient from '@/api/axios';

const form = reactive({
    nom: '',
    email: '',
    sujet: '',
    message: '',
});

const errors = reactive({
    nom: '',
    email: '',
    message: '',
});

const isSubmitting = ref(false);
const submitSuccess = ref(false);
const submitError = ref('');

function validateForm() {
    let isValid = true;

    errors.nom = '';
    errors.email = '';
    errors.message = '';

    if (!form.nom.trim()) {
        errors.nom = 'Le nom est requis.';
        isValid = false;
    }

    if (!form.email.trim()) {
        errors.email = "L'email est requis.";
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = 'Format email invalide.';
        isValid = false;
    }

    if (!form.message.trim()) {
        errors.message = 'Le message est requis.';
        isValid = false;
    }

    return isValid;
}

async function handleSubmit() {
    if (!validateForm()) return;

    isSubmitting.value = true;
    submitError.value = '';
    submitSuccess.value = false;

    try {
        await apiClient.post('/messages-contact', {
            nom: form.nom,
            email: form.email,
            sujet: form.sujet || null,
            message: form.message,
        });

        submitSuccess.value = true;
        form.nom = '';
        form.email = '';
        form.sujet = '';
        form.message = '';
    } catch (err) {
        submitError.value = "Une erreur est survenue lors de l'envoi. Veuillez réessayer.";
    } finally {
        isSubmitting.value = false;
    }
}
</script>

<template>
    <div class="contact-page">
        <section class="page-hero">
            <h1>Contactez-nous</h1>
            <p>Une question, une suggestion ? Nous sommes à votre écoute.</p>
        </section>

        <section class="page-content">
            <div class="contact-grid">
                <div class="contact-form-wrapper">
                    <div v-if="submitSuccess" class="success-message">
                        ✅ Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.
                    </div>

                    <div v-if="submitError" class="error-message">
                        {{ submitError }}
                    </div>

                    <form v-if="!submitSuccess" class="contact-form" @submit.prevent="handleSubmit">
                        <div class="form-group">
                            <label for="nom">Nom *</label>
                            <input
                                id="nom"
                                v-model="form.nom"
                                type="text"
                                placeholder="Votre nom"
                                :class="{ 'input-error': errors.nom }"
                            />
                            <span v-if="errors.nom" class="field-error">{{ errors.nom }}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                placeholder="votre@email.com"
                                :class="{ 'input-error': errors.email }"
                            />
                            <span v-if="errors.email" class="field-error">{{ errors.email }}</span>
                        </div>

                        <div class="form-group">
                            <label for="sujet">Sujet</label>
                            <input
                                id="sujet"
                                v-model="form.sujet"
                                type="text"
                                placeholder="Sujet de votre message"
                            />
                        </div>

                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea
                                id="message"
                                v-model="form.message"
                                rows="6"
                                placeholder="Votre message..."
                                :class="{ 'input-error': errors.message }"
                            ></textarea>
                            <span v-if="errors.message" class="field-error">{{ errors.message }}</span>
                        </div>

                        <button type="submit" class="btn-submit" :disabled="isSubmitting">
                            {{ isSubmitting ? 'Envoi en cours...' : 'Envoyer le message' }}
                        </button>
                    </form>
                </div>

                <div class="contact-info">
                    <h3>Nos coordonnées</h3>
                    <div class="info-item">
                        <span class="info-icon">📧</span>
                        <div>
                            <strong>Email</strong>
                            <p>contact@aeero.org</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="info-icon">📞</span>
                        <div>
                            <strong>Téléphone</strong>
                            <p>+226 XX XX XX XX</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="info-icon">📍</span>
                        <div>
                            <strong>Adresse</strong>
                            <p>Ouoghi, Burkina Faso</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.contact-page {
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
    max-width: 1100px;
    margin: 0 auto;
    padding: $spacing-xl $spacing-lg;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: $spacing-xl;
}

.contact-form-wrapper {
    background-color: $color-white;
    border-radius: $radius-lg;
    padding: $spacing-xl;
    box-shadow: $shadow-sm;
}

.success-message {
    background-color: lighten($color-success, 50%);
    color: darken($color-success, 10%);
    padding: $spacing-md;
    border-radius: $radius-md;
    font-size: $font-size-base;
    margin-bottom: $spacing-md;
    border: 1px solid lighten($color-success, 40%);
}

.error-message {
    background-color: lighten($color-danger, 40%);
    color: $color-danger;
    padding: $spacing-md;
    border-radius: $radius-md;
    font-size: $font-size-base;
    margin-bottom: $spacing-md;
    border: 1px solid lighten($color-danger, 30%);
}

.contact-form {
    display: flex;
    flex-direction: column;
    gap: $spacing-md;
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

    input,
    textarea {
        padding: $spacing-sm $spacing-md;
        border: 1px solid $color-border;
        border-radius: $radius-md;
        font-family: $font-family-base;
        font-size: $font-size-base;
        color: $color-text;
        transition: border-color $transition-base;
        outline: none;

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
        min-height: 120px;
    }
}

.field-error {
    font-size: 0.8rem;
    color: $color-danger;
}

.btn-submit {
    background-color: $color-secondary;
    color: $color-white;
    border: none;
    padding: $spacing-sm $spacing-xl;
    border-radius: $radius-md;
    font-size: $font-size-base;
    font-weight: $font-weight-semi-bold;
    cursor: pointer;
    transition: background-color $transition-base;
    align-self: flex-start;

    &:hover:not(:disabled) {
        background-color: $color-secondary-dark;
    }

    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
}

.contact-info {
    background-color: $color-primary;
    color: $color-white;
    border-radius: $radius-lg;
    padding: $spacing-xl;
    display: flex;
    flex-direction: column;
    gap: $spacing-lg;

    h3 {
        font-size: $font-size-xl;
        font-weight: $font-weight-semi-bold;
        margin-bottom: $spacing-sm;
    }
}

.info-item {
    display: flex;
    gap: $spacing-md;
    align-items: flex-start;

    .info-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    strong {
        display: block;
        font-size: $font-size-sm;
        margin-bottom: 2px;
    }

    p {
        font-size: $font-size-sm;
        opacity: 0.85;
    }
}

@media (max-width: $breakpoint-mobile) {
    .page-hero {
        padding: $spacing-xl $spacing-md;

        h1 {
            font-size: $font-size-2xl;
        }
    }

    .contact-grid {
        grid-template-columns: 1fr;
    }

    .contact-form-wrapper {
        padding: $spacing-md;
    }

    .btn-submit {
        align-self: stretch;
    }
}
</style>