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
     const { data } = await api.post('categories', payload)
     return data;
};

export const UpdateCategory = async (slug, payload) => {
  const { data } = await api.put(`admin/categories/${slug}`, payload)
  return data;
};
export const DeleteCategory = async (slug) => {
     const { data } = await api.delete(`categories/${slug}`)
     return data;
}