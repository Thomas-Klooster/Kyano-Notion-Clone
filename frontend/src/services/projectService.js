import api from "@/api/api";

export const getProjects = async () => {
 const { data } = await api.get('projects')
 return data;
}

export const getProject = async (slug) => {
 const { data } = await api.get(`projects/${slug}`)
 return data;
};

export const storeProject = async (formData) => {
   const { data } = await api.post('projects', formData, {
     headers: {
     'Content-Type': 'multipart/form-data'
     }
   });
   return data;
};

export const updateProject = async (formData) => {
     const { data } = await api.put('projects/{project}', formData, {
          headers: {
          'Content-Type': 'multipart/form-data'
          }
     });
     return data;
};

export const deleteProject = async () => {
     const { data } = await api.delete('projects/{project}')
     return data;
};
