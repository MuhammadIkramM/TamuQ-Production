<template>
  <LoginForm 
    @submit-login="handleLogin" 
    :is-loading="isLoading"
    :error-message="errorMessage"
  />
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/api'; 
import LoginForm from "@/components/Auth/LoginForm.vue";
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const auth = useAuthStore();
const isLoading = ref(false);
const errorMessage = ref('');

const handleLogin = async (formData) => {
  isLoading.value = true;
  errorMessage.value = '';

  try {
    await api.get('https://tamuq.my.id/sanctum/csrf-cookie');

    const response = await api.post('/login', formData);

    if (response.data.success) {

      const userData = response.data.data.user ? response.data.data.user : response.data.data;
      const userRole = userData.role; 

      auth.setUser(userData);

      if (userRole === 'staff' || userRole === 'superadmin') {
        router.push('/staff/dashboard'); 
      } else {
        router.push('/user/dashboard'); 
      }
    }

  } catch (error) {
    console.error('Login Error:', error);
    
    if (error.response && error.response.status === 422) {
       errorMessage.value = "Kredensial tidak valid. Harap periksa Nomor Induk dan Password Anda.";
    } 
    else if (error.response) {
      errorMessage.value = error.response.data.message || 'Login gagal. Periksa kredensial Anda.';
    } else {
      errorMessage.value = 'Terjadi kesalahan sistem/jaringan.';
    }
  } finally {
    isLoading.value = false;
  }
};
</script>