import React, { useEffect, useState } from 'react';
import { apiClient } from '../network';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Distribution } from 'types/Resources';

const useGetDistribution = (datasetSlug: string, distributionSlug: string) => {
    const [isLoading, setIsLoading] = useState(true);
    const [distribution, setDistribution] = useState<Distribution | null>(null);

    useEffect(() => {
        const getDistribution = async () => {
            try {
                const response = await apiClient.get(`/api/dataset/${datasetSlug}/distribution/${distributionSlug}`);
                setDistribution(response.data);
                setIsLoading(false);
            } catch (error) {
                setIsLoading(false);
                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the distribution information';
                toast.error(<ToastItem type="error" title={message} />);
            }
        };

        getDistribution();
    }, []); // Only run once on mount

    return { isLoading, distribution };
};

export default useGetDistribution;
