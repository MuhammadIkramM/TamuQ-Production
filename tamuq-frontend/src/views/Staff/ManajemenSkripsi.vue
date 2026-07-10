<template>
  <div class="space-y-8 relative">
    
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
          class="flex items-center gap-2 px-4 py-2 bg-[#00555A] hover:bg-[#004448] text-white rounded-xl transition-all text-sm font-medium shadow-md shadow-[#00555A]/20 whitespace-nowrap"
        >
          <PlusIcon class="w-5 h-5" />
          Tambah Skripsi
        </button>
      </div>
    </div>

    <div class="flex flex-wrap items-center gap-2 border-b border-gray-200">
      <button 
        v-for="tab in ['Menunggu', 'Disetujui', 'Ditolak', 'Semua']" 
        :key="tab"
        @click="activeStatusTab = tab"
        class="px-4 py-2.5 text-sm font-bold border-b-2 transition-colors flex items-center gap-2"
        :class="activeStatusTab === tab ? 'border-[#00555A] text-[#00555A]' : 'border-transparent text-gray-500 hover:text-gray-700'"
      >
        {{ tab }}
      </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-5 flex flex-col md:flex-row md:items-center justify-between bg-[#00555A] gap-4 rounded-t-2xl">
        <div class="flex items-center gap-3">
          <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <DocumentTextIcon class="w-5 h-5 text-[#F26430]" />
            Daftar Skripsi ({{ activeStatusTab }})
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

      <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
          <thead class="sticky top-0 z-10 shadow-sm">
            <tr class="bg-[#66999C] text-white text-xs uppercase tracking-wider font-semibold">
              <th class="px-4 py-3 text-center">Foto</th>
              <th class="px-6 py-3">Detail Skripsi</th>
              <th class="px-6 py-3">Mahasiswa</th>
              <th class="px-6 py-3 text-center">Status</th>
              <th class="px-6 py-3 text-center">Waktu Pengajuan</th>
              <th class="px-6 py-3 text-center">File</th>
              <th class="px-6 py-3">Keterangan</th>
              <th class="px-6 py-3 text-center">Tindakan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50 relative">
            <tr v-if="isLoadingFetch" class="absolute inset-0 bg-white/50 backdrop-blur-sm z-20 flex items-center justify-center min-h-[200px]">
                <td colspan="6" class="text-center">
                    <ArrowPathIcon class="w-8 h-8 animate-spin text-[#00555A] mx-auto mb-2" />
                    <span class="text-sm font-medium text-[#00555A]">Memuat data...</span>
                </td>
            </tr>
            <tr v-for="(skripsi, index) in filteredSkripsi" :key="skripsi.id_skripsi" class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-4 align-top text-center">
                  <div class="w-12 h-12 mx-auto rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center shrink-0 overflow-hidden relative">
                    <img v-if="skripsi.foto_url" :src="skripsi.foto_url" alt="Foto" class="w-full h-full object-cover" @error="e => e.target.style.display = 'none'" />
                    <DocumentTextIcon v-if="!skripsi.foto_url" class="w-6 h-6 text-[#00555A]/30 absolute" />
                  </div>
              </td>
              
              <td class="px-6 py-4 align-top cursor-pointer group" @click="openDetail(skripsi)">
                  <div class="min-w-0 max-w-[250px]">
                    <p class="text-sm font-bold text-gray-800 whitespace-normal line-clamp-2 group-hover:text-[#00555A] transition-colors" :title="skripsi.judul">{{ skripsi.judul }}</p>
                    <p class="text-xs text-gray-500 line-clamp-1 mt-1" :title="skripsi.abstrak">{{ skripsi.abstrak || 'Tidak ada abstrak.' }}</p>
                    <div class="flex items-center gap-2 mt-2">
                      <span class="px-1.5 py-0.5 rounded text-[10px] bg-gray-100 text-gray-600 border font-medium flex items-center gap-1">
                        <CalendarDaysIcon class="w-3 h-3" /> {{ skripsi.tahun_selesai }}
                      </span>
                    </div>
                  </div>
              </td>
              
              <td class="px-6 py-4 align-top">
                <p class="text-sm font-medium text-gray-700">{{ skripsi.nama }}</p>
                <div class="flex flex-col items-start gap-1 mt-0.5">
                  <p class="text-xs text-gray-500 font-mono">{{ skripsi.nim }}</p>
                  <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                    {{ skripsi.prodi }}
                  </span>
                </div>
              </td>
              
              <td class="px-6 py-4 text-center align-top">
                <div class="flex flex-col items-center gap-1.5">
                    <span :class="getStatusBadgeClass(skripsi.status)" class="px-2.5 py-1 text-[10px] font-bold rounded-lg border uppercase inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotClass(skripsi.status)"></span>
                        {{ skripsi.status }}
                    </span>
                </div>
              </td>
              
              <td class="px-6 py-4 text-center align-top">
                <div class="flex items-center justify-center gap-1 text-xs text-gray-500 font-medium">
                  <ClockIcon class="w-4 h-4 text-gray-400" />
                  {{ formatDate(skripsi.created_at) }}
                </div>
              </td>
              
              <td class="px-6 py-4 text-center align-top">
                <div class="flex flex-col items-center gap-1.5">
                    <button @click="previewSkripsi(skripsi, 'full')" class="text-[10px] text-[#00555A] hover:bg-[#00555A]/10 px-2 py-1 rounded transition border border-transparent hover:border-[#00555A]/20 flex items-center gap-1 whitespace-nowrap w-full justify-center">
                        <DocumentIcon class="w-3 h-3" /> File Skripsi
                    </button>
                    <button v-if="skripsi.file_preview_skripsi" @click="previewSkripsi(skripsi, 'preview')" class="text-[10px] text-gray-600 hover:bg-gray-100 px-2 py-1 rounded transition border border-transparent hover:border-gray-200 flex items-center gap-1 whitespace-nowrap w-full justify-center">
                        <DocumentIcon class="w-3 h-3" /> File Preview
                    </button>
                </div>
              </td>

              <td class="px-6 py-4 align-top max-w-[200px]">
                <p class="text-xs text-gray-600 whitespace-normal line-clamp-3" :title="skripsi.keterangan || '-'">
                  {{ skripsi.keterangan || '-' }}
                </p>
              </td>
              
              <td class="px-6 py-4 text-center align-top">
                <!-- Actions for Menunggu -->
                <div v-if="skripsi.status === 'menunggu'" class="flex flex-col gap-1.5 w-24 mx-auto mb-2 border-b border-gray-100 pb-2">
                  <button @click="openTerimaModal(skripsi)" class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 bg-[#00555A] hover:bg-[#004448] text-white rounded-lg text-xs font-medium transition shadow-sm w-full">
                    <CheckBadgeIcon class="w-4 h-4" /> Terima
                  </button>
                  <button @click="openTolakModal(skripsi)" class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 border border-red-200 rounded-lg text-xs font-medium transition shadow-sm w-full">
                    <XMarkIcon class="w-4 h-4" /> Tolak
                  </button>
                </div>
                
                <div class="flex justify-center gap-2">
                  <button @click="openSkripsiModal('edit', skripsi)" class="p-1.5 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg transition border border-amber-200" title="Edit Data">
                    <PencilSquareIcon class="w-4 h-4" />
                  </button>
                  <button @click="deleteSkripsi(skripsi)" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition border border-red-200" title="Hapus Permanen">
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredSkripsi.length === 0 && !isLoadingFetch">
              <td colspan="8" class="px-6 py-8 text-center text-gray-400 text-sm border-t border-gray-100">
                <InboxIcon class="w-12 h-12 mx-auto mb-2 text-gray-300" />
                {{ searchQuery ? 'Data skripsi tidak ditemukan.' : 'Belum ada data skripsi.' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <transition name="fade">
      <div v-if="showSkripsiModal" class="fixed inset-0 flex items-center justify-center z-50 bg-[#00282a]/50 backdrop-blur-sm p-4" @click.self="cancelSkripsi">
        <div class="bg-white shadow-2xl rounded-2xl w-full max-w-2xl overflow-hidden transform transition-all scale-100 flex flex-col max-h-[90vh]">
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 shrink-0">
             <h3 class="text-lg font-bold text-[#00555A]">{{ isEditing ? 'Edit Data Skripsi' : 'Tambah Skripsi Baru' }}</h3>
             <button @click="cancelSkripsi" class="text-gray-400 hover:text-gray-600 transition"><XMarkIcon class="w-6 h-6" /></button>
          </div>
          
          <div class="p-6 overflow-y-auto space-y-5">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                 <div class="space-y-4">
                     <div class="space-y-1">
                       <label class="text-xs font-bold text-gray-500 uppercase">Nama Mahasiswa <span class="text-red-500">*</span></label>
                       <input type="text" v-model="formSkripsi.nama" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] text-sm outline-none" :class="{'border-red-300': errors.nama}" placeholder="Nama lengkap..." />
                       <p v-if="errors.nama" class="text-[10px] text-red-500">{{ errors.nama[0] }}</p>
                     </div>
                     <div class="grid grid-cols-2 gap-3">
                         <div class="space-y-1">
                           <label class="text-xs font-bold text-gray-500 uppercase">NIM <span class="text-red-500">*</span></label>
                           <input type="text" v-model="formSkripsi.nim" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] text-sm outline-none" :class="{'border-red-300': errors.nim}" placeholder="NIM" />
                         </div>
                         <div class="space-y-1">
                           <label class="text-xs font-bold text-gray-500 uppercase">Prodi <span class="text-red-500">*</span></label>
                           <select v-model="formSkripsi.prodi" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] text-sm outline-none" :class="{'border-red-300': errors.prodi}">
                             <option value="" disabled>Pilih</option>
                             <option value="PTIK">PTIK</option>
                             <option value="TEKOM">TEKOM</option>
                           </select>
                         </div>
                     </div>
                     <div class="space-y-1">
                       <label class="text-xs font-bold text-gray-500 uppercase">Tahun Lulus <span class="text-red-500">*</span></label>
                       <input type="number" min="2000" max="2100" v-model="formSkripsi.tahun_selesai" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] text-sm outline-none" :class="{'border-red-300': errors.tahun_selesai}" placeholder="Tahun" />
                     </div>
                     <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-500 uppercase">Status & Keterangan</label>
                        <select v-model="formSkripsi.status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] text-sm outline-none mb-2">
                           <option value="menunggu">Menunggu</option>
                           <option value="disetujui">Disetujui</option>
                           <option value="ditolak">Ditolak</option>
                        </select>
                        <textarea v-model="formSkripsi.keterangan" rows="2" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] text-sm outline-none resize-none" placeholder="Keterangan opsional..."></textarea>
                     </div>
                 </div>

                 <div class="space-y-4">
                     <div class="space-y-1">
                       <label class="text-xs font-bold text-gray-500 uppercase">Judul Skripsi <span class="text-red-500">*</span></label>
                       <textarea v-model="formSkripsi.judul" rows="2" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] text-sm outline-none resize-none" :class="{'border-red-300': errors.judul}"></textarea>
                     </div>
                     <div class="space-y-1">
                       <label class="text-xs font-bold text-gray-500 uppercase">Abstrak</label>
                       <textarea v-model="formSkripsi.abstrak" rows="3" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] text-sm outline-none resize-none"></textarea>
                     </div>
                     
                     <div class="space-y-2 bg-gray-50 p-3 rounded-xl border border-gray-200">
                        <label class="text-[10px] font-bold text-gray-500 uppercase">Foto Mahasiswa (Max 2MB, JPG/PNG)</label>
                        <div class="flex items-center gap-3">
                          <div v-if="isEditing" class="w-12 h-12 rounded-lg bg-white border border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                             <img v-if="formSkripsi.fotoPreview" :src="formSkripsi.fotoPreview" class="w-full h-full object-cover" />
                             <img v-else-if="formSkripsi.foto_url_existing" :src="formSkripsi.foto_url_existing" class="w-full h-full object-cover" @error="e => e.target.style.display = 'none'" />
                             <UserIcon v-else class="w-6 h-6 text-gray-300" />
                          </div>
                          <div class="flex-1">
                            <input type="file" @change="e => handleFileChange(e, 'foto', 'image')" accept="image/jpeg,image/png" class="w-full text-xs" />
                            <p v-if="isEditing" class="text-[10px] text-amber-600 mt-1">* Kosongkan jika tidak ingin mengubah foto</p>
                          </div>
                        </div>
                     </div>
                     <div class="space-y-1 bg-gray-50 p-3 rounded-xl border border-gray-200">
                        <label class="text-[10px] font-bold text-gray-500 uppercase">File Skripsi (Max 30MB, PDF) <span v-if="!isEditing" class="text-red-500">*</span></label>
                        <input type="file" @change="e => handleFileChange(e, 'file_skripsi', 'pdf')" accept="application/pdf" class="w-full text-xs" />
                        <p v-if="isEditing" class="text-[10px] text-amber-600 mt-1">* Kosongkan jika tidak ingin mengubah file</p>
                     </div>
                     <div class="space-y-1 bg-gray-50 p-3 rounded-xl border border-gray-200">
                        <label class="text-[10px] font-bold text-gray-500 uppercase">File Preview (Max 30MB, PDF)</label>
                        <input type="file" @change="e => handleFileChange(e, 'file_preview_skripsi', 'pdf')" accept="application/pdf" class="w-full text-xs" />
                        <p v-if="isEditing" class="text-[10px] text-amber-600 mt-1">* Kosongkan jika tidak ingin mengubah file</p>
                     </div>
                 </div>
             </div>
          </div>
          
          <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-2xl border-t border-gray-100 shrink-0">
            <button @click="cancelSkripsi" :disabled="isSubmitting" class="px-4 py-2 text-gray-700 text-sm font-medium hover:bg-gray-100 rounded-lg transition disabled:opacity-50">Batal</button>
            <button @click="saveSkripsi" :disabled="isSubmitting" class="px-4 py-2 bg-[#00555A] text-white rounded-lg text-sm font-medium hover:bg-[#004448] shadow-md disabled:opacity-50 transition flex items-center gap-2">
              <svg v-if="isSubmitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              <span>{{ isSubmitting ? 'Menyimpan...' : (isEditing ? 'Update Skripsi' : 'Simpan Skripsi') }}</span>
            </button>
          </div>
        </div>
      </div>
    </transition>

    <transition name="fade">
      <div v-if="showTerimaModal" class="fixed inset-0 flex items-center justify-center z-[60] bg-[#00282a]/50 backdrop-blur-sm p-4" @click.self="cancelTerima">
        <div class="bg-white shadow-2xl rounded-2xl w-full max-w-md overflow-hidden transform transition-all scale-100 flex flex-col">
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <div>
              <h3 class="text-lg font-bold text-[#00555A] flex items-center gap-2">
                <CheckBadgeIcon class="w-5 h-5" /> Terima Pengajuan
              </h3>
              <p class="text-xs text-gray-500 mt-1">Untuk: {{ selectedVerifySkripsi?.nama }}</p>
            </div>
            <button @click="cancelTerima" :disabled="isSubmitting" class="text-gray-400 hover:text-gray-600 transition"><XMarkIcon class="w-6 h-6" /></button>
          </div>
          <div class="p-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan (Opsional)</label>
            <textarea
              v-model="terimaKeterangan"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] outline-none transition resize-none"
              placeholder="Tulis keterangan opsional..."
            ></textarea>
          </div>
          <div class="px-6 py-4 bg-gray-50 flex gap-3 border-t border-gray-100">
            <button @click="cancelTerima" :disabled="isSubmitting" class="flex-1 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition disabled:opacity-50">Batal</button>
            <button @click="submitTerima" :disabled="isSubmitting" class="flex-1 px-4 py-2 bg-[#00555A] hover:bg-[#004448] text-white rounded-lg text-sm font-medium transition shadow-md flex items-center justify-center gap-2 disabled:opacity-50">
              <ArrowPathIcon v-if="isSubmitting" class="w-4 h-4 animate-spin" />
              <CheckBadgeIcon v-else class="w-4 h-4" />
              {{ isSubmitting ? 'Memproses...' : 'Ya, Terima' }}
            </button>
          </div>
        </div>
      </div>
    </transition>

    <transition name="fade">
      <div v-if="showTolakModal" class="fixed inset-0 flex items-center justify-center z-[60] bg-[#00282a]/50 backdrop-blur-sm p-4" @click.self="cancelTolak">
        <div class="bg-white shadow-2xl rounded-2xl w-full max-w-md overflow-hidden transform transition-all scale-100 flex flex-col">
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <div>
              <h3 class="text-lg font-bold text-red-600 flex items-center gap-2">
                <ExclamationTriangleIcon class="w-5 h-5" /> Tolak Pengajuan
              </h3>
              <p class="text-xs text-gray-500 mt-1">Untuk: {{ selectedVerifySkripsi?.nama }}</p>
            </div>
            <button @click="cancelTolak" :disabled="isSubmitting" class="text-gray-400 hover:text-gray-600 transition"><XMarkIcon class="w-6 h-6" /></button>
          </div>
          <div class="p-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
            <textarea
              v-model="tolakKeterangan"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-400 focus:border-red-400 outline-none transition resize-none"
              placeholder="Tulis alasan penolakan agar mahasiswa dapat memperbaiki pengajuannya..."
            ></textarea>
          </div>
          <div class="px-6 py-4 bg-gray-50 flex gap-3 border-t border-gray-100">
            <button @click="cancelTolak" :disabled="isSubmitting" class="flex-1 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition disabled:opacity-50">Batal</button>
            <button @click="submitTolak" :disabled="isSubmitting || !tolakKeterangan.trim()" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition shadow-md flex items-center justify-center gap-2 disabled:opacity-50">
              <ArrowPathIcon v-if="isSubmitting" class="w-4 h-4 animate-spin" />
              <XMarkIcon v-else class="w-4 h-4" />
              {{ isSubmitting ? 'Memproses...' : 'Ya, Tolak' }}
            </button>
          </div>
        </div>
      </div>
    </transition>

    <Teleport to="body">
      <transition name="fade">
        <div v-if="showPreviewModal" class="fixed inset-0 flex items-center justify-center z-[90] bg-[#00282a]/70 backdrop-blur-sm p-4" @click.self="closePreview">
          <div class="bg-white shadow-2xl rounded-2xl w-full max-w-4xl h-[85vh] overflow-hidden transform transition-all scale-100 flex flex-col">
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl shrink-0">
             <div>
               <h3 class="text-lg font-bold text-[#00555A]">Preview: {{ previewType === 'full' ? 'Skripsi Lengkap' : 'Preview Skripsi' }}</h3>
               <p class="text-xs text-gray-400 truncate max-w-[400px]">{{ previewTitle }}</p>
             </div>
             <button @click="closePreview" class="text-gray-400 hover:text-gray-600 bg-white border rounded-full p-1"><XMarkIcon class="w-5 h-5" /></button>
          </div>
          <div class="flex-1 p-0 sm:p-4 bg-gray-100 relative">
            <div v-if="previewLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-white/80 z-10">
                <ArrowPathIcon class="w-10 h-10 animate-spin text-[#00555A] mb-3" />
                <p class="text-sm text-[#00555A] font-medium">Memuat dokumen...</p>
            </div>
            <iframe 
              v-if="previewUrl" 
              :src="previewUrl" 
              class="w-full h-full sm:rounded-lg sm:border border-gray-200 shadow-sm bg-white"
              frameborder="0"
            ></iframe>
            <div v-if="previewError" class="absolute inset-0 flex items-center justify-center">
              <div class="text-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <XCircleIcon class="w-12 h-12 text-red-400 mx-auto mb-2" />
                <p class="text-sm font-medium text-gray-600">Gagal memuat dokumen.</p>
                <p class="text-xs text-gray-400 mt-1">File tidak ditemukan di server atau terjadi kesalahan.</p>
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

    <transition name="fade">
      <div v-if="alertState.show" class="fixed inset-0 flex items-center justify-center z-[100] bg-[#00282a]/40 backdrop-blur-sm p-4" @click.self="alertState.type !== 'confirm' ? closeAlert() : null">
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
              :disabled="isDeleting"
            >Batal</button>
            <button 
              @click="handleAlertConfirm" 
              :disabled="isDeleting"
              class="px-5 py-2.5 rounded-xl text-white font-medium shadow-lg transition flex items-center justify-center gap-2 disabled:opacity-50"
              :class="alertState.type === 'error' ? 'bg-red-600 hover:bg-red-700 shadow-red-200' : (alertState.type === 'confirm' ? 'bg-[#00555A] hover:bg-[#004448] shadow-[#00555A]/30' : 'bg-green-600 hover:bg-green-700 shadow-green-200')"
            >
              <ArrowPathIcon v-if="isDeleting" class="h-4 w-4 animate-spin text-white" />
              <span>{{ isDeleting ? 'Memproses...' : (alertState.type === 'confirm' ? 'Ya, Lanjutkan' : 'Oke, Mengerti') }}</span>
            </button>
          </div>
        </div>
      </div>
    </transition>

    <transition name="slide-fade">
      <div v-if="toast.show" class="fixed top-5 right-5 z-[100] flex items-center gap-3 bg-white p-4 rounded-xl shadow-2xl border-l-4 w-full max-w-sm"
           :class="toast.type === 'success' ? 'border-green-500' : 'border-red-500'">
        <div class="flex-shrink-0">
          <CheckCircleIcon v-if="toast.type === 'success'" class="w-8 h-8 text-green-500" />
          <XCircleIcon v-else class="w-8 h-8 text-red-500" />
        </div>
        <div>
          <h4 class="font-bold text-sm" :class="toast.type === 'success' ? 'text-green-800' : 'text-red-800'">{{ toast.title }}</h4>
          <p class="text-xs text-gray-600 mt-0.5">{{ toast.message }}</p>
        </div>
        <button @click="toast.show = false" class="ml-auto text-gray-400 hover:text-gray-600">
          <XMarkIcon class="w-5 h-5" />
        </button>
      </div>
    </transition>

    <DetailSkripsiModal 
      :show="showDetailModal" 
      :skripsi="selectedDetailSkripsi" 
      @close="showDetailModal = false"
      @preview="(type) => previewSkripsi(selectedDetailSkripsi, type)"
      @download="(type) => downloadSkripsi(selectedDetailSkripsi, type)"
    />

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import api from '@/api';
import DetailSkripsiModal from '@/components/DetailSkripsiModal.vue';
import { 
  AcademicCapIcon, PlusIcon, DocumentTextIcon, MagnifyingGlassIcon, PencilSquareIcon,
  TrashIcon, XMarkIcon, CheckCircleIcon, XCircleIcon, ExclamationTriangleIcon,
  CalendarDaysIcon, DocumentIcon, CheckBadgeIcon, ArrowDownTrayIcon, ClockIcon, InboxIcon, ArrowPathIcon, UserIcon
} from '@heroicons/vue/24/outline';

