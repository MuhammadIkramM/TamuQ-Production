<template>
  <div class="max-w-5xl mx-auto space-y-8 relative">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-[#00555A] flex items-center gap-2">
          <AcademicCapIcon class="w-8 h-8 text-[#F26430]" />
          E-Skripsi
        </h1>
        <p class="text-gray-500 text-sm mt-1">Jelajahi arsip skripsi dan ajukan skripsi Anda.</p>
      </div>
      
      <button 
        v-if="!authStore.isStaffOrAdmin"
        @click="openAjukanModal"
        class="flex items-center gap-2 px-4 py-2 bg-[#00555A] hover:bg-[#004448] text-white rounded-xl transition-all text-sm font-medium shadow-md shadow-[#00555A]/20 whitespace-nowrap"
      >
        <PlusIcon class="w-5 h-5" />
        Ajukan Skripsi
      </button>
    </div>

    <div v-if="!authStore.isStaffOrAdmin" class="flex items-center gap-2 border-b border-gray-200">
      <button 
        @click="activeTab = 'katalog'"
        class="px-4 py-2.5 text-sm font-bold border-b-2 transition-colors flex items-center gap-2"
        :class="activeTab === 'katalog' ? 'border-[#00555A] text-[#00555A]' : 'border-transparent text-gray-500 hover:text-gray-700'"
      >
        <Square3Stack3DIcon class="w-5 h-5" />
        Katalog Skripsi
      </button>
      <button 
        @click="activeTab = 'riwayat'"
        class="px-4 py-2.5 text-sm font-bold border-b-2 transition-colors flex items-center gap-2"
        :class="activeTab === 'riwayat' ? 'border-[#00555A] text-[#00555A]' : 'border-transparent text-gray-500 hover:text-gray-700'"
      >
        <ClockIcon class="w-5 h-5" />
        Riwayat Pengajuan Saya
      </button>
    </div>

    <div v-show="activeTab === 'katalog' || authStore.isStaffOrAdmin" class="space-y-6">
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
          <div class="relative flex-1 min-w-[200px]">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
            </div>
            <input 
              type="text" 
              v-model="searchQuery" 
              class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none transition-all"
              placeholder="Cari judul, nama, atau NIM..."
            />
          </div>
          <div class="relative min-w-[150px]">
            <select
              v-model="filterProdi"
              class="w-full appearance-none pl-3 pr-8 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none transition-all cursor-pointer"
            >
              <option value="">Semua Prodi</option>
              <option value="PTIK">PTIK</option>
              <option value="TEKOM">TEKOM</option>
            </select>
            <FunnelIcon class="w-4 h-4 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
          </div>
          <div class="relative min-w-[150px]">
            <select
              v-model="filterTahun"
              class="w-full appearance-none pl-3 pr-8 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none transition-all cursor-pointer"
            >
              <option value="">Semua Tahun</option>
              <option v-for="thn in tahunOptions" :key="thn" :value="thn">{{ thn }}</option>
            </select>
            <FunnelIcon class="w-4 h-4 text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
          </div>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <Square3Stack3DIcon class="w-5 h-5 text-[#F26430]" />
        <h2 class="text-lg font-bold text-[#00555A]">Daftar Skripsi</h2>
        <span class="text-xs font-medium bg-[#00555A]/10 text-[#00555A] px-2 py-0.5 rounded-md">
          {{ filteredSkripsi.length }} dokumen
        </span>
      </div>

      <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-for="n in 6" :key="'skel-'+n" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm animate-pulse flex flex-col gap-4">
          <div class="flex gap-4">
            <div class="w-16 h-16 bg-gray-100 rounded-xl shrink-0"></div>
            <div class="flex-1 space-y-3">
              <div class="h-4 bg-gray-100 rounded w-3/4"></div>
              <div class="h-3 bg-gray-100 rounded w-1/2"></div>
              <div class="h-3 bg-gray-100 rounded w-1/3"></div>
            </div>
          </div>
          <div class="h-10 bg-gray-100 rounded-xl mt-auto"></div>
        </div>
      </div>

      <div v-else-if="filteredSkripsi.length === 0" class="bg-white border border-dashed border-gray-300 rounded-2xl p-10 text-center">
        <DocumentTextIcon class="w-12 h-12 text-gray-300 mx-auto mb-3" />
        <p class="text-gray-400 font-medium">
          {{ (searchQuery || filterProdi || filterTahun) ? 'Tidak ada skripsi yang cocok dengan pencarian Anda.' : 'Belum ada data skripsi yang tersedia.' }}
        </p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div 
          v-for="skripsi in filteredSkripsi" 
          :key="skripsi.id_skripsi"
          @click="openDetail(skripsi)"
          class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md hover:border-[#00555A]/20 transition-all flex flex-col group h-full relative overflow-hidden cursor-pointer"
        >
          <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#00555A] opacity-0 group-hover:opacity-100 transition-opacity"></div>

          <div class="flex items-start gap-4 mb-3">
            <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 bg-gray-100 flex items-center justify-center relative group-hover:scale-105 transition-transform border border-gray-200">
              <img v-if="skripsi.foto_url" :src="skripsi.foto_url" alt="Foto" class="w-full h-full object-cover" @error="e => e.target.style.display = 'none'" />
              <DocumentTextIcon v-if="!skripsi.foto_url" class="w-8 h-8 text-[#00555A]/30 absolute" />
            </div>
            
            <div class="flex-1 min-w-0">
              <h3 class="font-bold text-gray-800 text-sm leading-snug line-clamp-2 group-hover:text-[#00555A] transition-colors" :title="skripsi.judul">
                {{ skripsi.judul }}
              </h3>
              <p class="text-xs text-gray-500 mt-1 font-medium truncate">{{ skripsi.nama }}</p>
              <p class="text-xs text-gray-400 font-mono truncate">{{ skripsi.nim }}</p>
            </div>
          </div>

          <p class="text-xs text-gray-500 line-clamp-3 mb-4 flex-1">
            {{ skripsi.abstrak || 'Tidak ada abstrak.' }}
          </p>

          <div class="flex flex-wrap items-center gap-2 mt-auto pt-4 border-t border-gray-50 mb-4">
            <span 
              class="text-[10px] font-bold px-2 py-1 rounded"
              :class="skripsi.prodi === 'PTIK' ? 'text-blue-700 bg-blue-50 border border-blue-200' : 'text-purple-700 bg-purple-50 border border-purple-200'"
            >
              {{ skripsi.prodi }}
            </span>
            <span class="text-[10px] text-gray-500 bg-gray-100 px-2 py-1 rounded font-medium flex items-center gap-1">
              <CalendarDaysIcon class="w-3 h-3" />
              {{ skripsi.tahun_selesai }}
            </span>
          </div>
          
          <div class="flex gap-2 w-full">
            <button 
              v-if="skripsi.file_preview_skripsi"
              @click.stop="previewSkripsi(skripsi, 'preview')"
              class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-[#00555A]/5 hover:bg-[#00555A]/10 text-[#00555A] rounded-lg text-xs font-bold transition-colors"
            >
              <EyeIcon class="w-4 h-4" />
              Preview PDF (Publik)
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-show="activeTab === 'riwayat' && !authStore.isStaffOrAdmin" class="space-y-4">
      <div class="flex items-center gap-2 mb-4">
        <ClockIcon class="w-5 h-5 text-[#F26430]" />
        <h2 class="text-lg font-bold text-[#00555A]">Riwayat Pengajuan Saya</h2>
      </div>

      <div v-if="isLoadingRiwayat" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-for="n in 3" :key="'r-skel-'+n" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm animate-pulse flex gap-4">
          <div class="w-16 h-16 bg-gray-100 rounded-xl shrink-0"></div>
          <div class="flex-1 space-y-3">
            <div class="h-4 bg-gray-100 rounded w-3/4"></div>
            <div class="h-3 bg-gray-100 rounded w-1/2"></div>
            <div class="h-6 bg-gray-100 rounded w-24"></div>
          </div>
        </div>
      </div>

      <div v-else-if="riwayatList.length === 0" class="bg-white border border-dashed border-gray-300 rounded-2xl p-10 text-center">
        <InboxIcon class="w-12 h-12 text-gray-300 mx-auto mb-3" />
        <p class="text-gray-500 font-medium mb-1">Kamu belum pernah mengajukan skripsi.</p>
        <p class="text-xs text-gray-400 mb-4">Klik tombol "Ajukan Skripsi" untuk mulai.</p>
        <button 
          @click="openAjukanModal"
          class="inline-flex items-center gap-2 px-4 py-2 bg-[#00555A] hover:bg-[#004448] text-white rounded-xl transition-all text-sm font-medium shadow-md"
        >
          <PlusIcon class="w-4 h-4" />
          Ajukan Sekarang
        </button>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div 
          v-for="riwayat in riwayatList" 
          :key="riwayat.id_skripsi"
          @click="openDetail(riwayat)"
          class="bg-white rounded-2xl p-5 border shadow-sm flex flex-col h-full relative cursor-pointer hover:shadow-md transition-shadow"
          :class="getStatusBorderColor(riwayat.status)"
        >
          <div class="flex items-start gap-4 mb-4">
            <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 bg-gray-100 flex items-center justify-center border border-gray-200 relative">
              <img v-if="riwayat.foto_url" :src="riwayat.foto_url" alt="Foto" class="w-full h-full object-cover" @error="e => e.target.style.display = 'none'" />
              <DocumentTextIcon v-if="!riwayat.foto_url" class="w-8 h-8 text-[#00555A]/30 absolute" />
            </div>
            
            <div class="flex-1 min-w-0">
              <h3 class="font-bold text-gray-800 text-sm leading-snug line-clamp-2" :title="riwayat.judul">
                {{ riwayat.judul }}
              </h3>
              <p class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                <ClockIcon class="w-3 h-3" />
                {{ formatDate(riwayat.created_at) }}
              </p>
            </div>
          </div>
          
          <div class="mt-auto pt-3 border-t border-gray-50 space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-xs font-medium text-gray-500 flex items-center gap-1">
                <CalendarDaysIcon class="w-3.5 h-3.5" />
                Lulus: {{ riwayat.tahun_selesai }}
              </span>
              <span :class="getStatusBadgeClass(riwayat.status)" class="px-2.5 py-1 text-[10px] font-bold rounded-lg border uppercase">
                {{ riwayat.status }}
              </span>
            </div>

            <div v-if="riwayat.status === 'ditolak' && riwayat.keterangan" class="bg-red-50 p-3 rounded-xl border border-red-100">
              <p class="text-xs font-bold text-red-700 flex items-center gap-1 mb-1">
                <ExclamationCircleIcon class="w-4 h-4" /> Alasan Penolakan:
              </p>
              <p class="text-xs text-red-600">{{ riwayat.keterangan }}</p>
            </div>

            <div v-if="riwayat.status === 'disetujui'" class="flex gap-2">
              <button 
                v-if="riwayat.file_preview_skripsi"
                @click.stop="previewSkripsi(riwayat, 'preview')"
                class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-lg text-xs font-bold transition-colors shadow-sm"
              >
                <EyeIcon class="w-4 h-4" />
                Preview Publik
              </button>
              <button 
                @click.stop="previewSkripsi(riwayat, 'full')"
                class="flex-1 flex items-center justify-center gap-1 px-3 py-2 bg-[#00555A]/5 hover:bg-[#00555A]/10 text-[#00555A] rounded-lg text-xs font-bold transition-colors"
              >
                <EyeIcon class="w-4 h-4" />
                Preview Full
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <transition name="fade">
      <div v-if="showAjukanModal" class="fixed inset-0 flex items-center justify-center z-50 bg-[#00282a]/50 backdrop-blur-sm p-4" @click.self="cancelAjukanModal">
        <div class="bg-white shadow-2xl rounded-2xl w-full max-w-lg overflow-hidden transform transition-all scale-100 flex flex-col max-h-[90vh]">
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 shrink-0">
             <div>
               <h3 class="text-lg font-bold text-[#00555A]">Ajukan Skripsi</h3>
               <p class="text-xs text-gray-500">Isi formulir untuk pengajuan arsip skripsi.</p>
             </div>
             <button @click="cancelAjukanModal" class="text-gray-400 hover:text-gray-600 transition"><XMarkIcon class="w-6 h-6" /></button>
          </div>
          
          <div class="p-6 overflow-y-auto space-y-5">
             <div class="space-y-1">
               <label class="text-xs font-bold text-gray-500 uppercase">Foto Mahasiswa <span class="text-gray-400 normal-case font-normal">(opsional)</span></label>
               <div class="flex items-center gap-4">
                 <div class="w-16 h-16 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                    <img v-if="formAjukan.fotoPreview" :src="formAjukan.fotoPreview" class="w-full h-full object-cover" />
                    <UserIcon v-else class="w-8 h-8 text-gray-300" />
                 </div>
                 <div class="flex-1">
                   <input 
                     type="file" 
                     ref="fotoInputRef"
                     accept="image/jpeg,image/png"
                     @change="handleFotoChange"
                     class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#00555A]/10 file:text-[#00555A] hover:file:bg-[#00555A]/20"
                   />
                   <p class="text-[10px] text-gray-400 mt-1">Maksimal 2MB (JPEG, PNG)</p>
                   <p v-if="errors.foto" class="text-[10px] text-red-500 mt-1">{{ errors.foto[0] }}</p>
                 </div>
               </div>
             </div>

             <div class="space-y-1">
               <label class="text-xs font-bold text-gray-500 uppercase">Judul Skripsi <span class="text-red-500">*</span></label>
               <input type="text" v-model="formAjukan.judul" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none" :class="{'border-red-300 focus:ring-red-500': errors.judul}" placeholder="Masukkan judul skripsi..." />
               <p v-if="errors.judul" class="text-xs text-red-500">{{ errors.judul[0] }}</p>
             </div>

             <div class="space-y-1">
               <label class="text-xs font-bold text-gray-500 uppercase">Abstrak <span class="text-gray-400 normal-case font-normal">(opsional)</span></label>
               <textarea v-model="formAjukan.abstrak" rows="4" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none resize-none" :class="{'border-red-300 focus:ring-red-500': errors.abstrak}" placeholder="Masukkan abstrak skripsi..."></textarea>
               <p v-if="errors.abstrak" class="text-xs text-red-500">{{ errors.abstrak[0] }}</p>
             </div>

             <div class="space-y-1">
               <label class="text-xs font-bold text-gray-500 uppercase">Tahun Lulus <span class="text-red-500">*</span></label>
               <input type="number" min="2010" :max="new Date().getFullYear()" v-model="formAjukan.tahun_selesai" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none" :class="{'border-red-300 focus:ring-red-500': errors.tahun_selesai}" placeholder="Contoh: 2023" />
               <p v-if="errors.tahun_selesai" class="text-xs text-red-500">{{ errors.tahun_selesai[0] }}</p>
             </div>

             <div class="space-y-1">
               <label class="text-xs font-bold text-gray-500 uppercase">File Lengkap Skripsi <span class="text-red-500">*</span></label>
               <input 
                 type="file" 
                 ref="fileSkripsiRef"
                 accept="application/pdf"
                 @change="handleFileSkripsiChange"
                 class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#00555A]/10 file:text-[#00555A] hover:file:bg-[#00555A]/20" 
                 :class="{'border-red-300': errors.file_skripsi}"
               />
               <p class="text-[10px] text-gray-400 mt-1">Hanya PDF, Maksimal 30MB.</p>
               <p v-if="errors.file_skripsi" class="text-[10px] text-red-500">{{ errors.file_skripsi[0] }}</p>
             </div>

             <div class="space-y-1">
               <label class="text-xs font-bold text-gray-500 uppercase">File Preview <span class="text-gray-400 normal-case font-normal">(opsional, u/ publik)</span></label>
               <input 
                 type="file" 
                 ref="filePreviewRef"
                 accept="application/pdf"
                 @change="handleFilePreviewChange"
                 class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#00555A]/10 file:text-[#00555A] hover:file:bg-[#00555A]/20" 
                 :class="{'border-red-300': errors.file_preview_skripsi}"
               />
               <p class="text-[10px] text-gray-400 mt-1">Hanya PDF, Maksimal 30MB (Bab 1-3 dll).</p>
               <p v-if="errors.file_preview_skripsi" class="text-[10px] text-red-500">{{ errors.file_preview_skripsi[0] }}</p>
             </div>
          </div>
          
          <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-2xl border-t border-gray-100 shrink-0">
            <button @click="cancelAjukanModal" :disabled="isSubmitting" class="px-4 py-2 text-gray-700 text-sm font-medium hover:bg-gray-100 rounded-lg transition disabled:opacity-50">Batal</button>
            <button @click="submitPengajuan" :disabled="isSubmitting || !isFormValid" class="px-4 py-2 bg-[#00555A] text-white rounded-lg text-sm font-medium hover:bg-[#004448] shadow-md disabled:opacity-50 transition flex items-center gap-2">
              <svg v-if="isSubmitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              <PaperAirplaneIcon v-if="!isSubmitting" class="w-4 h-4" />
              <span>{{ isSubmitting ? 'Mengirim...' : 'Kirim Pengajuan' }}</span>
            </button>
          </div>
        </div>
      </div>
    </transition>

    <Teleport to="body">
      <transition name="fade">
        <div v-if="showPreviewModal" class="fixed inset-0 flex items-center justify-center z-[90] bg-[#00282a]/50 backdrop-blur-sm p-4" @click.self="closePreview">
          <div class="bg-white shadow-2xl rounded-2xl w-full max-w-4xl h-[85vh] overflow-hidden transform transition-all scale-100 flex flex-col relative">
          
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl shrink-0">
             <div>
               <h3 class="text-lg font-bold text-[#00555A] flex items-center gap-2">
                 <DocumentTextIcon class="w-5 h-5 text-[#F26430]" />
                 Preview Skripsi
               </h3>
               <p class="text-xs text-gray-400 truncate max-w-[300px] sm:max-w-[500px] mt-0.5">{{ previewTitle }}</p>
             </div>
             <button @click="closePreview" class="text-gray-400 hover:text-gray-600 bg-white shadow-sm border border-gray-200 rounded-full p-1.5 transition-colors">
               <XMarkIcon class="w-5 h-5" />
             </button>
          </div>
          
          <div class="flex-1 bg-gray-100 relative">
            <div v-if="previewLoading" class="absolute inset-0 flex items-center justify-center bg-white/80 z-10">
              <div class="flex flex-col items-center gap-3">
                <div class="w-10 h-10 border-4 border-[#00555A]/20 border-t-[#00555A] rounded-full animate-spin"></div>
                <p class="text-sm text-gray-500 font-medium">Memuat dokumen PDF...</p>
              </div>
            </div>
            <iframe 
              v-if="previewUrl" 
              :src="previewUrl" 
              class="w-full h-full"
              frameborder="0"
            ></iframe>
            <div v-if="previewError" class="absolute inset-0 flex items-center justify-center">
              <div class="text-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <XCircleIcon class="w-12 h-12 text-red-400 mx-auto mb-3" />
                <p class="text-sm text-gray-500 font-medium">Gagal memuat dokumen.</p>
                <p class="text-xs text-gray-400 mt-1">File mungkin tidak tersedia atau terjadi kesalahan server.</p>
              </div>
            </div>
          </div>
          
          <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end shrink-0 gap-3">
             <button @click="downloadSkripsi(previewSkripsiObj, previewType)" class="flex items-center gap-2 px-4 py-2 bg-[#F26430] hover:bg-[#D65020] text-white rounded-lg text-sm font-medium transition shadow-sm">
                <ArrowDownTrayIcon class="w-4 h-4" /> Unduh Dokumen
             </button>
          </div>
        </div>
      </div>
    </transition>
    </Teleport>

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
    
    <DetailSkripsiModal 
      :show="showDetailModal" 
      :skripsi="selectedDetailSkripsi" 
      @close="showDetailModal = false"
      @preview="(type) => previewSkripsi(selectedDetailSkripsi, type)"
    />

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import api from '@/api';
import { useAuthStore } from '@/stores/auth';
import { useProgressStore } from '@/stores/progress';
import DetailSkripsiModal from '@/components/DetailSkripsiModal.vue';
import { 
  AcademicCapIcon, DocumentTextIcon, MagnifyingGlassIcon, CalendarDaysIcon,
  EyeIcon, ArrowDownTrayIcon, XMarkIcon, XCircleIcon, PlusIcon, ClockIcon,
  CheckCircleIcon, ExclamationCircleIcon, UserIcon, InboxIcon, PaperAirplaneIcon
} from "@heroicons/vue/24/outline";
import { Square3Stack3DIcon, FunnelIcon } from "@heroicons/vue/24/solid";

const authStore = useAuthStore();
const activeTab = ref('katalog'); 
const showDetailModal = ref(false);
const selectedDetailSkripsi = ref(null);

const skripsiList = ref([]);
const isLoading = ref(true);
const searchQuery = ref("");
const filterProdi = ref("");
const filterTahun = ref("");

const riwayatList = ref([]);
const isLoadingRiwayat = ref(false);

const showAjukanModal = ref(false);
const isSubmitting = ref(false);
const formAjukan = ref({
  judul: '',
  abstrak: '',
  tahun_selesai: ''
});
const fileFoto = ref(null);
const fileSkripsi = ref(null);
const filePreview = ref(null);
const errors = ref({});

const fotoInputRef = ref(null);
const fileSkripsiRef = ref(null);
const filePreviewRef = ref(null);

const alert = ref({
  show: false,
  type: 'success',
  title: '',
  message: ''
});

const showPreviewModal = ref(false);
const previewUrl = ref(null);
const previewTitle = ref("");
const previewLoading = ref(false);
const previewError = ref(false);
const previewType = ref('full');
const previewSkripsiObj = ref(null);

const showAlert = (type, title, message) => {
  alert.value = { show: true, type, title, message };
  setTimeout(() => { alert.value.show = false; }, 4000); 
};

const fetchSkripsi = async () => {
    isLoading.value = true;
    try {
        const response = await api.get('/skripsi');
        skripsiList.value = response.data.data || [];
    } catch (error) {
        console.error("Gagal memuat data skripsi:", error);
    } finally {
        isLoading.value = false;
    }
};

const fetchRiwayat = async () => {
    isLoadingRiwayat.value = true;
    try {
        const response = await api.get('/skripsi/saya');
        riwayatList.value = response.data.data || [];
    } catch (error) {
        console.error("Gagal memuat riwayat skripsi:", error);
    } finally {
        isLoadingRiwayat.value = false;
    }
};

watch(activeTab, (newTab) => {
    if (newTab === 'riwayat') {
        fetchRiwayat();
    } else {
        fetchSkripsi();
    }
});

onMounted(() => {
    fetchSkripsi();
});

const tahunOptions = computed(() => {
    const years = skripsiList.value
        .map(s => s.tahun_selesai)
        .filter(y => y != null && y !== '');
    return [...new Set(years)].sort((a, b) => b - a);
});

const filteredSkripsi = computed(() => {
    let result = skripsiList.value;

    if (searchQuery.value) {
        const term = searchQuery.value.toLowerCase();
        result = result.filter(s => 
            (s.judul && s.judul.toLowerCase().includes(term)) ||
            (s.nama && s.nama.toLowerCase().includes(term)) ||
            (s.nim && s.nim.toLowerCase().includes(term))
        );
    }

    if (filterProdi.value) {
        result = result.filter(s => s.prodi === filterProdi.value);
    }

    if (filterTahun.value) {
        result = result.filter(s => String(s.tahun_selesai) === String(filterTahun.value));
    }

    return result;
});

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const options = { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateStr).toLocaleDateString('id-ID', options);
};

const getStatusBadgeClass = (status) => {
    if (status === 'disetujui') return 'bg-green-50 text-green-700 border-green-200';
    if (status === 'ditolak') return 'bg-red-50 text-red-700 border-red-200';
    return 'bg-yellow-50 text-yellow-700 border-yellow-200'; // menunggu
};

const getStatusBorderColor = (status) => {
    if (status === 'disetujui') return 'border-t-4 border-t-green-500 border-x-gray-100 border-b-gray-100';
    if (status === 'ditolak') return 'border-t-4 border-t-red-500 border-x-gray-100 border-b-gray-100';
    return 'border-t-4 border-t-yellow-400 border-x-gray-100 border-b-gray-100'; // menunggu
};

const openDetail = (skripsi) => {
    selectedDetailSkripsi.value = skripsi;
    showDetailModal.value = true;
};

const openAjukanModal = () => {
    formAjukan.value = { judul: '', abstrak: '', tahun_selesai: '' };
    fileFoto.value = null;
    fileSkripsi.value = null;
    filePreview.value = null;
    formAjukan.value.fotoPreview = null;
    errors.value = {};
    if(fotoInputRef.value) fotoInputRef.value.value = '';
    if(fileSkripsiRef.value) fileSkripsiRef.value.value = '';
    if(filePreviewRef.value) filePreviewRef.value.value = '';
    showAjukanModal.value = true;
};

const cancelAjukanModal = () => {
    if (isSubmitting.value) return;
    showAjukanModal.value = false;
};

const handleFotoChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) {
        errors.value.foto = ['Ukuran foto maksimal 2MB.'];
        e.target.value = '';
        fileFoto.value = null;
        formAjukan.value.fotoPreview = null;
        return;
    }
    errors.value.foto = null;
    fileFoto.value = file;
    formAjukan.value.fotoPreview = URL.createObjectURL(file);
};

const handleFileSkripsiChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    if (file.type !== 'application/pdf') {
        errors.value.file_skripsi = ['Hanya file PDF yang diperbolehkan.'];
        e.target.value = '';
        fileSkripsi.value = null;
        return;
    }
    if (file.size > 30 * 1024 * 1024) {
        errors.value.file_skripsi = ['Ukuran file skripsi maksimal 30MB.'];
        e.target.value = '';
        fileSkripsi.value = null;
        return;
    }
    errors.value.file_skripsi = null;
    fileSkripsi.value = file;
};

const handleFilePreviewChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    if (file.type !== 'application/pdf') {
        errors.value.file_preview_skripsi = ['Hanya file PDF yang diperbolehkan.'];
        e.target.value = '';
        filePreview.value = null;
        return;
    }
    if (file.size > 30 * 1024 * 1024) {
        errors.value.file_preview_skripsi = ['Ukuran file preview maksimal 30MB.'];
        e.target.value = '';
        filePreview.value = null;
        return;
    }
    errors.value.file_preview_skripsi = null;
    filePreview.value = file;
};

const isFormValid = computed(() => {
    return formAjukan.value.judul && formAjukan.value.tahun_selesai && fileSkripsi.value;
});

const submitPengajuan = async () => {
    if (isSubmitting.value || !isFormValid.value) return;

    isSubmitting.value = true;
    errors.value = {};

    const formData = new FormData();
    formData.append('judul', formAjukan.value.judul);
    formData.append('tahun_selesai', formAjukan.value.tahun_selesai);
    if (formAjukan.value.abstrak) formData.append('abstrak', formAjukan.value.abstrak);
    if (fileFoto.value) formData.append('foto', fileFoto.value);
    formData.append('file_skripsi', fileSkripsi.value);
    if (filePreview.value) formData.append('file_preview_skripsi', filePreview.value);

    try {
        await api.post('/skripsi', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        showAlert('success', 'Berhasil', 'Pengajuan berhasil dikirim, menunggu verifikasi staff.');
        showAjukanModal.value = false;
        
        if (activeTab.value === 'riwayat') {
            fetchRiwayat();
        } else {
            activeTab.value = 'riwayat';
        }
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors || {};
            showAlert('error', 'Validasi Gagal', 'Silakan periksa kembali form pengajuan.');
        } else {
            showAlert('error', 'Terjadi Kesalahan', e.response?.data?.message || 'Gagal mengirim pengajuan.');
        }
    } finally {
        isSubmitting.value = false;
    }
};

