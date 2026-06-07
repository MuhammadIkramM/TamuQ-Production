import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api'
import router from '@/router'

const SAFE_STORAGE_FIELDS = ['id_user', 'role', 'nama_lengkap', 'foto_profil']

function getStoredJSON(key) {
  try {
    const raw = localStorage.getItem(key)
    return raw ? JSON.parse(raw) : null
  } catch {
    localStorage.removeItem(key)
    return null
  }
}

function pickSafeFields(userData) {
  if (!userData) return null
  const safe = {}
  for (const key of SAFE_STORAGE_FIELDS) {
    if (userData[key] !== undefined) {
      safe[key] = userData[key]
    }
  }
  return safe
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref(getStoredJSON('auth_user'))
  const token = ref(localStorage.getItem('auth_token') || null)
  const isInitialized = ref(false)

  const isLoggedIn = computed(() => !!user.value)

  const userRole = computed(() => {
    if (!user.value) return 'pengunjung'
    return user.value.role ? user.value.role.toLowerCase() : 'pengunjung'
  })

  const isStaffOrAdmin = computed(() => {
    return ['staff', 'superadmin', 'super_admin', 'admin'].includes(userRole.value)
  })

  const setUser = (userData) => {
    user.value = userData
    if (userData) {
      localStorage.setItem('auth_user', JSON.stringify(pickSafeFields(userData)))
    } else {
      localStorage.removeItem('auth_user')
    }
  }

  const setToken = (tokenValue) => {
    token.value = tokenValue
    if (tokenValue) {
      localStorage.setItem('auth_token', tokenValue)
    } else {
      localStorage.removeItem('auth_token')
    }
  }

  const clearAuth = () => {
    user.value = null
    token.value = null
    localStorage.removeItem('auth_user')
    localStorage.removeItem('auth_token')
  }

  const initializeAuth = async () => {
    if (isInitialized.value) return user.value

    if (user.value) {
      try {
        const response = await api.get('/user')
        const data = response.data.data || response.data
        setUser(data)
      } catch (error) {
        console.warn('Session validation failed, clearing auth:', error.message)
        clearAuth()
      }
    }

    isInitialized.value = true
    return user.value
  }

  const fetchUser = async () => {
    try {
      const response = await api.get('/user')
      const data = response.data.data || response.data
      setUser(data)
      return data
    } catch (error) {
      console.error('Gagal fetch user:', error)
      if (error.response?.status === 401) {
        clearAuth()
        router.push('/login')
      }
      throw error
    }
  }

  const logout = async () => {
    try {
      await api.post('/logout')
    } catch (e) {
      console.error('Logout API error:', e)
    } finally {
      clearAuth()
      router.push('/login')
    }
  }

  return {
    user,
    token,
    isLoggedIn,
    isInitialized,
    userRole,
    isStaffOrAdmin,
    setUser,
    setToken,
    clearAuth,
    initializeAuth,
    fetchUser,
    logout
  }
})
