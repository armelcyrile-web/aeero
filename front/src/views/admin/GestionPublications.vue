<template>
  <AdminLayout>
    <div class="gestion-publications">
      <div class="page-header">
        <h1>Gestion des publications</h1>
        <button class="btn btn-primary" @click="openCreateForm">
          <Plus class="btn-icon" /> Nouvelle publication
        </button>
      </div>

      <div v-if="showForm" class="modal-overlay">
        <div class="modal">
          <div class="modal-header">
            <h2>{{ editingPublication ? 'Modifier la publication' : 'Nouvelle publication' }}</h2>
            <button class="close-btn" @click="closeForm"><X /></button>
          </div>
          <form @submit.prevent="savePublication" class="form">
            <div class="form-group">
              <label>Titre</label>
              <input type="text" v-model="form.titre" required />
            </div>
            <div class="form-group">
              <label>Contenu</label>
              <textarea v-model="form.contenu" rows="5" required></textarea>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Type</label>
                <select v-model="form.type" required>
                  <option value="actualite">Actualité</option>
                  <option value="evenement">Événement</option>
                  <option value="annonce">Annonce</option>
                </select>
              </div>
              <div class="form-group">
                <label>Date de publication</label>
                <input type="date" v-model="form.date_publication" required />
              </div>
            </div>
            <div class="form-group">
              <label>Image</label>
              <input type="file" @change="onImageChange" accept="image/*" />
            </div>
            <div class="form-actions">
              <button type="button" class="btn btn-secondary" @click="closeForm">Annuler</button>
              <button type="submit" class="btn btn-primary">
                {{ editingPublication ? 'Enregistrer' : 'Créer' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <div v-if="loading" class="loading">Chargement...</div>
      <table v-else class="table">
        <thead>
          <tr>
            <th>Image</th>
            <th>Titre</th>
            <th>Type</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="pub in publications" :key="pub.id">
            <td>
              <img v-if="pub.image" :src="getStorageUrl(pub.image)" class="thumb" />
              <span v-else class="no-image">—</span>
            </td>
            <td>{{ pub.titre }}</td>
            <td>{{ pub.type }}</td>
            <td>{{ pub.date_publication }}</td>
            <td class="actions">
              <button class="icon-btn" @click="editPublication(pub)" title="Modifier">
                <Pencil />
              </button>
              <button class="icon-btn danger" @click="deletePublication(pub.id)" title="Supprimer">
                <Trash2 />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from '@/utils/axios'
import Swal from 'sweetalert2'
import { Plus, X, Pencil, Trash2 } from 'lucide-vue-next'
import AdminLayout from '@/components/admin/AdminLayout.vue'

const publications = ref([])
const loading = ref(true)
const showForm = ref(false)
const editingPublication = ref(null)
const imageFile = ref(null)

const form = reactive({
  titre: '',
  contenu: '',
  type: 'actualite',
  date_publication: new Date().toISOString().slice(0, 10),
})

const getStorageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  const base = import.meta.env.VITE_STORAGE_URL || 'http://localhost:8000/storage'
  return `${base}/${path}`
}

const fetchPublications = async () => {
  loading.value = true
  try {
    const response = await axios.get('/publications')
    publications.value = response.data
  } catch (error) {
    console.error('Erreur lors du chargement', error)
  } finally {
    loading.value = false
  }
}

const openCreateForm = () => {
  editingPublication.value = null
  resetForm()
  showForm.value = true
}

const editPublication = (pub) => {
  editingPublication.value = pub
  form.titre = pub.titre
  form.contenu = pub.contenu
  form.type = pub.type
  form.date_publication = pub.date_publication
  imageFile.value = null
  showForm.value = true
}

const closeForm = () => {
  showForm.value = false
  resetForm()
}

const resetForm = () => {
  form.titre = ''
  form.contenu = ''
  form.type = 'actualite'
  form.date_publication = new Date().toISOString().slice(0, 10)
  imageFile.value = null
}

const onImageChange = (event) => {
  imageFile.value = event.target.files[0] || null
}

const savePublication = async () => {
  const formData = new FormData()
  formData.append('titre', form.titre)
  formData.append('contenu', form.contenu)
  formData.append('type', form.type)
  formData.append('date_publication', form.date_publication)
  if (imageFile.value) {
    formData.append('image', imageFile.value)
  }

  try {
    if (editingPublication.value) {
      formData.append('_method', 'PUT')
      await axios.post(`/publications/${editingPublication.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      Swal.fire('Succès', 'Publication modifiée.', 'success')
    } else {
      await axios.post('/publications', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      Swal.fire('Succès', 'Publication créée.', 'success')
    }
    closeForm()
    fetchPublications()
  } catch (error) {
    Swal.fire('Erreur', error.response?.data?.message || 'Une erreur est survenue.', 'error')
  }
}

const deletePublication = async (id) => {
  const result = await Swal.fire({
    title: 'Êtes-vous sûr ?',
    text: 'Cette action est irréversible.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Supprimer',
    cancelButtonText: 'Annuler',
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/publications/${id}`)
      Swal.fire('Supprimé', 'Publication supprimée.', 'success')
      fetchPublications()
    } catch (error) {
      Swal.fire('Erreur', error.response?.data?.message || 'Une erreur est survenue.', 'error')
    }
  }
}

onMounted(fetchPublications)
</script>

<style scoped>
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

h1 {
  font-size: 2rem;
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
  max-width: 600px;
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

input,
select,
textarea {
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
  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
}
</style>