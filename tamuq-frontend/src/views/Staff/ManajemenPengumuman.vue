<template>
  <div class="min-h-screen bg-[#EBF2FA] p-4 sm:p-6 rounded-xl relative space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-[#00555A] flex items-center gap-2.5">
          <MegaphoneIcon class="w-8 h-8 text-[#F26430]" />
          Manajemen Pengumuman
        </h1>
        <p class="text-[#00555A]/70 text-sm mt-1">
          Kelola pengumuman sistem, target penerima, dan durasi penayangan.
        </p>
      </div>

      <button
        @click="openCreateModal"
        class="flex items-center justify-center gap-2 px-5 py-2.5 bg-[#F26430] hover:bg-[#D65020] text-white rounded-xl transition-all text-sm font-medium shadow-lg shadow-[#F26430]/30 hover:shadow-[#F26430]/50 cursor-pointer shrink-0"
      >
        <PlusIcon class="w-5 h-5" />
        Buat Pengumuman
      </button>
    </div>

    <!-- Toast / Alert Notification -->
    <transition name="slide-fade">
      <div
        v-if="toast.show"
        class="fixed top-5 right-5 z-[70] flex items-center gap-3 bg-white p-4 rounded-xl shadow-2xl border-l-4 w-full max-w-sm"
        :class="toast.type === 'success' ? 'border-emerald-500' : 'border-red-500'"
      >
        <div class="shrink-0">
          <CheckCircleIcon v-if="toast.type === 'success'" class="w-7 h-7 text-emerald-500" />
          <ExclamationCircleIcon v-else class="w-7 h-7 text-red-500" />
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="font-bold text-sm" :class="toast.type === 'success' ? 'text-emerald-900' : 'text-red-900'">
            {{ toast.title }}
          </h4>
          <p class="text-xs text-gray-600 mt-0.5">{{ toast.message }}</p>
        </div>
        <button @click="toast.show = false" class="text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
          <XMarkIcon class="w-4 h-4" />
        </button>
      </div>
    </transition>

    <!-- Main Card Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Filter Tabs Header -->
      <div class="p-4 sm:p-5 bg-[#00555A] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
          <button
            v-for="tab in filterTabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer flex items-center gap-2"
            :class="[
              activeTab === tab.id
                ? 'bg-[#F26430] text-white shadow-md shadow-[#F26430]/30'
                : 'bg-white/10 text-white/90 hover:bg-white/20 hover:text-white'
            ]"
          >
            <span>{{ tab.label }}</span>
            <span
              class="px-2 py-0.5 text-[11px] rounded-full font-bold"
              :class="activeTab === tab.id ? 'bg-white text-[#F26430]' : 'bg-white/20 text-white'"
            >
              {{ tab.count }}
            </span>
          </button>
        </div>

        <div class="text-xs text-[#00555A] font-medium bg-white px-3.5 py-2 rounded-xl border border-gray-200 shadow-sm shrink-0 self-start sm:self-auto">
          Total Pengumuman: <span class="text-[#F26430] font-bold">{{ filteredPengumuman.length }}</span>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="p-12 flex flex-col items-center justify-center text-[#F26430]">
        <svg class="animate-spin h-10 w-10 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm font-medium text-gray-500">Memuat data pengumuman...</span>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredPengumuman.length === 0" class="p-12 text-center flex flex-col items-center justify-center">
        <div class="bg-[#EBF2FA] p-4 rounded-full mb-3">
          <MegaphoneIcon class="w-10 h-10 text-[#00555A]/40" />
        </div>
        <h3 class="font-bold text-[#00555A] text-base">Belum Ada Pengumuman</h3>
        <p class="text-gray-500 text-xs sm:text-sm mt-1 max-w-sm">
          Tidak ada data pengumuman yang sesuai dengan filter yang dipilih. Silakan buat pengumuman baru.
        </p>
      </div>

      <!-- Table View -->
      <div v-else class="overflow-x-auto max-h-[600px]">
        <table class="w-full text-left border-collapse">
          <thead class="sticky top-0 z-10 shadow-sm">
            <tr class="bg-[#66999C] text-xs uppercase tracking-wider text-white font-bold">
              <th class="px-5 py-4 w-12 text-center">No</th>
              <th class="px-5 py-4 min-w-[200px]">Judul</th>
              <th class="px-5 py-4">Tipe</th>
              <th class="px-5 py-4">Target</th>
              <th class="px-5 py-4 text-center">Status</th>
              <th class="px-5 py-4 min-w-[180px]">Periode</th>
              <th class="px-5 py-4">Dibuat Oleh</th>
              <th class="px-5 py-4 text-center min-w-[180px]">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr
              v-for="(item, index) in filteredPengumuman"
              :key="item.id"
              class="hover:bg-[#F26430]/5 transition-colors group text-sm"
            >
              <!-- Index -->
              <td class="px-5 py-4 text-center text-[#00555A] font-medium text-xs">
                {{ index + 1 }}
              </td>

              <!-- Judul & Preview -->
              <td class="px-5 py-4">
                <div class="max-w-xs sm:max-w-sm">
                  <p class="font-semibold text-[#00555A] truncate" :title="item.judul">
                    {{ item.judul }}
                  </p>
                  <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">
                    {{ item.isi_pesan }}
                  </p>
                </div>
              </td>

              <!-- Tipe Badge -->
              <td class="px-5 py-4">
                <span
                  class="px-2.5 py-1 text-[11px] font-bold rounded-lg border uppercase inline-flex items-center gap-1.5"
                  :class="getTipeBadgeClass(item.tipe)"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="getTipeDotClass(item.tipe)"></span>
                  {{ item.tipe }}
                </span>
              </td>

              <!-- Target Badge / Individu recipient list -->
              <td class="px-5 py-4">
                <div v-if="item.target !== 'individu'">
                  <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-teal-50 text-[#00555A] border border-teal-200 capitalize">
                    {{ item.target }}
                  </span>
                </div>
                <div v-else class="relative inline-block">
                  <button
                    @click="toggleRecipientPopover(item.id)"
                    class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-purple-50 text-purple-700 border border-purple-200 hover:bg-purple-100 transition flex items-center gap-1 cursor-pointer"
                  >
                    <UsersIcon class="w-3.5 h-3.5" />
                    <span>{{ (item.penerima?.length || 0) }} User</span>
                  </button>

                  <!-- Recipient Popover -->
                  <div
                    v-if="expandedRecipientId === item.id"
                    class="absolute left-0 top-full mt-1 z-30 bg-white border border-gray-200 rounded-xl shadow-xl p-3 w-56 space-y-1.5"
                  >
                    <div class="flex items-center justify-between border-b border-gray-100 pb-1.5">
                      <span class="text-[11px] font-bold text-[#00555A] uppercase">Penerima Tagged</span>
                      <button @click="expandedRecipientId = null" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                        <XMarkIcon class="w-3.5 h-3.5" />
                      </button>
                    </div>
                    <ul v-if="item.penerima && item.penerima.length > 0" class="max-h-36 overflow-y-auto space-y-1 text-xs text-gray-700">
                      <li v-for="user in item.penerima" :key="user.id_user || user.id" class="flex items-center gap-1.5 py-0.5">
                        <span class="w-1.5 h-1.5 bg-purple-500 rounded-full shrink-0"></span>
                        <span class="truncate font-medium">{{ user.nama_lengkap }}</span>
                      </li>
                    </ul>
                    <p v-else class="text-xs text-gray-400 italic">Tidak ada user tagged</p>
                  </div>
                </div>
              </td>

              <!-- Status Badge -->
              <td class="px-5 py-4 text-center">
                <span
                  class="px-2.5 py-1 text-[11px] font-bold rounded-lg border uppercase inline-flex items-center gap-1.5"
                  :class="item.status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-200'"
                >
                  <span
                    class="w-1.5 h-1.5 rounded-full"
                    :class="item.status === 'aktif' ? 'bg-emerald-500' : 'bg-gray-400'"
                  ></span>
                  {{ item.status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>

              <!-- Periode -->
              <td class="px-5 py-4">
                <div class="text-xs space-y-0.5 text-gray-700">
                  <p class="font-medium">
                    {{ formatDateID(item.tanggal_mulai || item.created_at) }}
                  </p>
                  <p class="text-gray-500 text-[11px]">
                    s/d
                    <span v-if="item.tanggal_berakhir" class="font-medium text-gray-700">
                      {{ formatDateID(item.tanggal_berakhir) }}
                    </span>
                    <span v-else class="italic text-gray-500">
                      Sampai dihapus/diganti
                    </span>
                  </p>
                </div>
              </td>

              <!-- Dibuat oleh -->
              <td class="px-5 py-4 text-xs font-medium text-gray-700">
                {{ item.pembuat?.nama_lengkap || '—' }}
              </td>

              <!-- Aksi -->
              <td class="px-5 py-4 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <!-- Toggle Aktif/Nonaktif -->
                  <button
                    @click="handleToggleStatus(item)"
                    :disabled="togglingId === item.id"
                    class="px-2.5 py-1 rounded-lg text-xs font-semibold border transition cursor-pointer flex items-center gap-1"
                    :class="[
                      item.status === 'aktif'
                        ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100'
                        : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'
                    ]"
                    :title="item.status === 'aktif' ? 'Nonaktifkan Pengumuman' : 'Aktifkan Pengumuman'"
                  >
                    <PowerIcon class="w-3.5 h-3.5" />
                    <span>{{ item.status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                  </button>

                  <!-- Edit Button -->
                  <button
                    @click="openEditModal(item)"
                    class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg border border-blue-200 transition cursor-pointer"
                    title="Edit Pengumuman"
                  >
                    <PencilSquareIcon class="w-4 h-4" />
                  </button>

                  <!-- Delete Button -->
                  <button
                    @click="handleDelete(item)"
                    class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg border border-red-200 transition cursor-pointer"
                    title="Hapus Pengumuman"
                  >
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL FORM (Create & Edit) -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <!-- Backdrop -->
        <div
          class="absolute inset-0 bg-[#00555A]/40 backdrop-blur-sm transition-opacity"
          @click="closeModal"
        ></div>

        <!-- Dialog Card -->
        <div class="bg-white shadow-2xl rounded-2xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col relative z-10 transform transition-all scale-100">
          <!-- Modal Header -->
          <div class="sticky top-0 z-20 px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-[#EBF2FA]/90 backdrop-blur-md shrink-0">
            <h3 class="text-lg font-bold text-[#00555A] flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#F26430] rounded-full"></span>
              {{ modalMode === 'create' ? 'Buat Pengumuman Baru' : 'Edit Pengumuman' }}
            </h3>
            <button
              @click="closeModal"
              class="text-gray-400 hover:text-red-500 transition rounded-full hover:bg-red-50 p-1 cursor-pointer"
            >
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>

          <!-- Modal Body (Scrollable) -->
          <div class="p-6 space-y-5 overflow-y-auto flex-1">
            <!-- Form Error Alert -->
            <div v-if="formError" class="p-3 bg-red-50 border border-red-100 text-red-600 text-sm rounded-xl flex items-start gap-2">
              <ExclamationTriangleIcon class="w-5 h-5 shrink-0 mt-0.5" />
              <span>{{ formError }}</span>
            </div>

            <!-- Field 1: Judul -->
            <div class="space-y-1.5">
              <div class="flex justify-between items-center">
                <label class="text-xs font-bold text-[#00555A]/80 uppercase tracking-wide">
                  Judul Pengumuman <span class="text-red-500">*</span>
                </label>
                <span
                  class="text-xs"
                  :class="formData.judul.length >= 150 ? 'text-red-500 font-bold' : 'text-gray-400'"
                >
                  {{ formData.judul.length }}/150
                </span>
              </div>
              <input
                type="text"
                v-model="formData.judul"
                maxlength="150"
                placeholder="Contoh: Pemeliharaan Sistem Perpustakaan..."
                class="w-full px-4 py-2.5 bg-[#EBF2FA]/30 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm outline-none transition text-[#00555A]"
              />
            </div>

            <!-- Field 2: Isi Pesan -->
            <div class="space-y-1.5">
              <label class="text-xs font-bold text-[#00555A]/80 uppercase tracking-wide">
                Isi Pesan <span class="text-red-500">*</span>
              </label>
              <textarea
                v-model="formData.isi_pesan"
                rows="4"
                placeholder="Tuliskan isi pengumuman secara detail..."
                class="w-full px-4 py-2.5 bg-[#EBF2FA]/30 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm outline-none transition text-[#00555A] resize-y"
              ></textarea>
            </div>

            <!-- Field 3: Tipe Pengumuman -->
            <div class="space-y-2">
              <label class="text-xs font-bold text-[#00555A]/80 uppercase tracking-wide">
                Tipe Pengumuman <span class="text-red-500">*</span>
              </label>
              <div class="grid grid-cols-3 gap-3">
                <button
                  type="button"
                  @click="formData.tipe = 'info'"
                  class="p-3 rounded-xl border text-sm font-semibold flex items-center justify-center gap-2 transition cursor-pointer"
                  :class="[
                    formData.tipe === 'info'
                      ? 'bg-blue-500 text-white border-blue-500 shadow-md shadow-blue-500/20'
                      : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'
                  ]"
                >
                  <span class="w-2.5 h-2.5 rounded-full" :class="formData.tipe === 'info' ? 'bg-white' : 'bg-blue-500'"></span>
                  Info
                </button>

                <button
                  type="button"
                  @click="formData.tipe = 'penting'"
                  class="p-3 rounded-xl border text-sm font-semibold flex items-center justify-center gap-2 transition cursor-pointer"
                  :class="[
                    formData.tipe === 'penting'
                      ? 'bg-amber-500 text-white border-amber-500 shadow-md shadow-amber-500/20'
                      : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'
                  ]"
                >
                  <span class="w-2.5 h-2.5 rounded-full" :class="formData.tipe === 'penting' ? 'bg-white' : 'bg-amber-500'"></span>
                  Penting
                </button>

                <button
                  type="button"
                  @click="formData.tipe = 'darurat'"
                  class="p-3 rounded-xl border text-sm font-semibold flex items-center justify-center gap-2 transition cursor-pointer"
                  :class="[
                    formData.tipe === 'darurat'
                      ? 'bg-red-500 text-white border-red-500 shadow-md shadow-red-500/20'
                      : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'
                  ]"
                >
                  <span class="w-2.5 h-2.5 rounded-full" :class="formData.tipe === 'darurat' ? 'bg-white' : 'bg-red-500'"></span>
                  Darurat
                </button>
              </div>
            </div>

            <!-- Field 4: Target Penerima -->
            <div class="space-y-2">
              <label class="text-xs font-bold text-[#00555A]/80 uppercase tracking-wide">
                Target Penerima <span class="text-red-500">*</span>
              </label>
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                <button
                  type="button"
                  v-for="targetOpt in targetOptions"
                  :key="targetOpt.id"
                  @click="formData.target = targetOpt.id"
                  class="py-2.5 px-3 rounded-xl border text-xs sm:text-sm font-semibold text-center transition cursor-pointer capitalize"
                  :class="[
                    formData.target === targetOpt.id
                      ? 'bg-[#00555A] text-white border-[#00555A] shadow-sm'
                      : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'
                  ]"
                >
                  {{ targetOpt.label }}
                </button>
              </div>

              <!-- Individu Target Box (Search + Tagged Chips) -->
              <div v-if="formData.target === 'individu'" class="mt-3 p-4 bg-[#EBF2FA]/40 border border-teal-100 rounded-xl space-y-3">
                <p class="text-xs font-semibold text-[#00555A]">
                  Cari & Tag User Penerima:
                </p>

                <!-- Search Box -->
                <div class="relative">
                  <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                  <input
                    type="text"
                    v-model="userSearchQuery"
                    @input="onUserSearchInput"
                    placeholder="Ketik Nama atau Nomor Induk..."
                    class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#F26430]"
                  />
                  <!-- Searching spinner -->
                  <div v-if="isSearchingUsers" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <svg class="animate-spin h-4 w-4 text-[#F26430]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                  </div>
                </div>

                <!-- Dropdown Search Results -->
                <div
                  v-if="userSearchResults.length > 0"
                  class="bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto divide-y divide-gray-100"
                >
                  <div
                    v-for="user in userSearchResults"
                    :key="user.id_user || user.id"
                    @click="addTaggedUser(user)"
                    class="p-2.5 hover:bg-[#F26430]/10 transition cursor-pointer flex items-center justify-between"
                  >
                    <div>
                      <p class="text-sm font-semibold text-[#00555A]">{{ user.nama_lengkap }}</p>
                      <p class="text-xs text-gray-500 font-mono">{{ user.nomor_induk || '-' }} &bull; {{ user.role }}</p>
                    </div>
                    <PlusIcon class="w-4 h-4 text-[#F26430]" />
                  </div>
                </div>

                <p v-else-if="userSearchQuery.trim() && !isSearchingUsers" class="text-xs text-gray-400 italic">
                  User tidak ditemukan.
                </p>

                <!-- Tagged Users Chips -->
                <div class="space-y-1.5">
                  <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wide">
                    Daftar Tagged ({{ taggedUsers.length }} User):
                  </p>
                  <div v-if="taggedUsers.length > 0" class="flex flex-wrap gap-2">
                    <span
                      v-for="u in taggedUsers"
                      :key="u.id_user || u.id"
                      class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#00555A] text-white text-xs rounded-full font-medium shadow-sm"
                    >
                      <span>{{ u.nama_lengkap }}</span>
                      <button
                        type="button"
                        @click="removeTaggedUser(u.id_user || u.id)"
                        class="hover:text-red-300 transition cursor-pointer p-0.5"
                      >
                        <XMarkIcon class="w-3.5 h-3.5" />
                      </button>
                    </span>
                  </div>
                  <p v-else class="text-xs text-red-500 italic">
                    Belum ada user yang ditag. Pilih minimal 1 user.
                  </p>
                </div>
              </div>
            </div>

            <!-- Field 5: Durasi Tayang -->
            <div class="space-y-2">
              <label class="text-xs font-bold text-[#00555A]/80 uppercase tracking-wide">
                Durasi Tayang <span class="text-red-500">*</span>
              </label>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                <button
                  type="button"
                  v-for="dur in durationOptions"
                  :key="dur.id"
                  @click="durationOption = dur.id"
                  class="py-2 px-3 rounded-xl border text-xs font-semibold text-center transition cursor-pointer"
                  :class="[
                    durationOption === dur.id
                      ? 'bg-[#F26430] text-white border-[#F26430] shadow-sm'
                      : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'
                  ]"
                >
                  {{ dur.label }}
                </button>
              </div>

              <!-- Custom Datetime Input -->
              <div v-if="durationOption === 'custom'" class="mt-2 space-y-1">
                <label class="text-xs text-gray-600 font-medium">Pilih Tanggal & Jam Berakhir:</label>
                <input
                  type="datetime-local"
                  v-model="customDatetime"
                  class="w-full px-4 py-2 bg-[#EBF2FA]/50 border border-gray-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-[#F26430] text-[#00555A]"
                />
              </div>

              <!-- Computed Preview -->
              <div v-if="previewTanggalBerakhir" class="mt-2 p-3 bg-teal-50 border border-teal-100 rounded-xl text-xs text-[#00555A] font-medium flex items-center gap-2">
                <ClockIcon class="w-4 h-4 text-[#F26430] shrink-0" />
                <span>Berakhir: <strong class="text-[#F26430]">{{ previewTanggalBerakhir }}</strong></span>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="sticky bottom-0 z-20 px-6 py-4 bg-[#EBF2FA]/90 backdrop-blur-md border-t border-gray-100 flex justify-end gap-3 shrink-0">
            <button
              type="button"
              @click="closeModal"
              class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-50 transition cursor-pointer shadow-sm"
            >
              Batal
            </button>
            <button
              type="button"
              @click="handleSave"
              :disabled="isSubmitting"
              class="px-5 py-2.5 bg-[#F26430] hover:bg-[#D65020] text-white rounded-xl text-sm font-semibold transition cursor-pointer shadow-lg shadow-[#F26430]/30 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <svg v-if="isSubmitting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}</span>
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '@/api';
import {
  MegaphoneIcon,
  PlusIcon,
  PencilSquareIcon,
  TrashIcon,
  XMarkIcon,
  MagnifyingGlassIcon,
  UsersIcon,
  ClockIcon,
  CheckCircleIcon,
  ExclamationCircleIcon,
  ExclamationTriangleIcon,
  PowerIcon,
} from '@heroicons/vue/24/outline';

