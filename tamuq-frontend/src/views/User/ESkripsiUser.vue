<template>
  <div class="max-w-5xl mx-auto space-y-8">
    
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-[#00555A] flex items-center gap-2">
        <AcademicCapIcon class="w-8 h-8 text-[#F26430]" />
        E-Skripsi
      </h1>
      <p class="text-gray-500 text-sm mt-1">Jelajahi dan unduh arsip skripsi mahasiswa PTIK dan TEKOM.</p>
    </div>

    <!-- Toolbar -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
      <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
        
        <!-- Search -->
        <div class="relative flex-1 min-w-[200px]">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
          </div>
          <input 
            type="text" 
            v-model="searchQuery" 
            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none transition-all"
            placeholder="Cari judul, nama, atau NIM..."
          />
        </div>

        <!-- Filter Prodi -->
        <div class="relative min-w-[150px]">
          <select
            v-model="filterProdi"
            class="w-full appearance-none pl-3 pr-8 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none transition-all cursor-pointer"
          >
            <option value="">Semua Prodi</option>
            <option value="PTIK">PTIK</option>
            <option value="TEKOM">TEKOM</option>
          </select>
          <FunnelIcon class="w-4 h-4 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
        </div>

        <!-- Filter Tahun -->
        <div class="relative min-w-[150px]">
          <select
            v-model="filterTahun"
            class="w-full appearance-none pl-3 pr-8 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none transition-all cursor-pointer"
          >
            <option value="">Semua Tahun</option>
            <option v-for="thn in tahunOptions" :key="thn" :value="thn">{{ thn }}</option>
          </select>
          <FunnelIcon class="w-4 h-4 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
        </div>
      </div>
    </div>

    <!-- Info & Grid -->
    <div class="space-y-4">
      <div class="flex items-center gap-2">
        <Square3Stack3DIcon class="w-5 h-5 text-[#F26430]" />
        <h2 class="text-lg font-bold text-[#00555A]">Daftar Skripsi</h2>
        <span class="text-xs font-medium bg-[#00555A]/10 text-[#00555A] px-2 py-0.5 rounded-md">
          {{ filteredSkripsi.length }} dokumen
        </span>
      </div>

      <!-- Loading Skeleton -->
      <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-for="n in 6" :key="'skel-'+n" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm animate-pulse flex gap-4">
          <div class="w-12 h-12 bg-gray-100 rounded-xl shrink-0"></div>
          <div class="flex-1 space-y-3">
            <div class="h-4 bg-gray-100 rounded w-3/4"></div>
            <div class="h-3 bg-gray-100 rounded w-1/2"></div>
            <div class="h-3 bg-gray-100 rounded w-1/3"></div>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else-if="filteredSkripsi.length === 0" class="bg-white border border-dashed border-gray-300 rounded-2xl p-10 text-center">
        <DocumentTextIcon class="w-12 h-12 text-gray-300 mx-auto mb-3" />
        <p class="text-gray-400 font-medium">
          {{ (searchQuery || filterProdi || filterTahun) ? 'Tidak ada skripsi yang cocok dengan pencarian Anda.' : 'Belum ada data skripsi yang tersedia.' }}
        </p>
      </div>

      <!-- Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div 
          v-for="skripsi in filteredSkripsi" 
          :key="skripsi.id_skripsi"
          class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md hover:border-[#00555A]/20 transition-all flex flex-col group h-full relative overflow-hidden"
        >
          <!-- Decoration Line -->
          <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#00555A] opacity-0 group-hover:opacity-100 transition-opacity"></div>

          <div class="flex items-start gap-4 mb-4">
            <!-- Icon Anchor -->
            <div class="w-12 h-12 bg-[#00555A]/10 rounded-xl flex items-center justify-center text-[#00555A] shrink-0 group-hover:scale-110 transition-transform">
              <DocumentTextIcon class="w-6 h-6" />
            </div>
            
            <div class="flex-1">
              <h3 class="font-bold text-gray-800 text-sm leading-snug line-clamp-2 group-hover:text-[#00555A] transition-colors" :title="skripsi.judul">
                {{ skripsi.judul }}
              </h3>
              <p class="text-xs text-gray-500 mt-1 font-medium">{{ skripsi.nama }}</p>
              <p class="text-xs text-gray-400 font-mono">{{ skripsi.nim }}</p>
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-2 mt-auto pt-4 border-t border-gray-50 mb-4">
            <span 
              class="text-[10px] font-bold px-2 py-1 rounded"
              :class="skripsi.prodi === 'PTIK' ? 'text-blue-700 bg-blue-50 border border-blue-200' : 'text-purple-700 bg-purple-50 border border-purple-200'"
            >
              {{ skripsi.prodi }}
            </span>
            <span class="text-[10px] text-gray-500 bg-gray-100 px-2 py-1 rounded font-medium flex items-center gap-1">
              <CalendarDaysIcon class="w-3 h-3" />
              {{ skripsi.tahun_selesai }}
            </span>
          </div>

          <!-- Actions -->
          <div class="flex gap-2 w-full">
            <button 
              @click="previewSkripsi(skripsi)"
              class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-[#00555A]/5 hover:bg-[#00555A]/10 text-[#00555A] rounded-lg text-xs font-bold transition-colors"
            >
              <EyeIcon class="w-4 h-4" />
              Preview
            </button>
            <button 
              @click="downloadSkripsi(skripsi)"
              class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-[#F26430] hover:bg-[#D65020] text-white rounded-lg text-xs font-bold transition-colors shadow-sm"
            >
              <ArrowDownTrayIcon class="w-4 h-4" />
              Download
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Preview PDF -->
    <transition name="fade">
      <div v-if="showPreviewModal" class="fixed inset-0 flex items-center justify-center z-50 bg-[#00282a]/50 backdrop-blur-sm p-4" @click.self="closePreview">
        <div class="bg-white shadow-2xl rounded-2xl w-full max-w-4xl h-[85vh] overflow-hidden transform transition-all scale-100 flex flex-col relative">
          
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl shrink-0">
             <div>
               <h3 class="text-lg font-bold text-[#00555A] flex items-center gap-2">
                 <DocumentTextIcon class="w-5 h-5 text-[#F26430]" />
                 Preview Skripsi
               </h3>
               <p class="text-xs text-gray-400 truncate max-w-[300px] sm:max-w-[500px] mt-0.5">{{ previewTitle }}</p>
             </div>
             <button @click="closePreview" class="text-gray-400 hover:text-gray-600 bg-white shadow-sm border border-gray-200 rounded-full p-1.5 transition-colors">
               <XMarkIcon class="w-5 h-5" />
             </button>
          </div>
          
          <div class="flex-1 bg-gray-100 relative">
            <div v-if="previewLoading" class="absolute inset-0 flex items-center justify-center bg-white/80 z-10">
              <div class="flex flex-col items-center gap-3">
                <div class="w-10 h-10 border-4 border-[#00555A]/20 border-t-[#00555A] rounded-full animate-spin"></div>
                <p class="text-sm text-gray-500 font-medium">Memuat dokumen PDF...</p>
              </div>
            </div>
            <iframe 
              v-if="previewUrl" 
              :src="previewUrl" 
              class="w-full h-full"
              frameborder="0"
            ></iframe>
            <div v-if="previewError" class="absolute inset-0 flex items-center justify-center">
              <div class="text-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <XCircleIcon class="w-12 h-12 text-red-400 mx-auto mb-3" />
                <p class="text-sm text-gray-500 font-medium">Gagal memuat dokumen.</p>
                <p class="text-xs text-gray-400 mt-1">File mungkin tidak tersedia atau terjadi kesalahan server.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import api from '@/api';
import { 
  AcademicCapIcon, DocumentTextIcon, MagnifyingGlassIcon, CalendarDaysIcon,
  EyeIcon, ArrowDownTrayIcon, XMarkIcon, XCircleIcon
} from "@heroicons/vue/24/outline";
import { Square3Stack3DIcon, FunnelIcon } from "@heroicons/vue/24/solid";

// State
const skripsiList = ref([]);
const isLoading = ref(true);

// Filters
const searchQuery = ref("");
const filterProdi = ref("");
const filterTahun = ref("");

// Preview Modal State
const showPreviewModal = ref(false);
const previewUrl = ref(null);
const previewTitle = ref("");
const previewLoading = ref(false);
const previewError = ref(false);

// Fetch Data
const fetchSkripsi = async () => {
    try {
        const response = await api.get('/skripsi');
        skripsiList.value = response.data.data || [];
    } catch (error) {
        console.error("Gagal memuat data skripsi:", error);
    } finally {
        isLoading.value = false;
    }
};

// Computed Options
const tahunOptions = computed(() => {
    const years = skripsiList.value
        .map(s => s.tahun_selesai)
        .filter(y => y != null && y !== '');
    return [...new Set(years)].sort((a, b) => b - a);
});

// Computed Filtered List
const filteredSkripsi = computed(() => {
    let result = skripsiList.value;

    if (searchQuery.value) {
        const term = searchQuery.value.toLowerCase();
        result = result.filter(s => 
            (s.judul && s.judul.toLowerCase().includes(term)) ||
            (s.nama && s.nama.toLowerCase().includes(term)) ||
            (s.nim && s.nim.toLowerCase().includes(term))
        );
    }

    if (filterProdi.value) {
        result = result.filter(s => s.prodi === filterProdi.value);
    }

    if (filterTahun.value) {
        result = result.filter(s => String(s.tahun_selesai) === String(filterTahun.value));
    }

    return result;
});

// Actions
const previewSkripsi = async (skripsi) => {
    previewTitle.value = skripsi.judul;
    previewLoading.value = true;
    previewError.value = false;
    previewUrl.value = null;
    showPreviewModal.value = true;

    try {
        const response = await api.get(`/skripsi/${skripsi.id_skripsi}/preview`, {
            responseType: 'blob'
        });
        const blob = new Blob([response.data], { type: 'application/pdf' });
        previewUrl.value = URL.createObjectURL(blob);
    } catch (e) {
        console.error('Preview error:', e);
        previewError.value = true;
    } finally {
        previewLoading.value = false;
    }
};

const closePreview = () => {
    if (previewUrl.value) {
        URL.revokeObjectURL(previewUrl.value);
    }
    showPreviewModal.value = false;
    previewUrl.value = null;
    previewTitle.value = "";
    previewError.value = false;
};

const downloadSkripsi = async (skripsi) => {
    try {
        const response = await api.get(`/skripsi/${skripsi.id_skripsi}/download`, {
            responseType: 'blob'
        });
        
        const blob = new Blob([response.data], { type: 'application/pdf' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        
        // Generate valid filename
        const cleanTitle = skripsi.judul.replace(/[^a-zA-Z0-9]/g, '_').substring(0, 30);
        link.setAttribute('download', `Skripsi_${skripsi.nim}_${cleanTitle}.pdf`);
        
        document.body.appendChild(link);
        link.click();
        
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Download error:', error);
        alert('Gagal mengunduh file skripsi. File mungkin tidak tersedia.');
    }
};

// Lifecycle
onMounted(() => {
    fetchSkripsi();
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
