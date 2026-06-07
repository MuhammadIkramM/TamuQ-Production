<template>
  <div class="space-y-6 relative">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-[#00555A] flex items-center gap-2">
          <DocumentTextIcon class="w-8 h-8 text-[#F26430]" />
          Daftar Pengajuan Surat
        </h1>
        <p class="text-gray-500 text-sm">Kelola pengajuan surat bebas pustaka & donasi buku.</p>
      </div>
      
      <button 
        @click="fetchData" 
        :disabled="isLoading"
        class="px-4 py-2 bg-[#00555A]/10 text-[#00555A] rounded-lg text-sm font-medium hover:bg-[#00555A]/20 transition flex items-center gap-2 disabled:opacity-50"
      >
        <ArrowPathIcon class="w-4 h-4" :class="{'animate-spin': isLoading}" />
        Refresh Data
      </button>
    </div>

    <div class="bg-[#00555A]/60 p-5 rounded-2xl shadow-sm border border-[#00555A]/20 backdrop-blur-sm">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
        
        <div class="space-y-1">
          <label class="text-xs font-semibold text-white uppercase tracking-wide">Jenis Surat</label>
          <div class="relative">
            <TagIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <select
              v-model="selectedJenis"
              class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm transition-all outline-none appearance-none cursor-pointer"
            >
              <option value="">Semua Jenis</option>
              <option value="Bebas Perpustakaan">Bebas Perpustakaan</option>
              <option value="Donasi Buku">Donasi Buku</option>
            </select>
          </div>
        </div>

        <div class="space-y-1">
          <label class="text-xs font-semibold text-white uppercase tracking-wide">Waktu</label>
          <div class="relative">
            <CalendarDaysIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <select
              v-model="selectedRange"
              class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm transition-all outline-none appearance-none cursor-pointer"
            >
              <option value="all">Semua Waktu</option>
              <option value="today">Hari Ini</option>
              <option value="week">1 Minggu Terakhir</option>
              <option value="month">1 Bulan Terakhir</option>
            </select>
          </div>
        </div>

        <div class="hidden lg:block lg:col-span-3"></div> 
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden min-h-[400px]">
      <div v-if="isLoading" class="p-12 text-center text-gray-500 h-full flex flex-col items-center justify-center">
        <ArrowPathIcon class="w-8 h-8 animate-spin mx-auto mb-2 text-[#00555A]" />
        <p>Memuat data pengajuan...</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-[#00555A] text-white text-xs uppercase tracking-wider font-semibold">
              <th class="px-6 py-4 rounded-tl-lg">Mahasiswa</th>
              <th class="px-6 py-4">Jenis & File User</th>
              <th class="px-6 py-4">Detail Pengajuan</th>
              <th class="px-6 py-4">Waktu Pengajuan</th>
              <th class="px-6 py-4 text-center">Status</th>
              <th class="px-6 py-4 text-center rounded-tr-lg">Tindakan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr
              v-for="(pengajuan, index) in filteredData"
              :key="pengajuan.id || index"
              class="hover:bg-gray-50 transition-colors group"
            >
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full shrink-0 overflow-hidden border border-gray-200 relative bg-gray-100">
                    <img 
                      v-if="pengajuan.user && pengajuan.user.foto_profil"
                      :src="pengajuan.user.foto_profil" 
                      alt="Foto Profil"
                      class="w-full h-full object-cover"
                      @error="e => e.target.style.display = 'none'" 
                    />
                    <div 
                      class="absolute inset-0 w-full h-full flex items-center justify-center bg-gradient-to-br from-[#00555A] to-[#004448] text-white font-bold text-xs"
                      :class="{'hidden': pengajuan.user && pengajuan.user.foto_profil}"
                    >
                        {{ getInitials(pengajuan.nama_lengkap) }}
                    </div>
                  </div>

                  <div>
                    <p class="font-medium text-gray-900">{{ pengajuan.nama_lengkap }}</p>
                    <p class="text-xs text-gray-500 font-mono">{{ pengajuan.nomor_induk }}</p> </div>
                </div>
              </td>
              
              <td class="px-6 py-4">
                <div class="flex flex-col items-start gap-1">
                  <span class="text-sm text-gray-700 font-medium">{{ pengajuan.jenis }}</span>
                  
                  <button 
                    v-if="pengajuan.file_pendukung"
                    @click="downloadFileUser(pengajuan.file_pendukung)"
                    class="text-xs flex items-center gap-1 text-[#F26430] hover:text-[#d14f1f] hover:underline cursor-pointer"
                  >
                    <PaperClipIcon class="w-3 h-3" />
                    Lihat Bukti User
                  </button>
                  <span v-else class="text-xs text-gray-400 italic">Tidak ada file</span>
                </div>
              </td>

              <td class="px-6 py-4">
                <div v-if="pengajuan.jenis === 'Donasi Buku'" class="flex flex-col space-y-1.5 text-xs">
                  <span class="font-semibold text-gray-700 flex items-center gap-1.5" title="Jabatan/Status">
                    <BriefcaseIcon class="w-3.5 h-3.5 text-gray-400" />
                    {{ pengajuan.jabatan || '-' }}
                  </span>
                  <span class="text-gray-500 flex items-center gap-1.5" title="Alamat">
                    <MapPinIcon class="w-3.5 h-3.5 text-gray-400 shrink-0" /> 
                    <span class="truncate max-w-[150px]">{{ pengajuan.alamat || '-' }}</span>
                  </span>
                  <span class="text-gray-500 flex items-center gap-1.5" title="Nomor HP">
                    <PhoneIcon class="w-3.5 h-3.5 text-gray-400 shrink-0" /> 
                    {{ pengajuan.no_hp || '-' }}
                  </span>
                </div>
                
                <div v-else class="flex flex-col space-y-1.5 text-xs">
                  <span class="font-semibold text-gray-700 flex items-center gap-1.5" title="Nama Lengkap">
                    <UserIcon class="w-3.5 h-3.5 text-gray-400" />
                    {{ pengajuan.nama_lengkap }}
                  </span>
                  <span class="text-gray-500 flex items-center gap-1.5" title="NIM">
                    <IdentificationIcon class="w-3.5 h-3.5 text-gray-400 shrink-0" /> 
                    {{ pengajuan.nomor_induk }} </span>
                </div>
              </td>

              <td class="px-6 py-4">
                <div class="flex items-center gap-2 text-sm text-gray-500">
                  <ClockIcon class="w-4 h-4 text-gray-400" />
                  {{ formatDate(pengajuan.created_at) }}
                </div>
              </td>

              <td class="px-6 py-4 text-center">
                <span
                  :class="[ 
                    'px-3 py-1 text-xs rounded-full font-semibold inline-flex items-center gap-1 border',
                    getStatusStyle(pengajuan.status)
                  ]"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDot(pengajuan.status)"></span>
                  {{ pengajuan.status }}
                </span>
              </td>

              <td class="px-6 py-4 text-center">
                <div v-if="pengajuan.status === 'Menunggu Verifikasi'" class="flex items-center justify-center gap-2">
                  <button
                    @click="openUpload(pengajuan)"
                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-[#00555A] hover:bg-[#004448] text-white rounded-lg text-xs font-medium transition shadow-sm hover:shadow-md"
                    title="Setujui & Upload Surat"
                  >
                    <CheckBadgeIcon class="w-4 h-4" />
                    Setujui
                  </button>
                  <button
                    @click="openConfirmModal(pengajuan)"
                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 border border-red-200 rounded-lg text-xs font-medium transition shadow-sm hover:shadow-md"
                    title="Tolak Pengajuan"
                  >
                    <XMarkIcon class="w-4 h-4" />
                    Tolak
                  </button>
                </div>
                
                <span v-else-if="pengajuan.status === 'Ditolak'" class="text-xs text-red-700 font-medium bg-red-50 px-3 py-1.5 rounded-lg border border-red-100 inline-flex items-center gap-1">
                  <XMarkIcon class="w-3.5 h-3.5" /> Ditolak
                </span>

                <div v-else class="flex flex-col items-center gap-2">
                  <span class="text-xs text-[#00555A] font-medium bg-[#00555A]/10 px-3 py-1.5 rounded-lg border border-[#00555A]/20 inline-flex items-center gap-1">
                    <CheckBadgeIcon class="w-3.5 h-3.5" /> Selesai
                  </span>
                  <div class="flex items-center gap-2 mt-1">
                    <button @click="openPreviewDocument(pengajuan)" class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded border border-blue-200 transition" title="Lihat Preview Dokumen">
                      <EyeIcon class="w-4 h-4" />
                    </button>
                    <button @click="openEditUpload(pengajuan)" class="p-1.5 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded border border-amber-200 transition" title="Edit/Upload Ulang Dokumen">
                      <PencilSquareIcon class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </td>
            </tr>

            <tr v-if="filteredData.length === 0 && !isLoading">
              <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                <div class="flex flex-col items-center justify-center">
                  <InboxIcon class="w-12 h-12 text-gray-300 mb-3" />
                  <p class="text-base font-medium text-gray-500">Tidak ada data pengajuan sesuai filter.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <transition name="fade">
      <div
        v-if="selectedPengajuan"
        class="fixed inset-0 flex items-center justify-center z-50 bg-[#00282a]/50 backdrop-blur-sm p-4"
        @click.self="cancelUpload"
      >
        <div class="bg-white shadow-2xl rounded-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100">
          
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
              <div>
                <h3 class="text-lg font-bold text-[#00555A]">
                  {{ isEditingMode ? 'Edit & Unggah Ulang Dokumen' : 'Setujui & Unggah Balasan' }}
                </h3>
                <p class="text-xs text-gray-500">Untuk: {{ selectedPengajuan.nama_lengkap }}</p>
              </div>
              <button @click="cancelUpload" class="text-gray-400 hover:text-gray-600 transition">
                <XMarkIcon class="w-6 h-6" />
              </button>
          </div>

          <div class="p-6 overflow-y-auto max-h-[70vh]">
            <div 
              @click="triggerFileSelect"
              class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:bg-gray-50 hover:border-[#F26430] transition-all group relative"
            >
              <input
                ref="fileInput"
                type="file"
                @change="handleFileUpload"
                class="hidden"
                accept=".pdf,.jpg,.jpeg,.png"
              />
              <CloudArrowUpIcon class="w-12 h-12 text-gray-300 group-hover:text-[#F26430] mx-auto mb-3 transition-colors" />
              <p class="text-sm font-medium text-gray-700">Klik untuk upload surat balasan</p>
              <p class="text-xs text-gray-400 mt-1">Format: PDF, JPG, PNG (Max 5MB)</p>
            </div>

            <div v-if="previewUrl" class="mt-4 border border-gray-200 rounded-xl overflow-hidden bg-gray-50">
               <div class="p-2 bg-gray-100 text-xs font-semibold text-gray-600 border-b border-gray-200">Preview Dokumen</div>
               <div class="p-2 flex justify-center">
                  <iframe v-if="uploadFile && uploadFile.type === 'application/pdf'" :src="previewUrl" class="w-full h-64 border-none rounded"></iframe>
                  <img v-else-if="uploadFile && uploadFile.type.startsWith('image/')" :src="previewUrl" class="max-h-64 object-contain rounded" />
               </div>
            </div>

            <div v-if="uploadFile" class="mt-4 bg-[#00555A]/5 px-3 py-2 rounded-lg border border-[#00555A]/20 flex items-center justify-between">
               <div class="flex items-center gap-2 overflow-hidden w-full">
                 <DocumentIcon class="w-5 h-5 text-[#00555A] flex-shrink-0" />
                 <div class="overflow-hidden">
                     <p class="text-sm font-medium text-[#00555A] truncate">{{ uploadFile.name }}</p>
                     <p class="text-[10px] text-gray-500">{{ (uploadFile.size / 1024).toFixed(1) }} KB</p>
                 </div>
               </div>
               <button @click="removeFile" class="text-gray-400 hover:text-red-500 flex-shrink-0 ml-2">
                 <XMarkIcon class="w-5 h-5" />
               </button>
            </div>
          </div>

          <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 border-t border-gray-100">
            <button
              @click="cancelUpload"
              class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition"
              :disabled="isSubmitting"
            >
              Batal
            </button>
            <button
              @click="confirmUpload"
              :disabled="!uploadFile || isSubmitting"
              class="px-4 py-2 bg-[#00555A] hover:bg-[#004448] text-white rounded-lg text-sm font-medium transition shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <PaperAirplaneIcon v-if="!isSubmitting" class="w-4 h-4" />
              <ArrowPathIcon v-else class="w-4 h-4 animate-spin" />
              {{ isSubmitting ? 'Menyimpan...' : (isEditingMode ? 'Simpan Perubahan' : 'Kirim & Selesai') }}
            </button>
          </div>

        </div>
      </div>
    </transition>

    <transition name="fade">
      <div v-if="previewModal.show" class="fixed inset-0 flex items-center justify-center z-[80] bg-[#00282a]/70 backdrop-blur-sm p-4" @click.self="closePreview">
        <div class="bg-white shadow-2xl rounded-2xl w-full max-w-4xl h-[85vh] flex flex-col overflow-hidden transform transition-all scale-100">
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 shrink-0">
            <div>
              <h3 class="text-lg font-bold text-[#00555A]">Preview Dokumen Balasan</h3>
            </div>
            <button @click="closePreview" class="text-gray-400 hover:text-gray-600 transition bg-white rounded-full p-1 border">
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
          <div class="flex-1 bg-gray-100 relative overflow-hidden flex items-center justify-center p-4">
            <div v-if="previewModal.isLoading" class="flex flex-col items-center gap-3 text-gray-400">
              <ArrowPathIcon class="w-10 h-10 animate-spin text-[#00555A]" />
              <p class="text-sm">Memuat dokumen...</p>
            </div>
            <template v-else>
              <iframe 
                v-if="previewModal.type === 'pdf'" 
                :src="previewModal.url" 
                class="w-full h-full rounded shadow-sm border border-gray-200"
              ></iframe>
              <img 
                v-else-if="previewModal.url"
                :src="previewModal.url" 
                class="max-w-full max-h-full object-contain rounded shadow-sm" 
                alt="Preview Dokumen" 
              />
            </template>
          </div>
        </div>
      </div>
    </transition>

    <transition name="fade">
      <div v-if="confirmModal.show" class="fixed inset-0 flex items-center justify-center z-[60] bg-[#00282a]/50 backdrop-blur-sm p-4" @click.self="closeConfirmModal">
        <div class="bg-white shadow-2xl rounded-2xl w-full max-w-sm overflow-hidden transform transition-all scale-100">
          <div class="p-6 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <ExclamationTriangleIcon class="w-8 h-8 text-red-600" />
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Konfirmasi Penolakan</h3>
            <p class="text-sm text-gray-500">
              Apakah Anda yakin ingin menolak pengajuan dari <span class="font-semibold text-gray-800">{{ confirmModal.pengajuan?.nama_lengkap }}</span>? Aksi ini tidak dapat dibatalkan.
            </p>
          </div>
          <div class="px-6 py-4 bg-gray-50 flex gap-3">
            <button @click="closeConfirmModal" :disabled="isRejecting" class="flex-1 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
              Batal
            </button>
            <button @click="proceedReject" :disabled="isRejecting" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition shadow-md flex items-center justify-center gap-2 disabled:opacity-50">
              <ArrowPathIcon v-if="isRejecting" class="w-4 h-4 animate-spin" />
              <XMarkIcon v-else class="w-4 h-4" />
              {{ isRejecting ? 'Memproses...' : 'Ya, Tolak' }}
            </button>
          </div>
        </div>
      </div>
    </transition>

    <transition name="slide-fade">
      <div v-if="alert.show" class="fixed top-5 right-5 z-[100] flex items-center gap-3 bg-white p-4 rounded-xl shadow-2xl border-l-4 w-full max-w-sm"
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
import { ref, computed, onMounted } from "vue";
import api from "@/api"; 
import { 
  DocumentTextIcon, TagIcon, CalendarDaysIcon, 
  ArrowPathIcon, ClockIcon, CloudArrowUpIcon,
  InboxIcon, XMarkIcon, DocumentIcon, PaperAirplaneIcon,
  PaperClipIcon, CheckBadgeIcon, CheckCircleIcon, ExclamationCircleIcon,
  MapPinIcon, PhoneIcon, IdentificationIcon, BriefcaseIcon, UserIcon,
  ExclamationTriangleIcon, EyeIcon, PencilSquareIcon
} from "@heroicons/vue/24/outline"

const pengajuanList = ref([]);
const isLoading = ref(false);
const isSubmitting = ref(false);

const selectedJenis = ref("");
const selectedRange = ref("all");

const alert = ref({
  show: false,
  type: 'success', 
  title: '',
  message: ''
});

const showAlert = (type, title, message) => {
  alert.value = { show: true, type, title, message };
  setTimeout(() => { alert.value.show = false; }, 3000); 
};

const getInitials = (name) => {
    if(!name) return '?';
    return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const options = { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateStr).toLocaleDateString('id-ID', options);
};

const downloadFileUser = async (filePath) => {
  if(!filePath) {
      showAlert('error', 'File Hilang', 'File tidak ditemukan di server.');
      return;
  }
  try {
    const fileName = filePath.split('/').pop();
    const response = await api.get(`/download-pendukung/${fileName}`, { responseType: 'blob' });
    const blob = new Blob([response.data], { type: response.headers['content-type'] });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    
    let downloadedFileName = fileName;
    const contentDisposition = response.headers['content-disposition'];
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
    console.error("Gagal mendownload:", error);
    showAlert('error', 'Akses Ditolak', 'Gagal mengunduh file.');
  }
};

const fetchData = async () => {
  isLoading.value = true;
  try {
    const response = await api.get('/pengajuan');
    const data = response.data.data ? response.data.data : response.data;
    pengajuanList.value = Array.isArray(data) ? data : [];
  } catch (error) {
    if(error.response && error.response.status === 401) {
        showAlert('error', 'Sesi Berakhir', 'Sesi habis, silakan login ulang.');
    }
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => { fetchData(); });

const getStatusStyle = (status) => {
  if (status === 'Diterima' || status === 'Selesai') return 'bg-[#00555A]/10 text-[#00555A] border-[#00555A]/20';
  if (status === 'Ditolak') return 'bg-red-50 text-red-700 border-red-100';
  return 'bg-[#F26430]/10 text-[#d14f1f] border-[#F26430]/20'; 
};

const getStatusDot = (status) => {
  if (status === 'Diterima' || status === 'Selesai') return 'bg-[#00555A]';
  if (status === 'Ditolak') return 'bg-red-500';
  return 'bg-[#F26430] animate-pulse';
};

const confirmModal = ref({ show: false, pengajuan: null });
const isRejecting = ref(false);

const openConfirmModal = (pengajuan) => {
  confirmModal.value = { show: true, pengajuan: pengajuan };
};

const closeConfirmModal = () => {
  if (isRejecting.value) return; 
  confirmModal.value = { show: false, pengajuan: null };
};

const proceedReject = async () => {
  const pengajuan = confirmModal.value.pengajuan;
  if (!pengajuan) return;

  isRejecting.value = true;

  try {
    const formData = new FormData();
    formData.append('status', 'Ditolak');

    await api.post(`/pengajuan/${pengajuan.id}/verify`, formData);
    
    showAlert('success', 'Ditolak', 'Pengajuan berhasil ditolak.');
    
    const index = pengajuanList.value.findIndex(p => p.id === pengajuan.id);
    if(index !== -1) {
        pengajuanList.value[index].status = 'Ditolak';
    }

  } catch (error) {
    console.error(error);
    showAlert('error', 'Gagal', 'Gagal menolak pengajuan. Pastikan server merespon dengan benar.');
  } finally {
    isRejecting.value = false;
    closeConfirmModal();
  }
};

const previewModal = ref({ show: false, url: '', type: '', isLoading: false });

const openPreviewDocument = async (pengajuan) => {
    if (!pengajuan.file_balasan) {
        showAlert('error', 'File Tidak Ada', 'Dokumen balasan belum tersedia.');
        return;
    }

    previewModal.value = { show: true, url: '', type: '', isLoading: true };

    try {
        const fileName = pengajuan.file_balasan.split('/').pop();
        
        const response = await api.get(`/download-balasan/${fileName}`, {
            responseType: 'blob'
        });

        const blob = new Blob([response.data], {
            type: response.headers['content-type']
        });
        const blobUrl = URL.createObjectURL(blob);
        const ext = fileName.split('.').pop().toLowerCase();

        previewModal.value = {
            show: true,
            url: blobUrl,
            type: ext === 'pdf' ? 'pdf' : 'image',
            isLoading: false
        };

    } catch (error) {
        previewModal.value.show = false;
        showAlert('error', 'Gagal Memuat', 'Dokumen tidak dapat dimuat dari server.');
    }
};

const closePreview = () => {
    if (previewModal.value.url?.startsWith('blob:')) {
        URL.revokeObjectURL(previewModal.value.url);
    }
    previewModal.value.show = false;
    setTimeout(() => { previewModal.value.url = ''; }, 300);
};

const selectedPengajuan = ref(null);
const uploadFile = ref(null);
const fileInput = ref(null);

const isEditingMode = ref(false);
const previewUrl = ref(null);

const openUpload = (pengajuan) => {
  isEditingMode.value = false;
  selectedPengajuan.value = pengajuan;
  resetUploadState();
};

const openEditUpload = (pengajuan) => {
  isEditingMode.value = true;
  selectedPengajuan.value = pengajuan;
  resetUploadState();
};

const triggerFileSelect = () => { if (fileInput.value) fileInput.value.click(); };

const handleFileUpload = (e) => {
  const file = e.target.files[0];
  if (file) {
      if(file.size > 5 * 1024 * 1024) {
          showAlert('error', 'Ukuran Berlebih', 'Ukuran maksimal 5MB.');
          e.target.value = '';
          return;
      }
      uploadFile.value = file;
      
      if (previewUrl.value) URL.revokeObjectURL(previewUrl.value);
      previewUrl.value = URL.createObjectURL(file);
  }
};

const removeFile = () => {
  resetUploadState();
};

const cancelUpload = () => {
  if(isSubmitting.value) return; 
  selectedPengajuan.value = null;
  resetUploadState();
};

const resetUploadState = () => {
  uploadFile.value = null;
  if (previewUrl.value) {
      URL.revokeObjectURL(previewUrl.value);
      previewUrl.value = null;
  }
  if (fileInput.value) fileInput.value.value = '';
};

const confirmUpload = async () => {
  if (!selectedPengajuan.value || !uploadFile.value) return;

  const currentPengajuan = selectedPengajuan.value;
  const currentFile = uploadFile.value;

  selectedPengajuan.value = null; 
  isSubmitting.value = true;
  
  try {
    const formData = new FormData();
    formData.append('file_balasan', currentFile);
    
    if (!isEditingMode.value) {
        formData.append('status', 'Diterima'); 
    }
    
    const apiEndpoint = isEditingMode.value 
        ? `/pengajuan/${currentPengajuan.id}/update-balasan` 
        : `/pengajuan/${currentPengajuan.id}/verify`;

    await api.post(apiEndpoint, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    });
    
    const pesanSukses = isEditingMode.value 
        ? 'Dokumen berhasil diperbarui.' 
        : 'Surat balasan terkirim.';

    showAlert('success', 'Berhasil', pesanSukses);
    
    fetchData(); 
    resetUploadState();

  } catch (error) {
    showAlert('error', 'Gagal', error.response?.data?.message || 'Terjadi kesalahan saat mengupload dokumen.');
    selectedPengajuan.value = currentPengajuan;
  } finally {
    isSubmitting.value = false;
  }
};

const filteredData = computed(() => {
  if(!pengajuanList.value) return [];
  const now = new Date();
  
  return pengajuanList.value.filter((p) => {
    if (selectedJenis.value && p.jenis !== selectedJenis.value) return false;
    if (selectedRange.value === 'all') return true;
    
    const waktu = new Date(p.created_at);
    
    if (selectedRange.value === 'today') return waktu.toDateString() === now.toDateString();
    if (selectedRange.value === 'week') {
       const weekAgo = new Date(now); weekAgo.setDate(now.getDate() - 7);
       return waktu >= weekAgo;
    }
    if (selectedRange.value === 'month') {
       const monthAgo = new Date(now); monthAgo.setMonth(now.getMonth() - 1);
       return waktu >= monthAgo;
    }
    return true;
  });
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-leave-active { transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1); }
.slide-fade-enter-from, .slide-fade-leave-to { transform: translateX(20px); opacity: 0; }
</style>