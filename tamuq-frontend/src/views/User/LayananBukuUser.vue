<template>
  <div class="max-w-5xl mx-auto space-y-10">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-[#00555A] flex items-center gap-2">
          <BookOpenIcon class="w-8 h-8 text-[#F26430]" />
          Layanan Buku
        </h1>
        <p class="text-gray-500 text-sm mt-1">Pantau status peminjaman dan riwayat bacaan Anda.</p>
      </div>

      <div class="relative w-full sm:w-80">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
        </div>
        <input 
          type="text" 
          v-model="searchQuery" 
          class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none transition-all shadow-sm"
          placeholder="Cari judul buku yang dipinjam / riwayat..."
        />
      </div>
    </div>

    <div class="space-y-4">
      <div class="flex items-center gap-2">
        <BookmarkSquareIcon class="w-5 h-5 text-[#F26430]" />
        <h2 class="text-lg font-bold text-[#00555A]">Sedang Dipinjam</h2>
      </div>

      <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm h-36 animate-pulse"></div>
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm h-36 animate-pulse"></div>
      </div>

      <div v-else-if="filteredAktif.length === 0" class="col-span-full bg-white border border-dashed border-gray-300 rounded-2xl p-8 text-center">
        <p class="text-gray-400 font-medium">
          {{ searchQuery ? 'Tidak ada buku aktif yang cocok dengan pencarian.' : 'Tidak ada buku yang sedang dipinjam.' }}
        </p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div 
          v-for="item in filteredAktif" 
          :key="item.id"
          class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md hover:border-[#F26430]/30 transition-all relative overflow-hidden group"
        >
          <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#F26430]"></div>
          <div class="flex items-start gap-4 pl-2">
            <div class="w-12 h-12 bg-[#F26430]/10 rounded-xl flex items-center justify-center text-[#F26430] shrink-0">
              <BookOpenIcon class="w-6 h-6" />
            </div>
            <div class="flex-1">
              <h3 class="font-bold text-gray-800 text-lg leading-tight group-hover:text-[#00555A] transition-colors">
                {{ item.judulBuku }}
              </h3>
              <div class="mt-3 space-y-1.5">
                <div class="flex items-center gap-2 text-sm text-gray-500">
                  <CalendarDaysIcon class="w-4 h-4 text-[#00555A]" />
                  <span>Pinjam: <span class="font-medium text-gray-700">{{ item.tanggalPinjam }}</span></span>
                </div>
                
                <div class="flex items-center gap-2 text-sm text-gray-500">
                  <ClockIcon class="w-4 h-4 text-[#F26430]" />
                  <span>Batas Kembali: <span class="font-medium text-gray-700">{{ item.batasPengembalian }}</span></span>
                </div>

                <div v-if="item.sisaHari < 0" class="flex items-center gap-1.5 text-[11px] text-white bg-red-500 w-fit px-2 py-1 rounded mt-1 font-bold shadow-sm">
                  <ExclamationCircleIcon class="w-3.5 h-3.5" />
                  <span>Terlambat {{ Math.abs(item.sisaHari) }} hari! Segera kembalikan!</span>
                </div>
                <div v-else-if="item.sisaHari === 0" class="flex items-center gap-1.5 text-[11px] text-white bg-orange-500 w-fit px-2 py-1 rounded mt-1 font-bold shadow-sm">
                  <ExclamationTriangleIcon class="w-3.5 h-3.5" />
                  <span>HARI INI batas terakhir pengembalian!</span>
                </div>
                <div v-else-if="item.sisaHari === 1" class="flex items-center gap-1.5 text-[11px] text-orange-600 bg-orange-100 border border-orange-200 w-fit px-2 py-1 rounded mt-1 font-bold">
                  <ClockIcon class="w-3.5 h-3.5" />
                  <span>Besok adalah batas pengembalian.</span>
                </div>
                <div v-else class="flex items-center gap-1.5 text-[11px] text-[#00555A] bg-[#00555A]/10 border border-[#00555A]/20 w-fit px-2 py-1 rounded mt-1 font-bold">
                  <CheckCircleIcon class="w-3.5 h-3.5" />
                  <span>Sisa waktu peminjaman: {{ item.sisaHari }} hari</span>
                </div>

              </div>
            </div>
            <span class="bg-[#F26430]/10 text-[#D65020] text-xs font-bold px-2 py-1 rounded uppercase tracking-wider">
              {{ item.status }}
            </span>
          </div>
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
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import api from '@/api';
import { 
  BookOpenIcon, BookmarkSquareIcon, ArchiveBoxIcon, CalendarDaysIcon,
  ExclamationCircleIcon, ExclamationTriangleIcon, CheckCircleIcon, ArrowLongRightIcon, ArrowLongLeftIcon,
  MagnifyingGlassIcon, ClockIcon
} from "@heroicons/vue/24/outline";

const peminjamanAktif = ref([]);
const riwayatPengembalian = ref([]);
const isLoading = ref(true);
const searchQuery = ref("");

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

onMounted(fetchUserBooks);
</script>