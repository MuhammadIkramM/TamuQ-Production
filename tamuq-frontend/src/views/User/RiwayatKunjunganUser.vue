<template>
  <div class="max-w-3xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-[#00555A] flex items-center gap-2">
          <ClockIcon class="w-7 h-7 text-[#F26430]" />
          Riwayat Kunjungan
        </h2>
        <p class="text-gray-500 text-sm mt-1">Daftar kehadiran Anda di perpustakaan.</p>
      </div>
      
      <div class="hidden sm:flex bg-white px-4 py-2 rounded-xl border border-gray-100 shadow-sm items-center gap-2">
        <span class="text-xs text-gray-400 uppercase font-bold tracking-wider">Total</span>
        <span class="text-xl font-bold text-[#00555A]">{{ riwayat.length }}</span>
      </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden min-h-[300px] relative">
      
      <div v-if="loading" class="absolute inset-0 flex flex-col items-center justify-center bg-white z-10">
          <div class="w-8 h-8 border-4 border-[#F26430]/30 border-t-[#F26430] rounded-full animate-spin mb-3"></div>
          <p class="text-gray-400 animate-pulse text-sm">Sedang memuat data...</p>
      </div>

      <div v-else-if="riwayat.length === 0" class="absolute inset-0 flex flex-col items-center justify-center text-center p-10">
        <div class="w-16 h-16 bg-[#EBF2FA] rounded-full flex items-center justify-center mb-4">
          <ClipboardDocumentListIcon class="w-8 h-8 text-gray-400" />
        </div>
        <h3 class="text-[#00555A] font-bold text-lg">Belum Ada Riwayat</h3>
        <p class="text-gray-500 text-sm mt-1">Anda belum pernah melakukan scan kunjungan.</p>
      </div>

      <div v-else class="divide-y divide-gray-50">
        <div 
          v-for="(item, index) in riwayat" 
          :key="index"
          class="p-5 hover:bg-[#EBF2FA]/50 transition-colors duration-200 group flex items-start gap-4"
        >
          <div class="flex-shrink-0 mt-1">
             <div class="w-10 h-10 rounded-full bg-[#EBF2FA] flex items-center justify-center group-hover:scale-110 transition-transform duration-300 ring-1 ring-[#00555A]/10">
                <CheckCircleIcon class="w-6 h-6 text-[#00555A]" />
             </div>
          </div>

          <div class="flex-1 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
            <div>
              <p class="text-[#00555A] font-bold text-lg leading-tight group-hover:text-[#F26430] transition-colors">
                Kunjungan Perpustakaan
              </p>
              <div class="flex items-center gap-2 text-sm text-gray-500 mt-1">
                 <CalendarDaysIcon class="w-4 h-4 text-[#F26430]" />
                 <span>{{ formatDate(item.waktu_scan) }}</span>
              </div>
            </div>

            <div class="flex items-center gap-3 mt-2 sm:mt-0">
               <div class="bg-gray-50 px-3 py-1 rounded-lg text-gray-600 font-mono text-sm font-medium flex items-center gap-1 border border-gray-100">
                  <ClockIcon class="w-3.5 h-3.5 text-gray-400" />
                  {{ formatTime(item.waktu_scan) }}
               </div>
               
               <span class="hidden sm:inline-block px-2 py-1 bg-[#F26430]/10 text-[#D65020] text-xs font-bold rounded uppercase tracking-wide">
                 Berhasil
               </span>
            </div>
          </div>

        </div>
      </div>
      
      <div v-if="!loading && riwayat.length > 0" class="p-4 bg-[#EBF2FA] border-t border-gray-100 text-center text-xs text-gray-500">
        Menampilkan {{ riwayat.length }} riwayat terakhir
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../../api"; 
import { 
  ClockIcon, 
  CalendarDaysIcon, 
  CheckCircleIcon,
  ClipboardDocumentListIcon 
} from "@heroicons/vue/24/outline";

const riwayat = ref([]);
const loading = ref(true);

const fetchHistory = async () => {
    loading.value = true;
    try {
        const response = await api.get('/kunjungan/history');
        riwayat.value = response.data.data ? response.data.data : response.data;
    } catch (error) {
        console.error("Error fetching history:", error);
    } finally {
        loading.value = false;
    }
};

const formatDate = (dateString) => {
    if (!dateString) return "-";
    const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};

const formatTime = (dateString) => {
    if (!dateString) return "--:--";
    return new Date(dateString).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
};

onMounted(() => {
    fetchHistory();
});
</script>