import { createRouter, createWebHistory } from "vue-router";
import LoginView from "../pages/LoginView.vue";
import RegisterView from "../pages/RegisterView.vue";
import HomeView from "../pages/HomeView.vue";
import ProfileView from "../pages/ProfileView.vue";
import DashboardView from "../pages/admin/DashboardView.vue";
import ComicsView from "../pages/admin/ComicsView.vue";
import ChaptersView from "../pages/admin/ChaptersView.vue";
import ChapterPagesView from "../pages/admin/ChapterPagesView.vue";
import LibraryView from "../pages/LibraryView.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: HomeView,
    },
    {
      path: "/login",
      name: "login",
      component: LoginView,
    },
    {
      path: "/register",
      name: "register",
      component: RegisterView,
    },
    {
      path: "/profile",
      name: "profile",
      component: ProfileView,
      meta: { requiresAuth: true },
    },
    {
      path: "/admin/dashboard",
      name: "admin.dashboard",
      component: DashboardView,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: "/admin/comics",
      name: "admin.comics",
      component: ComicsView,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: "/admin/comics/:comicId/chapters",
      name: "admin.chapters",
      component: ChaptersView,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: "/admin/chapters/:chapterId/pages",
      name: "admin.chapter.pages",
      component: ChapterPagesView,
      meta: { requiresAuth: true, requiresAdmin: true },
    },
    {
      path: "/library",
      name: "library",
      component: LibraryView,
      meta: { requiresAuth: true },
    },
  ],
});

router.beforeEach((to, from) => {
  const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);
  const requiresAdmin = to.matched.some((record) => record.meta.requiresAdmin);

  const token = localStorage.getItem("kroma_token");
  const isAuthenticated = !!token;

  let user = null;
  const userStr = localStorage.getItem("kroma_user");
  if (userStr) {
    try {
      user = JSON.parse(userStr);
    } catch (e) {
      console.error("Data user corrupt");
    }
  }

  // Skenario 1: Belum login tapi mau akses halaman yang butuh login
  if (requiresAuth && !isAuthenticated) {
    return "/login";
  }

  // Skenario 2: Sudah login, tapi mau buka halaman Login/Register lagi
  if ((to.path === "/login" || to.path === "/register") && isAuthenticated) {
    return user?.role === "admin" ? "/admin/dashboard" : "/";
  }

  // Skenario 3: Member biasa mau akses halaman Admin
  if (requiresAdmin && user?.role !== "admin") {
    return "/";
  }

  // Skenario 4: Admin mau jalan-jalan ke halaman Member biasa
  if (
    isAuthenticated &&
    user?.role === "admin" &&
    !to.path.startsWith("/admin")
  ) {
    return "/admin/dashboard";
  }
});

export default router;
