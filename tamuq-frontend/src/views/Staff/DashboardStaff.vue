<template>
  <div class="space-y-8">
    
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-[#00555A] tracking-tight">Dashboard Overview</h1>
        <p class="text-gray-500 text-sm mt-1">Ringkasan aktivitas dan kontrol operasional perpustakaan.</p>
      </div>
      
      <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-full shadow-sm border border-gray-200">
        <span class="relative flex h-3 w-3">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="isSystemOnline ? 'bg-emerald-400' : 'bg-red-400'"></span>
          <span class="relative inline-flex rounded-full h-3 w-3" :class="isSystemOnline ? 'bg-emerald-500' : 'bg-red-500'"></span>
        </span>
        <span class="text-xs font-bold text-gray-600 uppercase tracking-wide">
          {{ isSystemOnline ? 'Sistem Online' : 'Offline' }}
        </span>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group flex flex-col justify-center p-8 h-48">
        <div class="absolute left-0 top-0 bottom-0 w-2 bg-[#00555A]"></div>
        <div class="relative z-10 flex items-start justify-between">
          <div>
            <div class="flex items-center gap-2 text-[#00555A] mb-1">
              <ClockIcon class="w-5 h-5" />
              <span class="font-bold text-xs tracking-widest uppercase">Waktu Server</span>
            </div>
            <div class="text-5xl font-bold text-gray-800 tracking-tight mt-2 font-mono">{{ time }}</div>
            <div class="text-gray-500 font-medium mt-1">{{ date }}</div>
          </div>
          <div class="opacity-5 transform group-hover:scale-110 transition-transform duration-500 absolute right-4 top-1/2 -translate-y-1/2">
            <ClockIcon class="w-32 h-32 text-[#00555A]" />
          </div>
        </div>
      </div>

      <div 
        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between gap-6 relative overflow-hidden transition-all duration-500 h-48"
        :class="isOpen ? 'border-l-4 border-l-[#00555A]' : 'border-l-4 border-l-red-500'"
      >
        <div class="flex-1 relative z-10">
          <div class="flex items-center gap-2 mb-2">
            <BuildingLibraryIcon class="w-5 h-5 text-[#F26430]" />
            <span class="font-bold text-xs text-gray-400 tracking-widest uppercase">Status Operasional</span>
          </div>
          
          <h2 class="text-2xl font-bold text-gray-800 mb-4">
             Perpustakaan sedang
             <span :class="isOpen ? 'text-[#00555A]' : 'text-red-500'">{{ isOpen ? 'BUKA' : 'TUTUP' }}</span>
          </h2>
          
          <button
            class="w-full sm:w-auto px-6 py-2.5 rounded-xl font-bold text-sm transition-all duration-300 shadow-sm flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed transform active:scale-95"
            :class="isOpen 
              ? 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-100' 
              : 'bg-[#00555A] text-white hover:bg-[#004448] shadow-lg shadow-teal-900/20'"
            @click="toggleStatus"
            :disabled="isLoadingStatus"
          >
            <span v-if="isLoadingStatus" class="animate-spin h-4 w-4 border-2 border-current border-t-transparent rounded-full"></span>
            <span v-else class="flex items-center gap-2">
               <LockClosedIcon v-if="isOpen" class="w-4 h-4" />
               <LockOpenIcon v-else class="w-4 h-4" />
               {{ isOpen ? 'Tutup Akses Masuk' : 'Buka Perpustakaan' }}
            </span>
          </button>
        </div>

        <div class="hidden sm:flex items-center justify-center w-28 h-28 flex-shrink-0">
          <div 
            class="relative w-24 h-24 rounded-full flex items-center justify-center transition-all duration-500 ease-in-out shadow-inner"
            :class="isOpen ? 'bg-emerald-50' : 'bg-red-50'"
          >
            <div class="absolute inset-0 rounded-full animate-ping opacity-20"
                 :class="isOpen ? 'bg-[#00555A]' : 'bg-red-500'"></div>
                 
            <transition name="pop" mode="out-in">
              <div v-if="isOpen" key="open" class="flex flex-col items-center justify-center">
                 <CheckCircleIcon class="w-14 h-14 text-[#00555A]" />
              </div>
              <div v-else key="closed" class="flex flex-col items-center justify-center">
                 <XCircleIcon class="w-14 h-14 text-red-500" />
              </div>
            </transition>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 group hover:border-[#00555A]/50 transition-colors">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Total Koleksi</p>
            <h2 class="text-3xl font-bold text-[#00555A] mt-1">{{ stats.totalBuku }}</h2>
          </div>
          <div class="p-2 bg-[#EBF2FA] rounded-lg text-[#00555A]">
             <BookOpenIcon class="w-6 h-6" />
          </div>
        </div>
        <div class="text-xs text-gray-400">Unit buku terdaftar</div>
      </div>

      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 group hover:border-[#F26430]/50 transition-colors">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Pengunjung Hari Ini</p>
            <h2 class="text-3xl font-bold text-[#F26430] mt-1">{{ stats.pengunjungHariIni }}</h2>
          </div>
          <div class="p-2 bg-orange-50 rounded-lg text-[#F26430]">
            <UserGroupIcon class="w-6 h-6" />
          </div>
        </div>
        <div class="text-xs text-gray-400">Check-in hari ini</div>
      </div>

      <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32 group hover:border-gray-300 transition-colors">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Peminjaman Aktif</p>
            <h2 class="text-3xl font-bold text-gray-700 mt-1">{{ stats.peminjamanAktif }}</h2>
           </div>
          <div class="p-2 bg-gray-100 rounded-lg text-gray-600">
             <DocumentTextIcon class="w-6 h-6" />
          </div>
        </div>
        <div class="text-xs text-gray-400">Belum dikembalikan</div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <div class="bg-white shadow-sm rounded-2xl p-6 border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4 border-b border-gray-100 pb-4">
           <div>
             <h2 class="font-bold text-[#00555A] text-lg">Tren Kunjungan</h2>
             <div class="flex items-center gap-4 text-xs mt-1">
                <span class="flex items-center gap-1.5 text-gray-500">
                    <span class="w-3 h-0.5 bg-[#00555A]"></span> Jam Kerja (08-16)
                </span>
                <span v-if="hasOvertime" class="flex items-center gap-1.5 text-red-500 font-medium animate-pulse">
                    <span class="w-3 h-0.5 bg-red-500"></span> Di Luar Jam Kerja!
                </span>
             </div>
           </div>

           <div class="flex items-center gap-2">
            <input 
              v-if="selectedFilter === 'custom'"
              type="date"
              v-model="customDate"
              class="text-xs border-gray-200 rounded-lg focus:ring-[#00555A] focus:border-[#00555A] py-1.5 px-3 shadow-sm outline-none text-gray-600 bg-gray-50"
            />

            <select 
              v-model="selectedFilter"
              class="text-xs font-bold text-gray-600 bg-gray-50 border-gray-200 rounded-lg focus:ring-[#00555A] focus:border-[#00555A] py-2 px-3 cursor-pointer outline-none shadow-sm hover:bg-gray-100 transition-colors"
            >
              <option value="today">Hari Ini</option>
              <option value="week">Minggu Ini</option>
              <option value="month">Bulan Ini</option>
              <option value="custom">Pilih Tanggal...</option>
            </select>
          </div>
        </div>

        <div class="h-72">
           <LineChart v-if="loaded" :chart-data="chartData" :chart-options="chartOptions" />
           <div v-else class="h-full flex items-center justify-center text-gray-400 animate-pulse bg-gray-50 rounded-xl">
             <span class="text-sm font-medium">Memuat Data Grafik...</span>
           </div>
        </div>
      </div>

      <div class="bg-white shadow-sm rounded-2xl p-6 border border-gray-100">
        <h2 class="font-bold text-[#00555A] text-lg mb-1">Demografi Prodi</h2>
        <p class="text-xs text-gray-400 mb-6 border-b border-gray-100 pb-4">Akun Terdaftar: PTIK & TEKOM</p>
        
        <div class="h-72 flex items-center justify-center relative">
           <BarGraph v-if="loaded" :chart-data="prodiData" :chart-options="barOptions" />
           <div v-else class="h-full w-full flex items-center justify-center text-gray-400 animate-pulse bg-gray-50 rounded-xl">
             <span class="text-sm font-medium">Memuat Data Grafik...</span>
           </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { defineAsyncComponent, ref, onMounted, watch } from "vue";
import api from "@/api";
import { Chart as ChartJS, Filler } from 'chart.js';
ChartJS.register(Filler);
import { 
  ClockIcon, BuildingLibraryIcon,
  LockOpenIcon, LockClosedIcon, 
  BookOpenIcon, UserGroupIcon, DocumentTextIcon,
  CheckCircleIcon, XCircleIcon
} from "@heroicons/vue/24/outline";

const LineChart = defineAsyncComponent(() => import("@/components/charts/LineChart.vue"));
const BarGraph = defineAsyncComponent(() => import("@/components/charts/BarGraph.vue"));

const getLocalDateString = () => {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const isSystemOnline = ref(false);
const loaded = ref(false);
const isLoadingStatus = ref(false);
const isOpen = ref(false);

const selectedFilter = ref("today"); 
const customDate = ref(getLocalDateString());
const allHistoryData = ref([]); 
const hasOvertime = ref(false);

const stats = ref({ totalBuku: 0, pengunjungHariIni: 0, peminjamanAktif: 0 });
const time = ref("");
const date = ref("");

const chartData = ref({ labels: [], datasets: [] });
const prodiData = ref({ labels: [], datasets: [] });

const barOptions = { 
  responsive: true, 
  maintainAspectRatio: false, 
  plugins: { 
    legend: { display: false }, 
    tooltip: {
      callbacks: { label: (c) => ` ${c.parsed.y} Pengunjung` }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: { borderDash: [4, 4], drawBorder: false, color: '#f1f5f9' },
      ticks: { 
        precision: 0, 
        color: '#94a3b8' 
      },
      title: {
        display: true,
        text: 'Jumlah Pengunjung',
        color: '#94a3b8',
        font: { size: 10, weight: 'bold' }
      }
    },
    x: {
      grid: { display: false },
      ticks: { color: '#475569', font: { weight: 'bold' } }
    }
  }
};

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: { 
    legend: { display: false },
    tooltip: {
      callbacks: { label: (c) => ` ${c.parsed.y} Pengunjung` }
    }
  },
  scales: { 
    y: { 
        beginAtZero: true,
        grid: { borderDash: [4, 4], drawBorder: false, color: '#f1f5f9' },
        ticks: { precision: 0, color: '#94a3b8' } 
    },
    x: { 
      grid: { display: false },
      ticks: { color: '#94a3b8' }
    }
  },
  elements: { point: { radius: 4, hoverRadius: 6 } }
});

const updateChartFromHistory = () => {
    const period = selectedFilter.value;
    let labels = [];
    let dataCounts = [];
    let pointColors = [];
    
    const COLOR_TEAL = "#00555A";
    const COLOR_RED = "#EF4444";
    hasOvertime.value = false;

    const isSameDate = (d1, d2) => 
        d1.getFullYear() === d2.getFullYear() && 
        d1.getMonth() === d2.getMonth() && 
        d1.getDate() === d2.getDate();

    if (period === 'today' || period === 'custom') {
        const targetDateStr = period === 'today' ? getLocalDateString() : customDate.value;
        const targetDate = new Date(targetDateStr + "T00:00:00");
        
        let minHour = 8;
        let maxHour = 16; 

        const relevantData = allHistoryData.value.filter(item => {
             const d = new Date(item.waktu_scan);
             return isSameDate(d, targetDate);
        });

        relevantData.forEach(item => {
            const h = new Date(item.waktu_scan).getHours();
            if (h < minHour) { minHour = h; hasOvertime.value = true; }
            if (h >= maxHour) {
                maxHour = h + 1;
                if (h >= 17) hasOvertime.value = true;
            }
        });

        const bucketMap = {};
        for (let i = minHour; i <= maxHour; i++) {
            const label = i < 10 ? `0${i}.00` : `${i}.00`;
            labels.push(label);
            bucketMap[i] = 0;
        }

        relevantData.forEach(item => {
            const h = new Date(item.waktu_scan).getHours();
            if (bucketMap.hasOwnProperty(h)) bucketMap[h]++;
        });

        dataCounts = labels.map(l => bucketMap[parseInt(l)]);

        pointColors = labels.map(l => {
            const h = parseInt(l);
            if (h < 8 || h >= 17) return COLOR_RED;
            return COLOR_TEAL;
        });

    } else {
        const isWeekly = period === 'week';
        const now = new Date();
        now.setHours(0,0,0,0);
        
        if(isWeekly) {
             labels = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"];
             dataCounts = Array(7).fill(0);
             const dayOfWeek = now.getDay() || 7; 
             const startOfWeek = new Date(now);
             startOfWeek.setDate(now.getDate() - dayOfWeek + 1);
             const endOfWeek = new Date(startOfWeek);
             endOfWeek.setDate(startOfWeek.getDate() + 7);

             allHistoryData.value.forEach(item => {
                const visitTime = new Date(item.waktu_scan);
                if (visitTime >= startOfWeek && visitTime < endOfWeek) {
                    let dayIndex = visitTime.getDay() - 1; 
                    if (dayIndex === -1) dayIndex = 6;
                    dataCounts[dayIndex]++;
                }
             });
        } else {
             const daysInMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
             labels = Array.from({length: daysInMonth}, (_, i)=>String(i+1));
             dataCounts = Array(daysInMonth).fill(0);
             allHistoryData.value.forEach(item => {
                const visitTime = new Date(item.waktu_scan);
                if (visitTime.getMonth() === now.getMonth() && visitTime.getFullYear() === now.getFullYear()) {
                    dataCounts[visitTime.getDate() - 1]++;
                }
             });
        }
        pointColors = COLOR_TEAL;
    }

    chartData.value = {
        labels: labels,
        datasets: [{
            label: 'Pengunjung',
            data: dataCounts,
            borderColor: '#00555A',
            fill: true,
            tension: 0.3,
            segment: {
                borderColor: ctx => {
                    if (period !== 'today' && period !== 'custom') return COLOR_TEAL;
                    const idx = ctx.p0DataIndex;
                    const h = parseInt(labels[idx]);
                    if (h < 8) return COLOR_RED;   
                    if (h >= 17) return COLOR_RED;
                    return COLOR_TEAL;
                }
            },
            pointBackgroundColor: pointColors,
            pointBorderColor: pointColors,
            pointBorderWidth: 2,
            backgroundColor: (ctx) => {
                const canvas = ctx.chart.ctx;
                const gradient = canvas.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(0, 85, 90, 0.1)');
                gradient.addColorStop(1, 'rgba(0, 85, 90, 0)');
                return gradient;
            },
        }]
    };
};

const fetchAllData = async () => {
    try {
        const [dashRes, histRes] = await Promise.all([
            api.get('/staff/dashboard'),
            api.get('/kunjungan/history') 
        ]);

        const data = dashRes.data;
        stats.value = {
            totalBuku: data.total_buku,
            pengunjungHariIni: data.pengunjung_hari_ini,
            peminjamanAktif: data.peminjaman_aktif
        };
        isOpen.value = data.is_open;

        prodiData.value = {
            labels: ["PTIK", "TEKOM"],
            datasets: [{
                label: 'Jumlah Pengunjung',
                data: data.demografi, 
                backgroundColor: ["#00555A", "#F26430"], 
                borderRadius: 8, 
                barPercentage: 0.6 
            }]
        };

        allHistoryData.value = histRes.data;
        updateChartFromHistory();

        loaded.value = true;
        isSystemOnline.value = true;
    } catch (e) {
        console.error("Dashboard Error:", e);
        isSystemOnline.value = false;
    }
};

const toggleStatus = async () => {
    isLoadingStatus.value = true;
    try {
        const newStatus = !isOpen.value;
        await api.post('/staff/library-status', { status: newStatus });
        isOpen.value = newStatus; 
    } catch (error) {
        alert("Gagal koneksi ke server.");
    } finally {
        isLoadingStatus.value = false;
    }
};

watch([selectedFilter, customDate], updateChartFromHistory);

const updateTime = () => {
  const now = new Date();
  time.value = now.toLocaleTimeString("id-ID", { hour12: false });
  date.value = now.toLocaleDateString("id-ID", { weekday: "long", day: "numeric", month: "long", year: "numeric" });
};

onMounted(() => {
  updateTime();
  setInterval(updateTime, 1000);
  fetchAllData();
});
</script>

<style scoped>
.pop-enter-active, .pop-leave-active { transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.pop-enter-from, .pop-leave-to { opacity: 0; transform: scale(0.5); }
</style>