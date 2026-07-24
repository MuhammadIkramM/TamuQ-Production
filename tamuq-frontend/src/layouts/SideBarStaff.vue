<template>
  <div class="min-h-screen bg-[#F3F4F6] flex flex-col font-sans text-slate-800">
    
    <header class="fixed top-0 left-0 right-0 h-16 bg-white shadow-sm border-b border-gray-200 flex items-center justify-between px-4 z-40">
      
      <div class="flex items-center gap-3">
        <button 
          @click="toggleSidebar" 
          class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-[#00555A] focus:outline-none transition-colors duration-200"
          title="Toggle Sidebar"
        >
          <Bars3Icon v-if="isCollapsed" class="w-6 h-6" />
          <Bars3BottomLeftIcon v-else class="w-6 h-6" />
        </button>

        <div class="flex items-center gap-3 pl-2 border-l border-gray-200 h-8 select-none">
          <img src="@/assets/unmlogo.png" alt="Logo UNM" class="w-7 h-7 object-contain" />
          <div class="flex flex-col leading-none">
            <span class="text-lg font-bold text-[#00555A] tracking-tight">TamuQ</span>
            <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest">
              {{ user.role === 'superadmin' ? 'Superadmin Portal' : 'Staff Portal' }}
            </span>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-4">
        <div class="text-right hidden sm:block leading-tight">
          <p class="text-sm font-bold text-gray-800">{{ user.nama_lengkap || 'Memuat...' }}</p>
          <span 
            class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset"
            :class="user.role === 'superadmin' ? 'bg-purple-50 text-purple-700 ring-purple-600/20' : 'bg-teal-50 text-teal-700 ring-teal-600/20'"
          >
            {{ user.roleDisplay }}
          </span>
        </div>
        
        <div class="relative group cursor-pointer">
           <img 
             v-if="user.avatarUrl" 
             :src="user.avatarUrl" 
             alt="Profile" 
             class="w-9 h-9 rounded-lg object-cover border border-gray-200 shadow-sm bg-gray-50"
             @error="handleImageError"
           />
           <div 
             v-else 
             class="w-9 h-9 bg-gradient-to-br from-[#00555A] to-[#007F86] rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-sm"
           >
             {{ user.initials }}
           </div>
        </div>
      </div>
    </header>

    <div class="flex pt-16 h-screen overflow-hidden">
      
      <aside 
        class="bg-white border-r border-gray-200 flex flex-col transition-all duration-300 ease-[cubic-bezier(0.25,0.8,0.25,1)] z-30 h-full flex-shrink-0"
        :class="isCollapsed ? 'w-[72px]' : 'w-64'"
      >
        <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto overflow-x-hidden scrollbar-hide">
          
          <transition name="fade">
            <p v-if="!isCollapsed" class="px-3 mb-3 text-[11px] font-bold text-gray-400 uppercase tracking-widest truncate">
              Navigasi Utama
            </p>
          </transition>
          
          <div v-for="item in filteredMenuItems" :key="item.path">
            <button
              @click="navigateTo(item.path)"
              class="relative flex items-center w-full py-2.5 px-3 rounded-lg transition-all duration-200 group font-medium whitespace-nowrap"
              :class="isActive(item.path) 
                ? 'bg-[#00555A] text-white shadow-md shadow-teal-900/10' 
                : 'text-gray-500 hover:bg-gray-50 hover:text-[#00555A]'"
            >
              <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center">
                <component 
                  :is="item.icon" 
                  class="w-5 h-5 transition-transform duration-200" 
                  :class="[
                    isActive(item.path) ? 'text-white' : 'text-gray-400 group-hover:text-[#00555A]',
                    isCollapsed ? 'group-hover:scale-110' : ''
                  ]" 
                />
              </div>

              <span 
                class="transition-all duration-300 overflow-hidden text-sm ml-3"
                :class="isCollapsed ? 'w-0 opacity-0 hidden' : 'w-auto opacity-100 block'"
              >
                {{ item.name }}
              </span>

              <div 
                v-if="isCollapsed"
                class="absolute left-14 ml-2 bg-gray-900 text-white text-xs font-medium px-2.5 py-1.5 rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50 shadow-xl whitespace-nowrap"
              >
                {{ item.name }}
                <div class="absolute top-1/2 -left-1 -mt-1 w-2 h-2 bg-gray-900 rotate-45"></div>
              </div>
            </button>
          </div>
        </nav>

        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
          <button
            @click="confirmLogout"
            class="flex items-center w-full py-2.5 px-3 rounded-lg transition-colors duration-200 group whitespace-nowrap bg-white border border-gray-200 text-gray-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 shadow-sm"
          >
              <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center">
                <ArrowRightOnRectangleIcon class="w-5 h-5 transition-transform group-hover:translate-x-1" />
              </div>
              
              <span 
               class="font-semibold text-sm transition-all duration-300 overflow-hidden ml-3"
               :class="isCollapsed ? 'w-0 opacity-0 hidden' : 'w-auto opacity-100 block'"
              >
                Keluar
              </span>
          </button>
        </div>
      </aside>

      <main class="flex-1 overflow-y-auto bg-[#F3F4F6] p-4 sm:p-6 lg:p-8 relative w-full scroll-smooth">
         <div class="max-w-7xl mx-auto min-h-[calc(100vh-8rem)]">
            <router-view v-slot="{ Component }">
              <transition name="fade" mode="out-in">
                <component :is="Component" />
              </transition>
            </router-view>
         </div>
      </main>

    </div>

    <transition name="modal-fade">
      <div v-if="modal.show" class="fixed inset-0 z-[60] flex items-center justify-center px-4" aria-modal="true" role="dialog">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px] transition-opacity" @click="closeModal"></div>
        
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm relative z-10 overflow-hidden transform transition-all scale-100 border border-gray-100">
          
          <div class="h-1.5 w-full bg-gradient-to-r from-red-500 to-orange-500"></div>

          <div class="p-6">
            <div class="flex items-start gap-4">
              <div class="flex-shrink-0 w-10 h-10 bg-red-50 rounded-full flex items-center justify-center">
                <ExclamationTriangleIcon class="w-6 h-6 text-red-600" />
              </div>

              <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900">{{ modal.title }}</h3>
                <p class="mt-1 text-sm text-gray-500 leading-relaxed">
                  {{ modal.message }}
                </p>
              </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
              <button 
                @click="closeModal"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#00555A] transition-colors"
              >
                Batal
              </button>
              <button 
                @click="executeLogout"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm transition-colors"
              >
                Keluar
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "@/api";
import { useAuthStore } from '@/stores/auth';
import { 
  HomeIcon, QrCodeIcon, DocumentTextIcon, UserGroupIcon, 
  BookOpenIcon, CogIcon, UserIcon, ArrowRightOnRectangleIcon,
  Bars3Icon, Bars3BottomLeftIcon, ExclamationTriangleIcon,
  AcademicCapIcon
} from "@heroicons/vue/24/outline"; 

