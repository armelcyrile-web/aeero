<template>
  <AdminLayout>
    <div class="gestion-bureau">
      <div class="page-header">
        <h1>Gestion du bureau</h1>
      </div>

      <!-- Section Membres du bureau -->
      <section class="section">
        <div class="section-header">
          <h2>Membres du bureau actuel</h2>
          <button class="btn btn-primary" @click="openCreateMembre">
            <Plus class="btn-icon" /> Ajouter un membre
          </button>
        </div>

        <div v-if="showMembreForm" class="modal-overlay">
          <div class="modal">
            <div class="modal-header">
              <h2>{{ editingMembre ? 'Modifier le membre' : 'Nouveau membre' }}</h2>
              <button class="close-btn" @click="closeMembreForm"><X /></button>
            </div>
            <form @submit.prevent="saveMembre" class="form">
              <div class="form-row">
                <div class="form-group">
                  <label>Nom</label>
                  <input type="text" v-model="membreForm.nom" required />
                </div>
                <div class="form-group">
                  <label>Prénom</label>
                  <input type="text" v-model="membreForm.prenom" required />
                </div>
              </div>
              <div class="form-group">
                <label>Poste</label>
                <input type="text" v-model="membreForm.poste" required />
              </div>
              <div class="form-group">
                <label>Photo</label>
                <input type="file" @change="onMembrePhotoChange" accept="image/*" />
              </div>
              <div class="form-group">
                <label>Ordre d'affichage</label>
                <input type="number" v-model="membreForm.ordre_affichage" min="0" />
              </div>
              <div class="form-actions">
                <button type="button" class="btn btn-secondary" @click="closeMembreForm">Annuler</button>
                <button type="submit" class="btn btn-primary">
                  {{ editingMembre ? 'Enregistrer' : 'Créer' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <div v-if="loadingMembres" class="loading">Chargement...</div>
        <table v-else class="table">
          <thead>
            <tr>
              <th>Photo</th>
              <th>Nom</th>
              <th>Poste</th>
              <th>Ordre</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="membre in membres" :key="membre.id">
              <td>
                <img v-if="membre.photo" :src="getStorageUrl(membre.photo)" class="thumb" />
                <span v-else class="no-image">—</span>
              </td>
              <td>{{ membre.prenom }} {{ membre.nom }}</td>
              <td>{{ membre.poste }}</td>
              <td>{{ membre.ordre_affichage }}</td>
              <td class="actions">
                <button class="icon-btn" @click="editMembre(membre)" title="Modifier">
                  <Pencil />
                </button>
                <button class="icon-btn danger" @click="deleteMembre(membre.id)" title="Supprimer">
                  <Trash2 />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- Section Anciens présidents -->
      <section class="section">
        <div class="section-header">
          <h2>Anciens présidents</h2>
          <button class="btn btn-primary" @click="openCreateAncien">
            <Plus class="btn-icon" /> Ajouter
          </button>
        </div>

        <div v-if="showAncienForm" class="modal-overlay">
          <div class="modal">
            <div class="modal-header">
              <h2>{{ editingAncien ? 'Modifier l’ancien président' : 'Nouvel ancien président' }}</h2>
              <button class="close-btn" @click="closeAncienForm"><X /></button>
            </div>
            <form @submit.prevent="saveAncien" class="form">
              <div class="form-row">
                <div class="form-group">
                  <label>Nom</label>
                  <input type="text" v-model="ancienForm.nom" required />
                </div>
                <div class="form-group">
                  <label>Prénom</label>
                  <input type="text" v-model="ancienForm.prenom" required />
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Période début</label>
                  <input type="number" v-model="ancienForm.periode_debut" min="1900" max="2100" required />
                </div>
                <div class="form-group">
                  <label>Période fin</label>
                  <input type="number" v-model="ancienForm.periode_fin" min="1900" max="2100" />
                </div>
              </div>
              <div class="form-actions">
                <button type="button" class="btn btn-secondary" @click="closeAncienForm">Annuler</button>
                <button type="submit" class="btn btn-primary">
                  {{ editingAncien ? 'Enregistrer' : 'Créer' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <div v-if="loadingAnciens" class="loading">Chargement...</div>
        <table v-else class="table">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Période</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ancien in anciens" :key="ancien.id">
              <td>{{ ancien.prenom }} {{ ancien.nom }}</td>
              <td>{{ ancien.periode_debut }} – {{ ancien.periode_fin || 'Mandat en cours' }}</td>
              <td class="actions">
                <button class="icon-btn" @click="editAncien(ancien)" title="Modifier">
                  <Pencil />
                </button>
                <button class="icon-btn danger" @click="deleteAncien(ancien.id)" title="Supprimer">
                  <Trash2 />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from '@/utils/axios'
import Swal from 'sweetalert2'
import { Plus, X, Pencil, Trash2 } from 'lucide-vue-next'
import AdminLayout from '@/components/admin/AdminLayout.vue'

const membres = ref([])
const anciens = ref([])
const loadingMembres = ref(true)
const loadingAnciens = ref(true)
const showMembreForm = ref(false)
const showAncienForm = ref(false)
const editingMembre = ref(null)
const editingAncien = ref(null)
const membrePhotoFile = ref(null)

const membreForm = reactive({
  nom: '',
  prenom: '',
  poste: '',
  ordre_affichage: 0,
})

const ancienForm = reactive({
  nom: '',
  prenom: '',
  periode_debut: new Date().getFullYear(),
  periode_fin: null,
})

const getStorageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  const base = import.meta.env.VITE_STORAGE_URL || 'http://localhost:8000/storage'
  return `${base}/${path}`
}

const fetchMembres = async () => {
  loadingMembres.value = true
  try {
    const response = await axios.get('/membres-bureau')
    membres.value = response.data
  } catch (error) {
    console.error('Erreur lors du chargement', error)
  } finally {
    loadingMembres.value = false
  }
}

const fetchAnciens = async () => {
  loadingAnciens.value = true
  try {
    const response = await axios.get('/anciens-presidents')
    anciens.value = response.data
  } catch (error) {
    console.error('Erreur lors du chargement', error)
  } finally {
    loadingAnciens.value = false
  }
}

const openCreateMembre = () => {
  editingMembre.value = null
  resetMembreForm()
  showMembreForm.value = true
}

const editMembre = (membre) => {
  editingMembre.value = membre
  membreForm.nom = membre.nom
  membreForm.prenom = membre.prenom
  membreForm.poste = membre.poste
  membreForm.ordre_affichage = membre.ordre_affichage
  membrePhotoFile.value = null
  showMembreForm.value = true
}

const closeMembreForm = () => {
  showMembreForm.value = false
  resetMembreForm()
}

const resetMembreForm = () => {
  membreForm.nom = ''
  membreForm.prenom = ''
  membreForm.poste = ''
  membreForm.ordre_affichage = 0
  membrePhotoFile.value = null
}

const onMembrePhotoChange = (event) => {
  membrePhotoFile.value = event.target.files[0] || null
}

const saveMembre = async () => {
  const formData = new FormData()
  formData.append('nom', membreForm.nom)
  formData.append('prenom', membreForm.prenom)
  formData.append('poste', membreForm.poste)
  formData.append('ordre_affichage', membreForm.ordre_affichage)
  if (membrePhotoFile.value) {
    formData.append('photo', membrePhotoFile.value)
  }

  try {
    if (editingMembre.value) {
      formData.append('_method', 'PUT')
      await axios.post(`/membres-bureau/${editingMembre.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      Swal.fire('Succès', 'Membre modifié.', 'success')
    } else {
      await axios.post('/membres-bureau', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      Swal.fire('Succès', 'Membre ajouté.', 'success')
    }
    closeMembreForm()
    fetchMembres()
  } catch (error) {
    Swal.fire('Erreur', error.response?.data?.message || 'Une erreur est survenue.', 'error')
  }
}

const deleteMembre = async (id) => {
  const result = await Swal.fire({
    title: 'Supprimer ce membre ?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Supprimer',
    cancelButtonText: 'Annuler',
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/membres-bureau/${id}`)
      Swal.fire('Supprimé', 'Membre supprimé.', 'success')
      fetchMembres()
    } catch (error) {
      Swal.fire('Erreur', error.response?.data?.message || 'Une erreur est survenue.', 'error')
    }
  }
}

const openCreateAncien = () => {
  editingAncien.value = null
  resetAncienForm()
  showAncienForm.value = true
}

const editAncien = (ancien) => {
  editingAncien.value = ancien
  ancienForm.nom = ancien.nom
  ancienForm.prenom = ancien.prenom
  ancienForm.periode_debut = ancien.periode_debut
  ancienForm.periode_fin = ancien.periode_fin
  showAncienForm.value = true
}

const closeAncienForm = () => {
  showAncienForm.value = false
  resetAncienForm()
}

const resetAncienForm = () => {
  ancienForm.nom = ''
  ancienForm.prenom = ''
  ancienForm.periode_debut = new Date().getFullYear()
  ancienForm.periode_fin = null
}

const saveAncien = async () => {
  const data = {
    nom: ancienForm.nom,
    prenom: ancienForm.prenom,
    periode_debut: ancienForm.periode_debut,
    periode_fin: ancienForm.periode_fin || null,
  }

  try {
    if (editingAncien.value) {
      await axios.put(`/anciens-presidents/${editingAncien.value.id}`, data)
      Swal.fire('Succès', 'Ancien président modifié.', 'success')
    } else {
      await axios.post('/anciens-presidents', data)
      Swal.fire('Succès', 'Ancien président ajouté.', 'success')
    }
    closeAncienForm()
    fetchAnciens()
  } catch (error) {
    Swal.fire('Erreur', error.response?.data?.message || 'Une erreur est survenue.', 'error')
  }
}

const deleteAncien = async (id) => {
  const result = await Swal.fire({
    title: 'Supprimer cet ancien président ?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Supprimer',
    cancelButtonText: 'Annuler',
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/anciens-presidents/${id}`)
      Swal.fire('Supprimé', 'Ancien président supprimé.', 'success')
      fetchAnciens()
    } catch (error) {
      Swal.fire('Erreur', error.response?.data?.message || 'Une erreur est survenue.', 'error')
    }
  }
}

onMounted(() => {
  fetchMembres()
  fetchAnciens()
})
</script>

<style scoped>
.page-header {
  margin-bottom: 2rem;
}

h1 {
  font-size: 2rem;
  color: var(--text);
}

.section {
  margin-bottom: 3rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

h2 {
  font-size: 1.8rem;
  color: var(--text);
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.2rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-primary {
  background-color: var(--accent);
  color: #fff;
}

.btn-primary:hover {
  background-color: #d95f3c;
}

.btn-secondary {
  background-color: #6c757d;
  color: #fff;
}

.btn-icon {
  width: 18px;
  height: 18px;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background-color: var(--surface);
  border-radius: 12px;
  padding: 2rem;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.close-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--text);
}

.form-group {
  margin-bottom: 1rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: var(--text);
}

input {
  width: 100%;
  padding: 0.7rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-family: inherit;
  background-color: var(--bg);
  color: var(--text);
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
}

.table {
  width: 100%;
  border-collapse: collapse;
  background-color: var(--surface);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

th,
td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  color: var(--text);
}

th {
  background-color: var(--surface);
  font-weight: 600;
}

.thumb {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
}

.no-image {
  display: inline-block;
  width: 50px;
  height: 50px;
  background-color: var(--bg);
  text-align: center;
  line-height: 50px;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.icon-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: var(--text);
  padding: 0.25rem;
  transition: color 0.3s;
  
}

.icon-btn:hover {
  color: var(--primary);
}

.icon-btn.danger:hover {
  color: var(--accent);
}

.loading {
  text-align: center;
  padding: 2rem;
  color: var(--text);
}

@media (max-width: 768px) {
  .form-row {
    grid-template-columns: 1fr;
  }
  .section-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
}
</style>d