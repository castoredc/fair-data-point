import React, { useEffect, useState } from 'react';
import { apiClient } from '../network';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Dataset } from 'types/Resources';

const useGetDataset = (slug: string) => {
    const [isLoading, setIsLoading] = useState(true);
    const [dataset, setDataset] = useState<Dataset | null>(null);

    useEffect(() => {
        const getDataset = async () => {
            try {
                const response = await apiClient.get('/api/dataset/' + slug);
                setDataset(response.data);
                setIsLoading(false);
            } catch (error) {
                setIsLoading(false);
                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the dataset information';
                toast.error(<ToastItem type="error" title={message} />);
            }
        };

        getDataset();
    }, []); // Only run once on mount

    return { isLoading, dataset };
};

export default useGetDataset;
