<template>
  <div class="actualites">
    <section class="section-header">
      <h1>Actualités &amp; Événements</h1>
    </section>

    <!-- Filtres -->
    <div class="filters">
      <button
        v-for="filter in filters"
        :key="filter.value"
        class="filter-btn"
        :class="{ active: activeFilter === filter.value }"
        @click="activeFilter = filter.value"
      >
        {{ filter.label }}
      </button>
    </div>

    <!-- État de chargement -->
    <div v-if="loading" class="loading">Chargement des publications...</div>

    <!-- État vide (aucune publication) -->
    <div v-else-if="publications.length === 0" class="empty">
      Aucune publication disponible.
    </div>

    <!-- Liste des publications filtrées -->
    <div v-else>
      <div v-if="filteredPublications.length === 0" class="empty">
        Aucune publication pour ce filtre.
      </div>
      <div v-else class="publications-grid">
        <article
          v-for="pub in filteredPublications"
          :key="pub.id"
          class="publication-card"
          @click="openDetail(pub)"
        >
          <div class="card-image">
            <img v-if="pub.image" :src="getStorageUrl(pub.image)" :alt="pub.titre" />
            <div v-else class="no-image">Pas d'image</div>
            <span class="badge" :class="'badge-' + pub.type">
              {{ typeLabel(pub.type) }}
            </span>
          </div>
          <div class="card-body">
            <h3>{{ pub.titre }}</h3>
            <p class="date">{{ formatDate(pub.date_publication) }}</p>
            <p class="excerpt">{{ truncate(pub.contenu, 120) }}</p>
          </div>
        </article>
      </div>
    </div>

    <!-- Modal détail -->
    <Teleport to="body">
      <div v-if="selectedPublication" class="modal-overlay" @click.self="selectedPublication = null">
        <div class="modal">
          <button class="modal-close" @click="selectedPublication = null" aria-label="Fermer">
            <X />
          </button>
          <img
            v-if="selectedPublication.image"
            :src="getStorageUrl(selectedPublication.image)"
            :alt="selectedPublication.titre"
            class="modal-image"
          />
          <div class="modal-content">
            <span class="badge" :class="'badge-' + selectedPublication.type">
              {{ typeLabel(selectedPublication.type) }}
            </span>
            <h2>{{ selectedPublication.titre }}</h2>
            <p class="date">{{ formatDate(selectedPublication.date_publication) }}</p>
            <p class="full-content">{{ selectedPublication.contenu }}</p>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '@/utils/axios'
import { X } from 'lucide-vue-next'

const publications = ref([])
const loading = ref(true)
const activeFilter = ref('tout')
const selectedPublication = ref(null)

const filters = [
  { value: 'tout', label: 'Tout' },
  { value: 'actualite', label: 'Actualités' },
  { value: 'evenement', label: 'Événements' },
  { value: 'annonce', label: 'Annonces' },
]

const filteredPublications = computed(() => {
  if (activeFilter.value === 'tout') {
    return publications.value
  }
  return publications.value.filter((pub) => pub.type === activeFilter.value)
})

function getStorageUrl(path) {
  if (!path) return ''
  if (path.startsWith('http')) return path
  const base = import.meta.env.VITE_STORAGE_URL || 'http://localhost:8000/storage'
  return `${base}/${path}`
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}

function truncate(text, maxLength) {
  if (!text) return ''
  return text.length > maxLength ? text.substring(0, maxLength) + '…' : text
}

function typeLabel(type) {
  const labels = {
    actualite: 'Actualité',
    evenement: 'Événement',
    annonce: 'Annonce',
  }
  return labels[type] || type
}

function openDetail(pub) {
  selectedPublication.value = pub
}

onMounted(async () => {
  try {
    const response = await axios.get('/publications')
    publications.value = response.data.sort((a, b) =>
      new Date(b.date_publication) - new Date(a.date_publication)
    )
  } catch (error) {
    console.error('Erreur lors du chargement des publications', error)
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.actualites {
  max-width: 1200px;
  margin: 0 auto;
  padding: 3rem 1.5rem;
}

.section-header h1 {
  font-size: 2.5rem;
  margin-bottom: 2rem;
  color: var(--text);
}

.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.filter-btn {
  padding: 0.6rem 1.2rem;
  border: 2px solid var(--primary);
  background: transparent;
  color: var(--text);
  border-radius: 50px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s;
}

.filter-btn:hover {
  background: var(--primary);
  color: #fff;
}

.filter-btn.active {
  background: var(--primary);
  color: #fff;
}

.publications-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.publication-card {
  background: var(--surface);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s, box-shadow 0.3s;
  cursor: pointer;
  display: flex;
  flex-direction: column;
}

.publication-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.card-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.no-image {
  width: 100%;
  height: 100%;
  background: var(--bg);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text);
}

.badge {
  position: absolute;
  top: 1rem;
  left: 1rem;
  padding: 0.3rem 0.8rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  color: #fff;
}

.badge-actualite {
  background: #3498db;
}

.badge-evenement {
  background: #2ecc71;
}

.badge-annonce {
  background: #e67e22;
}

.card-body {
  padding: 1.2rem;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.card-body h3 {
  font-size: 1.3rem;
  margin-bottom: 0.5rem;
  color: var(--text);
}

.date {
  font-size: 0.9rem;
  color: var(--text);
  opacity: 0.7;
  margin-bottom: 0.5rem;
}

.excerpt {
  color: var(--text);
  line-height: 1.6;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  padding: 1rem;
}

.modal {
  background: var(--surface);
  border-radius: 16px;
  max-width: 700px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
}

.modal-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: rgba(0, 0, 0, 0.5);
  border: none;
  color: #fff;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 10;
}

.modal-close:hover {
  background: rgba(0, 0, 0, 0.7);
}

.modal-image {
  width: 100%;
  height: 300px;
  object-fit: cover;
  border-top-left-radius: 16px;
  border-top-right-radius: 16px;
}

.modal-content {
  padding: 1.5rem;
}

.modal-content .badge {
  position: static;
  display: inline-block;
  margin-bottom: 1rem;
}

.modal-content h2 {
  font-size: 1.8rem;
  margin-bottom: 0.5rem;
  color: var(--text);
}

.full-content {
  margin-top: 1rem;
  color: var(--text);
  line-height: 1.8;
  white-space: pre-line;
}

.loading,
.empty {
  text-align: center;
  padding: 3rem;
  color: var(--text);
  font-style: italic;
}

@media (max-width: 768px) {
  .publications-grid {
    grid-template-columns: 1fr;
  }
  .filters {
    justify-content: center;
  }
}
</style>