const daftarSkripsi = ref([]);
const isLoadingFetch = ref(false);
const searchQuery = ref('');
const activeStatusTab = ref('Menunggu'); 

const showDetailModal = ref(false);
const selectedDetailSkripsi = ref(null); 

const showSkripsiModal = ref(false);
const isEditing = ref(false);
const isSubmitting = ref(false);
const currentSkripsiId = ref(null);
const formSkripsi = ref({
  judul: '', abstrak: '', nama: '', nim: '', prodi: '', tahun_selesai: '', status: 'menunggu', keterangan: '', fotoPreview: null, foto_url_existing: null
});
const uploadedFiles = reactive({
    foto: null,
    file_skripsi: null,
    file_preview_skripsi: null
});
const errors = ref({});

const showTolakModal = ref(false);
const tolakKeterangan = ref('');
const showTerimaModal = ref(false);
const terimaKeterangan = ref('');
const selectedVerifySkripsi = ref(null);

const isDeleting = ref(false);

const alertState = reactive({ show: false, message: '', type: 'success', onConfirm: null });

const toast = ref({ show: false, type: 'success', title: '', message: '' });

const showToast = (type, title, message) => {
    toast.value = { show: true, type, title, message };
    setTimeout(() => { toast.value.show = false; }, 3000);
};

