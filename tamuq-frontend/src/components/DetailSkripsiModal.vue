<template>
  <Teleport to="body">
    <transition name="fade">
      <div v-if="show" class="fixed inset-0 flex items-center justify-center z-[80] bg-[#00282a]/50 backdrop-blur-sm p-4" @click.self="$emit('close')">
      <div class="bg-white shadow-2xl rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col transform transition-all scale-100">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 shrink-0">
          <h3 class="text-lg font-bold text-[#00555A] flex items-center gap-2">
            <DocumentTextIcon class="w-5 h-5 text-[#F26430]" />
            Detail Skripsi
          </h3>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 bg-white shadow-sm border border-gray-200 rounded-full p-1.5 transition-colors">
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>

        <div class="p-6 overflow-y-auto space-y-6 flex-1">
          <div class="flex flex-col md:flex-row gap-6">
            <div class="w-32 h-40 md:w-48 md:h-64 rounded-xl overflow-hidden shrink-0 bg-gray-100 flex items-center justify-center border border-gray-200 relative mx-auto md:mx-0">
              <img v-if="skripsi?.foto_url" :src="skripsi.foto_url" alt="Foto Mahasiswa" class="w-full h-full object-cover" @error="e => e.target.style.display = 'none'" />
              <DocumentTextIcon v-if="!skripsi?.foto_url" class="w-16 h-16 text-[#00555A]/30 absolute" />
            </div>

            <div class="flex-1 space-y-4">
              <div>
                <h2 class="text-xl font-bold text-gray-800 leading-snug">{{ skripsi?.judul }}</h2>
                <div class="flex flex-wrap items-center gap-2 mt-3">
                  <span :class="getStatusBadgeClass(skripsi?.status)" class="px-2.5 py-1 text-[10px] font-bold rounded-lg border uppercase inline-flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotClass(skripsi?.status)"></span>
                    {{ skripsi?.status }}
                  </span>
                  <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-gray-100 text-gray-600 border border-gray-200">
                    {{ skripsi?.prodi }}
                  </span>
                  <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-blue-50 text-blue-700 border border-blue-200 flex items-center gap-1">
                    <CalendarDaysIcon class="w-3 h-3" /> Lulus: {{ skripsi?.tahun_selesai }}
                  </span>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <div>
                  <p class="text-xs font-bold text-gray-500 uppercase mb-1">Nama Mahasiswa</p>
                  <p class="text-sm font-medium text-gray-800">{{ skripsi?.nama }}</p>
                </div>
                <div>
                  <p class="text-xs font-bold text-gray-500 uppercase mb-1">NIM</p>
                  <p class="text-sm font-medium text-gray-800">{{ skripsi?.nim }}</p>
                </div>
                <div class="sm:col-span-2">
                  <p class="text-xs font-bold text-gray-500 uppercase mb-1">Waktu Pengajuan</p>
                  <p class="text-sm text-gray-800 flex items-center gap-1">
                    <ClockIcon class="w-4 h-4 text-gray-400" />
                    {{ formatDate(skripsi?.created_at) }}
                  </p>
                </div>
              </div>

              <div v-if="skripsi?.keterangan != null" class="bg-amber-50 p-4 rounded-xl border border-amber-100">
                <p class="text-xs font-bold text-amber-700 uppercase mb-1 flex items-center gap-1">
                  <ChatBubbleBottomCenterTextIcon class="w-4 h-4" /> Keterangan
                </p>
                <p class="text-sm text-amber-900 whitespace-pre-wrap">{{ skripsi.keterangan }}</p>
              </div>
            </div>
          </div>

          <div class="space-y-2">
            <h4 class="text-sm font-bold text-gray-800 border-b border-gray-100 pb-2">Abstrak</h4>
            <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-wrap">{{ skripsi?.abstrak || 'Tidak ada abstrak.' }}</p>
          </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row gap-3 shrink-0 justify-end">
          <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            <button v-if="skripsi?.file_preview_skripsi" @click="$emit('preview', 'preview')" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-medium transition shadow-sm">
              <EyeIcon class="w-4 h-4" /> Preview PDF (Publik)
            </button>
          </div>
          <div v-if="canAccessFull" class="flex flex-wrap gap-2 w-full sm:w-auto">
            <button @click="$emit('preview', 'full')" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-[#00555A]/10 text-[#00555A] hover:bg-[#00555A]/20 rounded-lg text-sm font-bold transition shadow-sm">
              <EyeIcon class="w-4 h-4" /> Preview Full
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { 
  DocumentTextIcon, XMarkIcon, CalendarDaysIcon, ClockIcon, 
  ChatBubbleBottomCenterTextIcon, EyeIcon 
} from '@heroicons/vue/24/outline';

const props = defineProps({
  show: Boolean,
  skripsi: Object
});

defineEmits(['close', 'preview']);

const authStore = useAuthStore();
const canAccessFull = computed(() => {
  return authStore.isStaffOrAdmin || (authStore.user && Number(props.skripsi?.user_id) === Number(authStore.user.id_user));
});

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const options = { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateStr).toLocaleDateString('id-ID', options);
};

const getStatusBadgeClass = (status) => {
  if (status === 'disetujui') return 'bg-green-50 text-green-700 border-green-200';
  if (status === 'ditolak') return 'bg-red-50 text-red-700 border-red-200';
  return 'bg-yellow-50 text-yellow-700 border-yellow-200';
};

const getStatusDotClass = (status) => {
  if (status === 'disetujui') return 'bg-green-500';
  if (status === 'ditolak') return 'bg-red-500';
  return 'bg-yellow-500';
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease, transform 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(5px); }
</style>