// State
const pengumumanList = ref([]);
const isLoading = ref(false);
const isSubmitting = ref(false);
const togglingId = ref(null);
const activeTab = ref('Semua');
const showModal = ref(false);
const modalMode = ref('create'); // 'create' | 'edit'
const editingId = ref(null);
const formError = ref('');
const expandedRecipientId = ref(null);

// Toast notification
const toast = ref({
  show: false,
  type: 'success',
  title: '',
  message: '',
});

const showToast = (type, title, message) => {
  toast.value = { show: true, type, title, message };
  setTimeout(() => {
    toast.value.show = false;
  }, 4000);
};

// Form data
const formData = ref({
  judul: '',
  isi_pesan: '',
  tipe: 'info',
  target: 'semua',
});

// Duration state
const durationOption = ref('sampai_dihapus'); // 'sampai_dihapus', '1_hari', '1_minggu', '1_bulan', 'custom'
const customDatetime = ref('');

// Individu search & tagged users state
const taggedUsers = ref([]);
const userSearchQuery = ref('');
const userSearchResults = ref([]);
const isSearchingUsers = ref(false);
let searchTimeout = null;

// Static options
const targetOptions = [
  { id: 'semua', label: 'Semua' },
  { id: 'pengunjung', label: 'Pengunjung' },
  { id: 'staff', label: 'Staff' },
  { id: 'individu', label: 'Individu' },
];