const router = useRouter();
const route = useRoute();
const auth = useAuthStore();

const isCollapsed = ref(false);
const BASE_STORAGE_URL = import.meta.env.VITE_STORAGE_URL || "https://tamuq.my.id/storage/"; 

const user = reactive({
  nama_lengkap: "Memuat Pengguna...", 
  role: "", 
  roleDisplay: "",
  avatarUrl: null,
  initials: "..."
});

const modal = reactive({
  show: false,
  title: '',
  message: ''
});

const allMenuItems = [
  { name: "Dashboard", path: "/staff/dashboard", icon: HomeIcon },
  { name: "Scan QR", path: "/staff/scan-qr", icon: QrCodeIcon },
  { name: "Riwayat Pengunjung", path: "/staff/riwayat-pengunjung", icon: UserGroupIcon },
  { name: "Daftar Pengajuan", path: "/staff/pengajuan-surat", icon: DocumentTextIcon },
  { name: "Manajemen Buku", path: "/staff/manajemen-buku", icon: BookOpenIcon },
  { name: "Manajemen E-Skripsi", path: "/staff/manajemen-skripsi", icon: AcademicCapIcon },
  { name: "Manajemen Akun", path: "/staff/manajemen-akun", icon: UserIcon },
  { name: "Pengaturan", path: "/staff/pengaturan", icon: CogIcon },
];

