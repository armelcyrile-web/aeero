<template>
  <footer class="footer">
    <div class="container footer-container">
      <div class="footer-col">
        <h3 class="footer-logo">AEERO</h3>
        <p class="footer-devise">Solidarité – Travail – Développement</p>
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
        <p><MapPin class="footer-icon" /> Ouoghi, Commune de Savè</p>
        <p><Phone class="footer-icon" /> +229 00 00 00 00</p>
        <p><Mail class="footer-icon" /> contact@aeero.bj</p>
      </div>

      <div class="footer-col">
        <h4>Mentions légales</h4>
        <p>Association régie par la loi N°90-32 du 11 décembre 1990 en République du Bénin</p>
      </div>
    </div>

    <div class="footer-bottom">
      <div class="container">
        <p>© 2025 AEERO – Tous droits réservés</p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { MapPin, Phone, Mail } from 'lucide-vue-next'
</script>

<style scoped>
.footer {
  background-color: var(--primary);
  color: #fff;
  margin-top: auto;
}

.footer-container {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
  padding: 4rem 1.5rem 2rem;
}

.footer-col {
  flex: 1;
  min-width: 200px;
}

.footer-logo {
  font-size: 1.8rem;
  font-weight: var(--font-weight-bold);
  color: #fff;
  margin-bottom: 0.5rem;
}

.footer-devise {
  font-style: italic;
  color: rgba(255, 255, 255, 0.8);
}

.footer-col h4 {
  font-size: 1.1rem;
  font-weight: var(--font-weight-medium);
  margin-bottom: 1rem;
  color: #fff;
}

.footer-link {
  display: block;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  margin-bottom: 0.5rem;
  transition: color 0.2s;
}

.footer-link:hover {
  color: var(--accent);
}

.footer-col p {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
  color: rgba(255, 255, 255, 0.8);
}

.footer-icon {
  width: 18px;
  height: 18px;
  color: var(--accent);
  flex-shrink: 0;
}

.footer-bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.2);
  padding: 1.5rem 0;
  text-align: center;
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.7);
}

@media (max-width: 768px) {
  .footer-container {
    flex-direction: column;
    gap: 1.5rem;
  }
}
</style>