const durationOptions = [
  { id: 'sampai_dihapus', label: 'Sampai Dihapus/Diganti' },
  { id: '1_hari', label: '1 Hari' },
  { id: '1_minggu', label: '1 Minggu' },
  { id: '1_bulan', label: '1 Bulan' },
  { id: 'custom', label: 'Custom' },
];

// Computed Filter Tabs with Counts
const filterTabs = computed(() => {
  const allCount = pengumumanList.value.length;
  const infoCount = pengumumanList.value.filter(item => item.tipe?.toLowerCase() === 'info').length;
  const pentingCount = pengumumanList.value.filter(item => item.tipe?.toLowerCase() === 'penting').length;
  const daruratCount = pengumumanList.value.filter(item => item.tipe?.toLowerCase() === 'darurat').length;

  return [
    { id: 'Semua', label: 'Semua', count: allCount },
    { id: 'Info', label: 'Info', count: infoCount },
    { id: 'Penting', label: 'Penting', count: pentingCount },
    { id: 'Darurat', label: 'Darurat', count: daruratCount },
  ];
});

// Computed Filtered List
const filteredPengumuman = computed(() => {
  if (activeTab.value === 'Semua') return pengumumanList.value;
  return pengumumanList.value.filter(
    item => item.tipe?.toLowerCase() === activeTab.value.toLowerCase()
  );
});

