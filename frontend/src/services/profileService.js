import api from '@/api/api'

export const getProfile = async () => {
  const { data } = await api.get('/me')
  return data
}

export const updateProfile = async (payload) => {
  const { data } = await api.patch('/me', payload)
  return data
}

export const updatePassword = async (payload) => {
  const { data } = await api.post('/change-password', payload)
  return data
}
