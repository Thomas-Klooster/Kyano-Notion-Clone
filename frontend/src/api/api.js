import axios from 'axios';

const api = axios.create({
     baseURL: 'http://localhost:8000/api',
     withCredentials: true,
});

let isRefreshing = false;
let failedQueue = [];

const processQueue = (error, token = null) => {
     failedQueue.forEach(({ resolve, reject }) =>
     error ? reject(error) : resolve(token));
     failedQueue = [];
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
               localStorage.clear();
               // Commented out due by error page gets forced refreshed
               // window.location.href = '/auth/login';
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
                const { data } = await api.post('/refresh', {}, {
                    headers: { Authorization: `Bearer ${refreshToken}` },
                });

                    localStorage.setItem('accessToken', data.accessToken);
                    localStorage.setItem('refreshToken', data.refreshToken);
                    processQueue(null, data.accessToken);
                    return api(originalRequest);
               } catch (refreshError) {
                    processQueue(refreshError, null);
                    localStorage.clear();
                    // Commented out due by error page gets forced refreshed
                    // window.location.href = '/auth/login';
                    return Promise.reject(refreshError);
               } finally {
                    isRefreshing = false;
               }
          }
          return Promise.reject(error);
     }
);
export default api;