const showPreviewModal = ref(false);
const previewUrl = ref(null);
const previewTitle = ref('');
const previewType = ref('full'); 
const previewLoading = ref(false);
const previewError = ref(false);
const previewSkripsiObj = ref(null);

const fetchSkripsi = async () => {
  isLoadingFetch.value = true;
  try {
    let queryParam = '';
    if (activeStatusTab.value !== 'Semua') {
      queryParam = `?status=${activeStatusTab.value.toLowerCase()}`;
    }
    const res = await api.get(`/skripsi${queryParam}`);
    daftarSkripsi.value = res.data.data || [];
  } catch (e) {
    console.error('Error fetching skripsi:', e);
    if (e.response?.status === 401) showAlertBox('Sesi berakhir, silakan login kembali.', 'error');
  } finally {
    isLoadingFetch.value = false;
  }
};

watch(activeStatusTab, () => {
    fetchSkripsi();
});

onMounted(() => {
  fetchSkripsi();
});

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

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const options = { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateStr).toLocaleDateString('id-ID', options);
};
const getStatusBadgeClass = (status) => {
    if (status === 'disetujui') return 'bg-green-50 text-green-700 border-green-200';
    if (status === 'ditolak') return 'bg-red-50 text-red-700 border-red-200';
    return 'bg-yellow-50 text-yellow-700 border-yellow-200';
};
const getStatusDotClass = (status) => {
    if (status === 'disetujui') return 'bg-green-500';
    if (status === 'ditolak') return 'bg-red-500';
    return 'bg-yellow-500';
};

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
      if (alertState.type === 'confirm') closeAlert();
    }
  } else {
    closeAlert();
  }
};

