<template>
  <div class="min-h-screen flex w-full bg-[#EBF2FA] shadow-xl overflow-hidden">
    
    <div class="hidden md:flex md:w-1/2 bg-[#00555A] relative flex-col justify-between p-12 text-white overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-[#00555A] to-[#004448] opacity-90 z-0"></div>
      <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 z-0"></div>
      
      <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#F26430] rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
      <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-[#EBF2FA] rounded-full mix-blend-overlay filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>

      <div class="relative z-10">
        <div class="flex items-center gap-3">
          <router-link to="/login" class="w-10 h-10 bg-white/10 backdrop-blur rounded-lg flex items-center justify-center border border-white/20 hover:bg-white/30 transition-all cursor-pointer group" title="Kembali ke Login">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#F26430] transform transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
          </router-link>
          <span class="font-bold tracking-wider text-[#EBF2FA]">REGISTER</span>
        </div>
      </div>

      <div class="relative z-10 mb-10">
        <h1 class="text-5xl font-extrabold mb-4 leading-tight">
          Bergabung dengan <br/>
          <span class="text-[#F26430]">Kami</span>
        </h1>
        <p class="text-[#EBF2FA] text-lg font-light max-w-md">
          Daftarkan diri Anda untuk mengakses layanan perpustakaan dan fasilitas lainnya.
        </p>
      </div>
      
      <div class="relative z-10 text-sm text-[#EBF2FA]/60 font-mono">
        &copy; 2024 TamuQ By Ikrams. All rights reserved.
      </div>
    </div>

    <div class="w-full md:w-1/2 flex flex-col justify-center items-center p-8 md:p-12 bg-[#EBF2FA] relative overflow-y-auto">
      
      <div v-if="isLoading" class="absolute inset-0 bg-[#EBF2FA]/80 backdrop-blur-sm z-50 flex flex-col items-center justify-center">
        <div class="w-12 h-12 border-4 border-[#00555A]/20 border-t-[#F26430] rounded-full animate-spin mb-4"></div>
        <span class="text-[#00555A] font-medium animate-pulse">Mendaftarkan Akun...</span>
      </div>

      <div class="w-full max-w-md space-y-6 bg-white p-8 rounded-3xl shadow-xl border border-white">
        
        <div class="md:hidden text-center mb-6">
          <img :src="logo" class="w-16 h-16 mx-auto mb-4 rounded-full shadow-lg border-2 border-[#F26430]" />
          <h2 class="text-2xl font-bold text-[#00555A]">Buat Akun Baru</h2>
        </div>

        <div class="hidden md:block">
           <h2 class="text-3xl font-bold text-[#00555A]">Registrasi Akun</h2>
           <p class="text-slate-500 mt-2">Lengkapi data diri Anda dengan benar.</p>
        </div>

        <div v-if="errorMessage" class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg animate-fade-in-down">
          <p class="text-sm text-red-700">{{ errorMessage }}</p>
        </div>

        <form @submit.prevent="onSubmit" class="space-y-5">
          <div>
            <label class="block text-sm font-semibold text-[#00555A] mb-1">Nama Lengkap</label>
            <input v-model="form.name" type="text" placeholder="Masukkan nama lengkap" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:outline-none bg-white transition-all" required />
          </div>

          <div>
            <label class="block text-sm font-semibold text-[#00555A] mb-1">Nomor Induk / NIM</label>
            <input v-model="form.nomorInduk" type="text" placeholder="Contoh: 210209501" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:outline-none bg-white transition-all" pattern="[0-9]+" title="Hanya angka diperbolehkan" required />
          </div>

          <div>
            <label class="block text-sm font-semibold text-[#00555A] mb-1">Program Studi</label>
            <div class="relative">
              <select v-model="form.prodi" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:outline-none bg-white appearance-none cursor-pointer" required>
                <option disabled value="">Pilih Program Studi</option>
                <option value="PTIK">Pendidikan Teknik Informatika & Komputer (PTIK)</option>
                <option value="TEKOM">Teknik Komputer (TEKOM)</option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </div>
          </div>

          <div>
              <label class="block text-sm font-semibold text-[#00555A] mb-1">Role Akun</label>
              <div class="w-full px-4 py-3 border border-[#00555A]/20 bg-[#EBF2FA] text-[#00555A] rounded-xl flex items-center gap-2">
                 <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                 <span class="font-medium">Pengunjung</span>
              </div>
              <p class="text-xs text-slate-400 mt-1 ml-1">*Pendaftaran otomatis diset sebagai Pengunjung.</p>
          </div>

          <div>
            <label class="block text-sm font-semibold text-[#00555A] mb-1">Kata Sandi</label>
            <div class="relative">
              <input v-model="form.password" :type="showPassword ? 'text' : 'password'" placeholder="Buat kata sandi aman" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:outline-none pr-10 bg-white" required />
              <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-[#F26430] cursor-pointer">
                <span v-if="!showPassword" class="text-xs font-bold">SHOW</span>
                <span v-else class="text-xs font-bold">HIDE</span>
              </button>
            </div>
          </div>

          <button type="submit" :disabled="isLoading" class="w-full bg-[#F26430] text-white py-3.5 rounded-xl font-bold hover:bg-[#d9521e] transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed mt-2">
            <span v-if="!isLoading">Daftar Sekarang</span>
            <span v-else>Sedang Memproses...</span>
          </button>

          <p class="text-center text-sm text-slate-500 mt-6">
            Sudah terdaftar?
            <router-link to="/login" class="text-[#00555A] font-bold hover:underline">Masuk disini</router-link>
          </p>

        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import logo from "@/assets/tamuqicon.png";

defineProps({
  isLoading: Boolean,
  errorMessage: String
});

const emit = defineEmits(['submit-register']);
const showPassword = ref(false);

const form = reactive({
  name: "",
  nomorInduk: "",
  prodi: "",
  password: "",
  role: "pengunjung"
});

const onSubmit = () => {
  const payload = {
    name: form.name,
    nomor_induk: form.nomorInduk,
    prodi: form.prodi,
    password: form.password,
    role: form.role
  };

  emit('submit-register', payload);
};
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
</style>