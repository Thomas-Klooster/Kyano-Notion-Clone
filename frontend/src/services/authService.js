import api from "@/api/api";


export const login = async (email, password) => {
     const { data } = await api.post('/login', {
          email, password
     });
     localStorage.setItem('accessToken',
          data.accessToken
     );
     localStorage.setItem('refreshToken',
          data.refreshToken
     );
     return data.user
};

export const register = async (name, email, password, password_confirmation) => {
     const { data } = await api.post('/register', {
          name, email, password, password_confirmation
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
     await api.post('/logout');
     localStorage.clear();
     windows.location.href = '/login';
};