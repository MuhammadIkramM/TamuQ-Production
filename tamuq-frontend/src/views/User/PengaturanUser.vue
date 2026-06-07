<template>
  <div class="max-w-6xl mx-auto space-y-8 relative">
    
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-[#00555A] flex items-center gap-2">
          <Cog6ToothIcon class="w-8 h-8 text-[#F26430]" />
          Pengaturan Akun
        </h1>
        <p class="text-gray-500 text-sm mt-1">Kelola informasi profil dan keamanan akun Anda.</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 flex flex-col items-center text-center relative overflow-hidden">
          
          <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-br from-[#00555A] to-[#007F86]"></div>

          <div class="relative mt-8 group">
            <div class="w-32 h-32 rounded-full p-1 bg-white border-4 border-[#F26430]/20 shadow-lg overflow-hidden relative">
              <img 
                :src="previewUrl || user.foto_profil || defaultAvatar" 
                alt="Foto Profil" 
                class="w-full h-full object-cover rounded-full"
              />
            </div>
            
            <label class="absolute bottom-1 right-1 bg-[#F26430] hover:bg-[#D65020] text-white p-2.5 rounded-full cursor-pointer shadow-md transition-transform hover:scale-110 border-2 border-white group-hover:animate-bounce">
              <CameraIcon class="w-5 h-5" />
              <input type="file" @change="handleFotoSelect" accept="image/*" class="hidden" />
            </label>
          </div>

          <div class="mt-4">
            <h2 class="text-xl font-bold text-gray-800">{{ user.nama_lengkap || 'Memuat...' }}</h2>
            <p class="text-xs font-bold text-[#00555A] uppercase tracking-wider bg-[#EBF2FA] inline-block px-3 py-1 rounded-full mt-2 border border-[#00555A]/10">
              {{ user.role || 'Pengunjung' }}
            </p>
          </div>

          <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 translate-y-4" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <button 
              v-if="selectedFile"
              @click="uploadFoto"
              :disabled="isLoadingFoto"
              class="mt-6 w-full py-2.5 bg-[#00555A] hover:bg-[#004448] text-white rounded-xl font-semibold shadow-md text-sm flex justify-center items-center gap-2 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
            >
              <CheckIcon v-if="!isLoadingFoto" class="w-4 h-4" />
              <div v-else class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
              <span v-if="isLoadingFoto">Mengunggah...</span>
              <span v-else>Simpan Foto Baru</span>
            </button>
          </transition>
        </div>
      </div>

      <div class="lg:col-span-2 space-y-8">
        
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
          <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
            <UserCircleIcon class="w-6 h-6 text-[#F26430]" />
            <h3 class="font-bold text-[#00555A] text-lg">Data Diri</h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Nama Lengkap</label>
              <div class="relative">
                <input type="text" :value="user.nama_lengkap" disabled class="w-full pl-10 pr-4 py-3 bg-[#EBF2FA] border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed font-medium text-sm" />
                <UserIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <LockClosedIcon class="w-4 h-4 text-gray-300 absolute right-3 top-1/2 -translate-y-1/2" />
              </div>
            </div>

            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Nomor Induk / NIM</label>
              <div class="relative">
                <input type="text" :value="user.nomor_induk" disabled class="w-full pl-10 pr-4 py-3 bg-[#EBF2FA] border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed font-medium text-sm" />
                <IdentificationIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <LockClosedIcon class="w-4 h-4 text-gray-300 absolute right-3 top-1/2 -translate-y-1/2" />
              </div>
            </div>

            <div class="space-y-1 sm:col-span-2">
              <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Program Studi</label>
              <div class="relative">
                <input type="text" :value="user.prodi" disabled class="w-full pl-10 pr-4 py-3 bg-[#EBF2FA] border border-gray-200 rounded-xl text-gray-500 cursor-not-allowed font-medium text-sm" />
                <AcademicCapIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <LockClosedIcon class="w-4 h-4 text-gray-300 absolute right-3 top-1/2 -translate-y-1/2" />
              </div>
            </div>

            <div class="space-y-1 sm:col-span-2 mt-2">
              <label class="text-xs font-bold text-gray-400 uppercase tracking-wide text-[#00555A]">Email Pribadi (Opsional)</label>
              <div class="relative">
                <input 
                  type="email" 
                  v-model="formEmail" 
                  placeholder="Masukkan alamat email aktif Anda" 
                  class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] outline-none transition-all font-medium text-sm text-gray-700" 
                />
                <EnvelopeIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
              </div>
              <p class="text-xs text-gray-400 mt-1">Digunakan untuk menerima notifikasi persetujuan surat dari staff perpustakaan.</p>
            </div>
          </div>

          <div class="flex justify-end pt-6 border-t border-gray-100 mt-6">
             <button 
                @click="updateEmail"
                :disabled="isLoadingEmail"
                class="bg-[#EBF2FA] hover:bg-[#00555A] hover:text-white text-[#00555A] border border-[#00555A]/20 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed px-6 py-2.5 rounded-xl font-bold shadow-sm active:scale-95 text-sm flex items-center gap-2"
              >
                <div v-if="isLoadingEmail" class="w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></div>
                <span v-if="isLoadingEmail">Menyimpan...</span>
                <span v-else>Simpan Data Diri</span>
              </button>
          </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8">
          <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
            <ShieldCheckIcon class="w-6 h-6 text-[#F26430]" />
            <h3 class="font-bold text-[#00555A] text-lg">Keamanan</h3>
          </div>

          <div class="space-y-5">
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Password Saat Ini</label>
              <div class="relative">
                <input 
                  type="password" 
                  v-model="passwordLama" 
                  placeholder="Masukkan password saat ini"
                  class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] outline-none transition-all text-sm placeholder-gray-300 text-gray-700"
                />
                <LockClosedIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              
              <div class="space-y-1">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Password Baru</label>
                <div class="relative">
                  <input 
                    type="password" 
                    v-model="passwordBaru" 
                    placeholder="Minimal 8 karakter"
                    class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] outline-none transition-all text-sm placeholder-gray-300 text-gray-700"
                  />
                  <KeyIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                </div>
              </div>
              
              <div class="space-y-1">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Konfirmasi Password</label>
                <div class="relative">
                  <input 
                    type="password" 
                    v-model="konfirmasiPassword" 
                    placeholder="Ulangi password"
                    class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] outline-none transition-all text-sm placeholder-gray-300 text-gray-700"
                  />
                  <KeyIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                </div>
              </div>
            </div>

            <div class="flex justify-end pt-2">
              <button 
                @click="updatePassword"
                :disabled="isLoadingPass"
                class="bg-[#F26430] hover:bg-[#D65020] disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-orange-100 active:scale-95 transition-all text-sm flex items-center gap-2"
              >
                <div v-if="isLoadingPass" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                <span v-if="isLoadingPass">Memproses...</span>
                <span v-else>Update Password</span>
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>

    <transition name="modal-fade">
      <div v-if="modal.show" class="fixed inset-0 z-[60] flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-[#00282a]/60 backdrop-blur-sm transition-opacity" @click="closeModalIfAllowed"></div>
        
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm relative z-10 overflow-hidden transform transition-all scale-100">
          <div class="p-8 text-center">
            
            <div 
              class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6"
              :class="modalIconClass"
            >
              <component :is="modalIcon" class="w-10 h-10" />
            </div>
            
            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ modal.title }}</h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-8">
              {{ modal.message }}
            </p>
            
            <button 
              @click="handleModalAction" 
              class="w-full py-3.5 text-white rounded-xl font-bold shadow-lg transition-all active:scale-95"
              :class="modalButtonClass"
            >
              {{ modal.confirmText }}
            </button>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import api from "../../api"; 
