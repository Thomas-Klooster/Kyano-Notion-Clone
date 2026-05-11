import api from '@/api/api';



export const getUsers = async () => {
     const { data } = await api.get('users')
     return data;
}

export const getAdminUsers = async () => {
     const { data } = await api.get('admin/users')
     return data;
}

export const getUser = async () => {
     const { data } = await api.get('users/{user}')
     return data
}

export const postUser = async (formData) => {
     const { data } = await api.post('users', formData, {
          headers: {
          'Content-Type': 'multipart/form-data'
          }
     })
     return data;
} 

export const updateUser = async (formData) => {
     const { data } = await api.put('users', formData, {
          headers: {
               'Content-Type': 'multipart/form-data' 
          }
     })
     return data;
}

export const deleteUser = async () => {
     const { data } = await api.delete('users/{user}')
     return data;
}
