<template>
  <div class="min-h-screen flex flex-col bg-[#EBF2FA] font-sans selection:bg-orange-100 selection:text-[#F26430]">
    
    <header class="sticky top-0 z-30 flex items-center justify-between px-6 py-4 bg-white/80 backdrop-blur-md border-b border-gray-200 transition-all duration-300">
      <div class="flex items-center gap-3 cursor-pointer group" @click="navigateTo('/user/dashboard')">
        <div class="flex items-center justify-center transition-transform group-hover:scale-105">
          <img src="@/assets/unmlogo.png" alt="Logo TamuQ" class="w-10 h-10 object-contain drop-shadow-[0_0_10px_rgba(242,100,48,0.7)]" />
        </div>
        <div>
          <h1 class="font-bold text-[#00555A] text-lg leading-none tracking-tight">TamuQ</h1>
          <span class="text-[10px] font-bold text-[#F26430] uppercase tracking-widest">Student Portal</span>
        </div>
      </div>

      <div class="flex items-center gap-4">
        <div class="hidden md:block text-right">
           <p class="text-xs font-bold text-[#00555A]">{{ safeUser.nama_lengkap || 'Memuat...' }}</p>
           <p class="text-[10px] text-gray-500">{{ safeUser.prodi || '-' }}</p>
        </div>
        
        <button @click="toggleSidebar" class="p-2.5 rounded-xl text-gray-600 hover:bg-[#F26430]/10 hover:text-[#F26430] transition-all active:scale-95 focus:outline-none ring-offset-2 focus:ring-2 focus:ring-[#F26430]/20">
          <Bars3BottomRightIcon class="w-7 h-7" />
        </button>
      </div>
    </header>

    <transition name="fade">
      <div v-if="isSidebarOpen" @click="toggleSidebar" class="fixed inset-0 bg-[#00555A]/40 backdrop-blur-sm z-40 transition-opacity"></div>
    </transition>

    <transition name="slide">
      <aside v-if="isSidebarOpen" class="fixed top-0 right-0 w-80 h-full bg-white shadow-2xl z-50 flex flex-col rounded-l-3xl overflow-hidden">
        
        <div class="p-6 bg-[#00555A] relative overflow-hidden">
          <div class="absolute -right-6 -top-6 w-32 h-32 bg-[#F26430] opacity-20 rounded-full blur-2xl"></div>
          
          <div class="flex justify-between items-start relative z-10">
            <div class="flex items-center gap-4">
              <img 
                :src="profileImage" 
                alt="Profile" 
                class="w-14 h-14 rounded-full border-4 border-white/20 shadow-md object-cover bg-white" 
                @error="handleImageError"
              />
              <div>
                <p class="text-teal-100 text-xs font-medium uppercase tracking-wider">Selamat Datang</p>
                <p class="text-white font-bold text-lg truncate w-36" :title="safeUser.nama_lengkap">
                  {{ safeUser.nama_lengkap ? safeUser.nama_lengkap.split(' ')[0] : 'Guest' }}
                </p>
              </div>
            </div>
            
            <button @click="toggleSidebar" class="text-white/70 hover:text-white bg-white/10 hover:bg-white/20 p-2 rounded-lg transition backdrop-blur-md">
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto py-6 px-4 space-y-2 bg-white scrollbar-hide">
          <p class="px-4 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Menu Utama</p>
          
          <button
            v-for="item in menuItems"
            :key="item.name"
            @click="navigateTo(item.path)"
            :class="[
              isActive(item.path) 
                ? 'bg-[#F26430]/10 text-[#F26430] font-bold shadow-sm ring-1 ring-[#F26430]/20' 
                : 'text-gray-600 hover:bg-gray-50 hover:text-[#00555A] font-medium'
            ]"
            class="w-full flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all duration-200 group relative overflow-hidden"
          >
            <component 
              :is="item.icon" 
              class="w-6 h-6 transition-colors flex-shrink-0"
              :class="isActive(item.path) ? 'text-[#F26430]' : 'text-gray-400 group-hover:text-[#F26430]'"
            />
            
            <span class="relative z-10 flex-1 text-left whitespace-nowrap">{{ item.name }}</span>
            
            <ChevronRightIcon v-if="isActive(item.path)" class="w-4 h-4 text-[#F26430] flex-shrink-0" />
          </button>
        </div>

        <div class="p-6 border-t border-gray-100 bg-gray-50">
          <button @click="triggerLogout" class="w-full flex items-center justify-center gap-3 bg-white text-red-500 hover:bg-red-50 hover:text-red-600 font-semibold py-3.5 rounded-2xl border border-gray-200 hover:border-red-200 shadow-sm transition-all duration-200 group active:scale-95">
            <ArrowRightOnRectangleIcon class="w-5 h-5 group-hover:-translate-x-1 transition-transform" />
            Logout
          </button>
          <p class="text-center text-[10px] text-gray-300 mt-4 font-mono">TamuQ v1.0</p>
        </div>
      </aside>
    </transition>

    <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-hidden w-full max-w-5xl mx-auto">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <transition name="modal-fade">
      <div v-if="showLogoutModal" class="fixed inset-0 z-[60] flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-[#00282a]/60 backdrop-blur-sm transition-opacity" @click="showLogoutModal = false"></div>
        
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm relative z-10 overflow-hidden transform transition-all scale-100">
          <div class="p-8 text-center">
            
            <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6 text-red-500">
              <ArrowRightOnRectangleIcon class="w-10 h-10 ml-1" />
            </div>
            
            <h3 class="text-xl font-bold text-[#00555A] mb-2">Konfirmasi Logout</h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-8">
              Apakah Anda yakin ingin mengakhiri sesi ini? Anda harus login kembali untuk mengakses akun.
            </p>
            
            <div class="flex flex-col gap-3">
              <button 
                @click="confirmLogout" 
                class="w-full py-3.5 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold shadow-lg shadow-red-500/30 transition-all active:scale-95"
              >
                Ya, Keluar Sekarang
              </button>
              <button 
                @click="showLogoutModal = false" 
                class="w-full py-3.5 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl font-semibold transition-colors"
              >
                Batal
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "@/api";
import { useAuthStore } from '@/stores/auth';
import { 
  Bars3BottomRightIcon, XMarkIcon, HomeIcon, QrCodeIcon, ClockIcon, 
  DocumentTextIcon, GiftIcon, BookOpenIcon, Cog6ToothIcon, 
  ArrowRightOnRectangleIcon, ChevronRightIcon, AcademicCapIcon
} from "@heroicons/vue/24/outline";