const openDetail = (skripsi) => {
    selectedDetailSkripsi.value = skripsi;
    showDetailModal.value = true;
};

const handleFileChange = (e, field, typeValid) => {
  const file = e.target.files[0];
  if (!file) {
      uploadedFiles[field] = null;
      return;
  }
  
  if (typeValid === 'pdf' && file.type !== 'application/pdf') {
      showAlertBox(`File ${field} harus PDF.`, 'error');
      e.target.value = '';
      uploadedFiles[field] = null;
      return;
  }
  if (typeValid === 'image' && !['image/jpeg', 'image/png'].includes(file.type)) {
      showAlertBox(`Foto harus JPEG/PNG.`, 'error');
      e.target.value = '';
      uploadedFiles[field] = null;
      return;
  }
  
  const maxSize = typeValid === 'pdf' ? 30 : 2;
  if (file.size > maxSize * 1024 * 1024) {
      showAlertBox(`Ukuran maksimal ${maxSize}MB.`, 'error');
      e.target.value = '';
      uploadedFiles[field] = null;
      return;
  }
  uploadedFiles[field] = file;
  if (field === 'foto') {
      formSkripsi.value.fotoPreview = URL.createObjectURL(file);
  }
};

const openSkripsiModal = (mode, skripsi = null) => {
  errors.value = {};
  Object.keys(uploadedFiles).forEach(k => uploadedFiles[k] = null);
  
  if (mode === 'edit' && skripsi) {
    isEditing.value = true;
    currentSkripsiId.value = skripsi.id_skripsi;
    formSkripsi.value = {
      judul: skripsi.judul || '',
      abstrak: skripsi.abstrak || '',
      nama: skripsi.nama || '',
      nim: skripsi.nim || '',
      prodi: skripsi.prodi || '',
      tahun_selesai: skripsi.tahun_selesai || '',
      status: skripsi.status || 'menunggu',
      keterangan: skripsi.keterangan || '',
      foto_url_existing: skripsi.foto_url || null,
      fotoPreview: null
    };
  } else {
    isEditing.value = false;
    currentSkripsiId.value = null;
    formSkripsi.value = { judul: '', abstrak: '', nama: '', nim: '', prodi: '', tahun_selesai: '', status: 'menunggu', keterangan: '', fotoPreview: null, foto_url_existing: null };
  }
  showSkripsiModal.value = true;
};

