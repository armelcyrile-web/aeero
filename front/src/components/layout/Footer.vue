<template>
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-col">
        <h3 class="logo">AEERO</h3>
        <p class="devise">Solidarité – Travail – Développement</p>
      </div>

      <div class="footer-col">
        <h4>Liens rapides</h4>
        <router-link to="/" class="footer-link">Accueil</router-link>
        <router-link to="/presentation" class="footer-link">Présentation</router-link>
        <router-link to="/bureau" class="footer-link">Bureau</router-link>
        <router-link to="/galerie" class="footer-link">Galerie</router-link>
        <router-link to="/adhesion" class="footer-link">Adhésion</router-link>
        <router-link to="/dons" class="footer-link">Dons</router-link>
        <router-link to="/contact" class="footer-link">Contact</router-link>
      </div>

      <div class="footer-col">
        <h4>Contact</h4>
        <p><MapPin class="icon" /> Ouoghi, Commune de Savè</p>
        <p><Phone class="icon" /> {{ phone }}</p>
        <p><Mail class="icon" /> {{ email }}</p>
      </div>

      <div class="footer-col">
        <h4>Mentions légales</h4>
        <p>Association régie par la loi N°90-32 du 11 décembre 1990 en République du Bénin</p>
      </div>
    </div>

    <div class="footer-bottom">
      <p>© 2026 AEERO – Tous droits réservés</p>
    </div>
  </footer>
</template>

<script setup>
import { MapPin, Phone, Mail } from 'lucide-vue-next'

const phone = '+229 00 00 00 00'
const email = 'contact@aeero.bj'
</script>

<style scoped>
.footer {
  background-color: var(--surface);
  color: var(--text);
  margin-top: auto;
}

.footer-container {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  padding: 3rem 1.5rem 1.5rem;
}

.footer-col {
  flex: 1;
  min-width: 200px;
  display: flex;
  flex-direction: column;
}

.logo {
  font-family: 'Poppins', sans-serif;
  font-weight: 700;
  font-size: 1.8rem;
  color: var(--primary);
  margin-bottom: 0.5rem;
}

.devise {
  font-style: italic;
  color: var(--text);
}

.footer-col h4 {
  font-family: 'Poppins', sans-serif;
  margin-bottom: 1rem;
  color: var(--primary);
}

.footer-link {
  display: block;
  color: var(--text);
  text-decoration: none;
  margin-bottom: 0.5rem;
  transition: color 0.3s;
}

.footer-link:hover {
  color: var(--primary);
}

.footer-col p {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.icon {
  flex-shrink: 0;
}

.footer-bottom {
  text-align: center;
  padding: 1rem;
  border-top: 1px solid rgba(0, 0, 0, 0.1);
  font-size: 0.9rem;
  color: var(--text);
}

@media (max-width: 768px) {
  .footer-container {
    flex-direction: column;
    gap: 1.5rem;
  }
}
</style>