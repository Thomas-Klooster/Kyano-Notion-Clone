import axios from 'axios';

// ? Creating a custom instance for request containing i believe sanctums stateful domain??

//  !! DEBUG: if it doesn't work change it to be backend host
// !! It should work on the frontend since the sanctum is on it the current host though..
const api = axios.create({
     baseURL: 'http://localhost:5173'
});

let isRefreshing = false;
let failedQueue = [];

const processQueue = (error, token = null) => {
     failedQueue.forEach(({ resolve, reject }) =>
     error ? reject(error) : resolve(token));
     failedQueue = [];
};


// ? Adds a response interceptor
api.interceptors.request.use((config) => {
     const token = localStorage.getItem('accessToken');
     if (token) config.headers.Authorization = `Bearer ${token}`;
     return config;
})

// ! Api constance responds to the interceptor request
api.interceptors.response.use(
     (response) => response,
     async (error) => {
          const originalRequest = error.config;

          if (error.response?.status === 401 && !originalRequest._retry) {
               if (isRefreshing) {
                    // TODO Do something with the with the response data
                    return new Promise((resolve, reject) => {
                    failedQueue.push({ resolve, reject});
                    }).then((token) => {
                    originalRequest.headers['Authorization'] = `Bearer ${token}`;
                    return api(originalRequest);
                    })
               }
               originalRequest._retry = true;
               isRefreshing = true;

               try {
                    const { data } = await axios.post(`{api.defaults.baseURL}/auth/refresh`, {
                         refreshToken: localStorage.getItem('refreshToken'),
                    });
                    localStorage.setItem('accessToken', data.accessToken);
                    processQueue(null, data.accessToken);
                    return api(originalRequest);
               } catch (error) {
                    processQueue(error, null);
                    // ? back to the loginpage after logout feature
                    localStorage.clear();
                    window.location.href = '/auth/login';
                    return Promise.rejecct(error);
               } finally {
                    isRefreshing = false;
               }
          }
          return Promise.reject(error);
     }
)
export default api