<template>
  <div class="max-w-5xl mx-auto space-y-10">
    
    <div>
      <h1 class="text-2xl font-bold text-[#00555A] flex items-center gap-2">
        <BookOpenIcon class="w-8 h-8 text-[#F26430]" />
        Layanan Buku
      </h1>
      <p class="text-gray-500 text-sm mt-1">Jelajahi katalog buku, pinjam via QR, dan pantau status peminjaman Anda.</p>
    </div>

    <div class="flex items-center gap-2 border-b border-gray-200">
      <button 
        @click="activeTab = 'katalog'"
        class="px-4 py-2.5 text-sm font-bold border-b-2 transition-colors flex items-center gap-2"
        :class="activeTab === 'katalog' ? 'border-[#00555A] text-[#00555A]' : 'border-transparent text-gray-500 hover:text-gray-700'"
      >
        <Square3Stack3DIcon class="w-5 h-5" />
        Katalog Buku
      </button>
      <button 
        @click="activeTab = 'peminjaman'"
        class="px-4 py-2.5 text-sm font-bold border-b-2 transition-colors flex items-center gap-2"
        :class="activeTab === 'peminjaman' ? 'border-[#00555A] text-[#00555A]' : 'border-transparent text-gray-500 hover:text-gray-700'"
      >
        <BookOpenIcon class="w-5 h-5" />
        Peminjaman Saya
      </button>
    </div>

    <div v-show="activeTab === 'katalog'" class="space-y-6">
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
          <button
            @click="openQrModal"
            class="flex items-center justify-center gap-2 px-4 py-2.5 bg-[#F26430] hover:bg-[#D65020] text-white rounded-xl transition-all text-sm font-bold shadow-md shadow-[#F26430]/20 active:scale-95 shrink-0"
          >
            <QrCodeIcon class="w-5 h-5" />
            Pinjam Buku via QR
          </button>
          <div class="relative flex-1 min-w-[200px]">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
            </div>
            <input 
              type="text" 
              v-model="searchQuery" 
              class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none transition-all"
              placeholder="Cari kode, judul, penulis, atau kategori..."
            />
          </div>
          <div class="relative min-w-[150px]">
            <select
              v-model="filterKategori"
              class="w-full appearance-none pl-3 pr-8 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none transition-all cursor-pointer"
            >
              <option value="">Semua Kategori</option>
              <option v-for="kat in kategoriOptions" :key="kat" :value="kat">{{ kat }}</option>
            </select>
            <FunnelIcon class="w-4 h-4 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
          </div>
          <div class="relative min-w-[130px]">
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
      <div class="space-y-4">
      <div class="flex items-center gap-2">
        <Square3Stack3DIcon class="w-5 h-5 text-[#F26430]" />
        <h2 class="text-lg font-bold text-[#00555A]">Katalog Buku</h2>
        <span class="text-xs font-medium bg-[#00555A]/10 text-[#00555A] px-2 py-0.5 rounded-md">
          {{ filteredBuku.length }} buku
        </span>
      </div>
      <div v-if="isLoadingKatalog" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div v-for="n in 8" :key="'skel-'+n" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden animate-pulse">
          <div class="h-40 bg-gray-100"></div>
          <div class="p-3 space-y-2">
            <div class="h-4 bg-gray-100 rounded w-3/4"></div>
            <div class="h-3 bg-gray-100 rounded w-1/2"></div>
            <div class="h-3 bg-gray-100 rounded w-1/3"></div>
          </div>
        </div>
      </div>
      <div v-else-if="filteredBuku.length === 0" class="bg-white border border-dashed border-gray-300 rounded-2xl p-8 text-center">
        <p class="text-gray-400 font-medium">
          {{ (searchQuery || filterKategori || filterTahun) ? 'Tidak ada buku yang cocok dengan filter Anda.' : 'Belum ada buku dalam katalog.' }}
        </p>
      </div>
      <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div 
          v-for="buku in filteredBuku" 
          :key="buku.id_buku"
          class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-[#00555A]/20 transition-all overflow-hidden group"
        >
          <div class="relative h-44 bg-gray-50 overflow-hidden">
            <img 
              v-if="buku.foto_buku" 
              :src="'https://tamuq.my.id/storage/' + buku.foto_buku" 
              :alt="buku.judul"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              loading="lazy"
            />
          </div>
          <div class="p-3 space-y-1.5">
            <h3 class="font-bold text-gray-800 text-sm leading-tight line-clamp-2 group-hover:text-[#00555A] transition-colors">
              {{ buku.judul }}
            </h3>
            <p v-if="buku.penulis" class="text-xs text-gray-500 truncate">
              {{ buku.penulis }}
            </p>
            <div class="flex items-center justify-between gap-1 pt-1">
              <span v-if="buku.kategori" class="text-[10px] font-semibold text-[#00555A] bg-[#00555A]/10 px-1.5 py-0.5 rounded truncate max-w-[60%]">
                {{ buku.kategori }}
              </span>
              <span v-if="buku.tahun_buku" class="text-[10px] text-gray-400 font-medium shrink-0">
                {{ buku.tahun_buku }}
              </span>
            </div>
            <div class="flex items-center gap-1 pt-0.5">
              <span 
                class="text-[10px] font-bold px-1.5 py-0.5 rounded"
                :class="buku.jumlah_tersedia > 0 
                  ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' 
                  : 'text-red-600 bg-red-50 border border-red-200'"
              >
                {{ buku.jumlah_tersedia > 0 ? 'Tersedia: ' + buku.jumlah_tersedia : 'Habis' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    </div>

    <div v-show="activeTab === 'peminjaman'" class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
      <div class="space-y-4">
        <div class="flex items-center gap-2">
          <BookmarkSquareIcon class="w-5 h-5 text-[#F26430]" />
          <h2 class="text-lg font-bold text-[#00555A]">Sedang Dipinjam</h2>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
          
          <div v-if="isLoading" class="p-5 space-y-4">
              <div class="h-12 bg-gray-100 rounded-lg animate-pulse w-full"></div>
              <div class="h-12 bg-gray-100 rounded-lg animate-pulse w-full"></div>
          </div>

          <div v-else-if="filteredAktif.length === 0" class="p-10 text-center">
            <p class="text-gray-400 font-medium">
              {{ searchQuery ? 'Tidak ada buku aktif yang cocok dengan pencarian.' : 'Tidak ada buku yang sedang dipinjam.' }}
            </p>
          </div>

          <div v-else class="divide-y divide-gray-50">
            <div 
              v-for="item in filteredAktif" 
              :key="item.id"
              class="p-5 hover:bg-[#FFF3EC]/50 transition-colors flex flex-col sm:flex-row sm:items-start gap-4 relative group"
            >
              <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#F26430] opacity-0 group-hover:opacity-100 transition-opacity"></div>
              
              <div class="w-10 h-10 rounded-full bg-[#F26430]/10 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <BookOpenIcon class="w-5 h-5 text-[#F26430]" />
              </div>
              
              <div class="flex-1 min-w-0">
                <h4 class="font-bold text-gray-800 break-words group-hover:text-[#00555A] transition-colors leading-tight">{{ item.judulBuku }}</h4>
                <div class="flex flex-col sm:flex-row sm:items-center gap-x-4 gap-y-1 mt-1.5 text-xs text-gray-500">
                  <span class="flex items-center gap-1">
                    <CalendarDaysIcon class="w-4 h-4 text-[#00555A]" />
                    <span>Pinjam: <span class="font-medium text-gray-700">{{ item.tanggalPinjam }}</span></span>
                  </span>
                  <span class="flex items-center gap-1">
                    <ClockIcon class="w-4 h-4 text-[#F26430]" />
                    <span>Batas Kembali: <span class="font-medium text-gray-700">{{ item.batasPengembalian }}</span></span>
                  </span>
                </div>
                
                <div class="mt-2.5">
                  <div v-if="item.sisaHari < 0" class="flex items-center gap-1.5 text-[11px] text-white bg-red-500 w-fit px-2 py-1 rounded font-bold shadow-sm">
                    <ExclamationCircleIcon class="w-3.5 h-3.5" />
                    <span>Terlambat {{ Math.abs(item.sisaHari) }} hari! Segera kembalikan!</span>
                  </div>
                  <div v-else-if="item.sisaHari === 0" class="flex items-center gap-1.5 text-[11px] text-white bg-orange-500 w-fit px-2 py-1 rounded font-bold shadow-sm">
                    <ExclamationTriangleIcon class="w-3.5 h-3.5" />
                    <span>HARI INI batas terakhir pengembalian!</span>
                  </div>
                  <div v-else-if="item.sisaHari === 1" class="flex items-center gap-1.5 text-[11px] text-orange-600 bg-orange-100 border border-orange-200 w-fit px-2 py-1 rounded font-bold">
                    <ClockIcon class="w-3.5 h-3.5" />
                    <span>Besok adalah batas pengembalian.</span>
                  </div>
                  <div v-else class="flex items-center gap-1.5 text-[11px] text-[#00555A] bg-[#00555A]/10 border border-[#00555A]/20 w-fit px-2 py-1 rounded font-bold">
                    <CheckCircleIcon class="w-3.5 h-3.5" />
                    <span>Sisa waktu peminjaman: {{ item.sisaHari }} hari</span>
                  </div>
                </div>
              </div>
              
              <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto mt-2 sm:mt-0">
                <span class="bg-[#F26430]/10 text-[#D65020] text-xs font-bold px-3 py-1 rounded-full border border-[#F26430]/20 uppercase tracking-wider">
                  {{ item.status }}
                </span>
              </div>
            </div>
          </div>
          
          <div v-if="!isLoading && filteredAktif.length > 0" class="bg-[#FFF3EC] p-3 text-center border-t border-gray-100">
            <span class="text-xs text-[#D65020] font-medium">Menampilkan {{ filteredAktif.length }} buku sedang dipinjam</span>
          </div>

        </div>
      </div>
      <div class="space-y-4">
        <div class="flex items-center gap-2">
          <ArchiveBoxIcon class="w-5 h-5 text-[#00555A]" />
          <h2 class="text-lg font-bold text-[#00555A]">Riwayat Pengembalian</h2>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
          
          <div v-if="isLoading" class="p-5 space-y-4">
              <div class="h-12 bg-gray-100 rounded-lg animate-pulse w-full"></div>
              <div class="h-12 bg-gray-100 rounded-lg animate-pulse w-full"></div>
          </div>

          <div v-else-if="filteredRiwayat.length === 0" class="p-10 text-center">
            <p class="text-gray-400 font-medium">
              {{ searchQuery ? 'Tidak ada riwayat yang cocok dengan pencarian.' : 'Belum ada riwayat pengembalian buku.' }}
            </p>
          </div>

          <div v-else class="divide-y divide-gray-50">
            <div 
              v-for="item in filteredRiwayat" 
              :key="item.id"
              class="p-5 hover:bg-[#EBF2FA]/50 transition-colors flex flex-col sm:flex-row sm:items-center gap-4 group"
            >
              <div class="w-10 h-10 rounded-full bg-[#EBF2FA] flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <CheckCircleIcon class="w-5 h-5 text-[#00555A]" />
              </div>
              <div class="flex-1">
                <h4 class="font-bold text-gray-800 group-hover:text-[#00555A] transition-colors">{{ item.judulBuku }}</h4>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-xs text-gray-500">
                  <span class="flex items-center gap-1">
                    <ArrowLongRightIcon class="w-3 h-3 text-[#F26430]" />
                    Pinjam: {{ item.tanggalPinjam }}
                  </span>
                  <span class="flex items-center gap-1 text-[#00555A] font-medium">
                    <ArrowLongLeftIcon class="w-3 h-3" />
                    Kembali: {{ item.tanggalKembali }}
                  </span>
                </div>
              </div>
              <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto mt-2 sm:mt-0">
                <span class="bg-gray-100 text-gray-500 text-xs font-bold px-3 py-1 rounded-full border border-gray-200 uppercase tracking-wider">
                  {{ item.status }}
                </span>
              </div>
            </div>
          </div>

          <div v-if="!isLoading && filteredRiwayat.length > 0" class="bg-[#EBF2FA] p-3 text-center border-t border-gray-100">
            <span class="text-xs text-gray-500">Menampilkan {{ filteredRiwayat.length }} data terakhir</span>
          </div>

        </div>
      </div>

    </div>
    <transition name="fade">
      <div v-if="showQrModal" class="fixed inset-0 flex items-center justify-center z-50 bg-[#00282a]/50 backdrop-blur-sm p-4" @click.self="closeQrModal">
        <div class="bg-white shadow-2xl rounded-3xl w-full max-w-md overflow-hidden transform transition-all scale-100 relative">
          <button @click="closeQrModal" class="absolute top-4 right-4 z-30 text-gray-400 hover:text-gray-600 bg-white/80 backdrop-blur-sm rounded-full p-1 transition-colors">
            <XMarkIcon class="w-6 h-6" />
          </button>
          <div class="h-2 bg-gradient-to-r from-[#F26430] to-[#00555A] w-full"></div>

          <div class="p-8 flex flex-col items-center text-center space-y-8">
            <div class="text-center">
              <h2 class="text-xl font-bold text-[#00555A] flex items-center justify-center gap-2">
                <QrCodeIcon class="w-5 h-5 text-[#F26430]" />
                QR Peminjaman Buku
              </h2>
              <p class="text-gray-500 text-xs mt-1">Tunjukkan QR ini ke staff saat meminjam buku.</p>
            </div>
            <div class="space-y-1">
              <h1 class="text-5xl font-black tracking-tight text-[#00555A] font-mono">
                {{ qrTime }}
              </h1>
              <p class="text-gray-500 font-medium uppercase tracking-widest text-xs">
                {{ qrFormattedDate }}
              </p>
            </div>
            <div class="relative">
              <div class="absolute -inset-4 border-2 border-dashed border-gray-200 rounded-xl"></div>
              
              <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 relative z-10">
                <div v-if="qrValue" class="relative">
                  <qrcode-vue 
                    :value="qrValue" 
                    :size="qrSize" 
                    level="H" 
                    render-as="svg"
                    foreground="#1f2937" 
                    :class="{'blur-sm': qrExpired}"
                    class="transition-all duration-300"
                  />
                  <div v-if="qrExpired" class="absolute inset-0 flex items-center justify-center z-20">
                    <button 
                      @click="regenerateQr"
                      class="px-4 py-2 bg-[#F26430] hover:bg-[#D65020] text-white text-sm font-bold rounded-xl shadow-lg transition-all active:scale-95 flex items-center gap-2"
                    >
                      <ArrowPathIcon class="w-4 h-4" />
                      Generate ulang QR?
                    </button>
                  </div>
                </div>
                <div v-else class="flex items-center justify-center" :style="{ width: qrSize + 'px', height: qrSize + 'px' }">
                    <ArrowPathIcon class="w-8 h-8 text-gray-300 animate-spin" />
                </div>
              </div>
              <div 
                v-if="!qrExpired"
                class="absolute inset-0 flex items-center justify-center pointer-events-none z-20"
              >
                 <div class="bg-white p-1 rounded-full shadow-sm">
                   <div class="w-2 h-2 bg-[#F26430] rounded-full animate-pulse"></div>
                 </div>
              </div>
            </div>
            <div 
              class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold transition-colors duration-300"
              :class="qrExpired
                ? 'bg-red-50 text-red-600'
                : (qrCountdown <= 5 
                  ? 'bg-red-50 text-red-600 animate-bounce' 
                  : 'bg-[#F26430]/10 text-[#00555A]')"
            >
              <ArrowPathIcon class="w-4 h-4" :class="{'animate-spin': qrIsRefreshing}" />
              <span v-if="qrIsRefreshing">Mengambil QR...</span>
              <span v-else-if="qrExpired">QR kadaluarsa</span>
              <span v-else>Berlaku {{ qrCountdown }}s</span>
            </div>

            <div v-if="qrErrorMessage" class="text-red-500 text-xs mt-2 px-4 py-1 bg-red-50 rounded-lg border border-red-100">
               {{ qrErrorMessage }}
            </div>

          </div>

          <div class="bg-[#EBF2FA] p-4 text-center border-t border-gray-100">
            <p class="text-xs text-gray-500 flex items-center justify-center gap-1">
              <LightBulbIcon class="w-3 h-3 text-[#F26430]" />
              Pastikan kecerahan layar HP maksimum
            </p>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import QrcodeVue from "qrcode.vue";
import api from '@/api';
import { 
  BookOpenIcon, BookmarkSquareIcon, ArchiveBoxIcon, CalendarDaysIcon,
  ExclamationCircleIcon, ExclamationTriangleIcon, CheckCircleIcon, ArrowLongRightIcon, ArrowLongLeftIcon,
  MagnifyingGlassIcon, ClockIcon, QrCodeIcon, ArrowPathIcon, LightBulbIcon, XMarkIcon
} from "@heroicons/vue/24/outline";
import { Square3Stack3DIcon, FunnelIcon } from "@heroicons/vue/24/solid";

const peminjamanAktif = ref([]);
const riwayatPengembalian = ref([]);
const isLoading = ref(true);
const searchQuery = ref("");
const activeTab = ref('katalog');

const semuaBuku = ref([]);
const kategoriOptions = ref([]);
const filterKategori = ref("");
const filterTahun = ref("");
const isLoadingKatalog = ref(true);

const fetchUserBooks = async () => {
    try {
        const response = await api.get('/user/buku-saya');
        peminjamanAktif.value = response.data.aktif || [];
        riwayatPengembalian.value = response.data.riwayat || [];
    } catch (error) {
        console.error("Gagal memuat buku saya:", error);
    } finally {
        isLoading.value = false;
    }
};

const fetchSemuaBuku = async () => {
    try {
        const response = await api.get('/buku');
        semuaBuku.value = response.data || [];
    } catch (error) {
        console.error("Gagal memuat katalog buku:", error);
    } finally {
        isLoadingKatalog.value = false;
    }
};

const fetchKategori = async () => {
    try {
        const response = await api.get('/buku/kategori');
        kategoriOptions.value = response.data || [];
    } catch (error) {
        console.error("Gagal memuat kategori:", error);
    }
};

const tahunOptions = computed(() => {
    const years = semuaBuku.value
        .map(b => b.tahun_buku)
        .filter(y => y != null && y !== '');
    return [...new Set(years)].sort((a, b) => b - a);
});

const filteredBuku = computed(() => {
    let result = semuaBuku.value;
    if (searchQuery.value) {
        const term = searchQuery.value.toLowerCase();
        result = result.filter(buku => 
            (buku.kode_buku && buku.kode_buku.toLowerCase().includes(term)) ||
            (buku.judul && buku.judul.toLowerCase().includes(term)) ||
            (buku.penulis && buku.penulis.toLowerCase().includes(term)) ||
            (buku.kategori && buku.kategori.toLowerCase().includes(term))
        );
    }

    if (filterKategori.value) {
        result = result.filter(buku => buku.kategori === filterKategori.value);
    }

    if (filterTahun.value) {
        result = result.filter(buku => String(buku.tahun_buku) === String(filterTahun.value));
    }

    return result;
});

const filteredAktif = computed(() => {
  if (!searchQuery.value) return peminjamanAktif.value;
  const term = searchQuery.value.toLowerCase();
  return peminjamanAktif.value.filter(item => 
    item.judulBuku.toLowerCase().includes(term)
  );
});

const filteredRiwayat = computed(() => {
  if (!searchQuery.value) return riwayatPengembalian.value;
  const term = searchQuery.value.toLowerCase();
  return riwayatPengembalian.value.filter(item => 
    item.judulBuku.toLowerCase().includes(term)
  );
});

const showQrModal = ref(false);
const qrTime = ref("");
const qrFormattedDate = ref("");
const qrCountdown = ref(30);
const qrValue = ref("");
const qrIsRefreshing = ref(false);
const qrErrorMessage = ref("");
const qrExpired = ref(false);

let qrTimerInterval = null;
let qrCountdownInterval = null;

const qrSize = computed(() => {
  if (typeof window !== 'undefined' && window.innerWidth < 380) return 180;
  return 200;
});

const updateQrTime = () => {
  const now = new Date();
  qrTime.value = now.toLocaleTimeString("id-ID", { 
    hour: "2-digit", minute: "2-digit", second: "2-digit"
  }).replace(/\./g, ":");

  qrFormattedDate.value = now.toLocaleDateString("id-ID", {
    weekday: "long", day: "numeric", month: "short", year: "numeric",
  });
};

const fetchQrCode = async () => {
    if (qrIsRefreshing.value) return;

    try {
        qrIsRefreshing.value = true;
        qrErrorMessage.value = "";
        qrExpired.value = false;
        
        const response = await api.get('/qr/generate');
        
        qrValue.value = response.data.qr_code;
        qrCountdown.value = response.data.expires_in || 30;
        
    } catch (error) {
        console.error("Error Fetching QR:", error);
        
        if (error.response && error.response.data) {
            qrErrorMessage.value = error.response.data.message || "Terjadi kesalahan server.";
        } else {
            qrErrorMessage.value = "Gagal terhubung ke server.";
        }
        
        qrCountdown.value = 10;
    } finally {
        qrIsRefreshing.value = false;
    }
};

const startQrTimer = () => {
  updateQrTime();
  fetchQrCode();
  
  qrTimerInterval = setInterval(updateQrTime, 1000);

  qrCountdownInterval = setInterval(() => {
    if (qrCountdown.value > 0) {
        qrCountdown.value--;
    } else {
        qrExpired.value = true;
    }
  }, 1000);
};

const stopQrTimer = () => {
  if (qrTimerInterval) { clearInterval(qrTimerInterval); qrTimerInterval = null; }
  if (qrCountdownInterval) { clearInterval(qrCountdownInterval); qrCountdownInterval = null; }
};

const regenerateQr = () => {
  qrExpired.value = false;
  fetchQrCode();
};

const openQrModal = () => {
  showQrModal.value = true;
  qrValue.value = "";
  qrCountdown.value = 30;
  qrExpired.value = false;
  qrErrorMessage.value = "";
  startQrTimer();
};

const closeQrModal = () => {
  showQrModal.value = false;
  stopQrTimer();
  qrValue.value = "";
};

onMounted(() => {
  fetchUserBooks();
  fetchSemuaBuku();
  fetchKategori();
});

onUnmounted(() => {
  stopQrTimer();
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>