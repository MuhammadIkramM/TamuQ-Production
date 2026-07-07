<template>
  <div class="space-y-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-[#00555A] flex items-center gap-2">
          <AcademicCapIcon class="w-8 h-8 text-[#F26430]" />
          Manajemen E-Skripsi
        </h1>
        <p class="text-gray-500 text-sm">Kelola arsip skripsi mahasiswa dalam format digital.</p>
      </div>

      <div class="flex gap-3">
        <button
          @click="openSkripsiModal('add')"
          class="flex items-center gap-2 px-4 py-2 bg-[#00555A] hover:bg-[#004448] text-white rounded-xl transition-all text-sm font-medium shadow-md shadow-[#00555A]/20"
        >
          <PlusIcon class="w-5 h-5" />
          Tambah Skripsi
        </button>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-5 flex flex-col md:flex-row md:items-center justify-between bg-[#00555A] gap-4 rounded-t-2xl">
        <div class="flex items-center gap-3">
          <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <DocumentTextIcon class="w-5 h-5 text-[#F26430]" />
            Arsip Skripsi
          </h3>
          <span class="text-xs font-medium bg-white/20 text-white px-2 py-1 rounded-md">
            Total: {{ filteredSkripsi.length }} Data
          </span>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
          <div class="relative w-full sm:w-72">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
            </div>
            <input 
              type="text" 
              v-model="searchQuery" 
              class="w-full pl-10 pr-4 py-2 bg-white border-transparent rounded-xl focus:ring-2 focus:ring-[#F26430] text-sm outline-none transition-all shadow-sm"
              placeholder="Cari judul, nama, NIM..."
            />
          </div>
        </div>
      </div>

      <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
          <thead class="sticky top-0 z-10 shadow-sm">
            <tr class="bg-[#66999C] text-white text-xs uppercase tracking-wider font-semibold">
              <th class="px-4 py-3 text-center w-14">No</th>
              <th class="px-6 py-3">Judul</th>
              <th class="px-6 py-3">Nama</th>
              <th class="px-6 py-3 text-center">NIM</th>
              <th class="px-6 py-3 text-center">Prodi</th>
              <th class="px-6 py-3 text-center">Tahun Selesai</th>
              <th class="px-4 py-3 text-center w-20">Skripsi</th>
              <th class="px-6 py-3 text-center">Tindakan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="(skripsi, index) in filteredSkripsi" :key="skripsi.id_skripsi" class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 text-center text-sm text-gray-500 font-medium">{{ index + 1 }}</td>
              <td class="px-6 py-3 text-sm font-medium text-gray-800 max-w-[280px] truncate" :title="skripsi.judul">{{ skripsi.judul }}</td>
              <td class="px-6 py-3 text-sm text-gray-600">{{ skripsi.nama }}</td>
              <td class="px-6 py-3 text-center text-sm font-mono text-gray-600">{{ skripsi.nim }}</td>
              <td class="px-6 py-3 text-center">
                <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600 border border-gray-200">
                  {{ skripsi.prodi }}
                </span>
              </td>
              <td class="px-6 py-3 text-center text-sm text-gray-600">{{ skripsi.tahun_selesai }}</td>
              <td class="px-4 py-3 text-center">
                <button @click="previewSkripsi(skripsi)" class="p-2 text-[#00555A] hover:bg-[#00555A]/10 rounded-lg transition border border-transparent hover:border-[#00555A]/20" title="Preview Skripsi">
                  <DocumentTextIcon class="w-4 h-4" />
                </button>
              </td>
              <td class="px-6 py-4 text-center">
                <div class="flex justify-center gap-2">
                  <button @click="openSkripsiModal('edit', skripsi)" class="p-2 text-[#F26430] hover:bg-[#F26430]/10 rounded-lg transition border border-transparent hover:border-[#F26430]/20" title="Edit Data">
                    <PencilSquareIcon class="w-4 h-4" />
                  </button>
                  <button @click="deleteSkripsi(skripsi)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition border border-transparent hover:border-red-200" title="Hapus Permanen">
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredSkripsi.length === 0">
              <td colspan="8" class="px-6 py-8 text-center text-gray-400 text-sm">
                {{ searchQuery ? 'Data skripsi tidak ditemukan.' : 'Belum ada data skripsi.' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Tambah/Edit Skripsi -->
    <transition name="fade">
      <div v-if="showSkripsiModal" class="fixed inset-0 flex items-center justify-center z-50 bg-[#00282a]/50 backdrop-blur-sm p-4" @click.self="cancelSkripsi">
        <div class="bg-white shadow-2xl rounded-2xl w-full max-w-md overflow-hidden transform transition-all scale-100">
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
             <h3 class="text-lg font-bold text-[#00555A]">{{ isEditing ? 'Edit Data Skripsi' : 'Tambah Skripsi Baru' }}</h3>
             <button @click="cancelSkripsi" class="text-gray-400 hover:text-gray-600"><XMarkIcon class="w-6 h-6" /></button>
          </div>
          <div class="p-6 space-y-4 max-h-[65vh] overflow-y-auto">
             
             <div class="space-y-1">
               <label class="text-xs font-bold text-gray-500 uppercase">Judul Skripsi <span class="text-red-500">*</span></label>
               <input type="text" v-model="formSkripsi.judul" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none" placeholder="Masukkan judul skripsi..." />
             </div>
             
             <div class="space-y-1">
               <label class="text-xs font-bold text-gray-500 uppercase">Nama Mahasiswa <span class="text-red-500">*</span></label>
               <input type="text" v-model="formSkripsi.nama" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none" placeholder="Masukkan nama lengkap..." />
             </div>
             
             <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                  <label class="text-xs font-bold text-gray-500 uppercase">NIM <span class="text-red-500">*</span></label>
                  <input type="text" v-model="formSkripsi.nim" @input="formSkripsi.nim = formSkripsi.nim.replace(/[^0-9]/g, '')" inputmode="numeric" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none" placeholder="Contoh: 1234567890" />
                </div>
                <div class="space-y-1">
                  <label class="text-xs font-bold text-gray-500 uppercase">Prodi <span class="text-red-500">*</span></label>
                  <select v-model="formSkripsi.prodi" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none">
                    <option value="" disabled>Pilih Prodi</option>
                    <option value="PTIK">PTIK</option>
                    <option value="TEKOM">TEKOM</option>
                  </select>
                </div>
             </div>

             <div class="space-y-1">
               <label class="text-xs font-bold text-gray-500 uppercase">Tahun Selesai <span class="text-red-500">*</span></label>
               <input type="number" min="2000" max="2099" v-model="formSkripsi.tahun_selesai" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none" placeholder="Contoh: 2026" />
             </div>

             <div class="space-y-1">
               <label class="text-xs font-bold text-gray-500 uppercase">
                 File Skripsi (PDF) 
                 <span v-if="!isEditing" class="text-red-500">*</span>
                 <span v-else class="text-gray-400 normal-case font-normal"> — Kosongkan jika tidak diubah</span>
               </label>
               <input 
                 type="file" 
                 ref="fileInput"
                 accept=".pdf" 
                 @change="handleFileChange"
                 class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#00555A]/10 file:text-[#00555A] hover:file:bg-[#00555A]/20" 
               />
             </div>

          </div>
          <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-2xl border-t border-gray-100">
            <button @click="cancelSkripsi" class="px-4 py-2 text-gray-700 text-sm font-medium hover:bg-gray-100 rounded-lg transition">Batal</button>
            <button @click="saveSkripsi" :disabled="!isFormValid || isSubmitting" class="px-4 py-2 bg-[#00555A] text-white rounded-lg text-sm font-medium hover:bg-[#004448] shadow-md disabled:opacity-50 transition flex items-center gap-2">
              <svg v-if="isSubmitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              <span v-if="isSubmitting">Menyimpan...</span>
              <span v-else>{{ isEditing ? 'Update Skripsi' : 'Simpan Skripsi' }}</span>
            </button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Modal Preview PDF -->
    <transition name="fade">
      <div v-if="showPreviewModal" class="fixed inset-0 flex items-center justify-center z-50 bg-[#00282a]/50 backdrop-blur-sm p-4" @click.self="closePreview">
        <div class="bg-white shadow-2xl rounded-2xl w-full max-w-4xl h-[85vh] overflow-hidden transform transition-all scale-100 flex flex-col">
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl shrink-0">
             <div>
               <h3 class="text-lg font-bold text-[#00555A]">Preview Skripsi</h3>
               <p class="text-xs text-gray-400 truncate max-w-[400px]">{{ previewTitle }}</p>
             </div>
             <button @click="closePreview" class="text-gray-400 hover:text-gray-600"><XMarkIcon class="w-6 h-6" /></button>
          </div>
          <div class="flex-1 p-4 bg-gray-100 relative">
            <div v-if="previewLoading" class="absolute inset-0 flex items-center justify-center bg-white/80 z-10">
              <div class="flex flex-col items-center gap-3">
                <div class="w-10 h-10 border-4 border-[#00555A]/20 border-t-[#00555A] rounded-full animate-spin"></div>
                <p class="text-sm text-gray-500 font-medium">Memuat dokumen...</p>
              </div>
            </div>
            <iframe 
              v-if="previewUrl" 
              :src="previewUrl" 
              class="w-full h-full rounded-lg border border-gray-200"
              frameborder="0"
            ></iframe>
            <div v-if="previewError" class="absolute inset-0 flex items-center justify-center">
              <div class="text-center">
                <XCircleIcon class="w-12 h-12 text-red-400 mx-auto mb-2" />
                <p class="text-sm text-gray-500">Gagal memuat dokumen.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- Alert/Confirm Modal -->
    <transition name="fade">
      <div v-if="alertState.show" class="fixed inset-0 flex items-center justify-center z-[100] bg-[#00282a]/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center transform transition-all scale-100 border-t-4"
             :class="alertState.type === 'error' ? 'border-red-500' : (alertState.type === 'confirm' ? 'border-[#F26430]' : 'border-green-500')">
          
          <div class="flex justify-center mb-4">
            <div v-if="alertState.type === 'success'" class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
              <CheckCircleIcon class="w-10 h-10 text-green-600" />
            </div>
            <div v-else-if="alertState.type === 'error'" class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
              <XCircleIcon class="w-10 h-10 text-red-600" />
            </div>
            <div v-else class="w-16 h-16 bg-[#F26430]/10 rounded-full flex items-center justify-center">
              <ExclamationTriangleIcon class="w-10 h-10 text-[#F26430]" />
            </div>
          </div>

          <h3 class="text-lg font-bold text-gray-800 mb-2">
            {{ alertState.type === 'success' ? 'Berhasil' : (alertState.type === 'error' ? 'Terjadi Kesalahan' : 'Konfirmasi') }}
          </h3>
          <p class="text-gray-600 text-sm mb-6">{{ alertState.message }}</p>

          <div class="flex gap-3 justify-center">
            <button 
              v-if="alertState.type === 'confirm'" 
              @click="closeAlert" 
              class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-600 font-medium hover:bg-gray-50 transition"
            >
              Batal
            </button>
            <button 
              @click="handleAlertConfirm" 
              :disabled="isDeleting"
              class="px-5 py-2.5 rounded-xl text-white font-medium shadow-lg transition flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
              :class="alertState.type === 'error' ? 'bg-red-600 hover:bg-red-700 shadow-red-200' : (alertState.type === 'confirm' ? 'bg-[#00555A] hover:bg-[#004448] shadow-[#00555A]/30' : 'bg-green-600 hover:bg-green-700 shadow-green-200')"
            >
              <svg v-if="isDeleting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              <span v-if="isDeleting">Memproses...</span>
              <span v-else>{{ alertState.type === 'confirm' ? 'Ya, Lanjutkan' : 'Oke, Mengerti' }}</span>
            </button>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import api from '@/api';
import { 
  AcademicCapIcon,
  PlusIcon,
  DocumentTextIcon,
  MagnifyingGlassIcon,
  PencilSquareIcon,
  TrashIcon,
  XMarkIcon,
  CheckCircleIcon,
  XCircleIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline';

// ==================== STATE ====================
const daftarSkripsi = ref([]);
const searchQuery = ref('');

// Form Modal
const showSkripsiModal = ref(false);
const isEditing = ref(false);
const isSubmitting = ref(false);
const isDeleting = ref(false);
const currentSkripsiId = ref(null);
const formSkripsi = ref({
  judul: '',
  nama: '',
  nim: '',
  prodi: '',
  tahun_selesai: '',
});
const selectedFile = ref(null);
const fileInput = ref(null);

// Preview Modal
const showPreviewModal = ref(false);
const previewUrl = ref(null);
const previewTitle = ref('');
const previewLoading = ref(false);
const previewError = ref(false);

// Alert
const alertState = reactive({
  show: false,
  message: '',
  type: 'success',
  onConfirm: null
});

// ==================== COMPUTED ====================
const filteredSkripsi = computed(() => {
  if (!searchQuery.value) return daftarSkripsi.value;
  const query = searchQuery.value.toLowerCase();
  return daftarSkripsi.value.filter(s => 
    (s.judul && s.judul.toLowerCase().includes(query)) ||
    (s.nama && s.nama.toLowerCase().includes(query)) ||
    (s.nim && s.nim.toLowerCase().includes(query)) ||
    (s.prodi && s.prodi.toLowerCase().includes(query)) ||
    (s.tahun_selesai && s.tahun_selesai.toString().includes(query))
  );
});

const isFormValid = computed(() => {
  const base = formSkripsi.value.judul && formSkripsi.value.nama && formSkripsi.value.nim && formSkripsi.value.prodi && formSkripsi.value.tahun_selesai;
  if (!isEditing.value) {
    return base && selectedFile.value;
  }
  return base;
});

// ==================== LIFECYCLE ====================
onMounted(() => {
  fetchSkripsi();
});

// ==================== METHODS ====================
const showAlertBox = (message, type = 'success') => {
  alertState.message = message;
  alertState.type = type;
  alertState.onConfirm = null;
  alertState.show = true;
};

const showConfirmBox = (message, onConfirmAction) => {
  alertState.message = message;
  alertState.type = 'confirm';
  alertState.onConfirm = onConfirmAction;
  alertState.show = true;
};

const closeAlert = () => alertState.show = false;
const handleAlertConfirm = async () => {
  if (isDeleting.value) return;

  if (alertState.type === 'confirm' && alertState.onConfirm) {
    isDeleting.value = true;
    try {
      await alertState.onConfirm();
    } finally {
      isDeleting.value = false;
      if (alertState.type === 'confirm') {
        closeAlert();
      }
    }
  } else {
    closeAlert();
  }
};

const fetchSkripsi = async () => {
  try {
    const res = await api.get('/skripsi');
    daftarSkripsi.value = res.data.data || [];
  } catch (e) {
    console.error('Error fetching skripsi:', e);
    if (e.response?.status === 401) showAlertBox('Sesi berakhir, silakan login kembali.', 'error');
  }
};

const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    if (file.type !== 'application/pdf') {
      showAlertBox('Hanya file PDF yang diperbolehkan.', 'error');
      event.target.value = '';
      selectedFile.value = null;
      return;
    }
    selectedFile.value = file;
  } else {
    selectedFile.value = null;
  }
};

const openSkripsiModal = (mode, skripsi = null) => {
  if (mode === 'edit' && skripsi) {
    isEditing.value = true;
    currentSkripsiId.value = skripsi.id_skripsi;
    formSkripsi.value = {
      judul: skripsi.judul || '',
      nama: skripsi.nama || '',
      nim: skripsi.nim || '',
      prodi: skripsi.prodi || '',
      tahun_selesai: skripsi.tahun_selesai || '',
    };
  } else {
    isEditing.value = false;
    currentSkripsiId.value = null;
    formSkripsi.value = { judul: '', nama: '', nim: '', prodi: '', tahun_selesai: '' };
  }
  selectedFile.value = null;
  if (fileInput.value) fileInput.value.value = '';
  showSkripsiModal.value = true;
};

const cancelSkripsi = () => {
  showSkripsiModal.value = false;
};

const saveSkripsi = async () => {
  if (isSubmitting.value) return;
  if (!isFormValid.value) return;

  isSubmitting.value = true;

  const formData = new FormData();
  formData.append('judul', formSkripsi.value.judul);
  formData.append('nama', formSkripsi.value.nama);
  formData.append('nim', formSkripsi.value.nim);
  formData.append('prodi', formSkripsi.value.prodi);
  formData.append('tahun_selesai', formSkripsi.value.tahun_selesai);

  if (selectedFile.value) {
    formData.append('file_skripsi', selectedFile.value);
  }

  try {
    if (isEditing.value && currentSkripsiId.value) {
      await api.post(`/staff/skripsi/${currentSkripsiId.value}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      showAlertBox('Data skripsi berhasil diperbarui!', 'success');
    } else {
      await api.post('/staff/skripsi', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      showAlertBox('Skripsi berhasil ditambahkan!', 'success');
    }
    showSkripsiModal.value = false;
    await fetchSkripsi();
  } catch (e) {
    showAlertBox(e.response?.data?.message || 'Gagal menyimpan data skripsi.', 'error');
  } finally {
    isSubmitting.value = false;
  }
};

const deleteSkripsi = (skripsi) => {
  showConfirmBox(`Apakah Anda yakin ingin menghapus skripsi "${skripsi.judul}"?`, async () => {
    try {
      await api.delete(`/staff/skripsi/${skripsi.id_skripsi}`);
      await fetchSkripsi();
      showAlertBox('Data skripsi berhasil dihapus.', 'success');
    } catch (e) {
      showAlertBox(e.response?.data?.message || 'Gagal menghapus skripsi.', 'error');
    }
  });
};

const previewSkripsi = async (skripsi) => {
  previewTitle.value = skripsi.judul;
  previewLoading.value = true;
  previewError.value = false;
  previewUrl.value = null;
  showPreviewModal.value = true;

  try {
    const response = await api.get(`/skripsi/${skripsi.id_skripsi}/preview`, {
      responseType: 'blob'
    });
    const blob = new Blob([response.data], { type: 'application/pdf' });
    previewUrl.value = URL.createObjectURL(blob);
  } catch (e) {
    console.error('Preview error:', e);
    previewError.value = true;
    if (e.response?.status === 404) {
      showAlertBox('File skripsi tidak ditemukan di server.', 'error');
    }
  } finally {
    previewLoading.value = false;
  }
};

const closePreview = () => {
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value);
  }
  showPreviewModal.value = false;
  previewUrl.value = null;
  previewTitle.value = '';
  previewError.value = false;
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(5px);
}
</style>
