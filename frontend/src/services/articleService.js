import api from "@/api/api";

const unwrapResource = (payload) => payload?.data ?? payload

export const getArticles = async () => {
     const { data } = await api.get('articles')
     return data;
}

export const getArticle = async (slug) => {
    const { data } = await api.get(`articles/${slug}`)
    return unwrapResource(data);
}

export const postArticle = async (payload) => {
    const { data } = await api.post('articles', payload)
    return unwrapResource(data);
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

export const uploadArticleAttachments = async (articleSlug, files, options = {}) => {
    const formData = new FormData();
    files.forEach((file) => {
        formData.append('attachments[]', file);
    });

    const { data } = await api.post(
    `admin/articles/${articleSlug}/attachments`,
    formData,
    {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
        onUploadProgress: options.onUploadProgress,
    }
  );

  return unwrapResource(data);
};

export const deleteAttachment = async (articleSlug, attachmentId) => {
    const { data } = await api.delete(`admin/articles/${articleSlug}/attachments/${attachmentId}`)
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

export const updateArticle = async (slug, payload) => {
    const { data } = await api.put(`admin/articles/${slug}`, payload)
    return unwrapResource(data);
};

export const deleteArticle = async (articleId) => {
    const { data } = await api.delete(`admin/articles/${articleId}`)
    return data;
}
