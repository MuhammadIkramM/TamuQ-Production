<template>
  <div class="space-y-8">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-[#00555A] flex items-center gap-2">
          <BookOpenIcon class="w-8 h-8 text-[#F26430]" />
          Manajemen Buku
        </h1>
        <p class="text-gray-500 text-sm">Kelola stok buku dan sirkulasi peminjaman dalam satu tempat.</p>
      </div>

      <div class="flex gap-3">
        <button
          @click="openBookModal('add')"
          class="flex items-center gap-2 px-4 py-2 bg-white border border-[#00555A]/30 text-[#00555A] hover:bg-[#00555A]/5 rounded-xl transition-all text-sm font-medium shadow-sm"
        >
          <QueueListIcon class="w-5 h-5" />
          Tambah Stok Buku
        </button>

        <button
          @click="openPinjamModal"
          class="flex items-center gap-2 px-4 py-2 bg-[#00555A] hover:bg-[#004448] text-white rounded-xl transition-all text-sm font-medium shadow-md shadow-[#00555A]/20"
        >
          <PlusIcon class="w-5 h-5" />
          Catat Peminjaman
        </button>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-5 flex flex-col md:flex-row md:items-center justify-between bg-[#00555A] gap-4 rounded-t-2xl">
        <div class="flex items-center gap-3">
          <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <Square3Stack3DIcon class="w-5 h-5 text-[#F26430]" />
            Katalog Buku & Stok
          </h3>
          <span class="text-xs font-medium bg-white/20 text-white px-2 py-1 rounded-md">
            Total: {{ filteredKatalog.length }} Judul
          </span>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
          <button 
            @click="exportKatalogToExcel"
            :disabled="filteredKatalog.length === 0"
            class="flex items-center gap-1.5 px-3 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-sm font-medium transition-all shadow-sm disabled:opacity-50 w-full sm:w-auto justify-center"
            title="Unduh Data Katalog"
          >
            <ArrowDownTrayIcon class="w-4 h-4" />
            Unduh Excel
          </button>

          <div class="relative w-full sm:w-72">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
            </div>
            <input 
              type="text" 
              v-model="searchKatalog" 
              class="w-full pl-10 pr-4 py-2 bg-white border-transparent rounded-xl focus:ring-2 focus:ring-[#F26430] text-sm outline-none transition-all shadow-sm"
              placeholder="Cari judul, penulis, tahun, tempat..."
            />
          </div>
        </div>
      </div>

      <div class="overflow-x-auto max-h-[350px] overflow-y-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
          <thead class="sticky top-0 z-10 shadow-sm">
            <tr class="bg-[#66999C] text-white text-xs uppercase tracking-wider font-semibold">
              <th class="px-6 py-3">Kode Buku</th>
              <th class="px-6 py-3">Judul Buku</th>
              <th class="px-6 py-3">Kategori</th>
              <th class="px-6 py-3">Penulis</th>
              <th class="px-6 py-3">Tempat</th>
              <th class="px-6 py-3 text-center">Tahun</th>
              <th class="px-6 py-3 text-center">Stok Tersedia</th>
              <th class="px-6 py-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="buku in filteredKatalog" :key="buku.id_buku" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-3 text-sm font-mono text-gray-600">{{ buku.kode_buku || '-' }}</td>
              <td class="px-6 py-3 text-sm font-medium text-gray-800">{{ buku.judul }}</td>
              <td class="px-6 py-3 text-sm text-gray-500">
                <span v-if="buku.kategori" class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600 border border-gray-200">
                  {{ buku.kategori }}
                </span>
                <span v-else>-</span>
              </td>
              <td class="px-6 py-3 text-sm text-gray-600">{{ buku.penulis || '-' }}</td>
              <td class="px-6 py-3 text-sm text-[#00555A] font-medium">{{ buku.tempat || '-' }}</td>
              <td class="px-6 py-3 text-center text-sm text-gray-600">{{ buku.tahun_buku || '-' }}</td>
              <td class="px-6 py-3 text-center">
                <span 
                  :class="[
                    'px-2.5 py-0.5 rounded-full text-xs font-bold border',
                    buku.jumlah_tersedia > 0 
                      ? 'bg-[#00555A]/10 text-[#00555A] border-[#00555A]/20' 
                      : 'bg-red-50 text-red-600 border-red-100'
                  ]"
                >
                  {{ buku.jumlah_tersedia }}
                </span>
              </td>
              <td class="px-6 py-4 text-center">
                  <div class="flex justify-center gap-2">
                    <button @click="openBookModal('edit', buku)" class="p-2 text-[#F26430] hover:bg-[#F26430]/10 rounded-lg transition border border-transparent hover:border-[#F26430]/20" title="Edit Data">
                      <PencilSquareIcon class="w-4 h-4" />
                    </button>
                    <button @click="deleteBuku(buku)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition border border-transparent hover:border-red-200" title="Hapus Permanen">
                      <TrashIcon class="w-4 h-4" />
                    </button>
                  </div>
                </td>
            </tr>
            <tr v-if="filteredKatalog.length === 0">
              <td colspan="8" class="px-6 py-8 text-center text-gray-400 text-sm">
                {{ searchKatalog ? 'Buku tidak ditemukan.' : 'Belum ada data buku.' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-end sm:items-center mt-6 mb-2 gap-4">
      <h2 class="text-lg font-bold text-[#00555A]">Sirkulasi & Riwayat</h2>
      <div class="relative w-full sm:w-80">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <MagnifyingGlassIcon class="w-5 h-5 text-gray-400" />
        </div>
        <input 
          type="text" 
          v-model="searchSirkulasi" 
          class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none transition-all shadow-sm"
          placeholder="Cari nama peminjam atau judul buku..."
        />
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-fit">
        <div class="p-5 flex justify-between items-center bg-[#00555A] rounded-t-2xl">
          <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <ClockIcon class="w-5 h-5 text-[#F26430]" />
            Sedang Dipinjam
          </h3>
          <span class="text-xs font-medium bg-white/20 text-white px-2 py-1 rounded-md">
            {{ filteredPeminjamanAktif.length }} Aktif
          </span>
        </div>

        <div class="overflow-x-auto max-h-[400px] overflow-y-auto">
          <table class="w-full text-left">
            <thead class="bg-[#66999C] text-xs uppercase text-white font-semibold sticky top-0 z-10 shadow-sm">
              <tr>
                <th class="px-4 py-3 tracking-wider">Peminjam & Buku</th>
                <th class="px-4 py-3 text-center tracking-wider w-24">Hubungi</th>
                <th class="px-4 py-3 text-center tracking-wider w-28">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="pinjam in filteredPeminjamanAktif" :key="pinjam.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full overflow-hidden border border-gray-200 relative shrink-0">
                        <img 
                          v-if="pinjam.foto_profil" 
                          :src="pinjam.foto_profil" 
                          alt="Foto"
                          class="w-full h-full object-cover"
                          @error="e => e.target.style.display = 'none'" 
                        />
                        <div 
                          class="absolute inset-0 w-full h-full flex items-center justify-center bg-gradient-to-br from-[#00555A] to-[#004448] text-white font-bold text-xs"
                          :class="{'hidden': pinjam.foto_profil}"
                        >
                           {{ getInitials(pinjam.namaPeminjam) }}
                        </div>
                    </div>
                    <div class="flex flex-col justify-center">
                      <p class="text-sm font-bold text-gray-800 leading-tight">{{ pinjam.namaPeminjam }}</p>
                      <p class="text-xs text-gray-500 line-clamp-1 mt-0.5" :title="pinjam.judulBuku">{{ pinjam.judulBuku }}</p>
                      
                      <div class="mt-1.5">
                        <span v-if="pinjam.sisaHari < 0" class="text-[10px] text-red-600 font-bold bg-red-100 border border-red-200 px-1.5 py-0.5 rounded">
                          ⚠️ Telat {{ Math.abs(pinjam.sisaHari) }} Hari
                        </span>
                        <span v-else-if="pinjam.sisaHari === 0" class="text-[10px] text-orange-600 font-bold bg-orange-100 border border-orange-200 px-1.5 py-0.5 rounded">
                          ⏰ Jatuh Tempo Hari Ini
                        </span>
                        <span v-else-if="pinjam.sisaHari === 1" class="text-[10px] text-orange-600 font-bold bg-orange-100 border border-orange-200 px-1.5 py-0.5 rounded">
                          ⏳ Besok Jatuh Tempo
                        </span>
                        <span v-else class="text-[10px] text-[#00555A] font-bold bg-[#00555A]/10 border border-[#00555A]/20 px-1.5 py-0.5 rounded">
                          Sisa {{ pinjam.sisaHari }} Hari
                        </span>
                      </div>

                    </div>
                  </div>
                </td>

                <td class="px-4 py-4 text-center align-middle">
                  <a 
                    v-if="pinjam.noHp" 
                    :href="formatWA(pinjam.noHp)" 
                    target="_blank" 
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg text-[11px] font-bold transition shadow-sm hover:shadow-md"
                    title="Hubungi via WhatsApp"
                  >
                    <ChatBubbleLeftRightIcon class="w-3.5 h-3.5" />
                    WA
                  </a>
                  <span v-else class="text-xs text-gray-400 italic">-</span>
                </td>

                <td class="px-4 py-4 text-center align-middle">
                  <button @click="kembalikanBuku(pinjam)" class="px-3 py-1.5 bg-[#00555A] hover:bg-[#004448] text-white rounded-lg text-xs font-medium transition shadow-sm hover:shadow-md">
                    Kembalikan
                  </button>
                </td>
              </tr>
              <tr v-if="filteredPeminjamanAktif.length === 0">
                 <td colspan="3" class="px-4 py-6 text-center text-xs text-gray-400">
                   {{ searchSirkulasi ? 'Tidak ada data yang cocok dengan pencarian.' : 'Tidak ada peminjaman aktif.' }}
                 </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-fit">
        <div class="p-5 bg-[#00555A] text-lg font-bold text-white flex items-center gap-2 rounded-t-2xl">
            <ArchiveBoxIcon class="w-5 h-5 text-[#F26430]" />
            Riwayat Terakhir
        </div>

        <div class="overflow-x-auto max-h-[400px] overflow-y-auto">
          <table class="w-full text-left">
            <thead class="bg-[#66999C] text-xs uppercase text-white font-semibold sticky top-0 z-10 shadow-sm">
              <tr>
                <th class="px-4 py-3 tracking-wider">Peminjam & Buku</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="riwayat in filteredRiwayatPengembalian" :key="riwayat.id" class="hover:bg-gray-50">
                <td class="px-4 py-3">
                  <p class="text-sm font-medium text-gray-700">{{ riwayat.judulBuku }}</p>
                  <p class="text-xs text-gray-500">Oleh: {{ riwayat.namaPeminjam }}</p>
                  
                  <div class="mt-1 flex flex-col gap-0.5">
                    <p class="text-[10px] text-gray-400 flex items-center gap-1">
                      <span class="w-1.5 h-1.5 bg-[#F26430] rounded-full"></span>
                      Pinjam: {{ formatDate(riwayat.tanggalPinjam) }}
                    </p>
                    <p class="text-[10px] text-[#00555A] font-medium flex items-center gap-1">
                      <span class="w-1.5 h-1.5 bg-[#00555A] rounded-full"></span>
                      Kembali: {{ formatDate(riwayat.tanggalKembali) }}
                    </p>
                  </div>
                </td>
              </tr>
              <tr v-if="filteredRiwayatPengembalian.length === 0">
                 <td class="px-4 py-6 text-center text-xs text-gray-400">
                   {{ searchSirkulasi ? 'Tidak ada riwayat yang cocok dengan pencarian.' : 'Belum ada riwayat pengembalian.' }}
                 </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <transition name="fade">
      <div v-if="showPinjamModal" class="fixed inset-0 flex items-center justify-center z-50 bg-[#00282a]/50 backdrop-blur-sm p-4" @click.self="cancelPinjam">
        <div class="bg-white shadow-2xl rounded-2xl w-full max-w-md overflow-visible transform transition-all scale-100">
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl">
             <h3 class="text-lg font-bold text-[#00555A]">Catat Peminjaman</h3>
             <button @click="cancelPinjam" class="text-gray-400 hover:text-gray-600"><XMarkIcon class="w-6 h-6" /></button>
          </div>
          <div class="p-6 space-y-5">
             
             <div class="space-y-1 relative">
               <label class="text-xs font-bold text-gray-500 uppercase">Nama Peminjam <span class="text-red-500">*</span></label>
               <div class="relative">
                   <input 
                     type="text" 
                     v-model="searchUser"
                     @input="selectedUserId = null; showUserDropdown = true"
                     @focus="showUserDropdown = true; showBookDropdown = false"
                     class="w-full px-4 py-2.5 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none transition-all"
                     :class="selectedUserId ? 'border-[#00555A] bg-[#00555A]/5' : 'border-gray-200'"
                     placeholder="Cari nama peminjam..." 
                     autocomplete="off"
                   />
                   <div v-if="selectedUserId" class="absolute right-3 top-2.5 text-[#00555A]">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                   </div>
               </div>
               
               <div v-if="showUserDropdown && searchUser" class="absolute z-[60] w-full bg-white mt-1 border border-gray-100 rounded-xl shadow-xl max-h-40 overflow-y-auto">
                   <ul>
                       <li v-for="user in filteredUsers" :key="user.id_user" @click="selectUser(user)" class="px-4 py-2 hover:bg-[#00555A]/5 cursor-pointer text-sm text-gray-700 border-b border-gray-50 last:border-0">
                           <div class="font-medium text-[#00555A]">{{ user.nama_lengkap }}</div>
                           <div class="text-xs text-gray-400">{{ user.nomor_induk }}</div>
                       </li>
                       <li v-if="filteredUsers.length === 0" class="px-4 py-3 text-xs text-center text-gray-400">User tidak ditemukan</li>
                   </ul>
               </div>
             </div>

             <div class="space-y-1">
               <label class="text-xs font-bold text-gray-500 uppercase">Nomor WhatsApp / HP <span class="text-red-500">*</span></label>
               <input 
                 type="text" 
                 v-model="newPinjam.noHp" 
                 @input="newPinjam.noHp = newPinjam.noHp.replace(/[^0-9]/g, '')"
                 class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none" 
                 placeholder="08123xxxx (Hanya angka)"
                 maxlength="15"
               />
             </div>

             <div class="space-y-1 relative">
               <label class="text-xs font-bold text-gray-500 uppercase">Judul Buku <span class="text-red-500">*</span></label>
               <div class="relative">
                   <input 
                     type="text" 
                     v-model="searchBook"
                     @input="selectedBookId = null; showBookDropdown = true"
                     @focus="showBookDropdown = true; showUserDropdown = false"
                     class="w-full px-4 py-2.5 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none transition-all"
                     :class="selectedBookId ? 'border-[#00555A] bg-[#00555A]/5' : 'border-gray-200'"
                     placeholder="Cari judul buku dari katalog..." 
                     autocomplete="off"
                   />
                   <div v-if="selectedBookId" class="absolute right-3 top-2.5 text-[#00555A]">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                   </div>
               </div>

               <div v-if="showBookDropdown && searchBook" class="absolute z-[60] w-full bg-white mt-1 border border-gray-100 rounded-xl shadow-xl max-h-40 overflow-y-auto">
                   <ul>
                       <li 
                          v-for="buku in filteredBooks" 
                          :key="buku.id_buku" 
                          @click="selectBook(buku)" 
                          :class="[
                            'px-4 py-2 flex justify-between items-center text-sm border-b border-gray-50 last:border-0',
                            buku.jumlah_tersedia > 0 ? 'hover:bg-[#00555A]/5 cursor-pointer text-gray-700' : 'bg-red-50 text-red-400 cursor-not-allowed'
                          ]"
                       >
                           <div>
                             <div class="font-medium text-[#00555A]">{{ buku.judul }}</div>
                             <div class="text-[10px] text-gray-500">{{ buku.penulis || 'Tanpa Penulis' }}</div>
                           </div>
                           <div class="text-[10px] font-bold px-2 py-0.5 rounded bg-white border text-gray-500">
                             {{ buku.jumlah_tersedia }} stok
                           </div>
                       </li>
                       <li v-if="filteredBooks.length === 0" class="px-4 py-3 text-xs text-center text-gray-400">Buku tidak ditemukan</li>
                   </ul>
               </div>
             </div>

             <div class="grid grid-cols-2 gap-4">
               <div class="space-y-1">
                 <label class="text-xs font-bold text-gray-500 uppercase">Tgl Pinjam <span class="text-red-500">*</span></label>
                 <input type="date" lang="id-ID" v-model="newPinjam.tanggalPinjam" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none" />
               </div>
               
               <div class="space-y-1">
                 <label class="text-xs font-bold text-gray-500 uppercase">Batas Kembali <span class="text-red-500">*</span></label>
                 <input type="date" lang="id-ID" v-model="newPinjam.tanggalKembali" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none" />
               </div>
             </div>

          </div>
          <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-2xl border-t border-gray-100">
            <button @click="cancelPinjam" class="px-4 py-2 text-gray-700 text-sm font-medium hover:bg-gray-100 rounded-lg transition">Batal</button>
            <button @click="confirmPinjam" :disabled="!isPinjamFormValid" class="px-4 py-2 bg-[#00555A] text-white rounded-lg text-sm font-medium hover:bg-[#004448] shadow-md disabled:opacity-50 disabled:cursor-not-allowed transition">Simpan Data</button>
          </div>
        </div>
      </div>
    </transition>

    <div v-if="showUserDropdown || showBookDropdown" @click="closeDropdowns" class="fixed inset-0 z-40 bg-transparent"></div>

    <transition name="fade">
      <div v-if="showBookModal" class="fixed inset-0 flex items-center justify-center z-50 bg-[#00282a]/50 backdrop-blur-sm p-4" @click.self="cancelBook">
        <div class="bg-white shadow-2xl rounded-2xl w-full max-w-md overflow-hidden transform transition-all scale-100">
          <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
             <h3 class="text-lg font-bold text-[#00555A]">{{ isEditing ? 'Edit Data Buku' : 'Tambah Buku Baru' }}</h3>
             <button @click="cancelBook" class="text-gray-400 hover:text-gray-600"><XMarkIcon class="w-6 h-6" /></button>
          </div>
          <div class="p-6 space-y-4">
             
             <div class="space-y-1">
               <label class="text-xs font-bold text-gray-500 uppercase">Kode Buku</label>
               <input 
                 type="text" 
                 v-model="newBuku.kode_buku" 
                 @input="newBuku.kode_buku = newBuku.kode_buku.replace(/[^a-zA-Z0-9\-]/g, '').toUpperCase()"
                 class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none" 
                 placeholder="Contoh: B001" 
               />
             </div>
             
             <div class="space-y-1">
               <label class="text-xs font-bold text-gray-500 uppercase">Judul Buku <span class="text-red-500">*</span></label>
               <input type="text" v-model="newBuku.judul" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none" placeholder="Masukkan judul..." />
             </div>
             
             <div class="space-y-1">
               <label class="text-xs font-bold text-gray-500 uppercase">Penulis</label>
               <input type="text" v-model="newBuku.penulis" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none" placeholder="Nama penulis..." />
             </div>
             
             <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                  <label class="text-xs font-bold text-gray-500 uppercase">Kategori</label>
                  <input type="text" v-model="newBuku.kategori" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none" placeholder="Fiksi/Sains..." />
                </div>
                <div class="space-y-1">
                  <label class="text-xs font-bold text-gray-500 uppercase">Tahun Buku</label>
                  <input type="number" min="1900" max="2099" v-model="newBuku.tahun_buku" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none" placeholder="Contoh: 2021" />
                </div>
             </div>

             <div class="space-y-1">
               <label class="text-xs font-bold text-gray-500 uppercase">Tempat</label>
               <input type="text" v-model="newBuku.tempat" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none" placeholder="Masukkan tempat penyimpanan..." />
             </div>

             <div v-if="isEditing" class="space-y-1 pt-2 border-t border-gray-100">
                <label class="text-xs font-bold text-gray-500 uppercase">Jumlah Stok <span class="text-red-500">*</span></label>
                <input type="number" min="0" v-model="newBuku.jumlah_tersedia" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#00555A] focus:border-[#00555A] text-sm outline-none" />
             </div>
          </div>
          <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-2xl border-t border-gray-100">
            <button @click="cancelBook" class="px-4 py-2 text-gray-700 text-sm font-medium hover:bg-gray-100 rounded-lg transition">Batal</button>
            <button @click="saveBook" :disabled="!isBookFormValid" class="px-4 py-2 bg-[#00555A] text-white rounded-lg text-sm font-medium hover:bg-[#004448] shadow-md disabled:opacity-50 transition">
              {{ isEditing ? 'Update Buku' : 'Simpan Buku' }}
            </button>
          </div>
        </div>
      </div>
    </transition>

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
              class="px-5 py-2.5 rounded-xl text-white font-medium shadow-lg transition"
              :class="alertState.type === 'error' ? 'bg-red-600 hover:bg-red-700 shadow-red-200' : (alertState.type === 'confirm' ? 'bg-[#00555A] hover:bg-[#004448] shadow-[#00555A]/30' : 'bg-green-600 hover:bg-green-700 shadow-green-200')"
            >
              {{ alertState.type === 'confirm' ? 'Ya, Lanjutkan' : 'Oke, Mengerti' }}
            </button>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import * as XLSX from "xlsx";
import { saveAs } from "file-saver";
import api from '@/api';
import { 
  BookOpenIcon, PlusIcon, ClockIcon, ArchiveBoxIcon, 
  XMarkIcon, QueueListIcon, Square3Stack3DIcon,
  CheckCircleIcon, XCircleIcon, ExclamationTriangleIcon,
  PencilSquareIcon, TrashIcon, MagnifyingGlassIcon,
  ArrowDownTrayIcon, ChatBubbleLeftRightIcon
} from "@heroicons/vue/24/outline";

const listUsers = ref([]); 
const searchUser = ref(""); 
const showUserDropdown = ref(false);
const selectedUserId = ref(null); 

const searchBook = ref(""); 
const showBookDropdown = ref(false); 
const selectedBookId = ref(null); 

const searchSirkulasi = ref("");
const searchKatalog = ref(""); 

const showPinjamModal = ref(false);
const peminjamanAktif = ref([]);
const riwayatPengembalian = ref([]);
const newPinjam = ref({ tanggalPinjam: "", tanggalKembali: "", noHp: "" });

const showBookModal = ref(false);
const daftarBuku = ref([]);

const isEditing = ref(false);
const currentBookId = ref(null);
const newBuku = ref({ kode_buku: "", judul: "", penulis: "", kategori: "", tempat: "", tahun_buku: "", jumlah_tersedia: 1 });

const alertState = reactive({
  show: false,
  type: 'success', 
  message: '',
  onConfirm: null 
});

const isPinjamFormValid = computed(() => {
  const isPhoneValid = /^[0-9]+$/.test(newPinjam.value.noHp);
  return selectedUserId.value && selectedBookId.value && newPinjam.value.tanggalPinjam && newPinjam.value.tanggalKembali && newPinjam.value.noHp && isPhoneValid;
});

const isBookFormValid = computed(() => {
  const isKodeValid = !newBuku.value.kode_buku || /^[a-zA-Z0-9\-]+$/.test(newBuku.value.kode_buku);
  return newBuku.value.judul && newBuku.value.jumlah_tersedia >= 0 && isKodeValid;
});

const filteredUsers = computed(() => {
  if (!searchUser.value) return listUsers.value.slice(0, 5);
  return listUsers.value.filter(u => 
    u.nama_lengkap.toLowerCase().includes(searchUser.value.toLowerCase()) || 
    (u.nomor_induk && u.nomor_induk.includes(searchUser.value)) 
  ).slice(0, 5);
});

const filteredBooks = computed(() => {
  if (!searchBook.value) return daftarBuku.value.slice(0, 5);
  return daftarBuku.value.filter(b => 
    b.judul.toLowerCase().includes(searchBook.value.toLowerCase()) ||
    (b.kode_buku && b.kode_buku.toLowerCase().includes(searchBook.value.toLowerCase()))
  ).slice(0, 5);
});

const filteredKatalog = computed(() => {
  if (!searchKatalog.value) return daftarBuku.value;
  const query = searchKatalog.value.toLowerCase();
  return daftarBuku.value.filter(b => 
    (b.judul && b.judul.toLowerCase().includes(query)) ||
    (b.kode_buku && b.kode_buku.toLowerCase().includes(query)) ||
    (b.penulis && b.penulis.toLowerCase().includes(query)) ||
    (b.kategori && b.kategori.toLowerCase().includes(query)) ||
    (b.tempat && b.tempat.toLowerCase().includes(query)) ||
    (b.tahun_buku && b.tahun_buku.toString().includes(query))
  );
});

const filteredPeminjamanAktif = computed(() => {
  if (!searchSirkulasi.value) return peminjamanAktif.value;
  const query = searchSirkulasi.value.toLowerCase();
  return peminjamanAktif.value.filter(p => 
    p.namaPeminjam.toLowerCase().includes(query) || 
    p.judulBuku.toLowerCase().includes(query)
  );
});

const filteredRiwayatPengembalian = computed(() => {
  if (!searchSirkulasi.value) return riwayatPengembalian.value;
  const query = searchSirkulasi.value.toLowerCase();
  return riwayatPengembalian.value.filter(r => 
    r.namaPeminjam.toLowerCase().includes(query) || 
    r.judulBuku.toLowerCase().includes(query)
  );
});

const getInitials = (name) => {
    if(!name) return '?';
    return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
};

const formatDate = (dateStr) => {
  if(!dateStr) return '-';
  const date = new Date(dateStr);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${day}/${month}/${year}`;
};

const formatWA = (hp) => {
    if (!hp) return '';
    let cleaned = hp.replace(/\D/g, ''); 
    if (cleaned.startsWith('0')) {
        cleaned = '62' + cleaned.substring(1); 
    }
    return `https://wa.me/${cleaned}`;
};

const closeDropdowns = () => {
    showUserDropdown.value = false;
    showBookDropdown.value = false;
};

const exportKatalogToExcel = () => {
  const dataToExport = filteredKatalog.value.map((item, index) => ({
    'No': index + 1,
    'Kode Buku': item.kode_buku || '-',
    'Judul Buku': item.judul,
    'Kategori': item.kategori || '-',
    'Penulis': item.penulis || '-',
    'Tempat': item.tempat || '-',
    'Tahun Buku': item.tahun_buku || '-',
    'Jumlah Tersedia': item.jumlah_tersedia
  }));

  const worksheet = XLSX.utils.json_to_sheet(dataToExport);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, "Katalog Buku");

  const wscols = [ {wch:5}, {wch:15}, {wch:40}, {wch:15}, {wch:25}, {wch:20}, {wch:15}, {wch:15} ];
  worksheet['!cols'] = wscols;

  const wbout = XLSX.write(workbook, { bookType: "xlsx", type: "array" });
  saveAs(new Blob([wbout], { type: "application/octet-stream" }), `Katalog_Buku_${new Date().toISOString().slice(0,10)}.xlsx`);
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
const handleAlertConfirm = () => {
  if (alertState.type === 'confirm' && alertState.onConfirm) alertState.onConfirm();
  closeAlert();
};

const fetchData = async () => {
    try {
        const [resPinjam, resBuku, resUser] = await Promise.all([
            api.get('/staff/peminjaman'),
            api.get('/buku'), 
            api.get('/staff/users-list')
        ]);
        
        peminjamanAktif.value = resPinjam.data.aktif || [];
        riwayatPengembalian.value = resPinjam.data.riwayat || [];
        daftarBuku.value = Array.isArray(resBuku.data) ? resBuku.data : (resBuku.data.data || []); 
        listUsers.value = Array.isArray(resUser.data) ? resUser.data : (resUser.data.data || []);

    } catch (e) { 
        console.error("Error fetching:", e); 
        if(e.response?.status === 401) showAlertBox("Sesi berakhir, silakan login kembali.", "error");
    }
};

const selectUser = (user) => {
    searchUser.value = user.nama_lengkap; 
    selectedUserId.value = user.id_user; 
    showUserDropdown.value = false;
};

const selectBook = (buku) => {
    if(buku.jumlah_tersedia <= 0) return; 
    searchBook.value = buku.judul;       
    selectedBookId.value = buku.id_buku; 
    showBookDropdown.value = false;
};

const openPinjamModal = () => {
  searchUser.value = ""; selectedUserId.value = null;
  searchBook.value = ""; selectedBookId.value = null;
  newPinjam.value.noHp = ""; 
  
  const today = new Date();
  newPinjam.value.tanggalPinjam = today.toISOString().split('T')[0];
  
  const nextWeek = new Date(today);
  nextWeek.setDate(today.getDate() + 7);
  newPinjam.value.tanggalKembali = nextWeek.toISOString().split('T')[0];
  
  showPinjamModal.value = true;
};

const cancelPinjam = () => { showPinjamModal.value = false; closeDropdowns(); };

const confirmPinjam = async () => {
  if (!isPinjamFormValid.value) return;
  try {
      const payload = {
          id_user: selectedUserId.value, 
          id_buku: selectedBookId.value, 
          noHp: newPinjam.value.noHp, 
          tanggalPinjam: newPinjam.value.tanggalPinjam,
          tanggalKembali: newPinjam.value.tanggalKembali 
      };
      
      await api.post('/staff/peminjaman', payload);
      
      showPinjamModal.value = false;
      await fetchData(); 
      showAlertBox("Berhasil mencatat peminjaman!", "success");
  } catch (error) {
      showAlertBox(error.response?.data?.message || "Gagal menyimpan data peminjaman.", "error");
  }
};

const kembalikanBuku = (pinjam) => {
  showConfirmBox(`Kembalikan buku "${pinjam.judulBuku}"?`, async () => {
    try {
        await api.post(`/staff/peminjaman/${pinjam.id}/return`);
        await fetchData(); 
        showAlertBox("Buku berhasil dikembalikan.", "success");
    } catch (e) { 
        showAlertBox(e.response?.data?.message || "Gagal memproses pengembalian.", "error"); 
    }
  });
};

const openBookModal = (mode, buku = null) => {
  if (mode === 'edit' && buku) {
    isEditing.value = true;
    currentBookId.value = buku.id_buku;
    newBuku.value = { 
      kode_buku: buku.kode_buku || "", 
      judul: buku.judul, 
      penulis: buku.penulis, 
      kategori: buku.kategori, 
      tempat: buku.tempat || "", 
      tahun_buku: buku.tahun_buku || "",
      jumlah_tersedia: buku.jumlah_tersedia 
    };
  } else {
    isEditing.value = false;
    currentBookId.value = null;
    newBuku.value = { kode_buku: "", judul: "", penulis: "", kategori: "", tempat: "", tahun_buku: "", jumlah_tersedia: 1 };
  }
  showBookModal.value = true;
};

const cancelBook = () => showBookModal.value = false;

const saveBook = async () => {
  if (!isBookFormValid.value) return;
  
  try {
    if (isEditing.value && currentBookId.value) {
      await api.put(`/buku/${currentBookId.value}`, newBuku.value);
      showAlertBox("Data buku berhasil diperbarui!", "success");
    } else {
      await api.post('/buku', newBuku.value);
      showAlertBox("Buku berhasil ditambahkan!", "success");
    }
    showBookModal.value = false;
    await fetchData(); 
  } catch (e) { 
    showAlertBox(e.response?.data?.message || "Gagal menyimpan data buku.", "error"); 
  }
};

const deleteBuku = (buku) => {
  showConfirmBox(`Apakah Anda yakin ingin menghapus buku "${buku.judul}"?`, async () => {
    try {
        await api.delete(`/buku/${buku.id_buku}`);
        await fetchData();
        showAlertBox("Buku berhasil dihapus.", "success");
    } catch (e) {
        showAlertBox(e.response?.data?.message || "Gagal menghapus buku.", "error");
    }
  });
}

onMounted(fetchData);
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
::-webkit-scrollbar { width: 4px; }
::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>