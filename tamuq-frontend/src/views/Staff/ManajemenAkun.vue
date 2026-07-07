<template>
  <div class="min-h-screen bg-[#EBF2FA] p-6 rounded-xl relative">
    <div class="space-y-6">
      
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-[#00555A] flex items-center gap-2">
            <UsersIcon class="w-8 h-8 text-[#F26430]" />
            Manajemen Akun
          </h1>
          <p class="text-[#00555A]/70 text-sm mt-1">Kelola data pengguna, peran akses, dan status akun sistem.</p>
        </div>

        <button
          @click="openModal('add')"
          class="flex items-center gap-2 px-5 py-2.5 bg-[#F26430] hover:bg-[#D65020] text-white rounded-xl transition-all text-sm font-medium shadow-lg shadow-[#F26430]/30 hover:shadow-[#F26430]/50"
        >
          <UserPlusIcon class="w-5 h-5" />
          Tambah Akun
        </button>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#00555A]">
          <div class="relative w-full sm:w-72">
            <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input 
              type="text" 
              v-model="search" 
              placeholder="Cari Nama atau Nomor Induk..." 
              class="w-full pl-10 pr-4 py-2 bg-white border border-transparent rounded-xl focus:ring-2 focus:ring-[#F26430] text-sm outline-none transition shadow-sm placeholder-gray-400 text-[#00555A]"
            />
          </div>
          <div class="text-xs text-[#00555A] font-medium bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm">
            Total Pengguna: <span class="text-[#F26430] font-bold">{{ filteredUsers.length }}</span>
          </div>
        </div>

        <div v-if="isLoading" class="p-12 flex flex-col items-center justify-center text-[#F26430]">
          <svg class="animate-spin h-10 w-10 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span class="text-sm font-medium text-gray-500">Memuat data pengguna...</span>
        </div>

        <div v-else class="overflow-x-auto max-h-[500px]">
          <table class="w-full text-left border-collapse">
            <thead class="sticky top-0 z-10 shadow-sm">
              <tr class="bg-[#66999C] text-xs uppercase tracking-wider text-white font-bold">
                <th class="px-6 py-4 w-16 text-center">No</th>
                <th class="px-6 py-4">Pengguna</th>
                <th class="px-6 py-4">Nomor Induk</th>
                <th class="px-6 py-4">Role</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr
                v-for="(user, index) in filteredUsers"
                :key="user.id"
                class="hover:bg-[#F26430]/5 transition-colors group"
              >
                <td class="px-6 py-4 text-center text-sm text-[#00555A] font-medium">{{ index + 1 }}</td>

                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div class="relative flex-shrink-0">
                      <img 
                        v-if="user.foto_profil" 
                        :src="user.foto_profil" 
                        @error="handleImageError(user)"
                        alt="Avatar"
                        class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm"
                      />
                      <div 
                        v-else 
                        class="w-10 h-10 rounded-full bg-[#00555A] text-white flex items-center justify-center font-bold text-xs shadow-sm"
                      >
                        {{ getInitials(user.nama_lengkap) }}
                      </div>
                      <span 
                        class="absolute bottom-0 right-0 w-2.5 h-2.5 border-2 border-white rounded-full transition-colors"
                        :class="user.status === 'Aktif' ? 'bg-green-500' : 'bg-red-500'"
                      ></span>
                    </div>

                    <div>
                      <p class="font-semibold text-[#00555A] text-sm">{{ user.nama_lengkap }}</p>
                    </div>
                  </div>
                </td>
                
                <td class="px-6 py-4">
                  <div class="flex flex-col gap-0.5">
                    <span class="text-sm text-[#00555A] font-medium font-mono">{{ user.nomor_induk }}</span>
                    <span class="text-xs text-gray-500" :title="user.email">{{ user.email || '-' }}</span>
                    <span class="text-xs text-[#00555A]/70 bg-[#EBF2FA] px-2 py-0.5 rounded-md w-fit mt-1">{{ user.prodi }}</span>
                  </div>
                </td>

                <td class="px-6 py-4">
                  <span 
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border capitalize"
                    :class="getRoleBadgeClass(user.role)"
                  >
                    <ShieldCheckIcon v-if="user.role && user.role.toLowerCase() === 'superadmin'" class="w-3.5 h-3.5" />
                    {{ user.role }}
                  </span>
                </td>

                <td class="px-6 py-4 text-center">
                  <div class="flex flex-col items-center justify-center gap-1.5">
                    <button 
                      @click="toggleUserStatus(user)"
                      :disabled="togglingUserId !== null"
                      class="relative inline-flex h-6 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-[#00555A] focus:ring-offset-1 shadow-inner disabled:opacity-50 disabled:cursor-not-allowed"
                      :class="user.status === 'Aktif' ? 'bg-green-500' : 'bg-red-500'"
                      :title="user.status === 'Aktif' ? 'Klik untuk memblokir akun' : 'Klik untuk mengaktifkan akun'"
                    >
                      <span 
                        class="pointer-events-none flex items-center justify-center h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-300 ease-in-out"
                        :class="user.status === 'Aktif' ? 'translate-x-6' : 'translate-x-0'"
                      >
                        <svg v-if="togglingUserId === user.id" class="animate-spin h-3 w-3 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                      </span>
                    </button>
                    <span class="text-[10px] font-bold tracking-wider uppercase" :class="user.status === 'Aktif' ? 'text-green-600' : 'text-red-600'">
                      {{ user.status }}
                    </span>
                  </div>
                </td>

                <td class="px-6 py-4 text-center">
                  <div class="flex justify-center gap-2">
                    <button @click="openModal('edit', user)" class="p-2 text-[#F26430] hover:bg-[#F26430]/10 rounded-lg transition border border-transparent hover:border-[#F26430]/20" title="Edit Data">
                      <PencilSquareIcon class="w-4 h-4" />
                    </button>
                    <button @click="openDeleteConfirmation(user)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition border border-transparent hover:border-red-200" title="Hapus Permanen">
                      <TrashIcon class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>

              <tr v-if="filteredUsers.length === 0">
                <td colspan="6" class="px-6 py-16 text-center">
                  <div class="flex flex-col items-center justify-center text-gray-400">
                    <div class="bg-[#EBF2FA] p-4 rounded-full mb-3">
                      <UserIcon class="w-10 h-10 text-[#00555A]/30" />
                    </div>
                    <p class="font-medium text-[#00555A]">Data tidak ditemukan</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <transition name="fade">
        <div v-if="showModal" class="fixed inset-0 flex items-center justify-center z-50 px-4">
          <div class="absolute inset-0 bg-[#00555A]/40 backdrop-blur-sm transition-opacity" @click="closeModal"></div>

          <div class="bg-white shadow-2xl rounded-2xl w-full max-w-lg overflow-y-auto max-h-[90vh] transform transition-all scale-100 relative z-10">
            
            <div class="sticky top-0 z-20 px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-[#EBF2FA]/90 backdrop-blur-md">
              <h3 class="text-lg font-bold text-[#00555A] flex items-center gap-2">
                <span class="w-1 h-6 bg-[#F26430] rounded-full"></span>
                {{ modalMode === 'add' ? 'Tambah Akun Baru' : 'Edit Data Akun' }}
              </h3>
              <button @click="closeModal" class="text-gray-400 hover:text-red-500 transition rounded-full hover:bg-red-50 p-1">
                <XMarkIcon class="w-6 h-6" />
              </button>
            </div>

            <div class="p-6 space-y-5">
              <div v-if="errorMsg" class="p-3 bg-red-50 border border-red-100 text-red-600 text-sm rounded-lg flex items-start gap-2">
                  <ExclamationTriangleIcon class="h-5 w-5 shrink-0" />
                  <span>{{ errorMsg }}</span>
              </div>

              <div class="space-y-1.5">
                <label class="text-xs font-bold text-[#00555A]/70 uppercase tracking-wide">Nama Lengkap</label>
                <input type="text" v-model="formData.nama_lengkap" class="w-full px-4 py-2.5 bg-[#EBF2FA]/30 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm outline-none transition text-[#00555A]" placeholder="Contoh: Ahmad Fadli" />
              </div>

              <div class="space-y-1.5">
                <label class="text-xs font-bold text-[#00555A]/70 uppercase tracking-wide">Email</label>
                <input type="email" v-model="formData.email" class="w-full px-4 py-2.5 bg-[#EBF2FA]/30 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm outline-none transition text-[#00555A]" placeholder="Contoh: ahmad@student.undiksha.ac.id" />
              </div>

              <div class="grid grid-cols-2 gap-5">
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-[#00555A]/70 uppercase tracking-wide">Nomor Induk / NIM</label>
                  <input type="text" v-model="formData.nomor_induk" class="w-full px-4 py-2.5 bg-[#EBF2FA]/30 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm outline-none transition text-[#00555A]" placeholder="21020..." />
                </div>
                
                <div class="space-y-1.5">
                  <label class="text-xs font-bold text-[#00555A]/70 uppercase tracking-wide">Program Studi</label>
                  <select v-model="formData.prodi" class="w-full px-4 py-2.5 bg-[#EBF2FA]/30 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm outline-none transition cursor-pointer text-[#00555A]">
                    <option value="PTIK">PTIK</option>
                    <option value="TEKOM">TEKOM</option>
                  </select>
                </div>
              </div>

              <div class="space-y-1.5 relative">
                <label class="text-xs font-bold text-[#00555A]/70 uppercase tracking-wide">
                  Password 
                  <span v-if="modalMode === 'edit'" class="text-gray-400 font-normal normal-case">(Kosongkan jika tidak diubah)</span>
                </label>
                <div class="relative">
                  <input 
                    :type="showPassword ? 'text' : 'password'" 
                    v-model="formData.password" 
                    class="w-full px-4 py-2.5 bg-[#EBF2FA]/30 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm outline-none transition text-[#00555A] pr-10" 
                    :placeholder="modalMode === 'add' ? 'Masukkan Password...' : 'Ketik password baru...'" 
                  />
                  <button @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#00555A]">
                    <span v-if="!showPassword" class="text-xs font-bold">Show</span>
                    <span v-else class="text-xs font-bold">Hide</span>
                  </button>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-5">
                <div class="space-y-1.5" :class="modalMode === 'edit' ? 'col-span-2' : ''">
                  <label class="text-xs font-bold text-[#00555A]/70 uppercase tracking-wide">Role Akses</label>
                  <select v-model="formData.role" class="w-full px-4 py-2.5 bg-[#EBF2FA]/30 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm outline-none transition cursor-pointer text-[#00555A]">
                    <option value="Pengunjung">Pengunjung</option>
                    <option value="Staff">Staff</option>
                    <option value="Superadmin">Superadmin</option>
                  </select>
                </div>

                <div v-if="modalMode === 'add'" class="space-y-1.5">
                  <label class="text-xs font-bold text-[#00555A]/70 uppercase tracking-wide">Status Akun</label>
                  <select v-model="formData.status" class="w-full px-4 py-2.5 bg-[#EBF2FA]/30 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm outline-none transition cursor-pointer text-[#00555A]">
                    <option value="Aktif">Aktif</option>
                    <option value="Blokir">Blokir / Nonaktif</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="sticky bottom-0 z-20 px-6 py-4 bg-[#EBF2FA]/90 backdrop-blur-md border-t border-gray-100 flex justify-end gap-3">
              <button @click="closeModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-50 transition shadow-sm">Batal</button>
              <button @click="saveData" :disabled="isSubmitting" class="px-5 py-2.5 bg-[#F26430] hover:bg-[#D65020] text-white rounded-xl text-sm font-semibold transition shadow-lg shadow-[#F26430]/30 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                {{ isSubmitting ? 'Menyimpan...' : (modalMode === 'add' ? 'Simpan Akun' : 'Perbarui Data') }}
              </button>
            </div>

          </div>
        </div>
      </transition>

      <transition name="fade">
        <div v-if="deleteState.show" class="fixed inset-0 flex items-center justify-center z-50 px-4">
          <div class="absolute inset-0 bg-[#00555A]/40 backdrop-blur-sm transition-opacity" @click="closeDeleteModal"></div>
          <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 relative z-10 text-center transform transition-all scale-100">
            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <ExclamationTriangleIcon class="w-8 h-8 text-red-600" />
            </div>
            <h3 class="text-lg font-bold text-[#00555A] mb-2">Hapus Pengguna?</h3>
            <p class="text-gray-500 text-sm mb-6">
              Apakah Anda yakin ingin menghapus pengguna <span class="font-bold text-[#F26430]">"{{ deleteState.userName }}"</span>? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex gap-3 justify-center">
              <button @click="closeDeleteModal" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-50 transition">
                Batal
              </button>
              <button @click="confirmDelete" :disabled="isDeleting" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition shadow-lg shadow-red-600/30 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg v-if="isDeleting" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span v-if="isDeleting">Menghapus...</span>
                <span v-else>Ya, Hapus</span>
              </button>
            </div>
          </div>
        </div>
      </transition>

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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import api from "@/api";
import { 
  UsersIcon, UserPlusIcon, MagnifyingGlassIcon, PencilSquareIcon, 
  TrashIcon, XMarkIcon, ShieldCheckIcon, UserIcon, 
  CheckCircleIcon, ExclamationTriangleIcon, ExclamationCircleIcon 
} from "@heroicons/vue/24/outline";

const users = ref([]);
const search = ref("");
const showModal = ref(false);
const modalMode = ref("add");
const isLoading = ref(false);
const isSubmitting = ref(false);
const isDeleting = ref(false);
const togglingUserId = ref(null);
const errorMsg = ref("");
const showPassword = ref(false);

const alert = ref({
  show: false,
  type: 'success', 
  title: '',
  message: ''
});

const deleteState = ref({
  show: false,
  id: null,
  userName: ''
});

const formData = ref({
  id: null,
  nama_lengkap: "",
  nomor_induk: "", 
  email: "",
  prodi: "PTIK",
  role: "Pengunjung",
  status: "Aktif",
  password: ""
});

const filteredUsers = computed(() => {
  if (!search.value) return users.value;
  const term = search.value.toLowerCase();
  return users.value.filter(u => 
    u.nama_lengkap.toLowerCase().includes(term) || 
    (u.nomor_induk && u.nomor_induk.toString().includes(term)) || 
    (u.email && u.email.toLowerCase().includes(term))
  );
});

const getRoleBadgeClass = (role) => {
  if (!role) return 'bg-gray-100 text-gray-600 border-gray-200';
  const lowerRole = role.toLowerCase();
  if (lowerRole === 'superadmin') return 'bg-[#00555A]/10 text-[#00555A] border-[#00555A]/20';
  if (lowerRole === 'staff') return 'bg-[#F26430]/10 text-[#F26430] border-[#F26430]/20';
  return 'bg-gray-100 text-gray-600 border-gray-200';
};

const getInitials = (name) => {
  if (!name) return "?";
  return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
};

const handleImageError = (user) => {
  user.foto_profil = null; 
};

const showAlert = (type, title, message) => {
  alert.value = { show: true, type, title, message };
  setTimeout(() => {
    alert.value.show = false;
  }, 3000); 
};

const fetchUsers = async () => {
  isLoading.value = true;
  try {
    const response = await api.get('/admin/users');
    const rawData = response.data.data || response.data;

    users.value = rawData.map(u => ({
      ...u,
      id: u.id_user || u.id, 
      status: (u.status_akun === 'aktif' || u.status === 'Aktif') ? 'Aktif' : 'Blokir', 
      role: u.role ? u.role.charAt(0).toUpperCase() + u.role.slice(1) : 'Pengunjung'
    }));

  } catch (error) {
    console.error("Fetch Error:", error);
    showAlert('error', 'Gagal Memuat', 'Tidak dapat mengambil data pengguna.');
  } finally {
    isLoading.value = false;
  }
};

const toggleUserStatus = async (user) => {
  if (togglingUserId.value !== null) return;
  togglingUserId.value = user.id;

  const originalStatus = user.status;
  const newStatus = originalStatus === 'Aktif' ? 'Blokir' : 'Aktif';
  
  user.status = newStatus;

  const payload = new FormData();
  payload.append('_method', 'PUT');
  payload.append('nama_lengkap', user.nama_lengkap);
  payload.append('nomor_induk', user.nomor_induk);
  if (user.email) payload.append('email', user.email);
  payload.append('prodi', user.prodi);
  payload.append('role', user.role.toLowerCase());
  payload.append('status', newStatus.toLowerCase());

  try {
    await api.post(`/admin/users/${user.id}`, payload, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    showAlert('success', 'Status Berubah', `Akun ${user.nama_lengkap} berhasil diubah menjadi ${newStatus}.`);
  } catch (error) {
    user.status = originalStatus;
    console.error("Toggle Status Error:", error);
    showAlert('error', 'Gagal', 'Tidak dapat mengubah status pengguna.');
  } finally {
    togglingUserId.value = null;
  }
};

const saveData = async () => {
  errorMsg.value = "";
  if (!formData.value.nama_lengkap || !formData.value.nomor_induk) {
    errorMsg.value = "Nama dan Nomor Induk wajib diisi!";
    return;
  }
  if (modalMode.value === 'add' && !formData.value.password) {
    errorMsg.value = "Password wajib diisi untuk akun baru!";
    return;
  }

  isSubmitting.value = true;
  
  const payload = new FormData();
  payload.append('nama_lengkap', formData.value.nama_lengkap);
  payload.append('nomor_induk', formData.value.nomor_induk); 
  payload.append('email', formData.value.email || "");
  payload.append('prodi', formData.value.prodi);
  payload.append('role', formData.value.role.toLowerCase());
  payload.append('status', formData.value.status.toLowerCase()); 
  
  if (formData.value.password) {
    payload.append('password', formData.value.password);
  }

  try {
    let response;
    
    if (modalMode.value === 'add') {
      response = await api.post('/admin/users', payload, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      
      const newUserRaw = response.data.data || response.data;
      const newUserFormatted = {
        ...newUserRaw,
        id: newUserRaw.id_user || newUserRaw.id,
        status: (newUserRaw.status === 'aktif' || newUserRaw.status === 'Aktif') ? 'Aktif' : 'Blokir',
        role: newUserRaw.role ? newUserRaw.role.charAt(0).toUpperCase() + newUserRaw.role.slice(1) : 'Pengunjung'
      };

      users.value.unshift(newUserFormatted);
      showAlert('success', 'Berhasil', 'Akun pengguna baru berhasil dibuat!');

    } else {
      payload.append('_method', 'PUT'); 
      const url = `/admin/users/${formData.value.id}`;
      
      response = await api.post(url, payload, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      
      const idx = users.value.findIndex(u => u.id === formData.value.id);
      if (idx !== -1) {
        users.value[idx] = { 
            ...users.value[idx], 
            nama_lengkap: formData.value.nama_lengkap,
            nomor_induk: formData.value.nomor_induk, 
            email: formData.value.email,
            prodi: formData.value.prodi,
            role: formData.value.role,
            status: formData.value.status
        };
      }
      showAlert('success', 'Berhasil', 'Data pengguna berhasil diperbarui!');
    }
    
    closeModal();

  } catch (error) {
    console.error("Save Error:", error);
    if (error.response && error.response.data.errors) {
      const firstErrorKey = Object.keys(error.response.data.errors)[0];
      errorMsg.value = error.response.data.errors[firstErrorKey][0];
    } else {
      errorMsg.value = error.response?.data?.message || "Terjadi kesalahan saat menyimpan data.";
    }
  } finally {
    isSubmitting.value = false;
  }
};

const openDeleteConfirmation = (user) => {
  deleteState.value = {
    show: true,
    id: user.id,
    userName: user.nama_lengkap
  };
};

const closeDeleteModal = () => {
  deleteState.value = { show: false, id: null, userName: '' };
};

const confirmDelete = async () => {
  if (isDeleting.value) return;
  isDeleting.value = true;
  const id = deleteState.value.id;

  try {
    await api.delete(`/admin/users/${id}`);
    users.value = users.value.filter(u => u.id !== id);
    showAlert('success', 'Terhapus', 'Pengguna berhasil dihapus permanen.');
  } catch (error) {
    console.error("Delete Error:", error);
    showAlert('error', 'Gagal', 'Gagal menghapus pengguna: ' + (error.response?.data?.message || "Server Error"));
  } finally {
    isDeleting.value = false;
    closeDeleteModal(); 
  }
};

const openModal = (mode, user = null) => {
  modalMode.value = mode;
  errorMsg.value = "";
  showPassword.value = false;

  if (mode === 'edit' && user) {
    formData.value = { 
      id: user.id,
      nama_lengkap: user.nama_lengkap,
      nomor_induk: user.nomor_induk,
      email: user.email || "", 
      prodi: user.prodi || "PTIK",
      role: user.role, 
      status: user.status,
      password: "" 
    };
  } else {
    formData.value = { 
      id: null, 
      nama_lengkap: "", 
      nomor_induk: "",
      email: "",
      prodi: "PTIK", 
      role: "Pengunjung", 
      status: "Aktif",
      password: "" 
    };
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

onMounted(() => {
  fetchUsers();
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-leave-active { transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1); }
.slide-fade-enter-from, .slide-fade-leave-to { transform: translateX(20px); opacity: 0; }

.overflow-y-auto::-webkit-scrollbar { width: 6px; }
.overflow-y-auto::-webkit-scrollbar-track { background: transparent; }
.overflow-y-auto::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
</style>