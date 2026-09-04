<template>
  <div class="presentation">
    <section class="section-header">
      <h1>Qui sommes-nous</h1>
    </section>

    <section class="section about">
      <p>
        L’Association des Élèves et Étudiants Ressortissants de Ouoghi (AEERO) est une association
        apolitique régie par la loi N°90-32 du 11 décembre 1990 en République du Bénin. Basée à Ouoghi,
        dans la commune de Savè, elle rassemble les étudiants et élèves originaires de cette localité,
        au Bénin comme à l’étranger.
      </p>
    </section>

    <section class="section">
      <h2>Nos objectifs</h2>
      <ul class="objectifs-list">
        <li><Users class="obj-icon" /> Solidarité et entraide entre ressortissants de Ouoghi</li>
        <li><Heart class="obj-icon" /> Accueil et intégration des nouveaux étudiants</li>
        <li><GraduationCap class="obj-icon" /> Organisation de cours de vacances à prix quasi-gratuit</li>
        <li><BookOpen class="obj-icon" /> Accompagnement aux examens BEPC et Bac</li>
        <li><TrendingUp class="obj-icon" /> Accompagnement à l’insertion professionnelle post-études</li>
      </ul>
    </section>

    <section class="section">
      <h2>Nos actions phares</h2>
      <div class="actions-grid">
        <div class="action-card">
          <Flag class="action-icon" />
          <h3>Fête de l’Indépendance du 1er août</h3>
          <p>Célébration citoyenne et culturelle de la fête nationale au sein de la communauté.</p>
        </div>
        <div class="action-card">
          <BookOpen class="action-icon" />
          <h3>Cours de vacances</h3>
          <p>Des sessions de révision et d’accompagnement pour renforcer les compétences des élèves.</p>
        </div>
        <div class="action-card">
          <GraduationCap class="action-icon" />
          <h3>Renforcement BEPC/Bac</h3>
          <p>Programme de soutien scolaire pour la réussite aux examens nationaux.</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { Users, Heart, GraduationCap, BookOpen, TrendingUp, Flag } from 'lucide-vue-next'
</script>

<style scoped>
.presentation {
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

.about p {
  font-size: 1.1rem;
  line-height: 1.8;
  color: var(--text);
}

h2 {
  font-size: 2rem;
  margin-bottom: 1.5rem;
  color: var(--text);
}

.objectifs-list {
  list-style: none;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.objectifs-list li {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-size: 1.1rem;
  color: var(--text);
}

.obj-icon {
  color: var(--primary);
  width: 24px;
  height: 24px;
  flex-shrink: 0;
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
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s, box-shadow 0.3s;
}

.action-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.action-icon {
  color: var(--primary);
  width: 40px;
  height: 40px;
  margin-bottom: 1rem;
}

.action-card h3 {
  font-size: 1.3rem;
  margin-bottom: 0.5rem;
}

.action-card p {
  color: var(--text);
}

@media (max-width: 768px) {
  .actions-grid {
    grid-template-columns: 1fr;
  }
}
</style>