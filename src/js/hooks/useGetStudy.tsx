import { useEffect, useState } from 'react';
import { apiClient } from '../network';
import { Study } from 'types/Resources';
import { useNotifications } from 'components/WithNotifications';

const useGetStudy = (slug: string) => {
    const [isLoading, setIsLoading] = useState(true);
    const [study, setStudy] = useState<Study | null>(null);
    const notifications = useNotifications();

    useEffect(() => {
        const getStudy = async () => {
            try {
                const response = await apiClient.get('/api/study/slug/' + slug);
                setStudy(response.data);
                setIsLoading(false);
            } catch (error) {
                setIsLoading(false);
                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the study';
                notifications.show(message, { variant: 'error' });
            }
        };

        getStudy();
    }, []); // Only run once on mount

    return { isLoading, study };
};

export default useGetStudy;
