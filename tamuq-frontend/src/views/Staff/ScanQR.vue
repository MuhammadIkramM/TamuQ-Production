<template>
  <div class="min-h-screen bg-gray-50 flex flex-col lg:flex-row p-4 gap-6 animate-fade-in">
    
    <div class="w-full lg:w-1/2 flex flex-col bg-black rounded-3xl shadow-2xl overflow-hidden relative border-4 border-gray-800">
      
      <div class="absolute top-0 left-0 right-0 bg-gradient-to-b from-black/80 to-transparent p-4 z-20 flex justify-between items-center">
        <div class="flex items-center gap-2 text-white/90">
          <div class="bg-[#F26430] p-1.5 rounded-lg">
             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-white">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75zM16.5 19.5h.75v.75h-.75v-.75zM19.5 16.5h.75v.75h-.75v-.75z" />
             </svg>
          </div>
          <span class="font-bold tracking-wide font-mono text-sm">QR SCANNER SYSTEM</span>
        </div>
        
        <div class="flex items-center gap-2">
          <div class="flex items-center gap-2 px-3 py-1 bg-black/40 backdrop-blur-md rounded-full border border-white/10">
            <span v-if="!paused && !isProcessing" class="relative flex h-2.5 w-2.5">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
            </span>
            <span v-else class="h-2.5 w-2.5 rounded-full bg-red-500"></span>
            <span class="text-[10px] font-bold text-gray-300 uppercase">
                {{ isProcessing ? 'PROCESSING' : (paused ? 'PAUSED' : 'LIVE') }}
            </span>
          </div>
        </div>
      </div>

      <div class="relative flex-grow h-[500px] lg:h-auto overflow-hidden">
        <qrcode-stream 
          @detect="onDetect" 
          @error="onError"
          @camera-on="onCameraReady" 
          :paused="paused"
          :track="paintBoundingBox"
          class="h-full w-full object-cover"
        >
            <div v-if="cameraLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 z-10">
                 <div class="w-10 h-10 border-4 border-[#00555A] border-t-transparent rounded-full animate-spin mb-3"></div>
                 <p class="text-gray-400 text-xs font-mono uppercase tracking-widest">Inisialisasi Kamera...</p>
            </div>

            <div v-if="cameraError" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 z-50 p-6 text-center">
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-red-500 mb-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                 </svg>
                 <p class="text-white font-bold mb-1">Akses Kamera Gagal</p>
                 <p class="text-gray-400 text-sm mb-4">{{ cameraError }}</p>
                 <button @click="reloadPage" class="px-4 py-2 bg-[#F26430] text-white rounded-lg text-sm font-bold">Refresh Halaman</button>
            </div>

            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="relative w-64 h-64 border-2 border-white/30 rounded-3xl overflow-hidden">
                    <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-[#F26430] rounded-tl-lg"></div>
                    <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-[#F26430] rounded-tr-lg"></div>
                    <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-[#F26430] rounded-bl-lg"></div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-[#F26430] rounded-br-lg"></div>
                    
                    <div v-if="!paused" class="absolute w-full h-1 bg-gradient-to-r from-transparent via-[#F26430] to-transparent shadow-[0_0_15px_#F26430] animate-scan-line"></div>
                </div>
            </div>
        </qrcode-stream>
      </div>
      </div>

    <div class="w-full lg:w-1/2 flex flex-col">
      <div class="flex-grow bg-white rounded-3xl shadow-xl border border-gray-100 p-8 flex flex-col justify-center items-center text-center relative overflow-hidden">
        
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-[#00555A]/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-[#F26430]/5 rounded-full blur-3xl pointer-events-none"></div>

        <div v-if="isProcessing" class="relative z-10 w-full flex flex-col items-center animate-pulse">
            <div class="w-32 h-32 rounded-full bg-gray-100 mb-6 flex items-center justify-center">
                 <div class="w-16 h-16 border-4 border-[#00555A] border-t-transparent rounded-full animate-spin"></div>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Memverifikasi Data...</h3>
            <p class="text-gray-400">Mohon tunggu, sedang menghubungi server.</p>
        </div>

        <div v-else-if="scannedUser" class="relative z-10 w-full max-w-md animate-scale-up">
            <div class="relative inline-block mb-6 group">
                <div class="w-36 h-36 rounded-full p-1 bg-gradient-to-tr from-[#00555A] to-[#F26430] shadow-xl">
                    <img 
                        :src="scannedUser.foto" 
                        class="w-full h-full rounded-full object-cover border-4 border-white bg-white"
                        @error="handleImageError"
                        alt="Foto Profil"
                    />
                </div>
                <div class="absolute bottom-2 right-2 bg-green-500 text-white p-2 rounded-full border-4 border-white shadow-lg animate-bounce-small">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
            </div>

            <div class="space-y-1 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 capitalize leading-tight">
                {{ scannedUser.nama }}
                </h2>
                <p class="text-[#00555A] font-semibold text-lg">{{ scannedUser.prodi }}</p>
            </div>

            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 mb-8 w-full shadow-inner space-y-4">
                <div class="flex justify-between items-center border-b border-gray-200 pb-3">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Nomor Induk / NIM</span>
                    <span class="font-mono font-bold text-gray-700 text-lg">{{ scannedUser.nim }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-gray-200 pb-3">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Waktu Scan</span>
                    <span class="text-gray-700 font-medium">{{ scannedUser.waktu }} WITA</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Status</span>
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full uppercase tracking-wide flex items-center gap-1">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        Hadir / Masuk
                    </span>
                </div>
            </div>

            <button 
                @click="resetScan" 
                class="w-full bg-[#00555A] hover:bg-[#004448] text-white font-bold py-4 rounded-xl shadow-lg shadow-teal-900/20 transition-all active:scale-95 flex items-center justify-center gap-2 group"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span>Scan Pengunjung Berikutnya</span>
            </button>
        </div>

        <div v-else-if="scanError" class="relative z-10 flex flex-col items-center animate-shake w-full max-w-sm">
             <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mb-6 border-4 border-red-100">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-12 h-12 text-red-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
             </div>
             <h3 class="text-xl font-bold text-red-600 mb-2">Gagal Memproses</h3>
             <p class="text-gray-500 mb-6 text-center">{{ scanError }}</p>
             
             <button 
                @click="resetScan" 
                class="px-6 py-2 bg-white border-2 border-gray-200 hover:border-gray-400 text-gray-600 font-bold rounded-lg transition-colors"
            >
                Coba Lagi
            </button>
        </div>

        <div v-else class="relative z-10 flex flex-col items-center">
            <div class="w-24 h-24 bg-teal-50 rounded-full flex items-center justify-center mb-6 animate-pulse border-4 border-white shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-[#00555A]">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75zM16.5 19.5h.75v.75h-.75v-.75zM19.5 16.5h.75v.75h-.75v-.75z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Siap Memindai</h3>
            <p class="text-gray-500 max-w-xs mx-auto mb-6">
                Arahkan kode QR pengunjung ke kamera untuk melakukan check-in otomatis.
            </p>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '@/api'; 
import { QrcodeStream, setZXingModuleOverrides } from 'vue-qrcode-reader';

setZXingModuleOverrides({
  locateFile: (path, prefix) => {
    if (path.endsWith('.wasm')) {
      return '/wasm/zxing_reader.wasm';
    }
    return prefix + path;
  },
});

const paused = ref(false);
const isProcessing = ref(false);
const cameraLoading = ref(true);
const cameraError = ref('');
const scanError = ref('');
const scannedUser = ref(null);

const getStorageUrl = () => {
    const envURL = import.meta.env.VITE_API_BASE_URL;
    const baseUrl = envURL ? envURL.replace(/\/$/, '') : 'https://tamuq.my.id';
    return `${baseUrl}/storage`;
};

const getFullImageUrl = (path) => {
    if (!path) return `https://ui-avatars.com/api/?name=User&background=EBF2FA&color=00555A`;
    
    if (path.startsWith('http')) return path;
    
    const cleanPath = path.startsWith('/') ? path.substring(1) : path;
    const storageBase = getStorageUrl();
    
    return `${storageBase}/${cleanPath}`;
};

const handleImageError = (e) => {
    const name = scannedUser.value?.nama || 'User';
    e.target.src = `https://ui-avatars.com/api/?name=${name}&background=EBF2FA&color=00555A`;
};

const playSound = (type) => {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    
    osc.connect(gain);
    gain.connect(ctx.destination);
    
    if (type === 'success') {
        osc.frequency.value = 1000;
        osc.type = 'sine';
        gain.gain.setValueAtTime(0.1, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
        osc.start();
        osc.stop(ctx.currentTime + 0.5);
    } else {
        osc.frequency.value = 150;
        osc.type = 'sawtooth';
        gain.gain.setValueAtTime(0.1, ctx.currentTime);
        gain.gain.linearRampToValueAtTime(0.01, ctx.currentTime + 0.3);
        osc.start();
        osc.stop(ctx.currentTime + 0.3);
    }
};

const onCameraReady = () => {
    cameraLoading.value = false;
};

const paintBoundingBox = (detectedCodes, ctx) => {
  for (const detectedCode of detectedCodes) {
    const { boundingBox: { x, y, width, height } } = detectedCode
    ctx.lineWidth = 4
    ctx.strokeStyle = '#00555A'
    ctx.strokeRect(x, y, width, height)
  }
}

const onError = (err) => {
    cameraLoading.value = false;
    cameraError.value = err.message || "Camera permission denied";
}

const reloadPage = () => window.location.reload();

const onDetect = async (detectedCodes) => {
    if (paused.value || isProcessing.value) return;

    const rawValue = detectedCodes[0]?.rawValue;
    if (!rawValue) return;

    paused.value = true;
    isProcessing.value = true;
    cameraLoading.value = false;

    try {
        const response = await api.post('/kunjungan/scan', {
            qr_code: rawValue
        });

        const user = response.data.data || {};

        scannedUser.value = {
            nama: user.nama_lengkap || 'Unknown',
            nim: user.nomor_induk || '-', 
            prodi: user.prodi || '-',
            foto: getFullImageUrl(user.foto), 
            waktu: new Date().toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'})
        };

        playSound('success');

    } catch (error) {
        console.error("Scan Error:", error);
        
        let msg = "Terjadi kesalahan sistem";
        if (error.response) {
            msg = error.response.data.message || "QR Code tidak valid / User tidak ditemukan";
        } else if (error.request) {
            msg = "Tidak dapat terhubung ke server";
        }

        scanError.value = msg;
        playSound('error');

        setTimeout(() => {
            if(scanError.value) resetScan();
        }, 3000);

    } finally {
        isProcessing.value = false;
    }
};

const resetScan = () => {
    scannedUser.value = null;
    scanError.value = '';
    paused.value = false;
    isProcessing.value = false;
};
</script>

<style scoped>
@keyframes scan-line {
  0% { top: 0%; opacity: 0; }
  10% { opacity: 1; }
  90% { opacity: 1; }
  100% { top: 100%; opacity: 0; }
}
.animate-scan-line {
  animation: scan-line 2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}

@keyframes scale-up {
  0% { transform: scale(0.95); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}
.animate-scale-up {
  animation: scale-up 0.3s ease-out forwards;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  75% { transform: translateX(5px); }
}
.animate-shake {
  animation: shake 0.3s ease-in-out;
}

@keyframes bounce-small {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-3px); }
}
.animate-bounce-small {
    animation: bounce-small 2s infinite;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>