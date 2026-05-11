import api from "@/api/api";

export const getArticles = async () => {
     const { data } = await api.get('articles')
     return data;
}

export const getArticle = async (slug) => {
    const { data } = await api.get(`articles/${slug}`)
    return data;
}

export const postArticle = async (formData) => {
    const { data } = await api.post('articles', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
    return data;
};

export const updateArticle = async (formData) => {
    const { data } = await api.put('articles', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
    return data;
};

export const deleteArticle = async () => {
    const { data } = await api.delete('articles/{article}')
    return data;
}