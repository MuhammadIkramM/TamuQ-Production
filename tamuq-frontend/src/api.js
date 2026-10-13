import axios from 'axios';
import router from './router';
import { useAuthStore } from '@/stores/auth';
import { useProgressStore } from '@/stores/progress';

const api = axios.create({
  baseURL: `${import.meta.env.VITE_API_BASE_URL || 'https://tamuq.my.id'}/api`,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json' 
  },
  withCredentials: true
});

api.interceptors.request.use((config) => {
  const progressStore = useProgressStore();

  if (config.data instanceof FormData) {
    const opId = progressStore.addOperation('upload');
    config._progressOpId = opId;

    if (!config.onUploadProgress) {
      config.onUploadProgress = (progressEvent) => {
        if (progressEvent.lengthComputable) {
          const percent = Math.round((progressEvent.loaded * 100) / progressEvent.total);
          progressStore.updateOperation(opId, percent);
        }
      };
    }
  }

  if (config.responseType === 'blob') {
    const actionType = config.actionType || 'download';
    const type = actionType === 'preview' ? 'preview' : 'download';
    const opId = progressStore.addOperation(type);
    config._progressOpId = opId;

    if (!config.onDownloadProgress) {
      config.onDownloadProgress = (progressEvent) => {
        if (progressEvent.lengthComputable) {
          const percent = Math.round((progressEvent.loaded * 100) / progressEvent.total);
          progressStore.updateOperation(opId, percent);
        }
      };
    }
  }

  return config;
});

api.interceptors.response.use(
  (response) => {
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

    const progressStore = useProgressStore();
    const opId = response.config?._progressOpId;
    if (opId) {
      progressStore.completeOperation(opId, 'success');
    }

    return response;
  },
  (error) => {
    const progressStore = useProgressStore();
    const opId = error.config?._progressOpId;
    if (opId) {
      progressStore.completeOperation(opId, 'error');
    }

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
    }

    return Promise.reject(error);
  }
);

export default api;