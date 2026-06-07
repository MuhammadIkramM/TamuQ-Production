<template>
  <div class="min-h-screen flex w-full bg-[#EBF2FA] shadow-xl overflow-hidden relative">
    
    <div class="hidden md:flex md:w-1/2 bg-[#00555A] relative flex-col justify-between p-12 text-white overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-[#00555A] to-[#004448] opacity-90 z-0"></div>
      <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 z-0"></div>
      
      <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#F26430] rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
      <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-[#EBF2FA] rounded-full mix-blend-overlay filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>

      <div class="relative z-10">
        <div class="flex items-center gap-3">
          <router-link to="/" class="w-10 h-10 bg-white/10 backdrop-blur rounded-lg flex items-center justify-center border border-white/20 hover:bg-white/30 transition-all cursor-pointer group" title="Kembali ke Beranda">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#F26430] transform transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </router-link>
          <span class="font-bold tracking-wider text-[#EBF2FA]">LOGIN</span>
        </div>
      </div>

      <div class="relative z-10 mb-10">
        <h1 class="text-5xl font-extrabold mb-4 leading-tight">
          Selamat Datang di <br/>
          <span class="text-[#F26430]">TamuQ</span>
        </h1>
        <p class="text-[#EBF2FA] text-lg font-light max-w-md">
          Sistem Informasi Buku Tamu & Layanan Akademik Jurusan Teknik Informatika dan Komputer.
        </p>
      </div>
      
      <div class="relative z-10 text-sm text-[#EBF2FA]/60 font-mono">
        &copy; 2024 TamuQ By Ikrams. All rights reserved.
      </div>
    </div>

    <div class="w-full md:w-1/2 flex flex-col justify-center items-center p-8 md:p-16 bg-[#EBF2FA] relative">
      
      <div v-if="isLoading" class="absolute inset-0 bg-[#EBF2FA]/80 backdrop-blur-sm z-50 flex flex-col items-center justify-center">
        <div class="w-12 h-12 border-4 border-[#00555A]/20 border-t-[#F26430] rounded-full animate-spin mb-4"></div>
        <span class="text-[#00555A] font-medium animate-pulse">Memverifikasi Akun...</span>
      </div>

      <div class="w-full max-w-md space-y-8 bg-white p-8 rounded-3xl shadow-xl border border-white">
        
        <div class="md:hidden text-center mb-6">
          <img :src="logo" class="w-20 h-20 mx-auto mb-4 rounded-full shadow-lg border-2 border-[#F26430]" />
          <h2 class="text-3xl font-bold text-[#00555A]">TamuQ</h2>
          <p class="text-slate-500 text-sm">Masuk untuk melanjutkan</p>
        </div>

        <div class="hidden md:block">
           <h2 class="text-3xl font-bold text-[#00555A]">Masuk ke Akun</h2>
           <p class="text-slate-500 mt-2">Silakan masukkan kredensial Anda.</p>
        </div>

        <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg animate-fade-in-down">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            </div>
            <div class="ml-3">
              <p class="text-sm text-red-700">{{ errorMessage }}</p>
            </div>
          </div>
        </div>

        <form @submit.prevent="onSubmit" class="space-y-6">
          <div>
            <label for="nomorInduk" class="block text-sm font-semibold text-[#00555A] mb-2">Nomor Induk / NIM</label>
            <input id="nomorInduk" v-model="form.nomorInduk" type="text" required class="appearance-none block w-full px-4 py-3 border border-slate-300 rounded-xl shadow-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] sm:text-sm bg-white transition-all" placeholder="Contoh: 12345" />
          </div>

          <div>
             <label for="password" class="block text-sm font-semibold text-[#00555A] mb-2">Kata Sandi</label>
            <div class="relative rounded-md shadow-sm">
              <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" required class="appearance-none block w-full px-4 py-3 border border-slate-300 rounded-xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] sm:text-sm bg-white transition-all pr-10" placeholder="••••••••" />
              <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-[#F26430] transition-colors cursor-pointer">
                <span v-if="!showPassword" class="text-xs font-semibold">SHOW</span>
                <span v-else class="text-xs font-semibold">HIDE</span>
              </button>
            </div>
          </div>

          <div>
            <button type="submit" :disabled="isLoading" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-[#F26430] hover:bg-[#d9521e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F26430] disabled:opacity-50 disabled:cursor-not-allowed transform transition hover:-translate-y-0.5">
              <span v-if="isLoading">Memproses...</span>
              <span v-else>Masuk Sekarang</span>
            </button>
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-slate-500">Belum memiliki akun?</span>
            </div>
          </div>

          <div class="mt-6 grid grid-cols-1 gap-3">
             <router-link to="/register" class="w-full inline-flex justify-center py-3 px-4 border border-[#00555A]/30 rounded-xl shadow-sm bg-white text-sm font-bold text-[#00555A] hover:bg-[#EBF2FA] transition-colors">
              Daftar Akun Baru
            </router-link>
            
            <div class="pt-2 flex justify-center">
                <button type="button" @click="showForgotPasswordModal = true" class="flex items-center gap-2 group p-2 rounded-lg hover:bg-slate-50 transition-all cursor-pointer">
                  <div class="relative w-6 h-6">
                    <svg class="w-full h-full text-[#00555A] animate-ponder" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <circle cx="12" cy="12" r="10" class="opacity-90"/>
                      <path d="M9 9h.01" stroke-linecap="round"/>
                      <path d="M15 9h.01" stroke-linecap="round"/>
                      <path d="M9 16c1.5-1 4.5-1 6 0" stroke-linecap="round"/>
                    </svg>
                    <div class="absolute -top-2 -right-2 text-[#F26430] font-bold text-xs animate-float-question-1">?</div>
                  </div>
                  <span class="text-sm font-medium text-slate-500 group-hover:text-[#00555A] group-hover:underline transition-colors">Butuh Bantuan Login?</span>
                </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showForgotPasswordModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-[#001021]/60 backdrop-blur-sm transition-opacity" @click="showForgotPasswordModal = false"></div>
      <div class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 transform transition-all animate-fade-in-down z-10 border-t-4 border-[#F26430]">
        
        <button @click="showForgotPasswordModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-[#00555A]">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-[#EBF2FA] mb-4">
           <svg class="h-8 w-8 text-[#00555A] animate-ponder-modal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <circle cx="12" cy="12" r="10" class="opacity-90"/>
              <path d="M9 9h.01" stroke-linecap="round"/>
              <path d="M15 9h.01" stroke-linecap="round"/>
              <path d="M9 16c1.5-1 4.5-1 6 0" stroke-linecap="round"/>
           </svg>
        </div>

        <div class="text-center">
          <h3 class="text-lg leading-6 font-bold text-[#00555A] mb-2">Pusat Bantuan Akun</h3>
          <p class="text-sm text-slate-500 mb-6">Mengalami masalah login, lupa kata sandi, atau akun belum aktif? Silakan hubungi Admin JTIK.</p>
          
          <a :href="`https://wa.me/${adminWhatsApp}?text=Halo%20Admin%20JTIK,%20saya%20butuh%20bantuan%20terkait%20login%20akun%20TamuQ.`" target="_blank" class="inline-flex items-center justify-center w-full px-4 py-3 bg-[#25D366] hover:bg-[#20bd5a] text-white text-sm font-bold rounded-lg shadow-md transition-all">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
            Hubungi Admin via WhatsApp
          </a>

          <button @click="showForgotPasswordModal = false" class="mt-3 text-xs text-slate-400 hover:text-[#F26430] underline">Batal, kembali ke login</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, onUnmounted } from "vue";
import logo from "@/assets/tamuqicon.png"; 

defineProps({
  isLoading: Boolean,
  errorMessage: String
});

const emit = defineEmits(['submit-login']);
const showPassword = ref(false);
const showForgotPasswordModal = ref(false);
const adminWhatsApp = "6285395941858"; 

const form = reactive({
  nomorInduk: "",
  password: "",
});

const onSubmit = () => {
  const payload = {
    nomor_induk: form.nomorInduk,
    password: form.password
  };
  
  emit('submit-login', payload);
};

let metaRobots = null;

onMounted(() => {
  metaRobots = document.createElement('meta');
  metaRobots.name = 'robots';
  metaRobots.content = 'noindex, nofollow';
  document.head.appendChild(metaRobots);
});

onUnmounted(() => {
  if (metaRobots && document.head.contains(metaRobots)) {
    document.head.removeChild(metaRobots);
  }
});
</script>

<style scoped>
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}
.animate-blob { animation: blob 7s infinite; }
.animation-delay-2000 { animation-delay: 2s; }
.animate-fade-in-down { animation: fadeInDown 0.3s ease-out; }

@keyframes fadeInDown {
    from { opacity: 0; transform: translate3d(0, -20px, 0); }
    to { opacity: 1; transform: translate3d(0, 0, 0); }
}

@keyframes ponder { 0%, 100% { transform: rotate(-3deg); } 50% { transform: rotate(3deg); } }
.animate-ponder { animation: ponder 2s ease-in-out infinite; transform-origin: center; }

@keyframes ponderModal { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
.animate-ponder-modal { animation: ponderModal 2s ease-in-out infinite; }

@keyframes floatQuestion { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-3px); } }
.animate-float-question-1 { animation: floatQuestion 2s ease-in-out infinite; }
</style>