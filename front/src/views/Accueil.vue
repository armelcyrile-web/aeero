<template>
  <div class="accueil">
    <!-- HERO -->
   <section class="hero">
  <div class="hero-background" aria-hidden="true">AEERO</div>
  <div class="hero-content">
    <h1 class="hero-title">Association des Étudiants et Élèves Ressortissants de Ouoghi</h1>
    <p class="hero-subtitle">
      Ensemble, nous bâtissons l’avenir de notre communauté par l’éducation, la solidarité et la culture.
    </p>
    <div class="hero-actions">
      <router-link to="/adhesion" class="btn btn-primary">Rejoindre l’association</router-link>
      <router-link to="/presentation" class="btn btn-outline-light">Découvrir nos actions</router-link>
    </div>
  </div>
</section>

    <!-- CARROUSEL DYNAMIQUE -->
    <section class="carrousel-section">
      <div
        v-if="loading"
        class="carrousel-loading"
      >
        Chargement des actualités...
      </div>

      <div
        v-else-if="publications.length === 0"
        class="carrousel-empty"
      >
        Aucune actualité pour le moment.
      </div>

      <div
        v-else
        class="carrousel"
        @mouseenter="pauseAutoSlide"
        @mouseleave="resumeAutoSlide"
      >
        <div
          v-for="(pub, index) in publications"
          :key="pub.id"
          class="carrousel-slide"
          :class="{ active: index === currentSlide }"
        >
          <img
            :src="getStorageUrl(pub.image)"
            :alt="pub.titre"
            class="carrousel-img"
          />
          <div class="carrousel-overlay">
            <span
              class="badge"
              :class="'badge-' + pub.type"
            >
              {{ typeLabel(pub.type) }}
            </span>
            <h3>{{ pub.titre }}</h3>
            <p class="carrousel-date">{{ formatDate(pub.date_publication) }}</p>
          </div>
        </div>

        <button
          class="carrousel-btn prev"
          @click="prevSlide"
          aria-label="Image précédente"
        >
          <ChevronLeft />
        </button>
        <button
          class="carrousel-btn next"
          @click="nextSlide"
          aria-label="Image suivante"
        >
          <ChevronRight />
        </button>

        <div class="carrousel-dots">
          <span
            v-for="(pub, index) in publications"
            :key="pub.id"
            class="dot"
            :class="{ active: index === currentSlide }"
            @click="goToSlide(index)"
          ></span>
        </div>
      </div>
    </section>

    <!-- NOS ACTIONS PHARES -->
    <section class="actions-phares">
      <div class="section-header">
        <Target class="section-icon" />
        <h2>Nos actions phares</h2>
      </div>
      <div class="actions-grid">
        <div class="action-card">
          <Flag class="action-icon" />
          <h3>Fête de l’Indépendance</h3>
          <p>Chaque 1er août, AEERO célèbre notre fierté nationale à travers des activités citoyennes et culturelles.</p>
          <router-link to="/presentation" class="action-link">
            En savoir plus <ArrowRight class="link-icon" />
          </router-link>
        </div>
        <div class="action-card">
          <BookOpen class="action-icon" />
          <h3>Cours de vacances</h3>
          <p>Des sessions de révision et d’accompagnement pour renforcer les compétences des élèves.</p>
          <router-link to="/presentation" class="action-link">
            En savoir plus <ArrowRight class="link-icon" />
          </router-link>
        </div>
        <div class="action-card">
          <GraduationCap class="action-icon" />
          <h3>Renforcement BEPC/Bac</h3>
          <p>Programme de soutien scolaire pour la réussite aux examens nationaux.</p>
          <router-link to="/presentation" class="action-link">
            En savoir plus <ArrowRight class="link-icon" />
          </router-link>
        </div>
      </div>
    </section>

    <!-- GALERIE APERÇU -->
    <section class="galerie-apercu">
      <div class="section-header">
        <Images class="section-icon" />
        <h2>Galerie</h2>
        <router-link to="/galerie" class="voir-tout">
          Voir tous les albums <ArrowRight class="link-icon" />
        </router-link>
      </div>
      <div v-if="albums.length > 0" class="albums-grid">
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
      <p v-else class="empty-state">Aucun album disponible pour le moment.</p>
    </section>

    <!-- DONS -->
    <section class="dons">
      <div class="dons-content">
        <div class="dons-icon-circle">
          <Heart class="dons-icon" />
        </div>
        <h2>Soutenez nos actions</h2>
        <p>Vos dons contribuent directement au développement de nos initiatives.</p>
        <div class="mobile-money">
          <div class="money-item">
            <span class="operator">MTN Mobile Money</span>
            <span class="number">+229 00 00 00 00</span>
          </div>
          <div class="money-item">
            <span class="operator">Moov Africa</span>
            <span class="number">+229 00 00 00 00</span>
          </div>
        </div>
        <p class="dons-mention">
          <Check class="check-icon" /> 100% des dons vont à la caisse de l’association
        </p>
        <router-link to="/dons" class="btn btn-primary">Faire un don</router-link>
      </div>
    </section>

    <!-- NEWSLETTER -->
    <section class="newsletter">
      <div class="newsletter-content">
        <Mail class="newsletter-icon" />
        <h2>Restez informé</h2>
        <p>Abonnez-vous à notre newsletter pour recevoir nos actualités en avant-première.</p>
        <form @submit.prevent="subscribeNewsletter" class="newsletter-form">
          <input
            type="email"
            v-model="newsletterEmail"
            placeholder="Votre adresse email"
            required
          />
          <button type="submit" class="btn btn-primary">S’abonner</button>
        </form>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import {
  Target, Flag, BookOpen, GraduationCap, ArrowRight,
  Images, Heart, Check, Mail, ChevronLeft, ChevronRight
} from 'lucide-vue-next'
import axios from '@/utils/axios'
import Swal from 'sweetalert2'