import { 
  Cog6ToothIcon, CameraIcon, UserCircleIcon, UserIcon, IdentificationIcon,
  AcademicCapIcon, LockClosedIcon, ShieldCheckIcon, KeyIcon, CheckIcon,
  CheckCircleIcon, XCircleIcon, ExclamationTriangleIcon, QuestionMarkCircleIcon, EnvelopeIcon
} from "@heroicons/vue/24/outline";

const user = ref({});
const formEmail = ref(""); 

const defaultAvatar = "https://ui-avatars.com/api/?name=User&background=00555A&color=fff";
const isLoadingFoto = ref(false);
const isLoadingPass = ref(false);
const isLoadingEmail = ref(false); 

const selectedFile = ref(null);
const previewUrl = ref(null);

const passwordLama = ref("");
const passwordBaru = ref("");
const konfirmasiPassword = ref("");

const modal = ref({
  show: false,
  type: 'info', 
  title: '',
  message: '',
  confirmText: 'OK'
});

const openModal = (type, title, message, confirmText = 'OK') => {
  modal.value = { show: true, type, title, message, confirmText };
};

const closeModalIfAllowed = () => {
  modal.value.show = false;
};

const handleModalAction = () => {
  modal.value.show = false;
};

const modalIcon = computed(() => {
  switch (modal.value.type) {
    case 'success': return CheckCircleIcon;
    case 'error': return XCircleIcon;
    default: return ExclamationTriangleIcon;
  }
});

const modalIconClass = computed(() => {
  switch (modal.value.type) {
    case 'success': return 'bg-green-100 text-green-600';
    case 'error': return 'bg-red-100 text-red-600';
    default: return 'bg-gray-100 text-gray-600';
  }
});

const modalButtonClass = computed(() => {
  switch (modal.value.type) {
    case 'success': return 'bg-[#00555A] hover:bg-[#004448] shadow-teal-500/30';
    case 'error': return 'bg-red-500 hover:bg-red-600 shadow-red-500/30';
    default: return 'bg-gray-800 hover:bg-gray-900';
  }
});

