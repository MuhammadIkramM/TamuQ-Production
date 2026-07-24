<template>
  <div class="relative min-h-screen">
    <GlobalProgressBar />
    <router-view />

    <Transition name="modal-fade">
      <div v-if="showWarning" class="fixed inset-0 z-[9999] flex items-center justify-center px-4">
        
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click.prevent></div>

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 relative z-10 text-center transform transition-all border-t-4 border-[#F26430]">
          
          <div class="mx-auto w-16 h-16 bg-[#FEF3C7] rounded-full flex items-center justify-center mb-4 animate-pulse-slow">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-[#D97706] animate-wiggle">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>

          <h3 class="text-xl font-bold text-gray-800 mb-2">Masih Disana?</h3>
          <p class="text-gray-500 text-sm mb-6">
            Sesi Anda akan berakhir dalam <span class="font-bold text-[#F26430] text-lg">{{ countdown }}</span> detik karena tidak ada aktivitas.
          </p>

          <div class="w-full bg-gray-200 rounded-full h-1.5 mb-6 overflow-hidden">
            <div 
              class="bg-[#F26430] h-1.5 rounded-full transition-all duration-1000 ease-linear"
              :style="{ width: `${(countdown / WARNING_DURATION_SEC) * 100}%` }"
            ></div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <button 
              @click="logoutUser" 
              class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition"
            >
              Keluar Saja
            </button>
            <button 
              @click="extendSession" 
              class="px-4 py-2.5 rounded-xl bg-[#00555A] text-white text-sm font-semibold hover:bg-[#004448] shadow-lg shadow-[#00555A]/30 transition transform active:scale-95"
            >
              Saya Masih Disini
            </button>
          </div>

        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import api from '@/api';
import { useAuthStore } from '@/stores/auth';
import GlobalProgressBar from '@/components/GlobalProgressBar.vue';

const router = useRouter();
const route = useRoute();
const auth = useAuthStore();

const IDLE_LIMIT_MS = 24 * 60 * 1000; 

const WARNING_DURATION_SEC = 60; 

const showWarning = ref(false);
const countdown = ref(WARNING_DURATION_SEC);
let idleTimer = null;
let countdownInterval = null;

const resetIdleTimer = () => {
  if (route.path === '/login' || route.path === '/' || showWarning.value) return;

  clearTimeout(idleTimer);
  idleTimer = setTimeout(() => {
    startWarningPhase();
  }, IDLE_LIMIT_MS);
};

const startWarningPhase = () => {
  showWarning.value = true;
  countdown.value = WARNING_DURATION_SEC;
  
  countdownInterval = setInterval(() => {
    countdown.value--;
    if (countdown.value <= 0) {
      logoutUser(); 
    }
  }, 1000);
};

const extendSession = () => {
  clearInterval(countdownInterval);
  showWarning.value = false;
  resetIdleTimer(); 
}

const logoutUser = async () => {
  clearInterval(countdownInterval);
  clearTimeout(idleTimer);
  showWarning.value = false;

  console.log("Sesi berakhir. Logout...");
  
  auth.clearAuth();

  try {
    await api.post('/logout');
  } catch (e) { /* ignore error */ }

  router.push('/login');
};

onMounted(() => {
  const events = ['mousemove', 'mousedown', 'keypress', 'scroll', 'touchstart'];
  
  events.forEach(event => {
    window.addEventListener(event, resetIdleTimer, { passive: true });
  });

  resetIdleTimer();
});

onUnmounted(() => {
  const events = ['mousemove', 'mousedown', 'keypress', 'scroll', 'touchstart'];
  events.forEach(event => {
    window.removeEventListener(event, resetIdleTimer);
  });
  clearTimeout(idleTimer);
  clearInterval(countdownInterval);
});

watch(() => route.path, (newPath) => {
  if (newPath === '/login' || newPath === '/') {
    clearTimeout(idleTimer);
    clearInterval(countdownInterval);
    showWarning.value = false;
  } else {
    resetIdleTimer();
  }
});
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

.modal-fade-enter-to,
.modal-fade-leave-from {
  opacity: 1;
  transform: scale(1);
}

@keyframes wiggle {
  0%, 100% { transform: rotate(-3deg); }
  50% { transform: rotate(3deg); }
}
.animate-wiggle {
  animation: wiggle 1s ease-in-out infinite;
}

@keyframes pulse-slow {
  0%, 100% { box-shadow: 0 0 0 0 rgba(254, 243, 199, 0.7); }
  70% { box-shadow: 0 0 0 10px rgba(254, 243, 199, 0); }
}
.animate-pulse-slow {
  animation: pulse-slow 2s infinite;
}
</style>