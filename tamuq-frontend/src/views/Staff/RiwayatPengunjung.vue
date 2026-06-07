<template>
  <div class="space-y-6 animate-fade-in-up">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-[#00555A] flex items-center gap-2 tracking-tight">
          <ClipboardDocumentListIcon class="w-8 h-8 text-[#F26430]" />
          Riwayat Pengunjung
        </h1>
        <p class="text-gray-500 text-sm mt-1 ml-1">Pantau daftar kehadiran mahasiswa di perpustakaan secara real-time.</p>
      </div>

      <button
        @click="exportToExcel"
        :disabled="loading || filteredData.length === 0"
        class="flex items-center gap-2 px-5 py-2.5 bg-[#00555A] hover:bg-[#004448] text-white rounded-xl transition-all text-sm font-bold shadow-lg shadow-teal-900/20 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <ArrowDownTrayIcon class="w-4 h-4" />
        Unduh Excel
      </button>
    </div>

    <div class="bg-[#00555A]/60 backdrop-blur-sm p-5 rounded-2xl shadow-md border border-[#00555A]/20">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
        
        <div class="lg:col-span-4 space-y-1">
          <label class="text-xs font-bold text-white uppercase tracking-wide">Pencarian</label>
          <div class="relative group">
            <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-[#F26430] transition-colors" />
            <input
              type="text"
              v-model="search"
              placeholder="Cari Nama, Nomor Induk/NIM, atau Prodi..."
              class="w-full pl-9 pr-4 py-2.5 bg-white border border-transparent rounded-xl focus:ring-2 focus:ring-[#F26430]/50 focus:border-[#F26430] text-sm transition-all outline-none"
            />
          </div>
        </div>

        <div class="lg:col-span-3 space-y-1">
          <label class="text-xs font-bold text-white uppercase tracking-wide">Rentang Waktu</label>
          <div class="relative group">
            <CalendarDaysIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-[#F26430] transition-colors" />
            <select
              v-model="filter"
              @change="onFilterChange"
              class="w-full pl-9 pr-4 py-2.5 bg-white border border-transparent rounded-xl focus:ring-2 focus:ring-[#F26430]/50 focus:border-[#F26430] text-sm transition-all outline-none appearance-none cursor-pointer"
            >
              <option value="">Semua Riwayat</option>
              <option value="hari-ini">Hari Ini</option>
              <option value="kemarin">Kemarin</option>
              <option value="1-minggu-terakhir">1 Minggu Terakhir</option>
              <option value="pilih-bulan">Pilih Bulan Spesifik</option>
            </select>
          </div>
        </div>

        <div v-if="filter === 'pilih-bulan'" class="lg:col-span-3 space-y-1 animate-fade-in">
           <label class="text-xs font-bold text-white uppercase tracking-wide">Pilih Bulan</label>
           <input
            type="month"
            v-model="selectedMonth"
            class="w-full px-3 py-2.5 bg-white border border-transparent rounded-xl text-sm focus:ring-[#F26430]/50 focus:border-[#F26430] outline-none"
          />
        </div>
        <div v-else class="hidden lg:block lg:col-span-3"></div> 

        <div class="lg:col-span-2 space-y-1">
          <label class="text-xs font-bold text-white uppercase tracking-wide">Tampilkan</label>
          <div class="relative">
             <input
              type="number"
              v-model.number="limit"
              min="1"
              max="500"
              class="w-full px-3 py-2.5 bg-white border border-transparent rounded-xl text-sm focus:ring-[#F26430]/50 focus:border-[#F26430] outline-none text-center font-semibold"
            />
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">Baris</span>
          </div>
        </div>

      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-[#00555A] text-xs uppercase tracking-wider text-white font-bold">
              <th class="px-6 py-4 w-16 text-center">No</th>
              <th class="px-6 py-4">Mahasiswa</th>
              <th class="px-6 py-4">Program Studi</th>
              <th class="px-6 py-4">Waktu Berkunjung</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="loading">
                <td colspan="4" class="px-6 py-12 text-center text-gray-400 animate-pulse">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-8 h-8 border-4 border-[#F26430] border-t-transparent rounded-full animate-spin"></div>
                        <span class="text-sm font-medium">Memuat data riwayat...</span>
                    </div>
                </td>
            </tr>

            <tr v-else-if="filteredData.length === 0">
              <td colspan="4" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center text-gray-400">
                  <DocumentMagnifyingGlassIcon class="w-16 h-16 text-gray-200 mb-4" />
                  <p class="text-base font-bold text-gray-600">Data tidak ditemukan</p>
                  <p class="text-sm mt-1">Coba ubah kata kunci atau rentang waktu filter.</p>
                </div>
              </td>
            </tr>

            <tr
              v-else
              v-for="(item, index) in filteredData.slice(0, limit)"
              :key="index"
              class="hover:bg-gray-50 transition-colors group"
            >
              <td class="px-6 py-4 text-center text-sm text-gray-500 font-medium">
                {{ index + 1 }}
              </td>

              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-gray-100 shrink-0 overflow-hidden border border-gray-200 relative">
                    <img 
                        v-if="item.foto"
                        :src="item.foto" 
                        alt="Foto Profil"
                        class="w-full h-full object-cover"
                        @error="handleImageError"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#00555A] to-[#004448] text-white font-bold text-xs">
                        {{ getInitials(item.nama) }}
                    </div>
                  </div>

                  <div>
                    <p class="font-bold text-gray-800 group-hover:text-[#00555A] transition-colors text-sm">{{ item.nama }}</p>
                    <div class="flex items-center gap-1.5 text-xs text-gray-500 font-mono mt-0.5">
                      <IdentificationIcon class="w-3 h-3" />
                      {{ item.nim }}
                    </div>
                  </div>
                </div>
              </td>
              
              <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-[#00555A]/5 text-[#00555A] border border-[#00555A]/10">
                  {{ item.prodi }}
                </span>
              </td>

              <td class="px-6 py-4">
                <div class="flex flex-col">
                  <span class="text-sm font-medium text-gray-700 flex items-center gap-2">
                      <CalendarDaysIcon class="w-4 h-4 text-gray-400" />
                      {{ formatDateNice(item.rawDate) }}
                  </span>
                  <span class="text-xs text-gray-500 flex items-center gap-2 mt-1 ml-6">
                      <ClockIcon class="w-3 h-3 text-[#F26430]" />
                      <span class="font-mono">{{ item.waktu }} WITA</span>
                  </span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500 gap-2">
        <span class="font-medium">Menampilkan {{ Math.min(limit, filteredData.length) }} dari {{ filteredData.length }} pengunjung</span>
        <span class="flex items-center gap-1">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Real-time Update
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import * as XLSX from "xlsx";
import { saveAs } from "file-saver";
import api from "@/api"; 
import { 
  ClipboardDocumentListIcon, ArrowDownTrayIcon, MagnifyingGlassIcon, 
  CalendarDaysIcon, IdentificationIcon, ClockIcon, CheckCircleIcon,
  DocumentMagnifyingGlassIcon
} from "@heroicons/vue/24/outline"

const STORAGE_URL = import.meta.env.VITE_STORAGE_URL || "https://tamuq.my.id/storage";

const search = ref("");
const filter = ref("");
const limit = ref(10);
const selectedMonth = ref("");
const data = ref([]);
const loading = ref(true);

const getFullImageUrl = (path) => {
  if (!path) return null;
  if (path.startsWith('http')) return path;
  const cleanPath = path.startsWith('/') ? path.substring(1) : path;
  return `${STORAGE_URL}/${cleanPath}`;
};

const handleImageError = (e) => {
    e.target.style.display = 'none'; 
    e.target.nextElementSibling.style.display = 'flex'; 
};

const fetchHistory = async () => {
    try {
        loading.value = true;
        
        const response = await api.get('/kunjungan/history');
        
        const rawData = response.data.data || response.data; 

        data.value = rawData.map(item => {
            const userData = item.user || {}; 
            const dateObj = new Date(item.waktu_scan);
            
            return {
                id: item.id,
                nama: userData.nama_lengkap || 'User Tidak Dikenal', 
                nim: userData.nomor_induk || '-', 
                prodi: userData.prodi || '-',
                foto: getFullImageUrl(userData.foto_profil),
                rawDate: dateObj, 
                tanggalStr: dateObj.toISOString().split('T')[0], 
                waktu: dateObj.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}),
            };
        });

    } catch (error) {
        console.error("Gagal load history:", error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchHistory();
});

const getInitials = (name) => {
  if (!name || name === 'User Tidak Dikenal') return '?';
  return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
}

const formatDateNice = (dateObj) => {
  if(!dateObj) return '-';
  const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateObj).toLocaleDateString('id-ID', options);
}

const exportToExcel = () => {
  const dataToExport = filteredData.value.map((item, index) => ({
    'No': index + 1,
    'Nomor Induk / NIM': item.nim, // DIKEMBALIKAN
    'Nama Lengkap': item.nama,
    'Program Studi': item.prodi,
    'Tanggal': formatDateNice(item.rawDate),
    'Jam Masuk': item.waktu + ' WITA'
  }));

  const worksheet = XLSX.utils.json_to_sheet(dataToExport);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, "Riwayat Pengunjung");
  
  const wscols = [
      {wch:5},  
      {wch:20}, 
      {wch:30}, 
      {wch:25}, 
      {wch:20}, 
      {wch:15} 
  ];
  worksheet['!cols'] = wscols;

  const wbout = XLSX.write(workbook, { bookType: "xlsx", type: "array" });
  const fileName = `Laporan_Pengunjung_${new Date().toISOString().slice(0,10)}.xlsx`;
  
  saveAs(new Blob([wbout], { type: "application/octet-stream" }), fileName);
};

const onFilterChange = () => {
  if (filter.value !== "pilih-bulan") {
    selectedMonth.value = "";
  }
};

const filteredData = computed(() => {
  let result = data.value;

  if (search.value) {
      const term = search.value.toLowerCase();
      result = result.filter((item) => 
        (item.nama || "").toLowerCase().includes(term) ||
        (item.prodi || "").toLowerCase().includes(term) ||
        (item.nim || "").includes(term)
      );
  }

  if (filter.value) {
      const today = new Date();
      today.setHours(0,0,0,0); 

      result = result.filter((i) => {
          const itemDate = new Date(i.rawDate);
          itemDate.setHours(0,0,0,0);

          if (filter.value === "hari-ini") {
             return itemDate.getTime() === today.getTime();
          } 
          else if (filter.value === "kemarin") {
             const yesterday = new Date(today);
             yesterday.setDate(today.getDate() - 1);
             return itemDate.getTime() === yesterday.getTime();
          } 
          else if (filter.value === "1-minggu-terakhir") {
             const oneWeekAgo = new Date(today);
             oneWeekAgo.setDate(today.getDate() - 7);
             return itemDate >= oneWeekAgo && itemDate <= today;
          } 
          else if (filter.value === "pilih-bulan" && selectedMonth.value) {
             const itemMonthStr = i.rawDate.toISOString().slice(0, 7);
             return itemMonthStr === selectedMonth.value;
          }
          return true;
      });
  }

  return result.sort((a, b) => b.rawDate - a.rawDate);
});
</script>

<style scoped>
.animate-fade-in-up {
  animation: fadeInUp 0.5s ease-out;
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.3s ease-out forwards;
}
</style>