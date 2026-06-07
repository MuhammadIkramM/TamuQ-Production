<template>
  <div class="max-w-3xl mx-auto space-y-6">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-3xl font-extrabold text-[#00555A] flex items-center gap-2">
          Halo, {{ firstName }}! <span class="animate-wave origin-bottom-right inline-block">👋</span>
        </h1>
      </div>
      <div class="bg-white border border-gray-200 px-4 py-2 rounded-full shadow-sm flex items-center gap-2 text-sm text-gray-600">
        <span>📅</span>
        <span class="font-medium text-[#00555A]">{{ formattedDate }}</span>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-orange-50 border border-orange-100 p-6 rounded-3xl shadow-sm flex flex-col justify-between h-48 relative overflow-hidden group hover:shadow-md transition-all duration-300">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-[#F26430] rounded-full opacity-10 blur-xl group-hover:scale-150 transition-transform"></div>
        <div>
          <h3 class="text-[#D65020] font-bold text-lg mb-1">Waktu Sekarang</h3>
          <p class="text-orange-600/70 text-xs">Semoga Harimu Menyenangkan!</p>
        </div>
        <div class="mt-auto">
          <p class="text-4xl font-black text-[#00555A] tracking-tight font-mono">{{ formattedTime }}</p>
        </div>
      </div>

      <div class="relative bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden h-48 group hover:shadow-md transition-all duration-300">
        <img 
          src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=1000&auto=format&fit=crop" 
          alt="Library"
          class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 grayscale opacity-80"
          :class="{ 'grayscale-0 opacity-100': isLibraryOpen }" 
        />
        <div class="absolute inset-0 bg-gradient-to-t" :class="isLibraryOpen ? 'from-[#00555A]/90 via-[#00555A]/20' : 'from-gray-900/90 via-gray-900/40'"></div>

        <div class="absolute bottom-0 left-0 p-6 w-full">
          <div class="flex items-center gap-2 mb-1">
            <span class="relative flex h-3 w-3">
              <span v-if="isLibraryOpen" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3" :class="isLibraryOpen ? 'bg-emerald-500' : 'bg-red-500'"></span>
            </span>
            <span class="font-bold tracking-wide text-sm uppercase" :class="isLibraryOpen ? 'text-emerald-300' : 'text-red-300'">
              {{ isLibraryOpen ? 'Buka' : 'Tutup' }}
            </span>
          </div>
          <h2 class="text-white text-xl font-bold leading-tight">Perpustakaan</h2>
        </div>
      </div>
    </div>

    <div class="relative w-full bg-gradient-to-r from-[#00555A] to-[#004044] rounded-3xl shadow-lg p-6 md:p-8 text-white overflow-hidden transform hover:scale-[1.01] transition-transform duration-300">
      <svg class="absolute top-0 right-0 opacity-10 w-64 h-64 -mr-16 -mt-16 text-[#F26430]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" /></svg>
      
      <div v-if="isLoading && !user.nama_lengkap" class="relative z-10 flex flex-col items-center justify-center h-40">
         <p class="animate-pulse">Memuat Data Kartu...</p>
      </div>

      <div v-else class="relative z-10 flex flex-col md:flex-row gap-6 items-center md:items-start">
        <div class="w-20 h-20 md:w-24 md:h-24 rounded-full bg-white/10 backdrop-blur-sm border-4 border-white/20 flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
           <img v-if="user.foto_profil" :src="user.foto_profil" class="w-full h-full object-cover" />
           <span v-else class="text-3xl">🎓</span>
        </div>

        <div class="text-center md:text-left space-y-1 flex-1">
          <p class="text-teal-200 text-xs font-bold tracking-widest uppercase mb-1">Kartu Anggota Digital</p>
          <h2 class="text-2xl md:text-3xl font-bold font-sans tracking-tight leading-tight">{{ user.nama_lengkap || 'Pengguna' }}</h2>
          <p class="text-lg text-teal-100 font-mono mt-1">{{ user.nomor_induk || '-' }}</p> 
          
          <div class="mt-4 pt-4 border-t border-white/10 flex flex-wrap justify-center md:justify-start gap-3 text-sm">
            <div class="bg-black/20 px-3 py-1 rounded-lg backdrop-blur-md">
              <span class="opacity-70 text-xs block">Prodi</span>
              <span class="font-medium">{{ user.prodi || '-' }}</span>
            </div>
            <div class="bg-black/20 px-3 py-1 rounded-lg backdrop-blur-md">
              <span class="opacity-70 text-xs block">Status</span>
              <span class="font-medium" :class="user.status_akun === 'aktif' ? 'text-emerald-300' : 'text-red-300'">
                {{ capitalize(user.status_akun) || 'Nonaktif' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import api from '@/api';
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();
const user = ref({ nama_lengkap: '', nomor_induk: '', prodi: '', status_akun: '', foto_profil: null });
const isLoading = ref(true);
const isLibraryOpen = ref(false); 

const firstName = computed(() => user.value.nama_lengkap ? user.value.nama_lengkap.split(" ")[0] : "...");
const capitalize = (s) => typeof s === 'string' ? s.charAt(0).toUpperCase() + s.slice(1) : '';

const fetchData = async () => {
    try {
        if (auth.user) {
            user.value = auth.user;
            isLoading.value = false;
        }

        const [userRes, libRes] = await Promise.all([
            api.get('/user'),
            api.get('/library-status')
        ]);

        user.value = userRes.data;
        isLibraryOpen.value = libRes.data.is_open;
        
        auth.setUser(userRes.data);
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
};

const formattedDate = ref("");
const formattedTime = ref("");

const updateDateTime = () => {
  const now = new Date();
  formattedDate.value = now.toLocaleDateString("id-ID", { weekday: "long", day: "numeric", month: "long", year: "numeric" });
  formattedTime.value = now.toLocaleTimeString("id-ID", { hour: "2-digit", minute: "2-digit", second: "2-digit" }).replace(/\./g, ":");
};

let timer;
onMounted(() => {
  updateDateTime();
  fetchData();
  timer = setInterval(updateDateTime, 1000);
});
onUnmounted(() => { clearInterval(timer); });
</script>

<style scoped>
@keyframes wave {
  0% { transform: rotate(0.0deg) }
  10% { transform: rotate(14.0deg) }
  20% { transform: rotate(-8.0deg) }
  30% { transform: rotate(14.0deg) }
  40% { transform: rotate(-4.0deg) }
  50% { transform: rotate(10.0deg) }
  60% { transform: rotate(0.0deg) }
  100% { transform: rotate(0.0deg) }
}
.animate-wave {
  animation-name: wave;
  animation-duration: 2.5s;
  animation-iteration-count: infinite;
  transform-origin: 70% 70%;
  display: inline-block;
}
</style>