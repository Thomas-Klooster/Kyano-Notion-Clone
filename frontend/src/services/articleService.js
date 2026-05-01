import api from "@/api/api";

export const getArticles = async () => {
     const { data } = await api.get('articles')
     return data;
}