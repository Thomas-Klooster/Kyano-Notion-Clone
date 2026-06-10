import api, { ensureCsrfCookie } from '@/api/api';


export const login = async (email, password) => {
     await ensureCsrfCookie();
     const { data } = await api.post('/login', {
          email, password
     });
     return data.user
};

export const register = async (name, email, address, phone_number, company, password, password_confirmation) => {
     await ensureCsrfCookie();
     const { data } = await api.post('/register', {
          name, email, address, phone_number, company, password, password_confirmation
     });
     return data.user
};


export const logout = async () => {
     await ensureCsrfCookie();

     try {
     await api.post('/logout', { refreshToken });
     } finally {
     localStorage.removeItem('accessToken');
     localStorage.removeItem('refreshToken');
     window.location.href = '/auth/login';
     }
};