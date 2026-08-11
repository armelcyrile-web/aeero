<!-- src/views/admin/Login.vue -->

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { Eye, EyeOff } from 'lucide-vue-next';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const isLoading = ref(false);
const error = ref('');
const showPassword = ref(false);
const rememberMe = ref(true);

async function handleLogin() {
    if (!email.value || !password.value) {
        error.value = 'Veuillez remplir tous les champs.';
        return;
    }

    isLoading.value = true;
    error.value = '';

    try {
        await authStore.login(email.value, password.value, rememberMe.value);
        router.push({ name: 'AdminDashboard' });
    } catch (err) {
        if (err.response && err.response.status === 422) {
            error.value = err.response.data.message || 'Identifiants incorrects.';
        } else {
            error.value = 'Une erreur est survenue. Veuillez réessayer.';
        }
    } finally {
        isLoading.value = false;
    }
}

function togglePasswordVisibility() {
    showPassword.value = !showPassword.value;
}
</script>

<template>
    <div class="login-page">
        <div class="login-card">
            <div class="login-header">
                <h1>AEERO</h1>
                <p>Espace d'administration</p>
            </div>

            <form class="login-form" @submit.prevent="handleLogin">
                <div v-if="error" class="alert alert-error">
                    {{ error }}
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        id="email"
                        v-model="email"
                        type="email"
                        placeholder="votre@email.com"
                        autocomplete="email"
                        required
                    />
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="password-wrapper">
                        <input
                            id="password"
                            v-model="password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Votre mot de passe"
                            autocomplete="current-password"
                            required
                        />
                        <button
                            type="button"
                            class="password-toggle"
                            @click="togglePasswordVisibility"
                            :aria-label="showPassword ? 'Cacher le mot de passe' : 'Afficher le mot de passe'"
                        >
                            <EyeOff v-if="showPassword" :size="18" />
                            <Eye v-else :size="18" />
                        </button>
                    </div>
                </div>

                <div class="form-group form-group--checkbox">
                    <label class="checkbox-label">
                        <input
                            type="checkbox"
                            v-model="rememberMe"
                            class="checkbox-input"
                        />
                        <span class="checkbox-custom"></span>
                        <span>Se souvenir de moi</span>
                    </label>
                </div>

                <button type="submit" class="btn-login" :disabled="isLoading">
                    <span v-if="isLoading" class="spinner"></span>
                    {{ isLoading ? 'Connexion...' : 'Se connecter' }}
                </button>
            </form>
        </div>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: $color-bg;
    padding: $spacing-md;
}

.login-card {
    background-color: $color-white;
    border-radius: $radius-lg;
    box-shadow: $shadow-lg;
    padding: $spacing-2xl;
    width: 100%;
    max-width: 420px;
}

.login-header {
    text-align: center;
    margin-bottom: $spacing-xl;

    h1 {
        font-family: $font-family-base;
        font-size: $font-size-3xl;
        font-weight: $font-weight-bold;
        color: $color-primary;
        letter-spacing: 3px;
        margin-bottom: $spacing-xs;
    }

    p {
        font-size: $font-size-sm;
        color: $color-text-light;
    }
}

.login-form {
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

    input[type="email"],
    input[type="password"],
    input[type="text"] {
        padding: $spacing-sm $spacing-md;
        border: 1px solid $color-border;
        border-radius: $radius-md;
        font-family: $font-family-base;
        font-size: $font-size-base;
        color: $color-text;
        outline: none;
        width: 100%;
        transition: border-color $transition-base;

        &:focus {
            border-color: $color-secondary;
            box-shadow: 0 0 0 3px rgba($color-secondary, 0.1);
        }
    }

    &--checkbox {
        flex-direction: row;
        align-items: center;
    }
}

.password-wrapper {
    position: relative;
    display: flex;
    align-items: center;

    input {
        padding-right: 44px;
    }
}

.password-toggle {
    position: absolute;
    right: 8px;
    background: none;
    border: none;
    color: $color-text-light;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color $transition-base;

    &:hover {
        color: $color-text;
    }
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: $spacing-sm;
    cursor: pointer;
    font-size: $font-size-sm;
    color: $color-text;
    user-select: none;
}

.checkbox-input {
    display: none;
}

.checkbox-custom {
    width: 18px;
    height: 18px;
    border: 2px solid $color-border;
    border-radius: $radius-sm;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: border-color $transition-base, background-color $transition-base;
    flex-shrink: 0;

    .checkbox-input:checked + & {
        background-color: $color-secondary;
        border-color: $color-secondary;
    }

    .checkbox-input:checked + &::after {
        content: '';
        width: 5px;
        height: 10px;
        border: solid $color-white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
        margin-top: -2px;
    }
}

.btn-login {
    background-color: $color-secondary;
    color: $color-white;
    border: none;
    padding: $spacing-sm;
    border-radius: $radius-md;
    font-size: $font-size-base;
    font-weight: $font-weight-semi-bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: $spacing-sm;
    transition: background-color $transition-base;
    margin-top: $spacing-sm;

    &:hover:not(:disabled) {
        background-color: $color-secondary-dark;
    }

    &:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
}

.spinner {
    display: inline-block;
    width: 18px;
    height: 18px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: $color-white;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>