const publications = ref([])
const loading = ref(true)
const currentSlide = ref(0)
const albums = ref([])
const newsletterEmail = ref('')
let autoSlideInterval = null

const getStorageUrl = (path) => {
  if (!path) return 'https://placehold.co/1200x500/cccccc/333333?text=AEERO'
  if (path.startsWith('http')) return path
  const base = import.meta.env.VITE_STORAGE_URL || 'http://localhost:8000/storage'
  return `${base}/${path}`
}

const formatDate = (dateStr) => {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  })
}

const typeLabel = (type) => {
  const labels = {
    actualite: 'Actualité',
    evenement: 'Événement',
    annonce: 'Annonce',
  }
  return labels[type] || type
}

const nextSlide = () => {
  if (publications.value.length === 0) return
  currentSlide.value = (currentSlide.value + 1) % publications.value.length
}

const prevSlide = () => {
  if (publications.value.length === 0) return
  currentSlide.value = (currentSlide.value - 1 + publications.value.length) % publications.value.length
}

const goToSlide = (index) => {
  currentSlide.value = index
  resetAutoSlide()
}

const resetAutoSlide = () => {
  if (autoSlideInterval) {
    clearInterval(autoSlideInterval)
    autoSlideInterval = null
  }
  startAutoSlide()
}

const startAutoSlide = () => {
  if (autoSlideInterval) clearInterval(autoSlideInterval)
  autoSlideInterval = setInterval(() => {
    nextSlide()
  }, 5000)
}

const pauseAutoSlide = () => {
  if (autoSlideInterval) {
    clearInterval(autoSlideInterval)
    autoSlideInterval = null
  }
}

const resumeAutoSlide = () => {
  if (!autoSlideInterval && publications.value.length > 1) {
    startAutoSlide()
  }
}

const subscribeNewsletter = async () => {
  try {
    const response = await axios.post('/newsletter', { email: newsletterEmail.value })
    Swal.fire({
      icon: 'success',
      title: 'Inscription réussie',
      text: response.data.message || 'Merci pour votre inscription !',
      timer: 3000,
    })
    newsletterEmail.value = ''
  } catch (error) {
    if (error.response && error.response.data && error.response.data.message) {
      Swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: error.response.data.message,
      })
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: 'Une erreur est survenue, veuillez réessayer.',
      })
    }
  }
}

