import { useEffect, useState } from 'react';
import { apiClient } from '../network';
import { Catalog } from 'types/Resources';
import { useNotifications } from 'components/WithNotifications';

const useGetCatalog = (slug: string) => {
    const [isLoading, setIsLoading] = useState(true);
    const [catalog, setCatalog] = useState<Catalog | null>(null);
    const notifications = useNotifications();

    useEffect(() => {
        const getCatalog = async () => {
            try {
                const response = await apiClient.get('/api/catalog/' + slug);
                setCatalog(response.data);
                setIsLoading(false);
            } catch (error) {
                setIsLoading(false);
                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the catalog information';
                notifications.show(message, { variant: 'error' });
            }
        };

        getCatalog();
    }, []); // Only run once on mount

    return { isLoading, catalog };
};

export default useGetCatalog;
