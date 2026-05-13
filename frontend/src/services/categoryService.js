import api from '@/api/api';


export const getCategories = async () => {
     const { data } = await api.get('categories')
     return data;
};

export const getCategory = async (slug) => {
     const { data } = await api.get(`categories/${slug}`)
     return data;
};

export const storeCategory = async (payload) => {
     const { data } = await api.post('category', payload)
     return data;
};

export const UpdateCategory = async (categoryId, payload) => {
     const { data } = await api.put(`categories/${categoryId}`, payload)
     return data;
};

export const DeleteCategory = async (categoryId) => {
     const { data } = await api.delete(`categories/${categoryId}`)
     return data;
}