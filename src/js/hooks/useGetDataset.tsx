import { useEffect, useState } from 'react';
import { apiClient } from '../network';
import { Dataset } from 'types/Resources';
import { useNotifications } from 'components/WithNotifications';

const useGetDataset = (slug: string) => {
    const [isLoading, setIsLoading] = useState(true);
    const [dataset, setDataset] = useState<Dataset | null>(null);
    const notifications = useNotifications();

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
                notifications.show(message, { variant: 'error' });
            }
        };

        getDataset();
    }, []); // Only run once on mount

    return { isLoading, dataset };
};

export default useGetDataset;
