import { defineStore } from 'pinia';

export const useProgressStore = defineStore('progress', {
  state: () => ({
    activeOperations: [],
    _idCounter: 0,
  }),
  getters: {
    hasActiveUpload: (state) => state.activeOperations.some(op => op.type === 'upload' && op.status === 'active'),
    hasActivePreview: (state) => state.activeOperations.some(op => op.type === 'preview' && op.status === 'active'),
    stackOperations: (state) => state.activeOperations.filter(op => op.type !== 'preview'),
    activeUploadProgress: (state) => {
      const op = state.activeOperations.find(op => op.type === 'upload' && op.status === 'active');
      return op ? op.progress : 0;
    },
    activePreviewProgress: (state) => {
      const op = state.activeOperations.find(op => op.type === 'preview' && op.status === 'active');
      return op ? op.progress : 0;
    },
  },
  actions: {
  
    addOperation(type, label = '') {
      const id = `op_${Date.now()}_${++this._idCounter}`;
      this.activeOperations.unshift({
        id,
        type,
        label: label || this._getDefaultLabel(type),
        progress: 0,
        status: 'active', // 'active' | 'success' | 'error'
      });
      return id;
    },

    updateOperation(id, progress) {
      const op = this.activeOperations.find(op => op.id === id);
      if (op) {
        op.progress = progress;
      }
    },

    completeOperation(id, status = 'success') {
      const op = this.activeOperations.find(op => op.id === id);
      if (op) {
        op.status = status;
        op.progress = status === 'success' ? 100 : op.progress;
        const delay = status === 'success' ? 5000 : 10000;
        setTimeout(() => {
          this.removeOperation(id);
        }, delay);
      }
    },

    removeOperation(id) {
      const index = this.activeOperations.findIndex(op => op.id === id);
      if (index !== -1) {
        this.activeOperations.splice(index, 1);
      }
    },

    stopAllByType(type) {
      if (type) {
        this.activeOperations = this.activeOperations.filter(op => op.type !== type);
      } else {
        this.activeOperations = [];
      }
    },

    _getDefaultLabel(type) {
      switch (type) {
        case 'upload': return 'Mengunggah...';
        case 'download': return 'Mengunduh...';
        case 'preview': return 'Memuat pratinjau...';
        default: return 'Memproses...';
      }
    },
  },
});