// Computed Preview End Date
const previewTanggalBerakhir = computed(() => {
  if (durationOption.value === 'sampai_dihapus') return null;

  let targetDate = null;
  const now = new Date();

  if (durationOption.value === '1_hari') {
    targetDate = new Date(now.getTime() + 24 * 60 * 60 * 1000);
  } else if (durationOption.value === '1_minggu') {
    targetDate = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);
  } else if (durationOption.value === '1_bulan') {
    targetDate = new Date(now);
    targetDate.setMonth(targetDate.getMonth() + 1);
  } else if (durationOption.value === 'custom') {
    if (customDatetime.value) {
      targetDate = new Date(customDatetime.value);
    }
  }

  if (!targetDate || isNaN(targetDate.getTime())) return null;

  return targetDate.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
});

// Formatting Helpers
const formatDateID = (dateStr) => {
  if (!dateStr) return '—';
  const d = new Date(dateStr);
  if (isNaN(d.getTime())) return dateStr;
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
};

const getTipeBadgeClass = (tipe) => {
  switch (tipe?.toLowerCase()) {
    case 'darurat':
      return 'bg-red-50 text-red-700 border-red-200';
    case 'penting':
      return 'bg-amber-50 text-amber-800 border-amber-200';
    default:
      return 'bg-blue-50 text-blue-700 border-blue-200';
  }
};

