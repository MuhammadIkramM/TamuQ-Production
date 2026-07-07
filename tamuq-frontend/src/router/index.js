import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";

import LandingPage from "@/views/LandingPage.vue";

import LoginView from "@/views/Auth/LoginView.vue";
import RegisterView from "@/views/Auth/RegisterView.vue";

import SideBarStaff from "@/layouts/SideBarStaff.vue";
import BarUser from "@/layouts/BarUser.vue";

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: "/",
      name: "LandingPage",
      component: LandingPage,
      meta: { requiresAuth: false }
    },
    {
      path: "/login",
      name: "Login",
      component: LoginView,
      meta: { guest: true }
    },
    {
      path: "/register",
      name: "Register",
      component: RegisterView,
      meta: { guest: true }
    },

    {
      path: "/user",
      component: BarUser,
      meta: { requiresAuth: true, roles: ["pengunjung"] },
      children: [
        { path: "dashboard", name: "DashboardUser", component: () => import("@/views/User/DashboardUser.vue") },
        { path: "qr", name: "QRCodeUser", component: () => import("@/views/User/QRCodeUser.vue") },
        { path: "riwayat", name: "RiwayatKunjunganUser", component: () => import("@/views/User/RiwayatKunjunganUser.vue") },
        { path: "surat-bebas", name: "PengajuanSuratBebasUser", component: () => import("@/views/User/PengajuanSuratBebasUser.vue") },
        { path: "donasi", name: "PengajuanDonasiBukuUser", component: () => import("@/views/User/PengajuanDonasiBukuUser.vue") },
        { path: "layanan-buku", name: "LayananBukuUser", component: () => import("@/views/User/LayananBukuUser.vue") },
        { path: "e-skripsi", name: "ESkripsiUser", component: () => import("@/views/User/ESkripsiUser.vue") },
        { path: "pengaturan", name: "PengaturanUser", component: () => import("@/views/User/PengaturanUser.vue") },
      ],
    },

    {
      path: "/staff",
      component: SideBarStaff,
      meta: { requiresAuth: true },
      children: [
        {
          path: "dashboard",
          name: "DashboardStaff",
          component: () => import("@/views/Staff/DashboardStaff.vue"),
          meta: { roles: ['staff', 'superadmin', 'super_admin', 'admin'] }
        },
        {
          path: "scan-qr",
          name: "ScanQR",
          component: () => import("@/views/Staff/ScanQR.vue"),
          meta: { roles: ['staff'] }
        },
        {
          path: "pengajuan-surat",
          name: "PengajuanSurat",
          component: () => import("@/views/Staff/PengajuanSurat.vue"),
          meta: { roles: ['staff', 'superadmin', 'super_admin', 'admin'] }
        },
        {
          path: "riwayat-pengunjung",
          name: "RiwayatPengunjung",
          component: () => import("@/views/Staff/RiwayatPengunjung.vue"),
          meta: { roles: ['staff', 'superadmin', 'super_admin', 'admin'] }
        },
        {
          path: "manajemen-buku",
          name: "ManajemenBuku",
          component: () => import("@/views/Staff/ManajemenBuku.vue"),
          meta: { roles: ['staff', 'superadmin', 'super_admin', 'admin'] }
        },
        {
          path: "manajemen-skripsi",
          name: "ManajemenSkripsi",
          component: () => import("@/views/Staff/ManajemenSkripsi.vue"),
          meta: { roles: ['staff', 'superadmin', 'super_admin', 'admin'] }
        },
        {
          path: "manajemen-akun",
          name: "ManajemenAkun",
          component: () => import("@/views/Staff/ManajemenAkun.vue"),
          meta: { roles: ['superadmin', 'super_admin', 'admin'] }
        },
        {
          path: "pengaturan",
          name: "PengaturanStaff",
          component: () => import("@/views/Staff/PengaturanStaff.vue"),
          meta: { roles: ['staff', 'superadmin', 'super_admin', 'admin'] }
        },
      ],
    },

    {
      path: '/:pathMatch(.*)*',
      redirect: '/'
    }
  ],
});

router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();

  if (!auth.isInitialized) {
    await auth.initializeAuth();
  }

  const user = auth.user;

  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!user) return next("/login");

    const userRole = user.role ? user.role.toLowerCase() : "pengunjung";
    const isStaffOrAdmin = ["staff", "superadmin", "super_admin", "admin"].includes(userRole);

    if (isStaffOrAdmin && to.path.startsWith("/user")) {
      return next("/staff/dashboard");
    }
    if (!isStaffOrAdmin && to.path.startsWith("/staff")) {
      return next("/user/dashboard");
    }

    if (to.meta.roles && !to.meta.roles.includes(userRole)) {

      const overlay = document.createElement('div');
      overlay.id = "access-denied-popup";
      overlay.style.cssText = `
        position: fixed; inset: 0; z-index: 9999;
        display: flex; align-items: center; justify-content: center;
        background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);
        font-family: 'Inter', sans-serif;
      `;

      overlay.innerHTML = `
        <div style="background: white; width: 90%; max-width: 400px; padding: 24px; border-radius: 16px; text-align: center; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border-top: 6px solid #ef4444; animation: slideIn 0.3s ease-out;">
          <div style="background: #fee2e2; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <span style="font-size: 32px;">🚫</span>
          </div>
          <h2 style="margin: 0; color: #111827; font-size: 20px; font-weight: 800;">Akses Terbatas!</h2>
          <p style="margin: 12px 0 0; color: #4b5563; font-size: 14px; line-height: 1.5;">
            Halaman <b>${to.name || 'ini'}</b> hanya dapat diakses oleh personil yang berwenang.
          </p>
          <button id="close-popup" style="margin-top: 24px; width: 100%; background: #00555A; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;">
            Kembali ke Dashboard
          </button>
        </div>
        <style>
          @keyframes slideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        </style>
      `;

      document.body.appendChild(overlay);

      overlay.querySelector('#close-popup').onclick = () => {
        document.body.removeChild(overlay);
      };
      setTimeout(() => {
        if (document.getElementById("access-denied-popup")) {
          document.body.removeChild(overlay);
        }
      }, 3500);
      return next(isStaffOrAdmin ? "/staff/dashboard" : "/user/dashboard");
    }
  }

  if (to.meta.guest && user) {
    const userRole = user.role ? user.role.toLowerCase() : "pengunjung";
    const isStaffOrAdmin = ["staff", "superadmin", "super_admin", "admin"].includes(userRole);
    return next(isStaffOrAdmin ? "/staff/dashboard" : "/user/dashboard");
  }

  next();
});

export default router;