<template>
  <div v-if="progressStore.hasActiveUpload || progressStore.hasActivePreview" class="fixed top-0 left-0 w-full z-[10000] pointer-events-none">
    <div class="h-1.5 bg-gray-200 w-full">
      <div 
        class="h-1.5 transition-all duration-300 ease-out"
        :class="progressStore.hasActiveUpload ? 'bg-[#F26430]' : 'bg-[#00555A]'"
        :style="{ width: `${topBarProgress}%` }"
      >
      </div>
    </div>
  </div>

  <TransitionGroup
    v-if="progressStore.stackOperations.length > 0"
    name="toast"
    tag="div"
    class="fixed z-[10000] flex flex-col gap-2 pointer-events-none"
    :class="isStaffOrAdmin ? 'bottom-4 right-4 items-end' : 'bottom-4 left-4 items-start'"
  >
    <div
      v-for="op in progressStore.stackOperations"
      :key="op.id"
      class="pointer-events-auto bg-white shadow-lg border rounded-xl px-4 py-3 flex items-center gap-3 min-w-[220px] max-w-[320px] transition-all duration-300"
      :class="getToastBorderClass(op)"
    >
      <!-- Icon -->
      <div class="flex-shrink-0">
        <ArrowUpTrayIcon v-if="op.type === 'upload' && op.status === 'active'" class="w-5 h-5 text-[#F26430] animate-bounce" />
        <ArrowDownTrayIcon v-else-if="op.type === 'download' && op.status === 'active'" class="w-5 h-5 text-[#00555A] animate-bounce" />
        <CheckCircleIcon v-else-if="op.status === 'success'" class="w-5 h-5 text-green-500" />
        <XCircleIcon v-else-if="op.status === 'error'" class="w-5 h-5 text-red-500" />
      </div>

      <div class="flex-1 min-w-0">
        <p class="text-xs font-bold text-gray-700 truncate">
          {{ getToastLabel(op) }}
        </p>
        <!-- Progress bar (only when active) -->
        <div v-if="op.status === 'active'" class="mt-1.5 w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
          <div
            class="h-full rounded-full transition-all duration-300 ease-out"
            :class="op.type === 'upload' ? 'bg-[#F26430]' : 'bg-[#00555A]'"
            :style="{ width: `${op.progress}%` }"
          ></div>
        </div>
      </div>

      <span v-if="op.status === 'active'" class="text-xs font-bold text-gray-500 tabular-nums flex-shrink-0">
        {{ op.progress }}%
      </span>

      <button
        @click="progressStore.removeOperation(op.id)"
        class="flex-shrink-0 text-gray-300 hover:text-gray-500 transition-colors"
        title="Tutup"
      >
        <XMarkIcon class="w-4 h-4" />
      </button>
    </div>
  </TransitionGroup>
</template>

<script setup>
import { computed } from 'vue';
import { useProgressStore } from '@/stores/progress';
import { useAuthStore } from '@/stores/auth';
import { ArrowUpTrayIcon, ArrowDownTrayIcon, XMarkIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline';

const progressStore = useProgressStore();
const authStore = useAuthStore();

const isStaffOrAdmin = computed(() => authStore.isStaffOrAdmin);

const topBarProgress = computed(() => {
  if (progressStore.hasActiveUpload) return progressStore.activeUploadProgress;
  if (progressStore.hasActivePreview) return progressStore.activePreviewProgress;
  return 0;
});

const getToastBorderClass = (op) => {
  if (op.status === 'success') return 'border-green-200';
  if (op.status === 'error') return 'border-red-200';
  return 'border-gray-100';
};

const getToastLabel = (op) => {
  if (op.status === 'success') {
    return op.type === 'upload' ? 'Upload berhasil' : 'Unduhan selesai';
  }
  if (op.status === 'error') {
    return op.type === 'upload' ? 'Upload gagal' : 'Unduhan gagal';
  }
  return op.label;
};
</script>

<style scoped>
.toast-enter-active {
  transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.toast-leave-active {
  transition: all 0.25s ease-in;
}
.toast-enter-from {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(30px) scale(0.95);
}
.toast-move {
  transition: transform 0.3s ease;
}
</style>
