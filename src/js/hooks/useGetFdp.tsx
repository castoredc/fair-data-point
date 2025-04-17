import { useEffect, useState } from 'react';
import { apiClient } from '../network';
import { Fdp } from 'types/Resources';
import { useNotifications } from 'components/WithNotifications';

const useGetFdp = () => {
    const [isLoading, setIsLoading] = useState(true);
    const [fdp, setFdp] = useState<Fdp | null>(null);
    const notifications = useNotifications();

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
                notifications.show(message, { variant: 'error' });
            }
        };

        getFDP();
    }, []); // Only run once on mount

    return { isLoading, fdp };
};

export default useGetFdp;
