<template>
  <transition name="modal-fade">
    <div v-if="show" class="fixed inset-0 z-[60] flex items-center justify-center px-4">
      <!-- Backdrop -->
      <div 
        class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" 
        @click="emit('close')"
      ></div>

      <!-- Modal Card -->
      <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden transform transition-all">
        <!-- Close Button -->
        <button 
          @click="emit('close')" 
          class="absolute top-4 right-4 z-20 text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition focus:outline-none"
          title="Tutup"
        >
          <XMarkIcon class="w-5 h-5" />
        </button>

        <!-- Top Accent Gradient Line -->
        <div class="h-2 bg-gradient-to-r from-[#F26430] to-[#00555A] w-full"></div>

        <!-- Card Content -->
        <div class="p-6 sm:p-8 flex flex-col items-center text-center space-y-6">
          
          <!-- Header Title & Subtitle -->
          <div class="space-y-1">
            <h2 class="text-2xl font-bold text-[#00555A] flex items-center justify-center gap-2">
              <QrCodeIcon class="w-6 h-6 text-[#F26430]" />
              Akses Masuk
            </h2>
            <p class="text-gray-500 text-xs sm:text-sm">Tunjukkan QR Code ini ke staff perpustakaan.</p>
          </div>

          <!-- Digital Clock & Date -->
          <div class="space-y-1">
            <h1 class="text-4xl sm:text-5xl font-black tracking-tight text-[#00555A] font-mono">
              {{ time }}
            </h1>
            <p class="text-gray-500 font-medium uppercase tracking-widest text-xs">
              {{ formattedDate }}
            </p>
          </div>

          <!-- QR Code Display Box -->
          <div class="relative">
            <div class="absolute -inset-4 border-2 border-dashed border-gray-200 rounded-xl"></div>
            
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 relative z-10">
              <qrcode-vue 
                v-if="qrValue"
                :value="qrValue" 
                :size="qrSize" 
                level="H" 
                render-as="svg"
                foreground="#1f2937" 
              />
              <div v-else class="flex items-center justify-center" :style="{ width: qrSize + 'px', height: qrSize + 'px' }">
                <ArrowPathIcon class="w-8 h-8 text-gray-300 animate-spin" />
              </div>
            </div>

            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-20">
              <div class="bg-white p-1 rounded-full shadow-sm">
                <div class="w-2 h-2 bg-[#F26430] rounded-full animate-pulse"></div>
              </div>
            </div>
          </div>

          <!-- Countdown / Refresh Status Badge -->
          <div 
            class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold transition-colors duration-300"
            :class="countdown <= 5 
              ? 'bg-red-50 text-red-600 animate-bounce' 
              : 'bg-[#F26430]/10 text-[#00555A]'"
          >
            <ArrowPathIcon class="w-4 h-4" :class="{'animate-spin': isRefreshing}" />
            <span v-if="isRefreshing">Mengambil QR...</span>
            <span v-else>Refresh dalam {{ countdown }}s</span>
          </div>

          <!-- Error Message Display -->
          <div v-if="errorMessage" class="text-red-500 text-xs px-4 py-1 bg-red-50 rounded-lg border border-red-100">
            {{ errorMessage }}
          </div>

        </div>

        <!-- Footer Notice -->
        <div class="bg-[#EBF2FA] p-4 text-center border-t border-gray-100">
          <p class="text-xs text-gray-500 flex items-center justify-center gap-1">
            <LightBulbIcon class="w-3 h-3 text-[#F26430]" />
            Pastikan kecerahan layar HP maksimum
          </p>
        </div>

      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed, watch, onUnmounted } from "vue";
import QrcodeVue from "qrcode.vue";
import api from "@/api";
import { 
  XMarkIcon, 
  QrCodeIcon, 
  ArrowPathIcon, 
  LightBulbIcon 
} from "@heroicons/vue/24/outline";

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  }
});

const emit = defineEmits(["close"]);

const time = ref("");
const formattedDate = ref("");
const countdown = ref(30);
const qrValue = ref("");
const isRefreshing = ref(false);
const errorMessage = ref("");

const qrSize = computed(() => {
  if (typeof window !== 'undefined' && window.innerWidth < 380) return 180;
  return 200;
});

const updateTime = () => {
  const now = new Date();
  time.value = now.toLocaleTimeString("id-ID", { 
    hour: "2-digit", minute: "2-digit", second: "2-digit"
  }).replace(/\./g, ":");

  formattedDate.value = now.toLocaleDateString("id-ID", {
    weekday: "long", day: "numeric", month: "short", year: "numeric",
  });
};

const fetchQrCode = async () => {
  if (isRefreshing.value) return;

  try {
    isRefreshing.value = true;
    errorMessage.value = "";
    
    const response = await api.get('/qr/generate');
    
    qrValue.value = response.data.qr_code;
    countdown.value = response.data.expires_in || 30;
    
  } catch (error) {
    console.error("Error Fetching QR:", error);
    
    if (error.response && error.response.data) {
      errorMessage.value = error.response.data.message || "Terjadi kesalahan server.";
    } else {
      errorMessage.value = "Gagal terhubung ke server.";
    }
    
    countdown.value = 10;
  } finally {
    isRefreshing.value = false;
  }
};

let timerInterval = null;
let countdownInterval = null;

const stopTimers = () => {
  if (timerInterval) {
    clearInterval(timerInterval);
    timerInterval = null;
  }
  if (countdownInterval) {
    clearInterval(countdownInterval);
    countdownInterval = null;
  }
};

const startTimers = () => {
  stopTimers();
  updateTime();
  fetchQrCode();

  timerInterval = setInterval(updateTime, 1000);

  countdownInterval = setInterval(() => {
    if (countdown.value > 0) {
      countdown.value--;
    } else {
      fetchQrCode();
    }
  }, 1000);
};

watch(() => props.show, (newVal) => {
  if (newVal) {
    countdown.value = 30;
    startTimers();
  } else {
    stopTimers();
    qrValue.value = "";
    errorMessage.value = "";
  }
}, { immediate: true });

onUnmounted(() => {
  stopTimers();
});
</script>

<style scoped>
.modal-fade-enter-active, .modal-fade-leave-active {
  transition: opacity 0.3s ease;
}
.modal-fade-enter-from, .modal-fade-leave-to {
  opacity: 0;
}
.modal-fade-enter-active .transform, .modal-fade-leave-active .transform {
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.modal-fade-enter-from .transform, .modal-fade-leave-to .transform {
  transform: scale(0.95) translateY(10px);
}
</style>
