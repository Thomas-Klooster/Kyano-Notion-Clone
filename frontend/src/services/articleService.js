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

export const postFeedback = async (articleSlug, payload) => {
    const { data } = await api.post(`articles/${articleSlug}/feedback`, payload)
    return data;
}

export const getArticleFeedbacks = async (articleSlug) => {
    const { data } = await api.get(`admin/articles/${articleSlug}/feedbacks`);
    return data;
}

export const getFeedbacks = async () => {
    const { data } = await api.get('admin/feedbacks')
    return data;
}

export const markFeedbackAsRead = async (feedbackId, isRead = true) => {
    const { data } = await api.patch(`admin/feedbacks/${feedbackId}/read`, {
        is_read: isRead,
    })
    return data
}

export const deleteFeedback = async (feedbackId) => {
    const { data } = await api.delete(`admin/feedbacks/${feedbackId}`)
    return data
}

export const updateArticle = async (articleId, payload) => {
    const { data } = await api.put(`admin/articles/${articleId}`, payload)
    return data;
};

export const deleteArticle = async (articleId) => {
    const { data } = await api.delete(`admin/articles/${articleId}`)
    return data;
}