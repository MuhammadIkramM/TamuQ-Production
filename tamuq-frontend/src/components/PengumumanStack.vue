<template>
  <div v-if="visible.length > 0" class="flex flex-col gap-2.5">
    <div
      v-for="item in visible"
      :key="item.id"
      class="relative rounded-lg border-l-4 p-4 pr-10"
      :class="bannerClasses(item.tipe)"
    >
      <!-- Tombol close -->
      <button
        @click="dismiss(item.id)"
        class="absolute top-2.5 right-2.5 rounded-full p-0.5 transition-colors"
        :class="closeButtonClasses(item.tipe)"
        title="Tutup"
      >
        <XMarkIcon class="w-4 h-4" />
      </button>

      <!-- Judul dengan ikon -->
      <div class="flex items-start gap-2">
        <component :is="typeIcon(item.tipe)" class="w-5 h-5 mt-0.5 shrink-0" :class="iconClasses(item.tipe)" />
        <div class="min-w-0 flex-1">
          <p class="text-sm font-semibold" :class="titleClasses(item.tipe)">{{ item.judul }}</p>
          <p class="mt-1 text-xs leading-relaxed whitespace-pre-line" :class="bodyClasses(item.tipe)">{{ item.isi_pesan }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '@/api';
import {
  XMarkIcon,
  InformationCircleIcon,
  ExclamationTriangleIcon,
  ShieldExclamationIcon
} from '@heroicons/vue/24/outline';

const list = ref([]);
const dismissedIds = ref([]);
const loading = ref(true);

const visible = computed(() => {
  return list.value.filter(item => !dismissedIds.value.includes(item.id));
});

const dismiss = (id) => {
  dismissedIds.value.push(id);
};

const typeIcon = (tipe) => {
  if (tipe === 'darurat') return ShieldExclamationIcon;
  if (tipe === 'penting') return ExclamationTriangleIcon;
  return InformationCircleIcon;
};

const bannerClasses = (tipe) => {
  if (tipe === 'darurat') return 'border-red-500 bg-red-50';
  if (tipe === 'penting') return 'border-amber-500 bg-amber-50';
  return 'border-blue-500 bg-blue-50';
};

const closeButtonClasses = (tipe) => {
  if (tipe === 'darurat') return 'text-red-400 hover:text-red-600 hover:bg-red-100';
  if (tipe === 'penting') return 'text-amber-400 hover:text-amber-600 hover:bg-amber-100';
  return 'text-blue-400 hover:text-blue-600 hover:bg-blue-100';
};

const iconClasses = (tipe) => {
  if (tipe === 'darurat') return 'text-red-500';
  if (tipe === 'penting') return 'text-amber-500';
  return 'text-blue-500';
};

const titleClasses = (tipe) => {
  if (tipe === 'darurat') return 'text-red-800';
  if (tipe === 'penting') return 'text-amber-800';
  return 'text-blue-800';
};

const bodyClasses = (tipe) => {
  if (tipe === 'darurat') return 'text-red-700';
  if (tipe === 'penting') return 'text-amber-700';
  return 'text-blue-700';
};

onMounted(async () => {
  try {
    loading.value = true;
    const { data } = await api.get('/pengumuman/aktif');
    if (data.success && Array.isArray(data.data)) {
      list.value = data.data;
    }
  } catch {
    // Fail silent — komponen pelengkap, tidak boleh mengganggu UX
  } finally {
    loading.value = false;
  }
});
</script>
