<!-- src/views/public/Galerie.vue -->

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import apiClient from '@/api/axios';

const albums = ref([]);
const isLoading = ref(true);
const error = ref(null);

const selectedAlbum = ref(null);
const currentPhotoIndex = ref(0);

async function fetchAlbums() {
    isLoading.value = true;
    error.value = null;

    try {
       const response = await apiClient.get('/albums');
       albums.value = response.data;
    } catch (err) {
        error.value = 'Impossible de charger les albums. Veuillez réessayer plus tard.';
    } finally {
        isLoading.value = false;
    }
}

function openLightbox(album) {
    selectedAlbum.value = album;
    currentPhotoIndex.value = 0;
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    selectedAlbum.value = null;
    currentPhotoIndex.value = 0;
    document.body.style.overflow = '';
}

function nextPhoto() {
    if (selectedAlbum.value && currentPhotoIndex.value < selectedAlbum.value.photos.length - 1) {
        currentPhotoIndex.value++;
    }
}

function prevPhoto() {
    if (currentPhotoIndex.value > 0) {
        currentPhotoIndex.value--;
    }
}

function handleKeydown(e) {
    if (!selectedAlbum.value) return;

    if (e.key === 'Escape') {
        closeLightbox();
    } else if (e.key === 'ArrowRight') {
        nextPhoto();
    } else if (e.key === 'ArrowLeft') {
        prevPhoto();
    }
}

function getCoverPhoto(album) {
    return album.photos && album.photos.length > 0 ? album.photos[0] : null;
}

function getAlbumParent(album) {
    if (album.evenement) return album.evenement.titre;
    if (album.programme) return album.programme.titre;
    return null;
}

onMounted(() => {
    fetchAlbums();
    window.addEventListener('keydown', handleKeydown);
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <div class="galerie-page">
        <section class="page-hero">
            <h1>Galerie</h1>
            <p>Retour en images sur nos événements et activités</p>
        </section>

        <section class="page-content">
            <div v-if="isLoading" class="loading">Chargement de la galerie...</div>

            <div v-else-if="error" class="error">{{ error }}</div>

            <div v-else-if="albums.length === 0" class="empty">
                Aucun album photo pour le moment.
            </div>

            <div v-else class="albums-grid">
                <div
                    v-for="album in albums"
                    :key="album.id"
                    class="album-card"
                    @click="openLightbox(album)"
                >
                    <div class="album-cover">
                        <img
                            v-if="getCoverPhoto(album)"
                            :src="`http://127.0.0.1:8000/storage/${getCoverPhoto(album).chemin}`"
                            :alt="album.titre"
                        />
                        <div v-else class="album-cover-placeholder">
                            🖼️
                        </div>
                        <div class="album-count">
                            {{ album.photos?.length || 0 }} photo{{ album.photos?.length !== 1 ? 's' : '' }}
                        </div>
                    </div>
                    <div class="album-body">
                        <h3 class="album-title">{{ album.titre }}</h3>
                        <p v-if="getAlbumParent(album)" class="album-parent">
                            {{ getAlbumParent(album) }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Lightbox -->
        <Teleport to="body">
            <div v-if="selectedAlbum" class="lightbox" @click.self="closeLightbox">
                <button class="lightbox-close" @click="closeLightbox" aria-label="Fermer">
                    ✕
                </button>

                <button
                    v-if="currentPhotoIndex > 0"
                    class="lightbox-nav lightbox-nav--prev"
                    @click.stop="prevPhoto"
                    aria-label="Photo précédente"
                >
                    ‹
                </button>

                <div class="lightbox-content">
                    <img
                        :src="`http://127.0.0.1:8000/storage/${selectedAlbum.photos[currentPhotoIndex].chemin}`"
                        :alt="selectedAlbum.photos[currentPhotoIndex].legende || selectedAlbum.titre"
                    />
                    <p v-if="selectedAlbum.photos[currentPhotoIndex].legende" class="lightbox-caption">
                        {{ selectedAlbum.photos[currentPhotoIndex].legende }}
                    </p>
                </div>

                <button
                    v-if="currentPhotoIndex < selectedAlbum.photos.length - 1"
                    class="lightbox-nav lightbox-nav--next"
                    @click.stop="nextPhoto"
                    aria-label="Photo suivante"
                >
                    ›
                </button>

                <div class="lightbox-counter">
                    {{ currentPhotoIndex + 1 }} / {{ selectedAlbum.photos.length }}
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style lang="scss" scoped>
@use '@/assets/styles/variables' as *;

.galerie-page {
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
    max-width: 1200px;
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

.albums-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: $spacing-lg;
}

.album-card {
    background-color: $color-white;
    border-radius: $radius-lg;
    overflow: hidden;
    box-shadow: $shadow-sm;
    cursor: pointer;
    transition: transform $transition-base, box-shadow $transition-base;

    &:hover {
        transform: translateY(-4px);
        box-shadow: $shadow-lg;
    }
}

.album-cover {
    width: 100%;
    height: 200px;
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

.album-count {
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
}

// =============================================
// LIGHTBOX
// =============================================
.lightbox {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.9);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.lightbox-close {
    position: absolute;
    top: $spacing-md;
    right: $spacing-md;
    background: none;
    border: none;
    color: $color-white;
    font-size: $font-size-2xl;
    cursor: pointer;
    z-index: 10;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color $transition-base;

    &:hover {
        color: $color-danger;
    }
}

.lightbox-content {
    max-width: 90vw;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    align-items: center;

    img {
        max-width: 100%;
        max-height: 75vh;
        object-fit: contain;
        border-radius: $radius-md;
    }
}

.lightbox-caption {
    color: rgba(255, 255, 255, 0.8);
    font-size: $font-size-sm;
    margin-top: $spacing-md;
    text-align: center;
}

.lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: $color-white;
    font-size: 3rem;
    cursor: pointer;
    width: 50px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color $transition-base;
    border-radius: $radius-sm;

    &:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    &--prev {
        left: $spacing-md;
    }

    &--next {
        right: $spacing-md;
    }
}

.lightbox-counter {
    position: absolute;
    bottom: $spacing-md;
    left: 50%;
    transform: translateX(-50%);
    color: rgba(255, 255, 255, 0.6);
    font-size: $font-size-sm;
}

@media (max-width: $breakpoint-mobile) {
    .page-hero {
        padding: $spacing-xl $spacing-md;

        h1 {
            font-size: $font-size-2xl;
        }
    }

    .albums-grid {
        grid-template-columns: 1fr;
    }

    .lightbox-nav {
        font-size: 2rem;
        width: 40px;
        height: 60px;

        &--prev {
            left: $spacing-sm;
        }

        &--next {
            right: $spacing-sm;
        }
    }
}
</style>