const cancelSkripsi = () => {
  if (isSubmitting.value) return;
  showSkripsiModal.value = false;
};

const saveSkripsi = async () => {
  if (isSubmitting.value) return;
  
  if (!formSkripsi.value.judul || !formSkripsi.value.nama || !formSkripsi.value.nim || !formSkripsi.value.prodi || !formSkripsi.value.tahun_selesai) {
      showAlertBox('Mohon lengkapi field yang bertanda *', 'error');
      return;
  }
  if (!isEditing.value && !uploadedFiles.file_skripsi) {
      showAlertBox('File skripsi wajib diupload untuk data baru.', 'error');
      return;
  }

  isSubmitting.value = true;
  errors.value = {};

  const formData = new FormData();
  Object.keys(formSkripsi.value).forEach(k => {
      if (formSkripsi.value[k] !== null && formSkripsi.value[k] !== '') {
          formData.append(k, formSkripsi.value[k]);
      }
  });

  Object.keys(uploadedFiles).forEach(k => {
      if (uploadedFiles[k]) formData.append(k, uploadedFiles[k]);
  });

  try {
    if (isEditing.value && currentSkripsiId.value) {
      await api.post(`/staff/skripsi/${currentSkripsiId.value}`, formData, { headers: { 'Content-Type': 'multipart/form-data' } });
      showToast('success', 'Berhasil Update', 'Data skripsi berhasil diperbarui!');
    } else {
      await api.post('/staff/skripsi', formData, { headers: { 'Content-Type': 'multipart/form-data' } });
      showToast('success', 'Berhasil Tambah', 'Skripsi baru berhasil ditambahkan!');
    }
    showSkripsiModal.value = false;
    fetchSkripsi();
  } catch (e) {
    if (e.response?.status === 422) {
        errors.value = e.response.data.errors || {};
        showAlertBox('Validasi gagal. Periksa form Anda.', 'error');
    } else {
        showAlertBox(e.response?.data?.message || 'Gagal menyimpan data skripsi.', 'error');
    }
  } finally {
    isSubmitting.value = false;
  }
};

