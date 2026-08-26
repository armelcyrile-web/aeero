<template>
  <div class="dons-page">
    <section class="section-header">
      <h1>Soutenez nos actions</h1>
    </section>

    <section class="intro">
      <p>
        Vos dons contribuent directement au développement de nos initiatives : cours de vacances,
        renforcement scolaire, et organisation de la fête de l’Indépendance. Chaque contribution,
        même modeste, a un impact concret sur la vie des étudiants et élèves de Ouoghi.
      </p>
    </section>

    <section class="mobile-money">
      <div v-for="operateur in operateurs" :key="operateur.nom" class="money-card">
        <span class="operator">{{ operateur.nom }}</span>
        <span class="number">{{ operateur.numero }}</span>
      </div>
    </section>

    <p class="mention">
      <Check class="check-icon" />
      100% des dons vont à la caisse de l’association
    </p>
  </div>
</template>

<script setup>
import { Check } from 'lucide-vue-next'

const operateurs = [
  { nom: 'MTN Mobile Money', numero: '+229 00 00 00 00' },
  { nom: 'Moov Africa', numero: '+229 00 00 00 00' },
  { nom: 'Celtiis Money', numero: '+229 00 00 00 00' },
]
</script>

<style scoped>
.dons-page {
  max-width: 800px;
  margin: 0 auto;
  padding: 3rem 1.5rem;
}

.section-header h1 {
  font-size: 2.5rem;
  margin-bottom: 2rem;
  color: var(--text);
}

.intro p {
  font-size: 1.1rem;
  line-height: 1.8;
  margin-bottom: 2rem;
  color: var(--text);
}

.mobile-money {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.money-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: var(--surface);
  padding: 1.5rem 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s, box-shadow 0.3s;
}

.money-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
}

.operator {
  font-weight: 600;
  color: var(--text);
  font-size: 1.1rem;
}

.number {
  color: var(--text);
  font-size: 1.1rem;
  letter-spacing: 0.5px;
}

.mention {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  color: var(--text);
  font-size: 1.1rem;
  font-weight: 500;
}

.check-icon {
  color: #28a745;
  width: 24px;
  height: 24px;
}

@media (max-width: 600px) {
  .money-card {
    flex-direction: column;
    gap: 0.5rem;
    text-align: center;
  }
}
</style>