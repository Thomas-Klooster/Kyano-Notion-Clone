import api from '@/api/api';


export const getCategories = async () => {
     const { data } = await api.get('categories')
     return data;
};

export const getCategory = async (slug) => {
     const { data } = await api.get(`categories/${slug}`)
     return data;
};

