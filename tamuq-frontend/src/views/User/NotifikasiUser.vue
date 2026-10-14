<template>
  <div class="max-w-3xl mx-auto space-y-6">

    <!-- Header -->
    <div>
      <h1 class="text-3xl font-extrabold text-[#00555A] flex items-center gap-2">
        <BellIcon class="w-8 h-8" />
        Notifikasi
      </h1>
      <p class="mt-1 text-sm text-gray-500">Riwayat pengumuman dan notifikasi personal kamu.</p>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 bg-gray-100 rounded-xl p-1">
      <button
        @click="activeTab = 'umum'"
        class="flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all duration-200"
        :class="activeTab === 'umum'
          ? 'bg-white text-[#00555A] shadow-sm'
          : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
      >
        <span class="flex items-center justify-center gap-1.5">
          <MegaphoneIcon class="w-4 h-4" />
          Umum
        </span>
      </button>
      <button
        @click="activeTab = 'personal'"
        class="flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all duration-200 relative"
        :class="activeTab === 'personal'
          ? 'bg-white text-[#00555A] shadow-sm'
          : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
      >
        <span class="flex items-center justify-center gap-1.5">
          <UserIcon class="w-4 h-4" />
          Personal
          <span
            v-if="personalUnreadCount > 0"
            class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-red-500 text-white text-[11px] font-bold leading-none"
          >
            {{ personalUnreadCount }}
          </span>
        </span>
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-20 gap-3">
      <div class="w-8 h-8 border-[3px] border-gray-200 border-t-[#00555A] rounded-full animate-spin"></div>
      <p class="text-sm text-gray-400 font-medium">Memuat notifikasi...</p>
    </div>

    <!-- Error State -->
    <div
      v-else-if="errorMsg"
      class="flex flex-col items-center justify-center py-16 gap-3 text-center"
    >
      <ExclamationCircleIcon class="w-10 h-10 text-red-400" />
      <p class="text-sm text-red-500 font-medium">{{ errorMsg }}</p>
    </div>

    <!-- Content -->
    <template v-else>

      <!-- Tab Umum -->
      <div v-if="activeTab === 'umum'" class="flex flex-col gap-3">
        <!-- Empty State -->
        <div v-if="umum.length === 0" class="flex flex-col items-center justify-center py-16 gap-3 text-center">
          <MegaphoneIcon class="w-10 h-10 text-gray-300" />
          <p class="text-sm text-gray-400 font-medium">Belum ada pengumuman umum saat ini.</p>
        </div>

        <!-- List -->
        <div
          v-for="item in umum"
          :key="item.id"
          class="rounded-xl border-l-4 p-5 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
          :class="borderColorClass(item.tipe)"
        >
          <div class="flex items-start justify-between gap-3 mb-2">
            <h3 class="font-bold text-gray-800 text-[15px] leading-snug">{{ item.judul }}</h3>
            <span
              class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wide"
              :class="badgeClasses(item.tipe)"
            >
              <component :is="typeIcon(item.tipe)" class="w-3 h-3" />
              {{ item.tipe }}
            </span>
          </div>
          <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ item.isi_pesan }}</p>
          <p class="mt-3 text-xs text-gray-400 font-medium">{{ formatDate(item.created_at) }}</p>
        </div>
      </div>

      <!-- Tab Personal -->
      <div v-if="activeTab === 'personal'" class="flex flex-col gap-3">
        <!-- Empty State -->
        <div v-if="personal.length === 0" class="flex flex-col items-center justify-center py-16 gap-3 text-center">
          <UserIcon class="w-10 h-10 text-gray-300" />
          <p class="text-sm text-gray-400 font-medium">Belum ada notifikasi personal untuk kamu.</p>
        </div>

        <!-- List -->
        <div
          v-for="item in personal"
          :key="item.id"
          class="relative rounded-xl border-l-4 p-5 shadow-sm transition-all duration-200 cursor-pointer"
          :class="[
            borderColorClass(item.tipe),
            isUnread(item)
              ? 'bg-white ring-1 ring-gray-200 hover:shadow-md'
              : 'bg-gray-50/70 hover:bg-gray-50'
          ]"
          @click="markAsRead(item)"
        >
          <!-- Unread dot -->
          <span
            v-if="isUnread(item)"
            class="absolute top-3 left-[-2px] w-2.5 h-2.5 rounded-full shadow-sm"
            :class="dotColorClass(item.tipe)"
          ></span>

          <div class="flex items-start justify-between gap-3 mb-2">
            <h3
              class="text-[15px] leading-snug"
              :class="isUnread(item) ? 'font-bold text-gray-800' : 'font-semibold text-gray-600'"
            >
              {{ item.judul }}
            </h3>
            <span
              class="shrink-0 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wide"
              :class="badgeClasses(item.tipe)"
            >
              <component :is="typeIcon(item.tipe)" class="w-3 h-3" />
              {{ item.tipe }}
            </span>
          </div>
          <p
            class="text-sm leading-relaxed whitespace-pre-line"
            :class="isUnread(item) ? 'text-gray-600' : 'text-gray-500'"
          >
            {{ item.isi_pesan }}
          </p>
          <p class="mt-3 text-xs text-gray-400 font-medium">{{ formatDate(item.created_at) }}</p>
        </div>
      </div>

    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '@/api';
