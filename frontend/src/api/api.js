import axios from 'axios';

const api = axios.create({
     baseURL: 'http://localhost:8000/api',
     withCredentials: true,
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

api.interceptors.request.use((config) => {
     const token = localStorage.getItem('accessToken');
     if (token) config.headers.Authorization = `Bearer ${token}`;
     return config;
})

api.interceptors.response.use(
     (response) => response,
     async (error) => {
          const originalRequest = error.config;

          if (originalRequest.url === '/refresh') {
               notifySessionExpired();
               return Promise.reject(error);
          }

          if (error.response?.status === 401 && !originalRequest._retry) {
               if (isRefreshing) {
                    return new Promise((resolve, reject) => {
                    failedQueue.push({ resolve, reject});
                    }).then((token) => {
                    originalRequest.headers['Authorization'] = `Bearer ${token}`;
                    return api(originalRequest);
                    });
               }

               originalRequest._retry = true;
               isRefreshing = true;

               try {
                    const refreshToken = localStorage.getItem('refreshToken');
                    if (!refreshToken) {
                         notifySessionExpired();
                         return Promise.reject(error);
                    }

                const { data } = await api.post('/refresh', {
                   refreshToken: refreshToken
                });

                    localStorage.setItem('accessToken', data.accessToken);
                    localStorage.setItem('refreshToken', data.refreshToken);
                    hasEmittedSessionExpired = false;
                    processQueue(null, data.accessToken);
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