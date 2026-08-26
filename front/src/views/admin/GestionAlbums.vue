<template>
  <AdminLayout>
    <div class="gestion-albums">
      <div v-if="!selectedAlbum">
        <div class="page-header">
          <h1>Gestion des albums</h1>
          <button class="btn btn-primary" @click="openCreateForm">
            <Plus class="btn-icon" /> Nouvel album
          </button>
        </div>

        <div v-if="showForm" class="modal-overlay">
          <div class="modal">
            <div class="modal-header">
              <h2>{{ editingAlbum ? 'Modifier l’album' : 'Nouvel album' }}</h2>
              <button class="close-btn" @click="closeForm"><X /></button>
            </div>
            <form @submit.prevent="saveAlbum" class="form">
              <div class="form-group">
                <label>Titre</label>
                <input type="text" v-model="form.titre" required />
              </div>
              <div class="form-group">
                <label>Description</label>
                <textarea v-model="form.description" rows="3"></textarea>
              </div>
              <div class="form-group">
                <label>Date</label>
                <input type="date" v-model="form.date" required />
              </div>
              <div class="form-group">
                <label>Image de couverture</label>
                <input type="file" @change="onCoverChange" accept="image/*" />
              </div>
              <div class="form-actions">
                <button type="button" class="btn btn-secondary" @click="closeForm">Annuler</button>
                <button type="submit" class="btn btn-primary">
                  {{ editingAlbum ? 'Enregistrer' : 'Créer' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <div v-if="loading" class="loading">Chargement...</div>
        <div v-else class="albums-grid">
          <div v-for="album in albums" :key="album.id" class="album-card">
            <img v-if="album.cover_image" :src="getStorageUrl(album.cover_image)" class="cover" />
            <div v-else class="no-cover">Pas de couverture</div>
            <div class="album-info">
              <h3>{{ album.titre }}</h3>
              <p>{{ album.photos_count }} photo(s)</p>
              <div class="album-actions">
                <button class="btn btn-secondary" @click="editAlbum(album)">
                  <Pencil class="btn-icon" /> Modifier
                </button>
                <button class="btn btn-secondary" @click="managePhotos(album)">
                  <Images class="btn-icon" /> Photos
                </button>
                <button class="btn btn-danger" @click="deleteAlbum(album.id)">
                  <Trash2 class="btn-icon" /> Supprimer
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else>
        <div class="page-header">
          <button class="btn btn-secondary" @click="selectedAlbum = null">
            <ArrowLeft class="btn-icon" /> Retour
          </button>
          <h1>{{ selectedAlbum.titre }}</h1>
        </div>

        <div class="upload-section">
          <h2>Ajouter des photos</h2>
          <form @submit.prevent="uploadPhotos" class="upload-form">
            <input type="file" multiple accept="image/*" @change="onPhotosChange" required />
            <button type="submit" class="btn btn-primary">
              <Upload class="btn-icon" /> Envoyer
            </button>
          </form>
        </div>

        <div class="photos-grid">
          <div v-for="photo in selectedAlbum.photos" :key="photo.id" class="photo-card">
            <img :src="getStorageUrl(photo.chemin_image)" class="photo-img" />
            <button class="delete-photo" @click="deletePhoto(photo.id)">
              <Trash2 />
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from '@/utils/axios'
import Swal from 'sweetalert2'
import { Plus, X, Pencil, Trash2, Images, ArrowLeft, Upload } from 'lucide-vue-next'
import AdminLayout from '@/components/admin/AdminLayout.vue'

const albums = ref([])
const loading = ref(true)
const showForm = ref(false)
const editingAlbum = ref(null)
const coverFile = ref(null)
const selectedAlbum = ref(null)
const photosFiles = ref([])

const form = reactive({
  titre: '',
  description: '',
  date: new Date().toISOString().slice(0, 10),
})

const getStorageUrl = (path) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  const base = import.meta.env.VITE_STORAGE_URL || 'http://localhost:8000/storage'
  return `${base}/${path}`
}

const fetchAlbums = async () => {
  loading.value = true
  try {
    const response = await axios.get('/albums')
    albums.value = response.data
  } catch (error) {
    console.error('Erreur lors du chargement', error)
  } finally {
    loading.value = false
  }
}

const openCreateForm = () => {
  editingAlbum.value = null
  resetForm()
  showForm.value = true
}

const editAlbum = (album) => {
  editingAlbum.value = album
  form.titre = album.titre
  form.description = album.description || ''
  form.date = album.date
  coverFile.value = null
  showForm.value = true
}

const closeForm = () => {
  showForm.value = false
  resetForm()
}

const resetForm = () => {
  form.titre = ''
  form.description = ''
  form.date = new Date().toISOString().slice(0, 10)
  coverFile.value = null
}

const onCoverChange = (event) => {
  coverFile.value = event.target.files[0] || null
}

const onPhotosChange = (event) => {
  photosFiles.value = Array.from(event.target.files)
}

const saveAlbum = async () => {
  const formData = new FormData()
  formData.append('titre', form.titre)
  formData.append('description', form.description)
  formData.append('date', form.date)
  if (coverFile.value) {
    formData.append('cover_image', coverFile.value)
  }

  try {
    if (editingAlbum.value) {
      formData.append('_method', 'PUT')
      await axios.post(`/albums/${editingAlbum.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      Swal.fire('Succès', 'Album modifié.', 'success')
    } else {
      await axios.post('/albums', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      Swal.fire('Succès', 'Album créé.', 'success')
    }
    closeForm()
    fetchAlbums()
  } catch (error) {
    Swal.fire('Erreur', error.response?.data?.message || 'Une erreur est survenue.', 'error')
  }
}

const deleteAlbum = async (id) => {
  const result = await Swal.fire({
    title: 'Êtes-vous sûr ?',
    text: 'Cet album et toutes ses photos seront supprimés.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Supprimer',
    cancelButtonText: 'Annuler',
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/albums/${id}`)
      Swal.fire('Supprimé', 'Album supprimé.', 'success')
      fetchAlbums()
    } catch (error) {
      Swal.fire('Erreur', error.response?.data?.message || 'Une erreur est survenue.', 'error')
    }
  }
}

const managePhotos = async (album) => {
  try {
    const response = await axios.get(`/albums/${album.id}`)
    selectedAlbum.value = response.data
  } catch (error) {
    Swal.fire('Erreur', 'Impossible de charger les photos.', 'error')
  }
}

const uploadPhotos = async () => {
  if (!photosFiles.value.length) return

  const formData = new FormData()
  photosFiles.value.forEach((file) => {
    formData.append('photos[]', file)
  })

  try {
    await axios.post(`/albums/${selectedAlbum.value.id}/photos`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    Swal.fire('Succès', 'Photos ajoutées.', 'success')
    photosFiles.value = []
    const response = await axios.get(`/albums/${selectedAlbum.value.id}`)
    selectedAlbum.value = response.data
  } catch (error) {
    Swal.fire('Erreur', error.response?.data?.message || 'Une erreur est survenue.', 'error')
  }
}

const deletePhoto = async (photoId) => {
  const result = await Swal.fire({
    title: 'Supprimer cette photo ?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Supprimer',
    cancelButtonText: 'Annuler',
  })

  if (result.isConfirmed) {
    try {
      await axios.delete(`/photos/${photoId}`)
      Swal.fire('Supprimé', 'Photo supprimée.', 'success')
      const response = await axios.get(`/albums/${selectedAlbum.value.id}`)
      selectedAlbum.value = response.data
    } catch (error) {
      Swal.fire('Erreur', error.response?.data?.message || 'Une erreur est survenue.', 'error')
    }
  }
}

onMounted(fetchAlbums)
</script>

<style scoped>
.page-header {
  display: flex;
  align-items: center;
  gap: 1rem;
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

.btn-danger {
  background-color: #dc3545;
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

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: var(--text);
}

input,
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

.albums-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
}

.album-card {
  background-color: var(--surface);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.cover {
  width: 100%;
  height: 180px;
  object-fit: cover;
}

.no-cover {
  height: 180px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--bg);
  color: var(--text);
}

.album-info {
  padding: 1rem;
}

.album-info h3 {
  margin-bottom: 0.5rem;
  color: var(--text);
}

.album-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.upload-section {
  margin-bottom: 2rem;
}

.upload-form {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.photos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 1rem;
}

.photo-card {
  position: relative;
  border-radius: 8px;
  overflow: hidden;
}

.photo-img {
  width: 100%;
  height: 150px;
  object-fit: cover;
}

.delete-photo {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  background-color: rgba(0, 0, 0, 0.6);
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  opacity: 0;
  transition: opacity 0.3s;
}

.photo-card:hover .delete-photo {
  opacity: 1;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: var(--text);
}
</style>