const getTipeDotClass = (tipe) => {
  switch (tipe?.toLowerCase()) {
    case 'darurat':
      return 'bg-red-500';
    case 'penting':
      return 'bg-amber-500';
    default:
      return 'bg-blue-500';
  }
};

const toggleRecipientPopover = (id) => {
  expandedRecipientId.value = expandedRecipientId.value === id ? null : id;
};

// API Calls
const fetchPengumuman = async () => {
  isLoading.value = true;
  try {
    const res = await api.get('/staff/pengumuman');
    if (res.data && res.data.success) {
      pengumumanList.value = res.data.data || [];
    }
  } catch (err) {
    console.error('Gagal mengambil pengumuman:', err);
    showToast('error', 'Gagal', 'Terjadi kesalahan saat memuat data pengumuman.');
  } finally {
    isLoading.value = false;
  }
};

// User Search Input Handling
const onUserSearchInput = () => {
  if (searchTimeout) clearTimeout(searchTimeout);
  const q = userSearchQuery.value.trim();

  if (!q) {
    userSearchResults.value = [];
    isSearchingUsers.value = false;
    return;
  }

  isSearchingUsers.value = true;
  searchTimeout = setTimeout(async () => {
    try {
      const res = await api.get('/staff/pengumuman/cari-user', {
        params: { q },
      });
      if (res.data && res.data.success) {
        userSearchResults.value = res.data.data || [];
      }
    } catch (err) {
      console.error('Gagal mencari user:', err);
      userSearchResults.value = [];
    } finally {
      isSearchingUsers.value = false;
    }
  }, 400);
};

