import { useEffect, useState } from 'react';
import { apiClient } from '../network';
import { LanguageType } from 'types/LanguageType';
import { useNotifications } from 'components/WithNotifications';

const useGetLanguage = (code: string) => {
    const [isLoading, setIsLoading] = useState(true);
    const [language, setLanguage] = useState<LanguageType | null>(null);
    const notifications = useNotifications();

    useEffect(() => {
        const getLanguage = async () => {
            try {
                const response = await apiClient.get('/api/language/' + code);
                setLanguage(response.data);
                setIsLoading(false);
            } catch (error) {
                setIsLoading(false);
                notifications.show('Could not load language information.', { variant: 'error' });
            }
        };

        getLanguage();
    }, []); // Only run once on mount

    return { isLoading, language };
};

export default useGetLanguage;