let previewAbortController = null;

const previewSkripsi = async (skripsi, type = 'full') => {
    if (previewAbortController) {
        previewAbortController.abort();
    }
    previewAbortController = new AbortController();

    previewTitle.value = skripsi.judul;
    previewType.value = type;
    previewSkripsiObj.value = skripsi;
    previewLoading.value = true;
    previewError.value = false;
    previewUrl.value = null;
    showPreviewModal.value = true;

    try {
        const endpoint = type === 'full' 
            ? `/skripsi/${skripsi.id_skripsi}/preview` 
            : `/skripsi/${skripsi.id_skripsi}/preview-file`;
        await api.get(endpoint, {
            responseType: 'blob',
            actionType: 'preview',
            signal: previewAbortController.signal
        });
        previewUrl.value = `${api.defaults.baseURL}${endpoint}`;
    } catch (e) {
        if (e.message === 'canceled' || e.code === 'ERR_CANCELED') {
            console.log('Preview fetch canceled');
        } else if (e.response && e.response.status === 403) {
            console.error('Preview error 403:', e);
            previewError.value = true;
            showAlert('error', 'Akses Ditolak', 'Anda tidak memiliki akses ke file ini.');
            showPreviewModal.value = false;
        } else {
            console.error('Preview error:', e);
            previewError.value = true;
        }
    } finally {
        previewLoading.value = false;
    }
};

