import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../pages/LoginView.vue'
import RegisterView from '../pages/RegisterView.vue'
import HomeView from '../pages/HomeView.vue'
import ProfileView from '../pages/ProfileView.vue'
import DashboardView from '../pages/admin/DashboardView.vue'
import ComicsView from '../pages/admin/ComicsView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { requiresAuth: true }
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView
    },
    {
      path: '/profile',
      name: 'profile',
      component: ProfileView,
      meta: { requiresAuth: true }
    },
    // Rute Baru: Dasbor Admin
    {
      path: '/admin/dashboard',
      name: 'admin.dashboard',
      component: DashboardView,
      meta: { requiresAuth: true, requiresAdmin: true } // Wajib login & wajib Admin
    },
    {
      path: '/admin/comics',
      name: 'admin.comics',
      component: ComicsView,
      meta: { requiresAuth: true, requiresAdmin: true }
    }
  ]
})

router.beforeEach((to, from) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const requiresAdmin = to.matched.some(record => record.meta.requiresAdmin)
  
  const token = localStorage.getItem('kroma_token')
  const isAuthenticated = !!token
  
  // Parse data user untuk ngecek role
  let user = null
  const userStr = localStorage.getItem('kroma_user')
  if (userStr) {
    try {
      user = JSON.parse(userStr)
    } catch (e) {
      console.error('Data user corrupt')
    }
  }

  // Skenario 1: Belum login tapi mau akses halaman yang butuh login
  if (requiresAuth && !isAuthenticated) {
    return '/login'
  } 
  
  // Skenario 2: Sudah login, tapi mau buka halaman Login/Register lagi (Kena lempar sesuai Role)
  if ((to.path === '/login' || to.path === '/register') && isAuthenticated) {
    return user?.role === 'admin' ? '/admin/dashboard' : '/'
  }

  // Skenario 3: Member biasa mau akses halaman Admin (Satpam nendang ke Home)
  if (requiresAdmin && user?.role !== 'admin') {
    return '/'
  }

  // Skenario 4: Admin mau jalan-jalan ke halaman Member biasa (Satpam nendang ke Admin Dashboard)
  // Ngecek kalau dia admin, tapi halamannya tidak diawali dengan '/admin'
  if (isAuthenticated && user?.role === 'admin' && !to.path.startsWith('/admin')) {
    return '/admin/dashboard'
  }
})

export default router