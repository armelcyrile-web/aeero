<template>
  <div class="bureau">
    <section class="section">
      <h1>Le Bureau Exécutif</h1>

      <div v-if="loadingMembres" class="loading">Chargement des membres...</div>
      <div v-else-if="membres.length === 0" class="empty">Aucun membre du bureau à afficher.</div>
      <div v-else class="membres-grid">
        <div v-for="membre in membres" :key="membre.id" class="membre-card">
          <img
            :src="membre.photo ? getPhotoUrl(membre.photo) : `https://ui-avatars.com/api/?name=${encodeURIComponent(membre.prenom + ' ' + membre.nom)}&background=8B6F7A&color=fff`"
            :alt="membre.prenom + ' ' + membre.nom"
            class="membre-photo"
          />
          <h3>{{ membre.prenom }} {{ membre.nom }}</h3>
          <p class="poste">{{ membre.poste }}</p>
        </div>
      </div>
    </section>

    <section class="section">
      <h2>Anciens Présidents</h2>

      <div v-if="loadingAnciens" class="loading">Chargement des anciens présidents...</div>
      <div v-else-if="anciens.length === 0" class="empty">Aucun ancien président enregistré.</div>
      <ul v-else class="anciens-list">
        <li v-for="president in anciens" :key="president.id" class="ancien-item">
          <span class="nom">{{ president.prenom }} {{ president.nom }}</span>
          <span class="periode">{{ president.periode_debut }} – {{ president.periode_fin || 'Mandat en cours' }}</span>
        </li>
      </ul>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '@/utils/axios'

const membres = ref([])
const anciens = ref([])
const loadingMembres = ref(true)
const loadingAnciens = ref(true)

const getPhotoUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  const base = import.meta.env.VITE_STORAGE_URL || 'http://localhost:8000/storage'
  return `${base}/${path}`
}

onMounted(async () => {
  try {
    const response = await axios.get('/membres-bureau')
    membres.value = response.data
  } catch (error) {
    console.error('Erreur lors du chargement des membres', error)
  } finally {
    loadingMembres.value = false
  }

  try {
    const response = await axios.get('/anciens-presidents')
    anciens.value = response.data
  } catch (error) {
    console.error('Erreur lors du chargement des anciens présidents', error)
  } finally {
    loadingAnciens.value = false
  }
})
</script>

<style scoped>
.bureau {
  max-width: 1000px;
  margin: 0 auto;
  padding: 3rem 1.5rem;
}

.section {
  margin-bottom: 3rem;
}

h1 {
  font-size: 2.5rem;
  margin-bottom: 2rem;
  color: var(--text);
}

h2 {
  font-size: 2rem;
  margin-bottom: 1.5rem;
  color: var(--text);
}

.membres-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 2rem;
}

.membre-card {
  background: var(--surface);
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  text-align: center;
  transition: transform 0.3s, box-shadow 0.3s;
}

.membre-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.membre-photo {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 1rem;
}

.membre-card h3 {
  font-size: 1.2rem;
  margin-bottom: 0.25rem;
  color: var(--text);
}

.poste {
  color: var(--text);
  font-size: 0.95rem;
  opacity: 0.8;
}

.anciens-list {
  list-style: none;
  padding: 0;
}

.ancien-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  color: var(--text);
}

.ancien-item:last-child {
  border-bottom: none;
}

.nom {
  font-weight: 600;
}

.periode {
  font-size: 0.9rem;
  opacity: 0.7;
}

.loading,
.empty {
  text-align: center;
  padding: 2rem;
  color: var(--text);
  font-style: italic;
}

@media (max-width: 768px) {
  .membres-grid {
    grid-template-columns: 1fr;
  }
}
</style>