const addTaggedUser = (user) => {
  const userId = user.id_user || user.id;
  const exists = taggedUsers.value.some(u => (u.id_user || u.id) === userId);
  if (!exists) {
    taggedUsers.value.push({
      id_user: userId,
      nama_lengkap: user.nama_lengkap,
      nomor_induk: user.nomor_induk || '',
    });
  }
  userSearchQuery.value = '';
  userSearchResults.value = [];
};

const removeTaggedUser = (userId) => {
  taggedUsers.value = taggedUsers.value.filter(u => (u.id_user || u.id) !== userId);
};

// Modal Controls
const openCreateModal = () => {
  modalMode.value = 'create';
  editingId.value = null;
  formData.value = {
    judul: '',
    isi_pesan: '',
    tipe: 'info',
    target: 'semua',
  };
  durationOption.value = 'sampai_dihapus';
  customDatetime.value = '';
  taggedUsers.value = [];
  userSearchQuery.value = '';
  userSearchResults.value = [];
  formError.value = '';
  showModal.value = true;
};

const openEditModal = (item) => {
  modalMode.value = 'edit';
  editingId.value = item.id;
  formData.value = {
    judul: item.judul || '',
    isi_pesan: item.isi_pesan || '',
    tipe: item.tipe || 'info',
    target: item.target || 'semua',
  };

  if (item.penerima && item.penerima.length > 0) {
    taggedUsers.value = item.penerima.map(u => ({
      id_user: u.id_user || u.id,
      nama_lengkap: u.nama_lengkap,
      nomor_induk: u.nomor_induk || '',
    }));
  } else {
    taggedUsers.value = [];
  }

  userSearchQuery.value = '';
  userSearchResults.value = [];
  formError.value = '';

  if (!item.tanggal_berakhir) {
    durationOption.value = 'sampai_dihapus';
    customDatetime.value = '';
  } else {
    durationOption.value = 'custom';
    const d = new Date(item.tanggal_berakhir);
    if (!isNaN(d.getTime())) {
      const pad = (n) => String(n).padStart(2, '0');
      customDatetime.value = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    } else {
      customDatetime.value = '';
    }
  }

  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  formError.value = '';
};

