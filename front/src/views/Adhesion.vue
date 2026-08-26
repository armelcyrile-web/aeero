<template>
  <div class="adhesion">
    <section class="section-header">
      <h1>Rejoindre l’AEERO</h1>
    </section>

    <section class="section">
      <h2>Qui peut adhérer ?</h2>
      <p>
        L’adhésion à l’AEERO est ouverte à tout élève ou étudiant ressortissant de Ouoghi, résidant
        au Bénin ou à l’étranger. L’association est apolitique et accueille toute personne partageant
        ses valeurs de solidarité, de travail et de développement.
      </p>
    </section>

    <section class="section">
      <h2>Pourquoi nous rejoindre ?</h2>
      <div class="raisons-grid">
        <div class="raison">
          <Users class="raison-icon" />
          <h3>Réseau d’entraide</h3>
          <p>Intégrez une communauté soudée et solidaire, prête à vous accompagner tout au long de vos études.</p>
        </div>
        <div class="raison">
          <Handshake class="raison-icon" />
          <h3>Accès aux activités</h3>
          <p>Participez gratuitement ou à tarif réduit aux cours de vacances, renforcements et à la fête du 1er août.</p>
        </div>
        <div class="raison">
          <TrendingUp class="raison-icon" />
          <h3>Accompagnement post-études</h3>
          <p>Bénéficiez d’un réseau d’entraide pour faciliter votre insertion professionnelle et votre développement.</p>
        </div>
      </div>
    </section>

    <section class="section">
      <h2>Comment adhérer ?</h2>
      <p>
        L’adhésion se fait en présentiel : contactez le bureau ou présentez-vous lors d’une réunion
        ou d’un événement de l’association. Il n’y a pas d’inscription en ligne.
      </p>
      <router-link to="/contact" class="btn btn-primary">Nous contacter</router-link>
    </section>
  </div>
</template>

<script setup>
import { Users, Handshake, TrendingUp } from 'lucide-vue-next'
</script>

<style scoped>
.adhesion {
  max-width: 1000px;
  margin: 0 auto;
  padding: 3rem 1.5rem;
}

.section-header h1 {
  font-size: 2.5rem;
  margin-bottom: 2rem;
  color: var(--text);
}

.section {
  margin-bottom: 3rem;
}

h2 {
  font-size: 2rem;
  margin-bottom: 1.5rem;
  color: var(--text);
}

p {
  font-size: 1.1rem;
  line-height: 1.8;
  color: var(--text);
  margin-bottom: 1.5rem;
}

.raisons-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
}

.raison {
  background: var(--surface);
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s, box-shadow 0.3s;
}

.raison:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.raison-icon {
  color: var(--primary);
  width: 40px;
  height: 40px;
  margin-bottom: 1rem;
}

.raison h3 {
  font-size: 1.3rem;
  margin-bottom: 0.5rem;
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
}

.btn-primary {
  background-color: var(--accent);
  color: #fff;
}

.btn-primary:hover {
  background-color: #d95f3c;
}

@media (max-width: 768px) {
  .raisons-grid {
    grid-template-columns: 1fr;
  }
}
</style>