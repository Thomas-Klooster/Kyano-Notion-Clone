import api from "@/api/api";

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

export const postWorkspace = async (formData) => {
     const  { data } = await api.post('workspaces', formData, {
          headers: {
          'Content-Type': 'multipart/form-data'
          }
     });
     return data;
};


export const updateWorkspace = async (formData) => {
     const { data } = await api.put('workspaces/{workspace}', formData, {
          headers: {
               'Content-Type': 'multipart/form-data'
          }
     })
     return data;
};


export const deleteWorkspace = async () => {
     const { data } = await api.delete('workspaces/{workspace}')
     return data;
};

export const addMember = async () => {
     const { data } = await api.post('/workspaces/{workspace}/members')
     return data;
}

export const removeMember = async () => {
     const { data } = await api.post('/workspaces/{workspace}/members/{user}')
     return data;
}