const fetchUser = async () => {
    try {
        const response = await api.get('/user');
        user.value = response.data;
        formEmail.value = response.data.email || ""; 
    } catch (error) {
        console.error("Gagal mengambil data user", error);
    }
};

const handleFotoSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        const fileSizeMB = file.size / (1024 * 1024);
        if (fileSizeMB > 5) {
            openModal('error', 'Ukuran File Terlalu Besar', 'Maksimal ukuran foto adalah 5MB. Silakan pilih foto lain.', 'Mengerti');
            event.target.value = ""; 
            return;
        }

        selectedFile.value = file;
        previewUrl.value = URL.createObjectURL(file);
    }
};

const updateEmail = async () => {
    isLoadingEmail.value = true;
    const formData = new FormData();
    
    formData.append('nama_lengkap', user.value.nama_lengkap);
    if(formEmail.value) {
       formData.append('email', formEmail.value);
    }

    try {
        const response = await api.post('/user/update-profile', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if(response.data.user) {
            user.value.email = response.data.user.email;
        }

        openModal('success', 'Data Diperbarui', 'Alamat email Anda berhasil disimpan.', 'Mantap');
    } catch (error) {
        console.error("Gagal menyimpan email:", error);
        const msg = error.response?.data?.message || "Gagal menyimpan perubahan. Silakan coba lagi.";
        openModal('error', 'Gagal', msg, 'Tutup');
    } finally {
        isLoadingEmail.value = false;
    }
};

const uploadFoto = async () => {
    if (!selectedFile.value) return;
    
    isLoadingFoto.value = true;
    const formData = new FormData();
    
    formData.append('nama_lengkap', user.value.nama_lengkap);
    formData.append('foto', selectedFile.value); 

    try {
        const response = await api.post('/user/update-profile', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if(response.data.user) {
            user.value.foto_profil = response.data.user.foto_profil;
        }

        selectedFile.value = null;
        previewUrl.value = null;
        
        openModal('success', 'Foto Diperbarui', 'Foto profil Anda berhasil diubah.', 'Mantap');
        fetchUser(); 

    } catch (error) {
        console.error("Upload error:", error);
        
        let msg = "Gagal mengupload foto.";
        if (error.response?.status === 422 && error.response.data?.errors) {
            msg = Object.values(error.response.data.errors).flat()[0]; 
        } else {
            msg = error.response?.data?.message || msg;
        }
        
        openModal('error', 'Gagal Upload', msg, 'Coba Lagi');
    } finally {
        isLoadingFoto.value = false;
    }
};

const updatePassword = async () => {
    if (!passwordLama.value) {
        openModal('error', 'Form Belum Lengkap', 'Harap masukkan password saat ini untuk verifikasi.', 'Oke');
        return;
    }
    if (!passwordBaru.value || !konfirmasiPassword.value) {
        openModal('error', 'Form Belum Lengkap', 'Harap isi password baru dan konfirmasinya.', 'Oke');
        return;
    }
    if (passwordBaru.value.length < 8) {
        openModal('error', 'Password Terlalu Pendek', 'Password baru minimal 8 karakter demi keamanan.', 'Oke');
        return;
    }
    if (passwordBaru.value !== konfirmasiPassword.value) {
        openModal('error', 'Password Tidak Cocok', 'Konfirmasi password tidak sama dengan password baru.', 'Cek Lagi');
        return;
    }

    isLoadingPass.value = true;
    try {
        await api.post('/user/update-password', {
            current_password: passwordLama.value,
            new_password: passwordBaru.value,
            new_password_confirmation: konfirmasiPassword.value
        });

        openModal('success', 'Password Diperbarui', 'Password akun Anda berhasil diubah. Silakan gunakan password baru untuk login selanjutnya.', 'Siap');
        
        passwordLama.value = "";
        passwordBaru.value = "";
        konfirmasiPassword.value = "";

    } catch (error) {
        console.error("Password error:", error);
        
        let msg = "Gagal memperbarui password.";
        if (error.response?.status === 422 && error.response.data?.errors) {
            msg = Object.values(error.response.data.errors).flat()[0]; 
        } else if (error.response?.status === 400) {
            msg = error.response.data.message;
        } else {
            msg = error.response?.data?.message || msg;
        }
        
        openModal('error', 'Gagal Update Password', msg, 'Coba Lagi');
    } finally {
        isLoadingPass.value = false;
    }
};

onMounted(() => {
    fetchUser();
});
</script>

<style scoped>
.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.3s ease; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
.modal-fade-enter-active .transform, .modal-fade-leave-active .transform { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
.modal-fade-enter-from .transform, .modal-fade-leave-to .transform { transform: scale(0.95) translateY(10px); }
</style>