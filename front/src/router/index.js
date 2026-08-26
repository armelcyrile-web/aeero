import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Accueil from '@/views/Accueil.vue'
import Presentation from '@/views/Presentation.vue'
import Bureau from '@/views/Bureau.vue'
import Galerie from '@/views/Galerie.vue'
import AlbumDetail from '@/views/AlbumDetail.vue'
import Adhesion from '@/views/Adhesion.vue'
import Dons from '@/views/Dons.vue'
import Contact from '@/views/Contact.vue'
import Login from '@/views/admin/Login.vue'
import Dashboard from '@/views/admin/Dashboard.vue'
import GestionPublications from '@/views/admin/GestionPublications.vue'
import GestionAlbums from '@/views/admin/GestionAlbums.vue'
import GestionBureau from '@/views/admin/GestionBureau.vue'
import Actualites from '@/views/Actualites.vue'

const routes = [
  { path: '/', component: Accueil },
  { path: '/presentation', component: Presentation },
  { path: '/bureau', component: Bureau },
  { path: '/galerie', component: Galerie },
  { path: '/galerie/:id', component: AlbumDetail },
  { path: '/adhesion', component: Adhesion },
  { path: '/dons', component: Dons },
  { path: '/contact', component: Contact },
  { path: '/actualites', component: Actualites },
  { path: '/admin/login', component: Login },
  {
    path: '/admin',
    component: Dashboard,
    meta: { requiresAuth: true },
  },
  {
    path: '/admin/publications',
    component: GestionPublications,
    meta: { requiresAuth: true },
  },
  {
    path: '/admin/albums',
    component: GestionAlbums,
    meta: { requiresAuth: true },
  },
  {
    path: '/admin/bureau',
    component: GestionBureau,
    meta: { requiresAuth: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  const authStore = useAuthStore()
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return { path: '/admin/login' }
  }
})

export default router