<template>
  <div class="space-y-6 animate-fade-in-up relative">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-[#00555A] flex items-center gap-2 tracking-tight">
          <Cog6ToothIcon class="w-8 h-8 text-[#F26430]" />
          Pengaturan Akun Staff
        </h1>
        <p class="text-gray-500 text-sm mt-1 ml-1">Kelola profil pribadi dan keamanan akun Anda.</p>
      </div>
      
      <div class="hidden md:block">
         <button 
          @click="saveChanges" 
          :disabled="isLoading"
          class="flex items-center gap-2 px-6 py-2.5 bg-[#F26430] hover:bg-[#D65020] text-white rounded-xl transition-all duration-200 text-sm font-medium shadow-lg shadow-[#F26430]/30 hover:shadow-[#F26430]/40 disabled:opacity-70 disabled:cursor-not-allowed transform active:scale-95"
        >
          <span v-if="!isLoading">Simpan Perubahan</span>
          <span v-else class="flex items-center gap-2">
            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Menyimpan...
          </span>
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col items-center text-center relative overflow-hidden group">
          
          <div class="absolute top-0 left-0 w-full h-28 bg-gradient-to-br from-[#00555A] to-[#004044]"></div>
          
          <div class="relative mt-10 mb-4 cursor-pointer">
            <div class="w-28 h-28 rounded-full border-[5px] border-white bg-white flex items-center justify-center shadow-lg overflow-hidden relative z-10">
              <img 
                :src="previewUrl || formProfile.avatarUrl || `https://ui-avatars.com/api/?name=${encodeURIComponent(formProfile.nama || 'User')}&background=F26430&color=fff`" 
                class="w-full h-full object-cover" 
                alt="Foto Profil"
               />
            </div>
            
            <label class="absolute inset-0 z-20 rounded-full flex items-center justify-center transition-all duration-300 cursor-pointer group hover:bg-black/30">
               <div class="opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                  <div class="bg-white/20 backdrop-blur-sm p-2 rounded-full border border-white/50">
                    <CameraIcon class="w-6 h-6 text-white" />
                  </div>
               </div>
              <input type="file" @change="handleFotoSelect" accept="image/*" class="hidden" />
            </label>
          </div>

          <h2 class="text-xl font-bold text-[#00555A] tracking-tight">{{ formProfile.nama || 'Memuat...' }}</h2>
          <span class="inline-flex mt-2 items-center px-3 py-1 bg-[#F26430]/10 text-[#F26430] rounded-full text-xs font-bold uppercase tracking-wide border border-[#F26430]/20">
            {{ formProfile.role }}
          </span>

          <div class="mt-8 w-full space-y-4">
             <div class="flex justify-between items-center text-sm border-b border-gray-100 pb-3">
               <span class="text-gray-500 font-medium">Nomor Induk / NIP</span> <span class="font-mono text-[#00555A] font-bold bg-[#EBF2FA] px-2 py-1 rounded">{{ formProfile.nip }}</span>
             </div>
          </div>
        </div>
      </div>

      <div class="lg:col-span-2 space-y-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="p-5 bg-[#00555A] flex items-center justify-between">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
              <UserCircleIcon class="w-5 h-5 text-[#F26430]" />
              Informasi Pribadi
            </h3>
          </div>
          <div class="p-6 space-y-5">
            <div class="space-y-2">
              <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap</label>
              <input 
                type="text" 
                v-model="formProfile.nama" 
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm text-[#00555A] outline-none transition-all duration-200"
                placeholder="Masukkan nama lengkap"
              />
              <p class="text-xs text-gray-400">Nama ini akan ditampilkan di seluruh sistem.</p>
            </div>
            
            <div class="space-y-2">
              <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Email (Opsional)</label>
              <div class="relative">
                <input 
                  type="email" 
                  v-model="formProfile.email" 
                  class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm text-[#00555A] outline-none transition-all duration-200"
                  placeholder="Masukkan alamat email aktif"
                />
                <EnvelopeIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
              </div>
              <p class="text-xs text-gray-400">Digunakan untuk notifikasi surat atau reset password.</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="p-5 bg-[#00555A] flex justify-between items-center">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
              <KeyIcon class="w-5 h-5 text-[#F26430]" />
              Keamanan Password
            </h3>
          </div>
          <div class="p-6 space-y-5">
             <div class="space-y-2">
               <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Password Saat Ini</label>
               <input type="password" v-model="security.currentPassword" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm outline-none" placeholder="********" />
             </div>
             <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
               <div class="space-y-2">
                 <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Password Baru</label>
                 <input type="password" v-model="security.newPassword" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm outline-none" placeholder="Minimal 8 karakter" />
               </div>
               <div class="space-y-2">
                 <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Konfirmasi Password</label>
                 <input type="password" v-model="security.confirmPassword" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm outline-none" placeholder="Ulangi password baru" />
               </div>
             </div>
             <div class="flex justify-end pt-2">
               <button @click="updatePassword" :disabled="isLoadingPass" class="px-5 py-2.5 bg-white border border-gray-200 text-[#00555A] hover:text-[#F26430] hover:border-[#F26430] hover:bg-[#F26430]/5 rounded-xl text-sm font-semibold transition-all duration-200 shadow-sm active:scale-95 flex items-center gap-2">
                 <span v-if="isLoadingPass" class="animate-spin h-4 w-4 border-2 border-[#F26430] border-t-transparent rounded-full"></span>
                 {{ isLoadingPass ? 'Memproses...' : 'Update Password' }}
               </button>
             </div>
          </div>
        </div>

        <div class="md:hidden pt-4 pb-8">
          <button @click="saveChanges" :disabled="isLoading" class="w-full py-3.5 bg-[#F26430] text-white rounded-xl font-bold shadow-lg shadow-[#F26430]/30 active:scale-95 transition-all disabled:opacity-70 flex justify-center items-center gap-2">
            <span v-if="!isLoading">Simpan Perubahan</span>
            <span v-else>Menyimpan...</span>
          </button>
        </div>

      </div>
    </div>

    <transition name="slide-fade">
      <div v-if="alert.show" class="fixed top-5 right-5 z-[60] flex items-center gap-3 bg-white p-4 rounded-xl shadow-2xl border-l-4 w-full max-w-sm"
           :class="alert.type === 'success' ? 'border-green-500' : 'border-red-500'">
        <div class="flex-shrink-0">
          <CheckCircleIcon v-if="alert.type === 'success'" class="w-8 h-8 text-green-500" />
          <ExclamationCircleIcon v-else class="w-8 h-8 text-red-500" />
        </div>
        <div>
          <h4 class="font-bold text-sm" :class="alert.type === 'success' ? 'text-green-800' : 'text-red-800'">
            {{ alert.title }}
          </h4>
          <p class="text-xs text-gray-600 mt-0.5">{{ alert.message }}</p>
        </div>
        <button @click="alert.show = false" class="ml-auto text-gray-400 hover:text-gray-600">
          <XMarkIcon class="w-5 h-5" />
        </button>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import api from '@/api';
import { 
  Cog6ToothIcon, UserCircleIcon, KeyIcon, CameraIcon, 
  CheckCircleIcon, ExclamationCircleIcon, XMarkIcon, EnvelopeIcon
} from "@heroicons/vue/24/outline";

const isLoading = ref(false);
const isLoadingPass = ref(false);
const previewUrl = ref(null); 
const selectedFile = ref(null); 

const formProfile = reactive({
  nama: "",
  email: "",
  role: "",
  nip: "",
  avatarUrl: null 
});

const security = reactive({
  currentPassword: "",
  newPassword: "",
  confirmPassword: ""
});

const alert = ref({
  show: false,
  type: 'success', // 'success' | 'error'
  title: '',
  message: ''
});

const showAlert = (type, title, message) => {
  alert.value = { show: true, type, title, message };
  setTimeout(() => {
    alert.value.show = false;
  }, 3000); 
};

const getFullImageUrl = (path) => {
  if (!path) return null;
  if (path.startsWith('http')) return path;
  const envURL = import.meta.env.VITE_STORAGE_URL || 'http://127.0.0.1:8000/storage';
  return `${envURL}/${path}`;
};

const fetchUser = async () => {
    try {
        const response = await api.get('/user');
        const user = response.data;
        
        formProfile.nama = user.nama_lengkap;
        formProfile.email = user.email || ""; 
        formProfile.role = user.role;
        formProfile.nip = user.nomor_induk; 
        formProfile.avatarUrl = getFullImageUrl(user.foto_profil);
        
    } catch (error) {
        console.error("Gagal load user", error);
        showAlert('error', 'Gagal Memuat', 'Tidak dapat mengambil data profil pengguna.');
    }
};

const handleFotoSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (!file.type.match('image.*')) {
            showAlert('error', 'Format Salah', 'Mohon pilih file gambar (JPG/PNG).');
            return;
        }
        
        if (file.size > 5 * 1024 * 1024) {
            showAlert('error', 'File Terlalu Besar', 'Ukuran file maksimal 5MB.');
            event.target.value = ''; 
            return;
        }

        selectedFile.value = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const saveChanges = async () => {
  isLoading.value = true;
  const formData = new FormData();

  formData.append('nama_lengkap', formProfile.nama);
  if(formProfile.email !== null) {
      formData.append('email', formProfile.email); 
  }
  
  if (selectedFile.value) {
      formData.append('foto', selectedFile.value); 
  }

  try {
    const response = await api.post('/user/update-profile', formData, {
        headers: {
            'Content-Type': 'multipart/form-data' 
        }
    });

    const user = response.data.user || response.data; 
    
    if (user.nama_lengkap) formProfile.nama = user.nama_lengkap;
    if (user.email !== undefined) formProfile.email = user.email;
    if (user.foto_profil) formProfile.avatarUrl = getFullImageUrl(user.foto_profil);
    
    selectedFile.value = null;
    previewUrl.value = null;

    showAlert('success', 'Berhasil Disimpan', 'Data profil Anda telah diperbarui.');

  } catch (error) {
    console.error(error);
    const msg = error.response?.data?.message || "Cek koneksi atau format file.";
    showAlert('error', 'Gagal Menyimpan', msg);
  } finally {
    isLoading.value = false;
  }
};

const updatePassword = async () => {
  if (security.newPassword !== security.confirmPassword) {
    showAlert('error', 'Validasi Gagal', 'Password baru dan konfirmasi tidak cocok!');
    return;
  }
  if (!security.currentPassword) {
    showAlert('error', 'Validasi Gagal', 'Masukkan password saat ini untuk verifikasi.');
    return;
  }
  
  isLoadingPass.value = true;
  try {
    await api.post('/user/update-password', {
        current_password: security.currentPassword,
        new_password: security.newPassword,               
        new_password_confirmation: security.confirmPassword 
    });
    
    showAlert('success', 'Password Diperbarui', 'Password akun Anda berhasil diubah.');
    
    security.currentPassword = "";
    security.newPassword = "";
    security.confirmPassword = "";
  } catch (error) {
    console.error(error);
    const msg = error.response?.data?.message || "Gagal update password.";
    showAlert('error', 'Gagal', msg);
  } finally {
    isLoadingPass.value = false;
  }
};

onMounted(() => {
    fetchUser();
});
</script>

<style scoped>
.animate-fade-in-up {
  animation: fadeInUp 0.5s ease-out;
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-leave-active { transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1); }
.slide-fade-enter-from, .slide-fade-leave-to { transform: translateX(20px); opacity: 0; }
</style>