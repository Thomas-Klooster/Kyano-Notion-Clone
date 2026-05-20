import api from "@/api/api";

export const getProjects = async () => {
 const { data } = await api.get('projects')
 return data;
}

export const getProject = async (slug) => {
 const { data } = await api.get(`projects/${slug}`)
 return data;
};

export const storeProject = async (payload) => {
   const { data } = await api.post('projects', payload)
   return data;
};

export const updateProject = async (slug, payload) => {
     const { data } = await api.put(`admin/projects/${slug}`, payload)
     return data;
};

export const deleteProject = async (slug) => {
     const { data } = await api.delete(`admin/projects/${slug}`)
     return data;
};
