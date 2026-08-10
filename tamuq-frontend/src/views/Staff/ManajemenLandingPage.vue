<template>
  <div class="min-h-screen bg-[#EBF2FA] p-4 sm:p-6 rounded-xl relative space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-[#00555A] flex items-center gap-2.5">
          <PhotoIcon class="w-8 h-8 text-[#F26430]" />
          Manajemen Landing Page
        </h1>
        <p class="text-[#00555A]/70 text-sm mt-1">
          Kelola gambar hero carousel dan testimoni pengguna yang tampil di halaman depan.
        </p>
      </div>

      <button
        v-if="activeTab === 'hero'"
        @click="openCreateModal('hero')"
        class="flex items-center justify-center gap-2 px-5 py-2.5 bg-[#F26430] hover:bg-[#D65020] text-white rounded-xl transition-all text-sm font-medium shadow-lg shadow-[#F26430]/30 hover:shadow-[#F26430]/50 cursor-pointer shrink-0"
      >
        <PlusIcon class="w-5 h-5" />
        Tambah Gambar
      </button>

      <button
        v-else
        @click="openCreateModal('testimoni')"
        class="flex items-center justify-center gap-2 px-5 py-2.5 bg-[#F26430] hover:bg-[#D65020] text-white rounded-xl transition-all text-sm font-medium shadow-lg shadow-[#F26430]/30 hover:shadow-[#F26430]/50 cursor-pointer shrink-0"
      >
        <PlusIcon class="w-5 h-5" />
        Tambah Testimoni
      </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <div class="p-4 sm:p-5 bg-[#00555A] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <button
            @click="activeTab = 'hero'"
            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer flex items-center gap-2"
            :class="[
              activeTab === 'hero'
                ? 'bg-[#F26430] text-white shadow-md shadow-[#F26430]/30'
                : 'bg-white/10 text-white/90 hover:bg-white/20 hover:text-white'
            ]"
          >
            <span>Hero Images</span>
            <span
              class="px-2 py-0.5 text-[11px] rounded-full font-bold"
              :class="activeTab === 'hero' ? 'bg-white text-[#F26430]' : 'bg-white/20 text-white'"
            >
              {{ heroList.length }}
            </span>
          </button>

          <button
            @click="activeTab = 'testimoni'"
            class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer flex items-center gap-2"
            :class="[
              activeTab === 'testimoni'
                ? 'bg-[#F26430] text-white shadow-md shadow-[#F26430]/30'
                : 'bg-white/10 text-white/90 hover:bg-white/20 hover:text-white'
            ]"
          >
            <span>Testimoni</span>
            <span
              class="px-2 py-0.5 text-[11px] rounded-full font-bold"
              :class="activeTab === 'testimoni' ? 'bg-white text-[#F26430]' : 'bg-white/20 text-white'"
            >
              {{ testimoniList.length }}
            </span>
          </button>
        </div>

        <div class="text-xs text-[#00555A] font-medium bg-white px-3.5 py-2 rounded-xl border border-gray-200 shadow-sm shrink-0 self-start sm:self-auto">
          Total Item: <span class="text-[#F26430] font-bold">{{ activeTab === 'hero' ? heroList.length : testimoniList.length }}</span>
        </div>
      </div>

      <div v-if="activeTab === 'hero'" class="p-4 sm:p-6">
        <div v-if="loadingHero" class="py-12 flex flex-col items-center justify-center text-[#F26430]">
          <svg class="animate-spin h-10 w-10 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span class="text-sm font-medium text-gray-500">Memuat gambar hero...</span>
        </div>

        <div v-else-if="heroList.length === 0" class="py-12 text-center flex flex-col items-center justify-center">
          <div class="bg-[#EBF2FA] p-4 rounded-full mb-3">
            <PhotoIcon class="w-10 h-10 text-[#00555A]/40" />
          </div>
          <h3 class="font-bold text-[#00555A] text-base">Belum Ada Gambar Hero</h3>
          <p class="text-gray-500 text-xs sm:text-sm mt-1 max-w-sm">
            Belum ada gambar hero yang ditambahkan. Silakan tambahkan gambar hero baru.
          </p>
          <button
            @click="openCreateModal('hero')"
            class="mt-4 px-4 py-2 bg-[#F26430] hover:bg-[#D65020] text-white text-xs font-semibold rounded-xl transition cursor-pointer"
          >
            + Tambah Gambar Hero
          </button>
        </div>

        <!-- Draggable Hero List -->
        <draggable
          v-else
          v-model="heroList"
          item-key="id"
          handle=".drag-handle"
          @end="onHeroDragEnd"
          tag="div"
          class="space-y-3"
        >
          <template #item="{ element, index }">
            <div class="bg-white p-3 sm:p-4 rounded-xl border border-gray-200 hover:border-teal-200 transition shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div class="flex items-center gap-3 min-w-0">
                <div class="drag-handle cursor-grab active:cursor-grabbing p-2 text-gray-400 hover:text-[#00555A] rounded-lg hover:bg-[#EBF2FA] transition shrink-0" title="Geser untuk mengubah urutan">
                  <Bars3Icon class="w-6 h-6" />
                </div>

                <div class="w-16 h-16 sm:w-20 sm:h-14 rounded-lg overflow-hidden bg-gray-100 border border-gray-200 shrink-0">
                  <img
                    :src="getImageUrl(element.gambar)"
                    alt="Hero Thumbnail"
                    class="w-full h-full object-cover"
                  />
                </div>

                <div class="min-w-0 flex-1">
                  <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs font-bold text-[#00555A]">Gambar Hero #{{ index + 1 }}</span>
                    <span
                      class="px-2 py-0.5 text-[11px] font-bold rounded-md border uppercase inline-flex items-center gap-1"
                      :class="element.status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-200'"
                    >
                      <span class="w-1.5 h-1.5 rounded-full" :class="element.status === 'aktif' ? 'bg-emerald-500' : 'bg-gray-400'"></span>
                      {{ element.status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </div>
                  <p class="text-xs text-gray-400 truncate mt-1">
                    Path: {{ element.gambar }}
                  </p>
                </div>
              </div>

              <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                <button
                  @click="handleToggle('hero', element)"
                  class="px-2.5 py-1.5 rounded-lg text-xs font-semibold border transition cursor-pointer flex items-center gap-1"
                  :class="[
                    element.status === 'aktif'
                      ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100'
                      : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'
                  ]"
                  :title="element.status === 'aktif' ? 'Nonaktifkan Gambar' : 'Aktifkan Gambar'"
                >
                  <PowerIcon class="w-4 h-4" />
                  <span>{{ element.status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                </button>

                <button
                  @click="openEditModal('hero', element)"
                  class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg border border-blue-200 transition cursor-pointer"
                  title="Edit Gambar"
                >
                  <PencilSquareIcon class="w-4 h-4" />
                </button>

                <button
                  @click="handleDelete('hero', element)"
                  class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg border border-red-200 transition cursor-pointer"
                  title="Hapus Gambar"
                >
                  <TrashIcon class="w-4 h-4" />
                </button>
              </div>
            </div>
          </template>
        </draggable>
      </div>

      <div v-else-if="activeTab === 'testimoni'" class="p-4 sm:p-6">
        <div v-if="loadingTestimoni" class="py-12 flex flex-col items-center justify-center text-[#F26430]">
          <svg class="animate-spin h-10 w-10 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span class="text-sm font-medium text-gray-500">Memuat testimoni...</span>
        </div>

        <div v-else-if="testimoniList.length === 0" class="py-12 text-center flex flex-col items-center justify-center">
          <div class="bg-[#EBF2FA] p-4 rounded-full mb-3">
            <ChatBubbleBottomCenterTextIcon class="w-10 h-10 text-[#00555A]/40" />
          </div>
          <h3 class="font-bold text-[#00555A] text-base">Belum Ada Testimoni</h3>
          <p class="text-gray-500 text-xs sm:text-sm mt-1 max-w-sm">
            Belum ada testimoni pengguna yang ditambahkan. Silakan tambahkan testimoni baru.
          </p>
          <button
            @click="openCreateModal('testimoni')"
            class="mt-4 px-4 py-2 bg-[#F26430] hover:bg-[#D65020] text-white text-xs font-semibold rounded-xl transition cursor-pointer"
          >
            + Tambah Testimoni
          </button>
        </div>

        <draggable
          v-else
          v-model="testimoniList"
          item-key="id"
          handle=".drag-handle"
          @end="onTestimoniDragEnd"
          tag="div"
          class="space-y-3"
        >
          <template #item="{ element }">
            <div class="bg-white p-3 sm:p-4 rounded-xl border border-gray-200 hover:border-teal-200 transition shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="drag-handle cursor-grab active:cursor-grabbing p-2 text-gray-400 hover:text-[#00555A] rounded-lg hover:bg-[#EBF2FA] transition shrink-0" title="Geser untuk mengubah urutan">
                  <Bars3Icon class="w-6 h-6" />
                </div>

                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full overflow-hidden bg-gray-100 border border-gray-200 shrink-0">
                  <img
                    :src="getTestimoniPhotoUrl(element)"
                    alt="Foto Testimoni"
                    class="w-full h-full object-cover"
                  />
                </div>

                <div class="min-w-0 flex-1">
                  <div class="flex items-center gap-2 flex-wrap">
                    <h4 class="font-bold text-[#00555A] text-sm sm:text-base truncate">{{ element.nama }}</h4>
                    <span v-if="element.jabatan" class="text-xs text-gray-500 font-medium">({{ element.jabatan }})</span>
                    <span
                      class="px-2 py-0.5 text-[11px] font-bold rounded-md border uppercase inline-flex items-center gap-1"
                      :class="element.status === 'aktif' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-200'"
                    >
                      <span class="w-1.5 h-1.5 rounded-full" :class="element.status === 'aktif' ? 'bg-emerald-500' : 'bg-gray-400'"></span>
                      {{ element.status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </div>
                  <p class="text-xs text-gray-600 italic line-clamp-2 mt-1">
                    "{{ element.kutipan }}"
                  </p>
                </div>
              </div>

              <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                <button
                  @click="handleToggle('testimoni', element)"
                  class="px-2.5 py-1.5 rounded-lg text-xs font-semibold border transition cursor-pointer flex items-center gap-1"
                  :class="[
                    element.status === 'aktif'
                      ? 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100'
                      : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100'
                  ]"
                  :title="element.status === 'aktif' ? 'Nonaktifkan Testimoni' : 'Aktifkan Testimoni'"
                >
                  <PowerIcon class="w-4 h-4" />
                  <span>{{ element.status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                </button>

                <button
                  @click="openEditModal('testimoni', element)"
                  class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg border border-blue-200 transition cursor-pointer"
                  title="Edit Testimoni"
                >
                  <PencilSquareIcon class="w-4 h-4" />
                </button>

                <button
                  @click="handleDelete('testimoni', element)"
                  class="p-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg border border-red-200 transition cursor-pointer"
                  title="Hapus Testimoni"
                >
                  <TrashIcon class="w-4 h-4" />
                </button>
              </div>
            </div>
          </template>
        </draggable>
      </div>
    </div>

    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div
          class="absolute inset-0 bg-[#00555A]/40 backdrop-blur-sm transition-opacity"
          @click="closeModal"
        ></div>

        <div class="bg-white shadow-2xl rounded-2xl w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col relative z-10 transform transition-all scale-100">
          <div class="sticky top-0 z-20 px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-[#EBF2FA]/90 backdrop-blur-md shrink-0">
            <h3 class="text-lg font-bold text-[#00555A] flex items-center gap-2">
              <span class="w-1.5 h-6 bg-[#F26430] rounded-full"></span>
              <span>
                {{ modalMode === 'create' ? 'Tambah' : 'Edit' }}
                {{ modalType === 'hero' ? 'Gambar Hero' : 'Testimoni' }}
              </span>
            </h3>
            <button
              @click="closeModal"
              class="text-gray-400 hover:text-red-500 transition rounded-full hover:bg-red-50 p-1 cursor-pointer"
            >
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>

          <div class="p-6 space-y-4 overflow-y-auto flex-1">
            <div v-if="formError" class="p-3 bg-red-50 border border-red-100 text-red-600 text-sm rounded-xl flex items-start gap-2">
              <ExclamationTriangleIcon class="w-5 h-5 shrink-0 mt-0.5" />
              <span>{{ formError }}</span>
            </div>

            <template v-if="modalType === 'hero'">
              <div class="space-y-2">
                <label class="text-xs font-bold text-[#00555A]/80 uppercase tracking-wide">
                  File Gambar Hero
                  <span v-if="modalMode === 'create'" class="text-red-500">*</span>
                  <span v-else class="text-gray-400 font-normal capitalize">(Opsional jika tidak diganti)</span>
                </label>

                <input
                  type="file"
                  accept="image/*"
                  @change="onFileChange"
                  class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#00555A] file:text-white hover:file:bg-[#004044] file:cursor-pointer bg-[#EBF2FA]/30 rounded-xl border border-gray-200 p-1 transition"
                />

                <div v-if="previewUrl" class="mt-3 space-y-1">
                  <p class="text-xs font-semibold text-[#00555A]">Preview Gambar:</p>
                  <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-50 max-h-48 flex items-center justify-center">
                    <img :src="previewUrl" alt="Preview" class="max-h-48 object-contain" />
                  </div>
                </div>
              </div>
            </template>

            <template v-else-if="modalType === 'testimoni'">
              <div class="space-y-1.5">
                <label class="text-xs font-bold text-[#00555A]/80 uppercase tracking-wide">
                  Nama <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  v-model="formData.nama"
                  placeholder="Contoh: Ahmad Fauzi"
                  class="w-full px-4 py-2.5 bg-[#EBF2FA]/30 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm outline-none transition text-[#00555A]"
                />
              </div>

              <div class="space-y-1.5">
                <label class="text-xs font-bold text-[#00555A]/80 uppercase tracking-wide">
                  Jabatan <span class="text-gray-400 font-normal capitalize">(Opsional)</span>
                </label>
                <input
                  type="text"
                  v-model="formData.jabatan"
                  placeholder="Contoh: Mahasiswa Teknik Informatika / Tamu Umum"
                  class="w-full px-4 py-2.5 bg-[#EBF2FA]/30 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm outline-none transition text-[#00555A]"
                />
              </div>

              <div class="space-y-1.5">
                <label class="text-xs font-bold text-[#00555A]/80 uppercase tracking-wide">
                  Kutipan Testimoni <span class="text-red-500">*</span>
                </label>
                <textarea
                  v-model="formData.kutipan"
                  rows="3"
                  placeholder="Tuliskan pengalaman atau saran mengenai layanan perpustakaan..."
                  class="w-full px-4 py-2.5 bg-[#EBF2FA]/30 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#F26430] focus:border-[#F26430] text-sm outline-none transition text-[#00555A] resize-y"
                ></textarea>
              </div>

              <div class="space-y-2">
                <label class="text-xs font-bold text-[#00555A]/80 uppercase tracking-wide">
                  Foto Profile <span class="text-gray-400 font-normal capitalize">(Opsional)</span>
                </label>
                <input
                  type="file"
                  accept="image/*"
                  @change="onFileChange"
                  class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#00555A] file:text-white hover:file:bg-[#004044] file:cursor-pointer bg-[#EBF2FA]/30 rounded-xl border border-gray-200 p-1 transition"
                />

                <div v-if="previewUrl" class="mt-3 flex items-center gap-3">
                  <p class="text-xs font-semibold text-[#00555A]">Preview Foto:</p>
                  <div class="w-14 h-14 rounded-full overflow-hidden border border-gray-200 bg-gray-50">
                    <img :src="previewUrl" alt="Preview Foto" class="w-full h-full object-cover" />
                  </div>
                </div>
              </div>
            </template>
          </div>

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
              @click="handleSubmit"
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
import { ref, onMounted } from 'vue';
import draggable from 'vuedraggable';
import api from '@/api';
import {
  Bars3Icon,
  PlusIcon,
  PencilSquareIcon,
  TrashIcon,
  PowerIcon,
  XMarkIcon,
  ExclamationTriangleIcon,
  PhotoIcon,
  ChatBubbleBottomCenterTextIcon,
} from '@heroicons/vue/24/outline';

const activeTab = ref('hero');

const heroList = ref([]);
const testimoniList = ref([]);

const loadingHero = ref(false);
const loadingTestimoni = ref(false);

const showModal = ref(false);
const modalMode = ref('create'); 
const modalType = ref('hero'); 
const editingItem = ref(null);

const formError = ref('');
const isSubmitting = ref(false);

const selectedFile = ref(null);
const previewUrl = ref('');
const formData = ref({
  nama: '',
  jabatan: '',
  kutipan: '',
});

const getImageUrl = (path) => {
  if (!path) return '';
  if (path.startsWith('http://') || path.startsWith('https://')) return path;
  const storageUrl = import.meta.env.VITE_STORAGE_URL || 'https://tamuq.my.id/storage/';
  const baseUrl = storageUrl.endsWith('/') ? storageUrl : storageUrl + '/';
  const cleanPath = path.startsWith('/') ? path.slice(1) : path;
  return baseUrl + cleanPath;
};

const getTestimoniPhotoUrl = (item) => {
  if (item.foto) {
    return getImageUrl(item.foto);
  }
  const name = encodeURIComponent(item.nama || 'User');
  return `https://ui-avatars.com/api/?name=${name}&background=00555A&color=fff`;
};

const fetchHero = async () => {
  loadingHero.value = true;
  try {
    const res = await api.get('/staff/landing/hero');
    if (res.data && res.data.success) {
      heroList.value = res.data.data || [];
    }
  } catch (err) {
    console.error('Gagal mengambil data hero:', err);
  } finally {
    loadingHero.value = false;
  }
};

const fetchTestimoni = async () => {
  loadingTestimoni.value = true;
  try {
    const res = await api.get('/staff/landing/testimoni');
    if (res.data && res.data.success) {
      testimoniList.value = res.data.data || [];
    }
  } catch (err) {
    console.error('Gagal mengambil data testimoni:', err);
  } finally {
    loadingTestimoni.value = false;
  }
};

const fetchAllData = async () => {
  loadingHero.value = true;
  loadingTestimoni.value = true;
  try {
    const [resHero, resTestimoni] = await Promise.all([
      api.get('/staff/landing/hero'),
      api.get('/staff/landing/testimoni'),
    ]);
    if (resHero.data && resHero.data.success) {
      heroList.value = resHero.data.data || [];
    }
    if (resTestimoni.data && resTestimoni.data.success) {
      testimoniList.value = resTestimoni.data.data || [];
    }
  } catch (err) {
    console.error('Gagal mengambil data landing page:', err);
  } finally {
    loadingHero.value = false;
    loadingTestimoni.value = false;
  }
};

onMounted(() => {
  fetchAllData();
});

const onHeroDragEnd = async () => {
  try {
    const ids = heroList.value.map(item => item.id);
    await api.post('/staff/landing/hero/reorder', { ids });
  } catch (err) {
    console.error('Gagal memperbarui urutan hero:', err);
  }
};

const onTestimoniDragEnd = async () => {
  try {
    const ids = testimoniList.value.map(item => item.id);
    await api.post('/staff/landing/testimoni/reorder', { ids });
  } catch (err) {
    console.error('Gagal memperbarui urutan testimoni:', err);
  }
};

const onFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    selectedFile.value = file;
    if (previewUrl.value && previewUrl.value.startsWith('blob:')) {
      URL.revokeObjectURL(previewUrl.value);
    }
    previewUrl.value = URL.createObjectURL(file);
  }
};

const openCreateModal = (type) => {
  modalType.value = type;
  modalMode.value = 'create';
  editingItem.value = null;
  selectedFile.value = null;
  if (previewUrl.value && previewUrl.value.startsWith('blob:')) {
    URL.revokeObjectURL(previewUrl.value);
  }
  previewUrl.value = '';
  formData.value = {
    nama: '',
    jabatan: '',
    kutipan: '',
  };
  formError.value = '';
  showModal.value = true;
};

const openEditModal = (type, item) => {
  modalType.value = type;
  modalMode.value = 'edit';
  editingItem.value = item;
  selectedFile.value = null;

  if (previewUrl.value && previewUrl.value.startsWith('blob:')) {
    URL.revokeObjectURL(previewUrl.value);
  }

  if (type === 'hero') {
    previewUrl.value = getImageUrl(item.gambar);
  } else {
    previewUrl.value = item.foto ? getImageUrl(item.foto) : '';
    formData.value = {
      nama: item.nama || '',
      jabatan: item.jabatan || '',
      kutipan: item.kutipan || '',
    };
  }

  formError.value = '';
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  if (previewUrl.value && previewUrl.value.startsWith('blob:')) {
    URL.revokeObjectURL(previewUrl.value);
  }
  previewUrl.value = '';
  selectedFile.value = null;
  editingItem.value = null;
  formError.value = '';
};

const handleSubmit = async () => {
  formError.value = '';

  if (modalType.value === 'hero') {
    if (modalMode.value === 'create' && !selectedFile.value) {
      formError.value = 'File gambar wajib dipilih.';
      return;
    }
  } else if (modalType.value === 'testimoni') {
    if (!formData.value.nama.trim()) {
      formError.value = 'Nama wajib diisi.';
      return;
    }
    if (!formData.value.kutipan.trim()) {
      formError.value = 'Kutipan testimoni wajib diisi.';
      return;
    }
  }

  isSubmitting.value = true;

  try {
    const payload = new FormData();

    if (modalType.value === 'hero') {
      if (selectedFile.value) {
        payload.append('gambar', selectedFile.value);
      }
    } else {
      payload.append('nama', formData.value.nama.trim());
      if (formData.value.jabatan.trim()) {
        payload.append('jabatan', formData.value.jabatan.trim());
      }
      payload.append('kutipan', formData.value.kutipan.trim());
      if (selectedFile.value) {
        payload.append('foto', selectedFile.value);
      }
    }

    let url = '';
    if (modalType.value === 'hero') {
      url = modalMode.value === 'create'
        ? '/staff/landing/hero'
        : `/staff/landing/hero/${editingItem.value.id}`;
    } else {
      url = modalMode.value === 'create'
        ? '/staff/landing/testimoni'
        : `/staff/landing/testimoni/${editingItem.value.id}`;
    }

    await api.post(url, payload, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    closeModal();

    if (modalType.value === 'hero') {
      await fetchHero();
    } else {
      await fetchTestimoni();
    }
  } catch (err) {
    console.error('Gagal menyimpan data:', err);
    if (err.response && err.response.data && err.response.data.message) {
      formError.value = err.response.data.message;
    } else {
      formError.value = 'Terjadi kesalahan saat menyimpan data.';
    }
  } finally {
    isSubmitting.value = false;
  }
};

const handleDelete = async (type, item) => {
  const itemLabel = type === 'hero' ? 'gambar hero ini' : `testimoni dari "${item.nama}"`;
  const confirmed = window.confirm(`Yakin ingin menghapus ${itemLabel}? Tindakan ini permanen.`);

  if (!confirmed) return;

  try {
    const url = type === 'hero'
      ? `/staff/landing/hero/${item.id}`
      : `/staff/landing/testimoni/${item.id}`;

    await api.delete(url);

    if (type === 'hero') {
      await fetchHero();
    } else {
      await fetchTestimoni();
    }
  } catch (err) {
    console.error('Gagal menghapus item:', err);
  }
};

const handleToggle = async (type, item) => {
  try {
    const url = type === 'hero'
      ? `/staff/landing/hero/${item.id}/toggle`
      : `/staff/landing/testimoni/${item.id}/toggle`;

    await api.post(url);

    if (type === 'hero') {
      await fetchHero();
    } else {
      await fetchTestimoni();
    }
  } catch (err) {
    console.error('Gagal mengubah status:', err);
  }
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
