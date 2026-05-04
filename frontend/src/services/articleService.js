import api from "@/api/api";

export const getArticles = async () => {
     const { data } = await api.get('articles')
     return data;
}

export const createArticles = async (formData) => {
    const { data } = await api.post('articles', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
    return data;
}