const closePreview = () => {
    showPreviewModal.value = false;
    previewUrl.value = null;
    previewTitle.value = "";
    previewError.value = false;

    if (previewAbortController) {
        previewAbortController.abort();
        previewAbortController = null;
    }

    const progressStore = useProgressStore();
    progressStore.stopAllByType('preview');
};

const downloadSkripsi = async (skripsi, type = 'full') => {
    try {
        const endpoint = type === 'full' 
            ? `/skripsi/${skripsi.id_skripsi}/download` 
            : `/skripsi/${skripsi.id_skripsi}/download-file`;
        const response = await api.get(endpoint, {
            responseType: 'blob',
            actionType: 'download'
        });
        
        const blob = new Blob([response.data], { type: 'application/pdf' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        const typeLabel = type === 'full' ? 'Full' : 'Preview';
        const cleanTitle = skripsi.judul.replace(/[^a-zA-Z0-9]/g, '_').substring(0, 30);
        link.setAttribute('download', `Skripsi_${typeLabel}_${skripsi.nim}_${cleanTitle}.pdf`);
        
        document.body.appendChild(link);
        link.click();
        
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        if (error.response && error.response.status === 403) {
            console.error('Download error 403:', error);
            showAlert('error', 'Akses Ditolak', 'Anda tidak memiliki akses untuk mengunduh file ini.');
        } else {
            console.error('Download error:', error);
            alert('Gagal mengunduh file skripsi. File mungkin tidak tersedia.');
        }
    }
};

</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-leave-active { transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1); }
.slide-fade-enter-from, .slide-fade-leave-to { transform: translateX(20px); opacity: 0; }
</style>
