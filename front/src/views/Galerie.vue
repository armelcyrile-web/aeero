<template>
  <div class="galerie">
    <section class="section-header">
      <h1>Galerie</h1>
    </section>

    <div v-if="loading" class="loading">Chargement des albums...</div>
    <div v-else-if="albums.length === 0" class="empty">
      Aucun album disponible pour le moment.
    </div>
    <div v-else class="albums-grid">
      <router-link
        v-for="album in albums"
        :key="album.id"
        :to="`/galerie/${album.id}`"
        class="album-card"
      >
        <div class="album-image-wrapper">
          <img :src="getStorageUrl(album.cover_image)" :alt="album.titre" />
          <span class="album-count">{{ album.photos_count }} photo(s)</span>
        </div>
        <h3>{{ album.titre }}</h3>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '@/utils/axios'

const albums = ref([])
const loading = ref(true)

const getStorageUrl = (path) => {
  if (!path) return 'https://placehold.co/300x200/cccccc/333333?text=Image'
  if (path.startsWith('http')) return path
  const base = import.meta.env.VITE_STORAGE_URL || 'http://localhost:8000/storage'
  return `${base}/${path}`
}

onMounted(async () => {
  try {
    const response = await axios.get('/albums')
    albums.value = response.data
  } catch (error) {
    console.error('Erreur lors du chargement des albums', error)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.galerie {
  max-width: 1200px;
  margin: 0 auto;
  padding: 3rem 1.5rem;
}

.section-header h1 {
  font-size: 2.5rem;
  margin-bottom: 2rem;
  color: var(--text);
}

.albums-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
}

.album-card {
  display: flex;
  flex-direction: column;
  text-decoration: none;
  color: var(--text);
  background-color: var(--surface);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s, box-shadow 0.3s;
}

.album-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.album-image-wrapper {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.album-image-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.album-count {
  position: absolute;
  bottom: 0.5rem;
  left: 0.5rem;
  background-color: rgba(0, 0, 0, 0.7);
  color: #fff;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
}

.album-card h3 {
  padding: 1rem;
  font-size: 1.1rem;
}

.loading,
.empty {
  text-align: center;
  padding: 3rem;
  color: var(--text);
  font-style: italic;
}
</style>