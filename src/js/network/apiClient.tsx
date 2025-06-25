import axios, { AxiosError } from 'axios';

/**
 * TODO:
 * - This file should be a .ts file and not a .tsx file.
 * - Change it back to .ts file when we create a function level API for toasts
 */

const isLocalEnv = process.env.NODE_ENV === 'development';

/**
 * Create an Axios Client with defaults
 */
const apiClient = axios.create();

apiClient.interceptors.response.use(
    response => {
        return response;
    },
    (error: AxiosError) => {
        if (error.response) {
            if (isLocalEnv) {
                // Request was made but server responded with something other than 2xx
                console.error('Status:', error.response.status);
                console.error('Data:', error.response.data);
            }

            // Redirect the user to login page if the authorization fails
            if (error.response.status === 401) {
                window.location.href = '/login?path=' + encodeURIComponent(window.location.pathname);
            }
        } else {
            // Something else happened while setting up the request triggered the error
            isLocalEnv && console.error('Error Message:', error.message);
        }
        return Promise.reject(error);
    },
);

export { apiClient };
