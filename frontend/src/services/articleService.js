import api from "@/api/api";

export const getArticles = async () => {
     const { data } = await api.get('articles')
     return data;
}


export const createArticles = async () => {
     const { data } = await api.post('articles')
     return data;
}