import {
  BellIcon,
  MegaphoneIcon,
  UserIcon,
  ExclamationCircleIcon,
  InformationCircleIcon,
  ExclamationTriangleIcon,
  ShieldExclamationIcon
} from '@heroicons/vue/24/outline';

const list = ref([]);
const loading = ref(true);
const errorMsg = ref('');
const activeTab = ref('umum');

// ─── Computed ────────────────────────────────────────────

const umum = computed(() => list.value.filter(item => item.target !== 'individu'));

const personal = computed(() => list.value.filter(item => item.target === 'individu'));

const personalUnreadCount = computed(() =>
  personal.value.filter(item => item.penerima?.[0]?.pivot?.dibaca === false).length
);

// ─── Helpers ─────────────────────────────────────────────

const isUnread = (item) => item.penerima?.[0]?.pivot?.dibaca === false;

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const typeIcon = (tipe) => {
  if (tipe === 'darurat') return ShieldExclamationIcon;
  if (tipe === 'penting') return ExclamationTriangleIcon;
  return InformationCircleIcon;
};

// Border-left color (matching PengumumanStack)
const borderColorClass = (tipe) => {
  if (tipe === 'darurat') return 'border-red-500';
  if (tipe === 'penting') return 'border-amber-500';
  return 'border-blue-500';
};

// Badge styling
const badgeClasses = (tipe) => {
  if (tipe === 'darurat') return 'bg-red-100 text-red-700';
  if (tipe === 'penting') return 'bg-amber-100 text-amber-700';
  return 'bg-blue-100 text-blue-700';
};

// Unread dot color
const dotColorClass = (tipe) => {
  if (tipe === 'darurat') return 'bg-red-500';
  if (tipe === 'penting') return 'bg-amber-500';
  return 'bg-blue-500';
};

// ─── Actions ─────────────────────────────────────────────

const markAsRead = async (item) => {
  if (!isUnread(item)) return;

  // Optimistic update
  if (item.penerima?.[0]?.pivot) {
    item.penerima[0].pivot.dibaca = true;
  }

  try {
    await api.post(`/notifikasi/${item.id}/tandai-dibaca`);
  } catch {
    // Revert jika gagal
    if (item.penerima?.[0]?.pivot) {
      item.penerima[0].pivot.dibaca = false;
    }
  }
};

// ─── Fetch ───────────────────────────────────────────────

onMounted(async () => {
  try {
    loading.value = true;
    const { data } = await api.get('/notifikasi-saya');
    if (data.success && Array.isArray(data.data)) {
      list.value = data.data;
    }
  } catch {
    errorMsg.value = 'Gagal memuat notifikasi';
  } finally {
    loading.value = false;
  }
});
</script>
