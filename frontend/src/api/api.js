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

const processQueue = (error) => {
  failedQueue.forEach(({ resolve, reject }) =>
    error ? reject(error) : resolve());
  failedQueue = [];
};

const notifySessionExpired = () => {
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

api.interceptors.request.use((config) => config);

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config;

    if ('/auth/login'.includes(originalRequest?.url)) {
      return Promise.reject(error);
    }

    if (originalRequest?.url === '/auth/refresh') {
      notifySessionExpired();
      return Promise.reject(error);
    }

    if (error.response?.status === 401 && originalRequest && !originalRequest._retry) {
      if (isRefreshing) {
        return new Promise((resolve, reject) => {
          failedQueue.push({ resolve, reject });
        }).then(() => api(originalRequest));
      }

      originalRequest._retry = true;
      isRefreshing = true;

      try {
        await api.post('/auth/refresh');

        hasEmittedSessionExpired = false;
        processQueue(null);

        return api(originalRequest);
      } catch (refreshError) {
        processQueue(refreshError);
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
