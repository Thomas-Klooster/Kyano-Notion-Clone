import api from '@/api/api';


export const getCategories = async () => {
     const { data } = await api.get('categories')
     return data;
};

export const getCategory = async (slug) => {
     const { data } = await api.get(`categories/${slug}`)
     return data;
};

export const storeCategory = async (formData) => {
     const { data } = await api.post('category', formData, {
          headers: {
          'Content-Type': 'multipart/form-data'
          }
     });
     return data;
};

export const UpdateCategory = async (formData) => {
     const { data } = await api.put('categories/{category}', formData, {
          headers: {
          'Content-Type': 'multipart/form-data'
          }
     });
     return data;
};

export const DeleteCategory = async () => {
     const { data } = await api.delete('categories/{category}')
     return data;
}