onMounted(async () => {
  // Chargement des publications pour le carrousel
  try {
    const response = await axios.get('/publications')
    publications.value = response.data.sort((a, b) =>
      new Date(b.date_publication) - new Date(a.date_publication)
    )
    if (publications.value.length > 1) {
      startAutoSlide()
    }
  } catch (error) {
    console.error('Erreur lors du chargement des publications', error)
  } finally {
    loading.value = false
  }

  // Chargement des albums
  try {
    const response = await axios.get('/albums')
    albums.value = response.data.slice(0, 4)
  } catch (error) {
    console.error('Erreur lors du chargement des albums', error)
  }

  // Animation on scroll
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible')
      }
    })
  }, { threshold: 0.2 })
  document.querySelectorAll('.action-card, .album-card').forEach(el => observer.observe(el))
})

onUnmounted(() => {
  if (autoSlideInterval) {
    clearInterval(autoSlideInterval)
  }
})
</script>

<style scoped>
/* Réutilisation des styles précédents, avec ajouts pour carrousel dynamique */
.accueil {
  max-width: 100%;
}

/* HERO */
.hero {
  position: relative;
  background-color: var(--primary);
  color: #fff;
  padding: 6rem 1.5rem;
  overflow: hidden;
  text-align: center;
  transition: background-color 0.3s ease;

}

.hero-background {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12rem;
  font-weight: 700;
  color: #fff;
  opacity: 0.06;
  pointer-events: none;
  letter-spacing: 0.2em;
  user-select: none;
}
.hero-content {
  position: relative;
  max-width: 800px;
  margin: 0 auto;
}

.hero-title {
  font-size: 2.8rem;
  font-weight: 700;
  margin-bottom: 1rem;
  color: #ffffff;
}

.hero-subtitle {
  font-size: 1.2rem;
  margin-bottom: 2rem;
  color: #E2E8F0;
}

.hero-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.8rem 1.8rem;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.2s ease;
  border: 2px solid transparent;
}

.btn-primary {
  background-color: var(--accent);
  color: var(--primary);
  border-color: var(--accent);
}

.btn-primary:hover {
  background-color: darken(#9ACD32, 5%);
  border-color: darken(#9ACD32, 5%);
}

.btn-outline-light {
  background-color: transparent;
  color: #fff;
  border-color: #fff;
}

.btn-outline-light:hover {
  background-color: #fff;
  color: var(--primary);
}


/* CARROUSEL */
.carrousel-section {
  padding: 2rem 0;
}
.carrousel {
  position: relative;
  max-width: 1200px;
  margin: 0 auto;
  overflow: hidden;
  border-radius: 12px;
}
.carrousel-slide {
  display: none;
  position: relative;
}
.carrousel-slide.active {
  display: block;
}
.carrousel-img {
  width: 100%;
  height: 400px;
  object-fit: cover;
}
.carrousel-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 1.5rem;
  background: linear-gradient(transparent, rgba(0,0,0,0.8));
  color: #fff;
}
.badge {
  display: inline-block;
  padding: 0.3rem 0.8rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
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
.carrousel-overlay h3 {
  font-size: 1.5rem;
  margin-bottom: 0.25rem;
}
.carrousel-date {
  font-size: 0.9rem;
  opacity: 0.9;
}
.carrousel-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0,0,0,0.5);
  color: #fff;
  border: none;
  padding: 0.5rem;
  cursor: pointer;
  border-radius: 50%;
  z-index: 5;
}
.carrousel-btn.prev { left: 1rem; }
.carrousel-btn.next { right: 1rem; }
.carrousel-dots {
  position: absolute;
  bottom: 1rem;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 0.5rem;
}
.dot {
  width: 10px;
  height: 10px;
  background: #fff;
  border-radius: 50%;
  opacity: 0.5;
  cursor: pointer;
}
.dot.active {
  opacity: 1;
}
.carrousel-loading,
.carrousel-empty {
  text-align: center;
  padding: 3rem;
  color: var(--text);
  font-style: italic;
}

