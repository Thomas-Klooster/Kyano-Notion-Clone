import api from "@/api/api";


export const login = async (email, password) => {
     const { data } = await api.post('/auth/login', {
          email, password
     });
     localStorage.setItem('accessToken',
          data.accessToken
     );
     localStorage.setItem('refreshToken',
          data.refreshToken
     );
     return data.user;
};

export const register = async (name, email, password) => {
     const { data } = await api.post('/auth/register', {
          name, email, password
     });
     localStorage.setItem('accessToken',
          data.accessToken
     );
     localStorage.setItem('refreshToken',
          data.refreshToken
     );
     return data.user
};


export const logout = async () => {
     await api.post('/auth/logout');
     localStorage.clear();
     windows.location.href = '/login';
};