const deleteSkripsi = (skripsi) => {
  showConfirmBox(`Apakah Anda yakin ingin menghapus skripsi "${skripsi.judul}"?`, async () => {
    try {
      await api.delete(`/staff/skripsi/${skripsi.id_skripsi}`);
      showToast('success', 'Berhasil Hapus', 'Data skripsi berhasil dihapus.');
      fetchSkripsi();
    } catch (e) {
      showAlertBox(e.response?.data?.message || 'Gagal menghapus skripsi.', 'error');
    }
  });
};

const openTerimaModal = (skripsi) => {
    selectedVerifySkripsi.value = skripsi;
    terimaKeterangan.value = '';
    showTerimaModal.value = true;
};

const cancelTerima = () => {
    if (isSubmitting.value) return;
    showTerimaModal.value = false;
    selectedVerifySkripsi.value = null;
};

const submitTerima = async () => {
    if (isSubmitting.value || !selectedVerifySkripsi.value) return;
    
    isSubmitting.value = true;
    try {
        const payload = { status: 'disetujui' };
        if (terimaKeterangan.value.trim() !== '') {
            payload.keterangan = terimaKeterangan.value.trim();
        }
        
        await api.post(`/staff/skripsi/${selectedVerifySkripsi.value.id_skripsi}/verify`, payload);
        showToast('success', 'Disetujui', 'Pengajuan skripsi berhasil disetujui.');
        showTerimaModal.value = false;
        fetchSkripsi();
    } catch (e) {
        showAlertBox(e.response?.data?.message || 'Gagal menyetujui pengajuan.', 'error');
    } finally {
        isSubmitting.value = false;
    }
};