/* SECTIONS COMMUNES */
.section-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 2rem;
}
.section-icon {
  color: var(--primary);
  width: 32px;
  height: 32px;
}
.section-header h2 {
  font-size: 2rem;
  color: var(--text);
}
.voir-tout {
  margin-left: auto;
  color: var(--primary);
  text-decoration: none;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.voir-tout:hover {
  text-decoration: underline;
}
.link-icon {
  width: 16px;
  height: 16px;
}

/* ACTIONS PHARES */
.actions-phares {
  padding: 3rem 1.5rem;
  max-width: 1200px;
  margin: 0 auto;
}
.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
}
.action-card {
  background: var(--surface);
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  transition: transform 0.3s, box-shadow 0.3s;
  opacity: 0;
  transform: translateY(20px);
}
.action-card.visible {
  opacity: 1;
  transform: translateY(0);
}
.action-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
.action-icon {
  color: var(--primary);
  width: 40px;
  height: 40px;
  margin-bottom: 1rem;
}
.action-card h3 {
  font-size: 1.4rem;
  margin-bottom: 0.5rem;
}
.action-card p {
  color: var(--text);
  margin-bottom: 1rem;
}
.action-link {
  color: var(--accent);
  text-decoration: none;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}
.action-link:hover {
  text-decoration: underline;
}

/* GALERIE APERÇU */
.galerie-apercu {
  padding: 3rem 1.5rem;
  max-width: 1200px;
  margin: 0 auto;
}
.albums-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}
.album-card {
  text-decoration: none;
  color: var(--text);
  border-radius: 12px;
  overflow: hidden;
  background: var(--surface);
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  transition: transform 0.3s, box-shadow 0.3s;
  opacity: 0;
  transform: translateY(20px);
}
.album-card.visible {
  opacity: 1;
  transform: translateY(0);
}
.album-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
.album-image-wrapper {
  position: relative;
}
.album-image-wrapper img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}
.album-count {
  position: absolute;
  bottom: 0.5rem;
  left: 0.5rem;
  background: rgba(0,0,0,0.7);
  color: #fff;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.85rem;
}
.album-card h3 {
  padding: 1rem;
  font-size: 1.1rem;
}
.empty-state {
  text-align: center;
  color: var(--text);
  font-style: italic;
}

/* DONS */
.dons {
  background: var(--bg);
  padding: 4rem 1.5rem;
}
.dons-content {
  max-width: 800px;
  margin: 0 auto;
  text-align: center;
  background: var(--surface);
  padding: 3rem 2rem;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.dons-icon-circle {
  width: 80px;
  height: 80px;
  background: var(--secondary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
}
.dons-icon {
  color: #fff;
  width: 40px;
  height: 40px;
}
.dons-content h2 {
  font-size: 2rem;
  margin-bottom: 1rem;
}
.dons-content p {
  margin-bottom: 1.5rem;
}
.mobile-money {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  align-items: center;
  margin-bottom: 1.5rem;
}
.money-item {
  background: var(--bg);
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  display: flex;
  gap: 1rem;
  align-items: center;
}
.operator {
  font-weight: 600;
  color: var(--text);
}
.number {
  color: var(--text);
}
.dons-mention {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  color: var(--text);
}
.check-icon {
  color: #28a745;
  width: 20px;
  height: 20px;
}

/* NEWSLETTER */
.newsletter {
  background: var(--bg);
  padding: 3rem 1.5rem;
}
.newsletter-content {
  max-width: 600px;
  margin: 0 auto;
  text-align: center;
  background: var(--surface);
  padding: 2.5rem 2rem;
  border-radius: 16px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
.newsletter-icon {
  width: 48px;
  height: 48px;
  color: var(--primary);
  margin-bottom: 1rem;
}
.newsletter-content h2 {
  font-size: 2rem;
  margin-bottom: 1rem;
}
.newsletter-content p {
  margin-bottom: 2rem;
}
.newsletter-form {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  flex-wrap: wrap;
}
.newsletter-form input {
  padding: 0.8rem 1.2rem;
  border: 1px solid #ccc;
  border-radius: 50px;
  flex: 1;
  min-width: 200px;
  font-family: inherit;
  background: var(--bg);
  color: var(--text);
}
.newsletter-form input:focus {
  outline: none;
  border-color: var(--primary);
}

/* RESPONSIVE */
@media (max-width: 768px) {
  .hero-title { font-size: 2rem; }
  .section-header { flex-wrap: wrap; }
  .voir-tout { margin-left: 0; width: 100%; margin-top: 0.5rem; }
  .actions-grid, .albums-grid { grid-template-columns: 1fr; }
  .newsletter-form { flex-direction: column; }
}
</style>