// Submit Save Handler
const handleSave = async () => {
  formError.value = '';

  // Validation
  if (!formData.value.judul.trim()) {
    formError.value = 'Judul pengumuman wajib diisi.';
    return;
  }
  if (!formData.value.isi_pesan.trim()) {
    formError.value = 'Isi pesan pengumuman wajib diisi.';
    return;
  }
  if (formData.value.target === 'individu' && taggedUsers.value.length === 0) {
    formError.value = 'Minimal 1 user harus ditag jika target individu.';
    return;
  }
  if (durationOption.value === 'custom' && !customDatetime.value) {
    formError.value = 'Silakan pilih tanggal & jam berakhir untuk durasi custom.';
    return;
  }

  // Calculate tanggal_berakhir payload
  let payloadTanggalBerakhir = null;
  const now = new Date();
  if (durationOption.value === '1_hari') {
    payloadTanggalBerakhir = new Date(now.getTime() + 24 * 60 * 60 * 1000).toISOString();
  } else if (durationOption.value === '1_minggu') {
    payloadTanggalBerakhir = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000).toISOString();
  } else if (durationOption.value === '1_bulan') {
    const targetDate = new Date(now);
    targetDate.setMonth(targetDate.getMonth() + 1);
    payloadTanggalBerakhir = targetDate.toISOString();
  } else if (durationOption.value === 'custom') {
    payloadTanggalBerakhir = new Date(customDatetime.value).toISOString();
  }

  const body = {
    judul: formData.value.judul.trim(),
    isi_pesan: formData.value.isi_pesan.trim(),
    tipe: formData.value.tipe,
    target: formData.value.target,
    tanggal_berakhir: payloadTanggalBerakhir,
  };

  if (formData.value.target === 'individu') {
    body.user_ids = taggedUsers.value.map(u => u.id_user || u.id);
  }

  isSubmitting.value = true;
  try {
    if (modalMode.value === 'create') {
      await api.post('/staff/pengumuman', body);
      showToast('success', 'Berhasil', 'Pengumuman baru telah berhasil dibuat.');
    } else {
      await api.put(`/staff/pengumuman/${editingId.value}`, body);
      showToast('success', 'Berhasil', 'Pengumuman telah berhasil diperbarui.');
    }

    closeModal();
    fetchPengumuman();
  } catch (err) {
    console.error('Gagal menyimpan pengumuman:', err);
    formError.value = err.response?.data?.message || 'Terjadi kesalahan saat menyimpan pengumuman.';
  } finally {
    isSubmitting.value = false;
  }
};