const filteredMenuItems = computed(() => {
  const currentRole = user.role ? user.role.toLowerCase() : '';
  
  return allMenuItems.filter(item => {
    if (currentRole === 'staff' && item.path === '/staff/manajemen-akun') {
      return false;
    }
    
    if ((currentRole === 'superadmin' || currentRole === 'admin') && item.path === '/staff/scan-qr') {
      return false; 
    }
    
    return true;
  });
});

onMounted(() => {
  loadFromStore(); 
  fetchUserProfile();  
  checkScreenSize(); 
  window.addEventListener('resize', checkScreenSize);
});

const loadFromStore = () => {
  const userData = auth.user;
  if (userData) {
    updateUserState(userData);
  }
};

const fetchUserProfile = async () => {
  try {
    const response = await api.get('/user');
    const data = response.data.data || response.data;
    
    updateUserState(data);
    auth.setUser(data);
  } catch (error) {
    console.error("Gagal refresh user:", error);
    if(error.response?.status === 401) executeLogout();
  }
};

const updateUserState = (data) => {
  user.nama_lengkap = data.nama_lengkap || data.name || "Administrator";
  user.role = data.role;
  user.roleDisplay = formatRoleDisplay(data.role);
  user.initials = getInitials(user.nama_lengkap);

  const fotoPath = data.foto_profil || data.avatar;
  if (fotoPath) {
      if (fotoPath.startsWith('http')) {
        user.avatarUrl = fotoPath;
      } else {
        const cleanPath = fotoPath.replace(/^\//, '');
        user.avatarUrl = `${BASE_STORAGE_URL}${cleanPath}`;
      }
  } else {
      user.avatarUrl = null;
  }
};

const formatRoleDisplay = (roleRaw) => {
  if (!roleRaw) return "Staff";
  const role = roleRaw.toLowerCase();
  if (role.includes('super') || role === 'admin') return 'Super Admin';
  if (role === 'staff') return 'Staff';
  return role.charAt(0).toUpperCase() + role.slice(1);
};

const getInitials = (name) => {
  if (!name) return "A";
  const parts = name.trim().split(" ");
  if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
  return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
};

const handleImageError = () => { 
  user.avatarUrl = null;
};

const toggleSidebar = () => { 
  isCollapsed.value = !isCollapsed.value; 
};

const checkScreenSize = () => {
  if (window.innerWidth < 1024) {
    isCollapsed.value = true;
  } else {
    isCollapsed.value = false;
  }
};

const navigateTo = (path) => {
  router.push(path);
  if (window.innerWidth < 768) {
    isCollapsed.value = true;
  }
};

const isActive = (path) => {
  return route.path === path || route.path.startsWith(`${path}/`);
};

const confirmLogout = () => {
  modal.title = 'Konfirmasi Keluar';
  modal.message = 'Apakah Anda yakin ingin mengakhiri sesi ini?';
  modal.show = true;
};

const closeModal = () => {
  modal.show = false;
};

const executeLogout = async () => {
  modal.show = false;
  try { 
     await api.post('/logout'); 
  } catch(e) {
     console.error("Logout error pada server", e);
  } finally {
     auth.clearAuth();
     router.push("/login");
  }
};
</script>

<style scoped>
aside {
  transition-property: width, transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(5px);
}

.modal-fade-enter-active, .modal-fade-leave-active {
  transition: opacity 0.2s ease;
}
.modal-fade-enter-from, .modal-fade-leave-to {
  opacity: 0;
}
.modal-fade-enter-active .transform, 
.modal-fade-leave-active .transform {
  transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.modal-fade-enter-from .transform,
.modal-fade-leave-to .transform {
  transform: scale(0.95) translateY(10px);
}
</style>