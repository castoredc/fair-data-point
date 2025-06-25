import { useEffect, useState } from 'react';
import { apiClient } from '../network';
import { Distribution } from 'types/Resources';
import { useNotifications } from 'components/WithNotifications';

const useGetDistribution = (datasetSlug: string, distributionSlug: string) => {
    const [isLoading, setIsLoading] = useState(true);
    const [distribution, setDistribution] = useState<Distribution | null>(null);
    const notifications = useNotifications();

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
                notifications.show(message, { variant: 'error' });
            }
        };

        getDistribution();
    }, []); // Only run once on mount

    return { isLoading, distribution };
};

export default useGetDistribution;