const openTolakModal = (skripsi) => {
    selectedVerifySkripsi.value = skripsi;
    tolakKeterangan.value = '';
    showTolakModal.value = true;
};

const cancelTolak = () => {
    if (isSubmitting.value) return;
    showTolakModal.value = false;
    selectedVerifySkripsi.value = null;
};

const submitTolak = async () => {
    if (isSubmitting.value || !selectedVerifySkripsi.value || !tolakKeterangan.value.trim()) return;
    
    isSubmitting.value = true;
    try {
        await api.post(`/staff/skripsi/${selectedVerifySkripsi.value.id_skripsi}/verify`, { 
            status: 'ditolak',
            keterangan: tolakKeterangan.value
        });
        showToast('success', 'Ditolak', 'Pengajuan skripsi telah ditolak.');
        showTolakModal.value = false;
        fetchSkripsi();
    } catch (e) {
        showAlertBox(e.response?.data?.message || 'Gagal menolak pengajuan.', 'error');
    } finally {
        isSubmitting.value = false;
    }
};

const previewSkripsi = async (skripsi, type) => {
  previewTitle.value = skripsi.judul;
  previewType.value = type;
  previewLoading.value = true;
  previewError.value = false;
  previewUrl.value = null;
  previewSkripsiObj.value = skripsi;
  showPreviewModal.value = true;

  try {
    const endpoint = type === 'full' 
        ? `/skripsi/${skripsi.id_skripsi}/preview` 
        : `/skripsi/${skripsi.id_skripsi}/preview-file`;
        
    await api.get(endpoint, { responseType: 'blob' });
    previewUrl.value = `${api.defaults.baseURL}${endpoint}`;
  } catch (e) {
    console.error('Preview error:', e);
    previewError.value = true;
  } finally {
    previewLoading.value = false;
  }
};

const closePreview = () => {
  showPreviewModal.value = false;
  previewUrl.value = null;
  previewTitle.value = '';
  previewError.value = false;
  previewSkripsiObj.value = null;
};

const downloadSkripsi = async (skripsi, type) => {
    try {
        const endpoint = type === 'full' 
            ? `/skripsi/${skripsi.id_skripsi}/download` 
            : `/skripsi/${skripsi.id_skripsi}/download-file`;
            
        const response = await api.get(endpoint, { responseType: 'blob' });
        
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
        console.error('Download error:', error);
        showAlertBox('Gagal mengunduh file. File mungkin tidak tersedia.', 'error');
    }
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease, transform 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(5px); }
.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-leave-active { transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1); }
.slide-fade-enter-from, .slide-fade-leave-to { transform: translateX(20px); opacity: 0; }
</style>
