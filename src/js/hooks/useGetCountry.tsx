import { useEffect, useState } from 'react';
import { apiClient } from '../network';
import { CountryType } from 'types/CountryType';
import { useNotifications } from 'components/WithNotifications';

const useGetCountry = (code: string) => {
    const [isLoading, setIsLoading] = useState(true);
    const [country, setCountry] = useState<CountryType | null>(null);
    const notifications = useNotifications();

    useEffect(() => {
        const getCountry = async () => {
            try {
                const response = await apiClient.get('/api/country/' + code);
                setCountry(response.data);
                setIsLoading(false);
            } catch (error) {
                setIsLoading(false);
                notifications.show('Could not load country information.', { variant: 'error' });
            }
        };

        getCountry();
    }, []); // Only run once on mount

    return { isLoading, country };
};

export default useGetCountry;
