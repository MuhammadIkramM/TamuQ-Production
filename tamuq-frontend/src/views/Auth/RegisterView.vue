<template>
  <RegisterForm 
    @submit-register="handleRegister" 
    :is-loading="isLoading"
    :error-message="errorMessage"
  />
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api';
import RegisterForm from "@/components/Auth/RegisterForm.vue";
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const auth = useAuthStore();
const isLoading = ref(false);
const errorMessage = ref('');

const handleRegister = async (formData) => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    await api.get('https://tamuq.my.id/sanctum/csrf-cookie');
    const response = await api.post('/register', formData);

    if (response.data.success) {
      const userData = response.data.data.user ? response.data.data.user : response.data.data;
      auth.setUser(userData);

      alert('Registrasi Berhasil! Anda akan diarahkan ke Dashboard.');
      router.push('/user/dashboard');
    } else {
      errorMessage.value = response.data.message || 'Registrasi gagal. Silakan coba lagi.';
    }

  } catch (error) {
    console.error('Register Error:', error);
    
    if (error.response) {
      if (error.response.status === 422 && error.response.data.errors) {
        errorMessage.value = Object.values(error.response.data.errors).flat().join(' | ');
      } else {
        errorMessage.value = error.response.data.message || 'Gagal mendaftar. Silakan coba lagi.';
      }
    } else if (error.request) {
      errorMessage.value = 'Tidak dapat terhubung ke server backend. Cek koneksi Anda.';
    } else {
      errorMessage.value = 'Terjadi kesalahan sistem.';
    }
  } finally {
    isLoading.value = false;
  }
};
</script>