const isSidebarOpen = ref(false);
const showLogoutModal = ref(false);
const router = useRouter();
const route = useRoute();
const auth = useAuthStore();

const user = ref(auth.user || {});

const menuItems = [
  { name: "Beranda", path: "/user/dashboard", icon: HomeIcon },
  { name: "QR Code", path: "/user/qr", icon: QrCodeIcon },
  { name: "Riwayat Kunjungan", path: "/user/riwayat", icon: ClockIcon },
  { name: "Surat Bebas Perpustakaan", path: "/user/surat-bebas", icon: DocumentTextIcon },
  { name: "Surat Donasi Buku", path: "/user/donasi", icon: GiftIcon },
  { name: "Layanan Buku", path: "/user/layanan-buku", icon: BookOpenIcon },
  { name: "E-Skripsi", path: "/user/e-skripsi", icon: AcademicCapIcon },
  { name: "Pengaturan", path: "/user/pengaturan", icon: Cog6ToothIcon },
];

const safeUser = computed(() => {
  const u = user.value;
  
  if (!u) return {};

  if (u.data && u.data.user) return u.data.user; 
  if (u.data && u.data.nama_lengkap) return u.data; 
  if (u.user) return u.user;
  if (u.nama_lengkap) return u;

  return {};
});

const dynamicAvatar = computed(() => {
  const name = safeUser.value?.nama_lengkap || 'User';
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=00555A&color=fff&bold=true&length=2`;
});

const profileImage = computed(() => {
  if (safeUser.value?.foto_profil && safeUser.value.foto_profil !== "") {
    return safeUser.value.foto_profil;
  }
  return dynamicAvatar.value;
});

const handleImageError = (e) => {
  e.target.src = dynamicAvatar.value;
};

const fetchUserData = async () => {
  try {
    const response = await api.get('/user');
    
    if (response.data) {
        user.value = response.data;
        auth.setUser(response.data);
    }

  } catch (error) {
    console.error("Gagal update user:", error);
    if (error.response?.status === 401) {
        forceLogout();
    }
  }
};

onMounted(() => {
    fetchUserData();
});

const toggleSidebar = () => { isSidebarOpen.value = !isSidebarOpen.value; };

const isActive = (path) => {
  return route.path === path || route.path.startsWith(`${path}/`);
};

const navigateTo = (path) => {
  isSidebarOpen.value = false;
  router.push(path);
};

const triggerLogout = () => {
  isSidebarOpen.value = false; 
  showLogoutModal.value = true;
};

const confirmLogout = async () => {
  showLogoutModal.value = false;
  
  try {
    await api.post('/logout');
  } catch (error) {
    console.error("Logout API error", error);
  } finally {
    auth.clearAuth();
    router.push("/login");
  }
};

const forceLogout = () => {
  auth.clearAuth();
  router.push("/login");
};
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.slide-enter-active, .slide-leave-active { transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.slide-enter-from, .slide-leave-to { transform: translateX(100%); }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.3s ease; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
.modal-fade-enter-active .transform, .modal-fade-leave-active .transform { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
.modal-fade-enter-from .transform, .modal-fade-leave-to .transform { transform: scale(0.95) translateY(10px); }
</style>