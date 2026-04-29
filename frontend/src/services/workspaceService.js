import api from "@/api/api";

export const getWorkspaces = async () => {
     const { data } = await api.get('/workspaces')
     return data;
};

