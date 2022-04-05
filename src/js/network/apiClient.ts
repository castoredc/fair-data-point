import axios, { AxiosError } from "axios";

/**
 * Create an Axios Client with defaults
 */
const apiClient = axios.create();

apiClient.interceptors.response.use(
  (response) => {
    // Only logs the response when user is accessing console in development mode
    if (process.env.NODE_ENV === "development") {
      console.log(response);
    }
    return response;
  },
  (error: AxiosError) => {
    if (error.response) {
      // Request was made but server responded with something other than 2xx
      console.error("Status:", error.response.status);
      console.error("Data:", error.response.data);

      // Logout the user if the authorization fails
      if (error.response.status === 401) {
        window.location.href =
          "/login?path=" + encodeURIComponent(window.location.pathname);
      }
    } else {
      // Something else happened while setting up the request triggered the error
      console.error("Error Message:", error.message);
    }
    return Promise.reject(error);
  }
);

export { apiClient };
