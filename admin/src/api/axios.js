import axios from "axios";

const api = axios.create({
  baseURL:
    import.meta.env.VITE_API_BASE_URL ||
    "http://127.0.0.1:8000/api/v1",

  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

/*
|--------------------------------------------------------------------------
| Request Interceptor
|--------------------------------------------------------------------------
*/

api.interceptors.request.use(
  (config) => {
    const token =
      localStorage.getItem(
        "auth_token"
      );

    if (token) {
      config.headers.Authorization =
        `Bearer ${token}`;
    }

    return config;
  },

  (error) => Promise.reject(error)
);

/*
|--------------------------------------------------------------------------
| Response Interceptor
|--------------------------------------------------------------------------
*/

api.interceptors.response.use(

  (response) => {
    return response;
  },

  (error) => {

    if (
      error.response?.status === 401
    ) {

      localStorage.removeItem(
        "auth_token"
      );

      localStorage.removeItem(
        "user"
      );

      if (
        window.location.pathname !==
        "/login"
      ) {

        window.location.href =
          "/";
      }
    }

    return Promise.reject(error);
  }
);

export default api;