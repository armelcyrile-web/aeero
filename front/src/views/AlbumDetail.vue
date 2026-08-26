<template>
  <div class="album-detail">
    <div v-if="loading" class="loading">Chargement de l’album...</div>

    <div v-else-if="!album" class="error">
      <p>Album introuvable.</p>
      <router-link to="/galerie" class="btn-back">
        <ArrowLeft class="icon" /> Retour à la galerie
      </router-link>
    </div>

    <template v-else>
      <router-link to="/galerie" class="btn-back">
        <ArrowLeft class="icon" /> Retour à la galerie
      </router-link>

      <h1>{{ album.titre }}</h1>
      <p v-if="album.description" class="description">{{ album.description }}</p>
      <p class="date">Date : {{ album.date }}</p>

      <div v-if="album.photos && album.photos.length > 0" class="photos-grid">
        <div
          v-for="(photo, index) in album.photos"
          :key="photo.id"
          class="photo-item"
          @click="openLightbox(index)"
        >
          <img :src="getStorageUrl(photo.chemin_image)" :alt="album.titre" />
        </div>
      </div>
      <p v-else class="empty">Aucune photo dans cet album.</p>
    </template>

    <Teleport to="body">
      <div v-if="lightboxOpen" class="lightbox-overlay" @click.self="closeLightbox">
        <button class="lightbox-close" @click="closeLightbox" aria-label="Fermer">
          <X />
        </button>
        <button class="lightbox-nav prev" @click.stop="prevPhoto" aria-label="Photo précédente">
          <ChevronLeft />
        </button>
        <img
          class="lightbox-img"
          :src="getStorageUrl(currentPhoto?.chemin_image)"
          alt="Photo agrandie"
          @click.stop
        />
        <button class="lightbox-nav next" @click.stop="nextPhoto" aria-label="Photo suivante">
          <ChevronRight />
        </button>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from '@/utils/axios'
import { ArrowLeft, X, ChevronLeft, ChevronRight } from 'lucide-vue-next'

const route = useRoute()
const album = ref(null)
const loading = ref(true)
const lightboxOpen = ref(false)
const currentIndex = ref(0)

const currentPhoto = computed(() => {
  return album.value?.photos?.[currentIndex.value] || null
})

const getStorageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  const base = import.meta.env.VITE_STORAGE_URL || 'http://localhost:8000/storage'
  return `${base}/${path}`
}

const openLightbox = (index) => {
  currentIndex.value = index
  lightboxOpen.value = true
}

const closeLightbox = () => {
  lightboxOpen.value = false
}

const nextPhoto = () => {
  if (!album.value?.photos?.length) return
  currentIndex.value = (currentIndex.value + 1) % album.value.photos.length
}

const prevPhoto = () => {
  if (!album.value?.photos?.length) return
  currentIndex.value = (currentIndex.value - 1 + album.value.photos.length) % album.value.photos.length
}

onMounted(async () => {
  try {
    const response = await axios.get(`/albums/${route.params.id}`)
    album.value = response.data
  } catch (error) {
    console.error('Erreur lors du chargement de l’album', error)
    album.value = null
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.album-detail {
  max-width: 1000px;
  margin: 0 auto;
  padding: 3rem 1.5rem;
}

.btn-back {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  color: var(--primary);
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
}

.btn-back:hover {
  color: var(--accent);
}

.icon {
  width: 20px;
  height: 20px;
}

h1 {
  font-size: 2.5rem;
  margin-bottom: 1rem;
  color: var(--text);
}

.description {
  font-size: 1.1rem;
  line-height: 1.8;
  margin-bottom: 0.5rem;
  color: var(--text);
}

.date {
  font-size: 1rem;
  margin-bottom: 2rem;
  color: var(--text);
  opacity: 0.7;
}

.photos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1rem;
}

.photo-item {
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  height: 200px;
  transition: opacity 0.3s;
}

.photo-item:hover {
  opacity: 0.85;
}

.photo-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.empty,
.loading,
.error {
  text-align: center;
  padding: 3rem;
  color: var(--text);
  font-style: italic;
}

.lightbox-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.85);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.lightbox-img {
  max-width: 90%;
  max-height: 90vh;
  object-fit: contain;
}

.lightbox-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: rgba(255, 255, 255, 0.1);
  border: none;
  color: #fff;
  padding: 0.75rem;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.3s;
}

.lightbox-close:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.lightbox-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(255, 255, 255, 0.1);
  border: none;
  color: #fff;
  padding: 0.75rem;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.3s;
}

.lightbox-nav:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.lightbox-nav.prev {
  left: 1rem;
}

.lightbox-nav.next {
  right: 1rem;
}
</style>