<template>
  <div class="contact">
    <section class="section-header">
      <h1>Contactez-nous</h1>
    </section>

    <div class="contact-layout">
      <form class="contact-form" @submit.prevent="submitForm">
        <div class="form-group">
          <label for="nom">Nom</label>
          <input type="text" id="nom" v-model="form.nom" required />
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" v-model="form.email" required />
        </div>
        <div class="form-group">
          <label for="message">Message</label>
          <textarea id="message" v-model="form.message" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Envoyer</button>
      </form>

      <div class="coordonnees">
        <h2>Nos coordonnées</h2>
        <p><MapPin class="coord-icon" /> Ouoghi, Commune de Savè, Bénin</p>
        <p><Phone class="coord-icon" /> +229 00 00 00 00</p>
        <p><Mail class="coord-icon" /> contact@aeero.bj</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import axios from '@/utils/axios'
import Swal from 'sweetalert2'
import { MapPin, Phone, Mail } from 'lucide-vue-next'

const form = reactive({
  nom: '',
  email: '',
  message: '',
})

const submitForm = async () => {
  try {
    const response = await axios.post('/contact', form)
    Swal.fire({
      icon: 'success',
      title: 'Message envoyé',
      text: response.data.message || 'Votre message a été envoyé avec succès.',
      timer: 3000,
    })
    form.nom = ''
    form.email = ''
    form.message = ''
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
</script>

<style scoped>
.contact {
  max-width: 1000px;
  margin: 0 auto;
  padding: 3rem 1.5rem;
}

.section-header h1 {
  font-size: 2.5rem;
  margin-bottom: 2rem;
  color: var(--text);
}

.contact-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  align-items: start;
}

.contact-form {
  background: var(--surface);
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: var(--text);
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-family: inherit;
  background: var(--bg);
  color: var(--text);
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: var(--primary);
}

.btn {
  display: inline-block;
  padding: 0.8rem 1.8rem;
  border-radius: 50px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s;
  cursor: pointer;
  border: none;
  font-size: 1rem;
}

.btn-primary {
  background-color: var(--accent);
  color: #fff;
}

.btn-primary:hover {
  background-color: #d95f3c;
}

.coordonnees {
  background: var(--surface);
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.coordonnees h2 {
  font-size: 1.8rem;
  margin-bottom: 1.5rem;
  color: var(--text);
}

.coordonnees p {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
  color: var(--text);
  font-size: 1.05rem;
}

.coord-icon {
  color: var(--primary);
  width: 22px;
  height: 22px;
  flex-shrink: 0;
}

@media (max-width: 768px) {
  .contact-layout {
    grid-template-columns: 1fr;
  }
}
</style>