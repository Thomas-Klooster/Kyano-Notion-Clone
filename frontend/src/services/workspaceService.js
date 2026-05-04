import api from "@/api/api";

export const getWorkspaces = async () => {
     const { data } = await api.get('workspaces')
     return data;
};

export const createWorkspace = async () => {
     const  { data } = await api.post('workspaces')
     return data;
}

export const addMember = async () => {
     const { data } = await api.post('/workspaces/{workspace}/members')
     return data;
}

export const removeMember = async () => {
     const { data } = await api.post('/workspaces/{workspace}/members/{user}')
     return data;
}