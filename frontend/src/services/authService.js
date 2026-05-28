import api, { ensureCsrfCookie } from '@/api/api';


export const login = async (email, password) => {
     await ensureCsrfCookie();
     const { data } = await api.post('/login', {
          email, password
     });

     localStorage.setItem('accessToken', data.accessToken);
     localStorage.setItem('refreshToken', data.refreshToken);
     return data.user
};

export const register = async (name, email, address, phone_number, company, password, password_confirmation) => {
     await ensureCsrfCookie();
     const { data } = await api.post('/register', {
          name, email, address, phone_number, company, password, password_confirmation
     });
     localStorage.setItem('accessToken', data.accessToken);
     localStorage.setItem('refreshToken', data.refreshToken);
     return data.user
};


export const logout = async () => {
     await ensureCsrfCookie();
     await api.post('/logout');
     localStorage.removeItem('accessToken');
     localStorage.removeItem('refreshToken');
     window.location.href = '/auth/login';
};