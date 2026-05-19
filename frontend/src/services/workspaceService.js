import api from '@/api/api';

export const getWorkspaces = async () => {
     const { data } = await api.get('workspaces')
     return data;
};

export const getAdminWorkspaces = async () => {
     const { data } = await api.get('admin/workspaces')
     return data;
};

export const getWorkspace = async (slug) => {
     const { data } = await api.get(`workspaces/${slug}`)
     return data;
};

export const postWorkspace = async (payload) => {
     const  { data } = await api.post('workspaces', payload)
     return data;
};


export const updateWorkspace = async (slug, payload) => {
     const { data } = await api.put(`admin/workspaces/${slug}`, payload)
     return data;
};

export const deleteWorkspace = async (workspaceId) => {
     const { data } = await api.delete(`workspaces/${workspaceId}`)
     return data;
};

export const addMember = async (workspaceId) => {
     const { data } = await api.post(`/workspaces/${workspaceId}/members`)
     return data;
}

export const removeMember = async (workspaceId, userId) => {
     const { data } = await api.post(`/workspaces/${workspaceId}/members/${userId}`)
     return data;
}
