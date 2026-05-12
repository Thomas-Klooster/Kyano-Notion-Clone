import api from '@/api/api';



export const getUsers = async () => {
     const { data } = await api.get('users')
     return data;
}

export const getAdminUsers = async () => {
     const { data } = await api.get('admin/users')
     return data;
}

export const getUser = async (userId) => {
     const { data } = await api.get(`admin/users/${userId}`)
     return data;
}

export const postUser = async (payload) => {
     const { data } = await api.post('admin/users', payload)
     return data;
}

export const updateUser = async (userId, payload) => {
     const { data } = await api.put(`admin/users/${userId}`, payload)
     return data;
}

export const deleteUser = async (userId) => {
     const { data } = await api.delete(`admin/users/${userId}`)
     return data;
}
