import axios from 'axios';
import router from './router';
import { useAuthStore } from '@/stores/auth';

const api = axios.create({
  baseURL: 'https://tamuq.my.id/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json' 
  },
  withCredentials: true
});
api.interceptors.response.use(
  (response) => {
    // Detect when backend returns HTML instead of JSON (e.g., catch-all route)
    const contentType = response.headers['content-type'] || '';
    if (contentType.includes('text/html') && !response.config?.responseType) {
      console.error('API returned HTML instead of JSON. URL:', response.config?.url);
      return Promise.reject({
        response: {
          status: 502,
          data: { message: 'Server mengembalikan respons yang tidak valid. Pastikan API backend berjalan dengan benar.' }
        }
      });
    }
    return response;
  },
  (error) => {
    const auth = useAuthStore();

    if (error.response && error.response.status === 401) {
      if (router.currentRoute.value.path !== '/login') {
        auth.clearAuth();
        if (auth.isInitialized) {
          router.push('/login');
        }
      }
    }

    if (error.response && error.response.status === 403) {
      console.warn("Akses API ditolak oleh server.");

      const user = auth.user;
      let isStaff = false;
      if (user) {
        const r = user.role ? user.role.toLowerCase() : "";
        isStaff = ["staff", "superadmin", "admin"].includes(r);
      }

      if (isStaff) {
        router.push('/staff/dashboard');
      } else {
        router.push('/user/dashboard');
      }
    }

    return Promise.reject(error);
  }
);

export default api;