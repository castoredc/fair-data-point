import React, { useEffect, useState } from 'react';
import { apiClient } from '../network';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';

const useGetFdp = () => {
    const [isLoading, setIsLoading] = useState(true);
    const [fdp, setFdp] = useState<any | null>(null);

    useEffect(() => {
        const getFDP = async () => {
            try {
                const response = await apiClient.get('/api/fdp');
                setFdp(response.data);
                setIsLoading(false);
            } catch (error) {
                setIsLoading(false);
                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the FAIR Data Point information';
                toast.error(<ToastItem type="error" title={message} />);
            }
        };

        getFDP();
    }, []); // Only run once on mount

    return { isLoading, fdp };
}

export default useGetFdp