// Toggle Status Handler
const handleToggleStatus = async (item) => {
  togglingId.value = item.id;
  try {
    const res = await api.post(`/staff/pengumuman/${item.id}/toggle`);
    if (res.data && res.data.success) {
      const newStatus = res.data.data?.status || (item.status === 'aktif' ? 'nonaktif' : 'aktif');
      showToast(
        'success',
        'Status Diubah',
        `Pengumuman "${item.judul}" sekarang berstatus ${newStatus === 'aktif' ? 'Aktif' : 'Nonaktif'}.`
      );
      fetchPengumuman();
    }
  } catch (err) {
    console.error('Gagal mengubah status pengumuman:', err);
    showToast('error', 'Gagal', 'Gagal mengubah status pengumuman.');
  } finally {
    togglingId.value = null;
  }
};

// Delete Handler
const handleDelete = async (item) => {
  const confirmed = window.confirm(
    `Yakin ingin menghapus pengumuman "${item.judul}"? Tindakan ini permanen.`
  );
  if (!confirmed) return;

  try {
    await api.delete(`/staff/pengumuman/${item.id}`);
    showToast('success', 'Berhasil Hapus', 'Pengumuman telah berhasil dihapus.');
    fetchPengumuman();
  } catch (err) {
    console.error('Gagal menghapus pengumuman:', err);
    showToast('error', 'Gagal', 'Terjadi kesalahan saat menghapus pengumuman.');
  }
};

onMounted(() => {
  fetchPengumuman();
});
</script>
