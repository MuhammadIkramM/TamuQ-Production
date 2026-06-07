<template>
  <div class="max-w-5xl mx-auto space-y-8 relative">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-[#00555A] flex items-center gap-2">
          <GiftIcon class="w-8 h-8 text-[#F26430]" />
          Pengajuan Bukti Penyerahan Donasi Buku
        </h1>
        <p class="text-gray-500 text-sm mt-1">
          Isi formulir & upload bukti buku untuk mendapatkan tanda terima.
        </p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
          
          <div class="absolute top-0 right-0 w-24 h-24 bg-[#00555A] rounded-bl-full -mr-4 -mt-4 opacity-10"></div>

          <h3 class="font-bold text-[#00555A] text-lg mb-6 relative z-10">Data Donatur</h3>
          
          <form @submit.prevent="validateAndConfirm" class="space-y-4 relative z-10">
            
            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Jabatan / Status <span class="text-red-500">*</span></label>
              <div class="relative">
                <select 
                  v-model="form.jabatan" 
                  class="w-full pl-10 pr-4 py-3 bg-[#EBF2FA] border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] outline-none transition-all text-sm font-medium text-gray-700 appearance-none cursor-pointer"
                  required
                >
                  <option value="Mahasiswa">Mahasiswa</option>
                  <option value="Dosen">Dosen</option>
                </select>
                <BriefcaseIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
              </div>
            </div>

            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Judul Buku <span class="text-red-500">*</span></label>
              <div class="relative">
                <input 
                  v-model="form.judul_buku" 
                  type="text" 
                  placeholder="Masukkan judul buku..."
                  class="w-full pl-10 pr-4 py-3 bg-[#EBF2FA] border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] outline-none transition-all text-sm font-medium text-gray-700 placeholder-gray-400"
                  required
                />
                <BookOpenIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
              </div>
            </div>

            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Alamat <span class="text-red-500">*</span></label>
              <div class="relative">
                <input 
                  v-model="form.alamat" 
                  type="text" 
                  placeholder="Alamat Domisili"
                  class="w-full pl-10 pr-4 py-3 bg-[#EBF2FA] border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] outline-none transition-all text-sm font-medium text-gray-700 placeholder-gray-400"
                  required
                />
                <MapPinIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
              </div>
            </div>

            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Nomor HP <span class="text-red-500">*</span></label>
              <div class="relative">
                <input 
                  v-model="form.hp" 
                  type="text" 
                  inputmode="numeric"
                  @input="validasiAngka"
                  placeholder="08xxxxxxxxxx"
                  class="w-full pl-10 pr-4 py-3 bg-[#EBF2FA] border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] outline-none transition-all text-sm font-medium text-gray-700 placeholder-gray-400"
                  required
                />
                <PhoneIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
              </div>
            </div>

            <div class="space-y-1">
              <label class="text-xs font-bold text-gray-400 uppercase tracking-wide">Upload Bukti Buku <span class="text-red-500">*</span></label>
              <label 
                class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-[#F26430]/5 hover:border-[#F26430]/50 transition-colors group relative overflow-hidden"
                :class="{'border-[#00555A] bg-[#00555A]/5': form.fileName}"
              >
                <div class="flex flex-col items-center justify-center pt-5 pb-6 relative z-10">
                  <CloudArrowUpIcon 
                    class="w-8 h-8 mb-2 transition-colors"
                    :class="form.fileName ? 'text-[#00555A]' : 'text-gray-400 group-hover:text-[#F26430]'"
                  />
                  <p class="mb-1 text-sm text-gray-500">
                    <span class="font-semibold" :class="form.fileName ? 'text-[#00555A]' : 'text-[#F26430]'">
                        {{ form.fileName ? 'File Terpilih' : 'Klik untuk upload' }}
                    </span>
                  </p>
                  <p class="text-xs text-gray-400" v-if="!form.fileName">PDF, JPG, PNG (Max. 5MB)</p>
                  <p class="text-xs text-[#00555A] font-medium truncate px-4" v-else>{{ form.fileName }}</p>
                </div>
                <input ref="fileInputRef" type="file" class="hidden" @change="handleFile" accept=".pdf,.jpg,.jpeg,.png" required />
              </label>
            </div>

            <button 
              type="submit" 
              :disabled="isLoading"
              class="w-full py-3 bg-[#F26430] hover:bg-[#D65020] text-white rounded-xl font-bold shadow-lg shadow-orange-200 active:scale-95 transition-all flex justify-center items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <PaperAirplaneIcon v-if="!isLoading" class="w-5 h-5 -rotate-45" />
              <ArrowPathIcon v-else class="w-5 h-5 animate-spin" />
              <span v-if="isLoading">Memproses...</span>
              <span v-else>Kirim Data</span>
            </button>
          </form>
        </div>
      </div>

      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden min-h-[500px] flex flex-col">
          
          <div class="p-6 border-b border-gray-100 bg-[#EBF2FA]/50 flex justify-between items-center">
            <h3 class="font-bold text-[#00555A] text-lg">Riwayat Donasi</h3>
            <span class="bg-[#00555A]/10 text-[#00555A] text-xs font-bold px-2 py-1 rounded-md">{{ statusList.length }} Item</span>
          </div>
          
          <div v-if="isLoadingFetch" class="flex-1 flex items-center justify-center">
             <div class="flex flex-col items-center gap-2">
                <ArrowPathIcon class="w-8 h-8 text-[#F26430] animate-spin" />
                <span class="text-xs text-gray-400">Memuat data...</span>
             </div>
          </div>

          <div v-else-if="statusList.length === 0" class="flex-1 flex flex-col items-center justify-center p-10 text-center opacity-60">
            <ArchiveBoxIcon class="w-20 h-20 text-gray-200 mb-4" />
            <p class="text-gray-500 font-medium">Belum ada data donasi buku.</p>
          </div>

          <div v-else class="flex-1 overflow-y-auto p-4 space-y-3">
            <div 
              v-for="(item, index) in statusList" 
              :key="item.id || index" 
              class="bg-white border border-gray-100 rounded-2xl p-4 hover:shadow-md hover:border-[#F26430]/30 transition-all flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between group"
            >
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-[#EBF2FA] flex items-center justify-center text-[#00555A] shrink-0 group-hover:bg-[#F26430] group-hover:text-white transition-colors duration-300">
                  <BookOpenIcon class="w-6 h-6" />
                </div>
                
                <div>
                  <h4 class="font-bold text-gray-800 text-sm group-hover:text-[#00555A] transition-colors">
                    {{ item.judul_buku && item.judul_buku !== '-' ? item.judul_buku : item.jenis }}
                  </h4>
                  <div class="flex items-center gap-2 text-xs text-gray-500 mt-1">
                    <ClockIcon class="w-3 h-3 text-[#F26430]" />
                    <span>{{ formatDate(item.created_at) }}</span>
                  </div>
                  
                  <button 
                    v-if="item.file_pendukung" 
                    @click="downloadFileSecure(item.file_pendukung, 'pendukung')" 
                    class="text-xs text-[#00555A] underline mt-1 block hover:text-[#F26430] transition-colors cursor-pointer text-left"
                  >
                    Lihat Bukti Upload
                  </button>
                </div>
              </div>

              <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                <span 
                  class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border"
                  :class="getStatusClass(item.status)"
                >
                  {{ item.status }}
                </span>
                
                <button 
                  v-if="['Diterima', 'Selesai'].includes(item.status) && item.file_balasan"
                  @click="downloadFileSecure(item.file_balasan, 'balasan')"
                  class="flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-[#00555A] bg-[#EBF2FA] hover:bg-[#00555A] hover:text-white rounded-lg transition-colors border border-[#00555A]/20 cursor-pointer"
                  title="Unduh Tanda Terima"
                >
                  <ArrowDownTrayIcon class="w-4 h-4" />
                  <span class="hidden sm:inline">Tanda Terima</span>
                </button>

                <div 
                  v-if="item.status === 'Ditolak'"
                  class="p-2 text-red-500 bg-red-50 rounded-lg border border-red-200"
                  title="Pengajuan Ditolak. Silakan periksa kembali."
                >
                  <XCircleIcon class="w-5 h-5" />
                </div>
              </div>

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
            
            <div class="flex flex-col gap-3">
              <button 
                @click="handleModalAction" 
                class="w-full py-3.5 text-white rounded-xl font-bold shadow-lg transition-all active:scale-95"
                :class="modalButtonClass"
              >
                {{ modal.confirmText }}
              </button>
              
              <button 
                v-if="modal.type === 'confirm'"
                @click="modal.show = false" 
                class="w-full py-3.5 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl font-semibold transition-colors"
              >
                Batal
              </button>
            </div>
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
  GiftIcon, BriefcaseIcon, MapPinIcon, PhoneIcon,
  PaperAirplaneIcon, ArchiveBoxIcon, BookOpenIcon, ClockIcon, 
  ArrowDownTrayIcon, CloudArrowUpIcon, ArrowPathIcon,
  CheckCircleIcon, XCircleIcon, ExclamationTriangleIcon, QuestionMarkCircleIcon
} from "@heroicons/vue/24/outline";

const form = ref({
  jabatan: "Mahasiswa", 
  alamat: "",
  hp: "",
  judul_buku: "", 
  file: null, 
  fileName: "" 
});

const fileInputRef = ref(null);
const isLoading = ref(false);
const isLoadingFetch = ref(false);
const statusList = ref([]); 

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
  if(modal.value.type !== 'loading') modal.value.show = false;
};

const modalIcon = computed(() => {
  switch (modal.value.type) {
    case 'confirm': return QuestionMarkCircleIcon;
    case 'success': return CheckCircleIcon;
    case 'error': return XCircleIcon;
    default: return ExclamationTriangleIcon;
  }
});

const modalIconClass = computed(() => {
  switch (modal.value.type) {
    case 'confirm': return 'bg-[#F26430]/10 text-[#F26430]';
    case 'success': return 'bg-green-100 text-green-600';
    case 'error': return 'bg-red-100 text-red-600';
    default: return 'bg-gray-100 text-gray-600';
  }
});

const modalButtonClass = computed(() => {
  switch (modal.value.type) {
    case 'confirm': return 'bg-[#F26430] hover:bg-[#D65020] shadow-orange-500/30';
    case 'success': return 'bg-[#00555A] hover:bg-[#004448] shadow-teal-500/30';
    case 'error': return 'bg-red-500 hover:bg-red-600 shadow-red-500/30';
    default: return 'bg-gray-800 hover:bg-gray-900';
  }
});

const handleModalAction = () => {
  if (modal.value.type === 'confirm') {
    modal.value.show = false;
    processSubmit(); 
  } else {
    modal.value.show = false;
  }
};

const fetchHistory = async () => {
    try {
        const response = await api.get('/pengajuan?jenis=Donasi Buku');
        statusList.value = response.data;
    } catch (error) {
        console.error("Gagal load history:", error);
    }
};

onMounted(async () => {
  isLoadingFetch.value = true;
  await fetchHistory();
  isLoadingFetch.value = false;
});

const handleFile = (e) => {
  const file = e.target.files[0];
  if (!file) return;

  const allowedTypes = [
    'application/pdf',
    'image/jpeg',
    'image/png'
  ];

  if (!allowedTypes.includes(file.type)) {
    openModal('error', 'Format Tidak Didukung', 'Gunakan format file PDF, JPG, atau PNG.', 'Mengerti');
    if (fileInputRef.value) fileInputRef.value.value = ''; 
    return;
  }

  if (file.size > 5 * 1024 * 1024) {
    openModal('error', 'File Terlalu Besar', 'Ukuran file maksimal adalah 5MB. Silakan kompres file Anda.', 'Mengerti');
    if (fileInputRef.value) fileInputRef.value.value = ''; 
    return;
  }

  form.value.file = file;
  form.value.fileName = file.name;
};

const validasiAngka = () => {
  form.value.hp = form.value.hp.replace(/[^0-9]/g, "");
};

const validateAndConfirm = () => {
  if (isLoading.value) return; 

  if (!form.value.file) {
    openModal('error', 'Bukti Belum Diunggah', 'Mohon upload bukti pendukung (PDF/JPG/PNG) sebelum mengirim donasi.', 'Oke');
    return;
  }
  openModal(
    'confirm', 
    'Konfirmasi Donasi', 
    'Pastikan judul buku, alamat, dan foto bukti donasi sudah benar. Apakah Anda yakin ingin mengirim?', 
    'Ya, Kirim Sekarang'
  );
};

const processSubmit = async () => {
  if (isLoading.value) return; 
  isLoading.value = true;

  try {
    const formData = new FormData();
    formData.append('jenis', 'Donasi Buku'); 
    formData.append('file_pendukung', form.value.file);
    formData.append('jabatan', form.value.jabatan);
    formData.append('alamat', form.value.alamat);
    formData.append('no_hp', form.value.hp);
    formData.append('judul_buku', form.value.judul_buku);

    await api.post('/pengajuan', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    form.value = { jabatan: "Mahasiswa", alamat: "", hp: "", judul_buku: "", file: null, fileName: "" };
    if (fileInputRef.value) fileInputRef.value.value = ''; 

    openModal('success', 'Berhasil Terkirim!', 'Terima kasih atas donasi buku Anda. Data akan segera divalidasi dan dicatat di sistem.', 'Tutup');
    
    isLoadingFetch.value = true;
    await fetchHistory();
    isLoadingFetch.value = false;

  } catch (error) {
    console.error(error);
    const msg = error.response?.data?.message || "Terjadi kesalahan saat mengirim data.";
    openModal('error', 'Gagal Mengirim', msg, 'Coba Lagi');
  } finally {
    isLoading.value = false;
  }
};

const downloadFileSecure = async (filePath, tipe) => {
  if (!filePath) return;
  
  try {
    const fileName = filePath.split('/').pop();
    
    const endpoint = tipe === 'pendukung' 
      ? `/download-pendukung/${fileName}` 
      : `/download-balasan/${fileName}`;

    const response = await api.get(endpoint, {
      responseType: 'blob', 
    });

    const blob = new Blob([response.data], { type: response.headers['content-type'] });
    const url = window.URL.createObjectURL(blob);
    
    const link = document.createElement('a');
    link.href = url;
    
    const contentDisposition = response.headers['content-disposition'];
    let downloadedFileName = fileName;
    if (contentDisposition) {
      const match = contentDisposition.match(/filename="?([^"]+)"?/);
      if (match && match[1]) downloadedFileName = match[1];
    }
    
    link.setAttribute('download', downloadedFileName);
    document.body.appendChild(link);
    link.click();
    
    link.remove();
    window.URL.revokeObjectURL(url);

  } catch (error) {
    console.error("Gagal mendownload file:", error);
    openModal('error', 'Akses Ditolak', 'File tidak ditemukan atau Anda tidak memiliki izin untuk mengunduhnya.', 'Tutup');
  }
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const options = { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute:'2-digit' };
  return new Date(dateStr).toLocaleDateString("id-ID", options);
};

const getStatusClass = (status) => {
  if (status === 'Menunggu Verifikasi') return 'bg-yellow-50 text-yellow-600 border-yellow-200';
  if (status === 'Diterima' || status === 'Selesai') return 'bg-[#00555A]/10 text-[#00555A] border-[#00555A]/20';
  if (status === 'Ditolak') return 'bg-red-50 text-red-600 border-red-200';
  return 'bg-gray-50 text-gray-600 border-gray-200';
};
</script>

<style scoped>
.overflow-y-auto::-webkit-scrollbar { width: 6px; }
.overflow-y-auto::-webkit-scrollbar-thumb { background-color: #e5e7eb; border-radius: 20px; }

.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.3s ease; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
.modal-fade-enter-active .transform, .modal-fade-leave-active .transform { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
.modal-fade-enter-from .transform, .modal-fade-leave-to .transform { transform: scale(0.95) translateY(10px); }
</style>