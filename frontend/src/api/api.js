import axios from 'axios';

const api = axios.create({
     baseURL: 'http://localhost:8000/api',
     withCredentials: true,
     withXSRFToken: true,
     headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
     },
});

export const AUTH_SESSION_EXPIRED_EVENT = 'auth:session-expired';
let isRefreshing = false;
let failedQueue = [];
let hasEmittedSessionExpired = false;

const processQueue = (error, token = null) => {
     failedQueue.forEach(({ resolve, reject }) =>
     error ? reject(error) : resolve(token));
     failedQueue = [];
};

const notifySessionExpired = () => {
     localStorage.clear();

     if (hasEmittedSessionExpired) return;

     hasEmittedSessionExpired = true;
     window.dispatchEvent(new CustomEvent(AUTH_SESSION_EXPIRED_EVENT));
};

export const ensureCsrfCookie = () =>
     axios.get('http://localhost:8000/sanctum/csrf-cookie', {
          withCredentials: true,
          withXSRFToken: true,
          headers: {
               Accept: 'application/json',
          },
     });

api.interceptors.request.use((config) => {
     const token = localStorage.getItem('accessToken') || document.cookie
     .split('; ').find(row => row.startsWith('accessToken='))?.split('=')[1];
     if (token) {
     config.headers.Authorization = `Bearer ${token}`;
     }
     return config;
});

api.interceptors.response.use(
     (response) => response,
     async (error) => {
          const originalRequest = error.config;

          if (originalRequest?.url === '/auth/refresh') {
               notifySessionExpired();
               return Promise.reject(error);
          }

          if (error.response?.status === 401 && originalRequest && !originalRequest._retry) {
               if (isRefreshing) {
                    return new Promise((resolve, reject) => {
                         failedQueue.push({ resolve, reject });
                    }).then((token) => {
                         originalRequest.headers.Authorization = `Bearer ${token}`;
                         return api(originalRequest);
                    });
               }

               originalRequest._retry = true;
               isRefreshing = true;

               try {
                    const refreshToken = localStorage.getItem('refreshToken') || document.cookie
                    .split('; ')?.split('=')[1];

                    if (!refreshToken) {
                         notifySessionExpired();
                         return Promise.reject(error);
                    }

                    const { data } = await api.post('/auth/refresh', { refreshToken });

                    localStorage.setItem('accessToken', data.accessToken);
                    localStorage.setItem('refreshToken', data.refreshToken);
                    hasEmittedSessionExpired = false;
                    processQueue(null, data.accessToken);
                    originalRequest.headers.Authorization = `Bearer ${data.accessToken}`;

                    return api(originalRequest);
               } catch (refreshError) {
                    processQueue(refreshError, null);
                    notifySessionExpired();
                    return Promise.reject(refreshError);
               } finally {
                    isRefreshing = false;
               }
          }

          return Promise.reject(error);
     }
);
export default api;