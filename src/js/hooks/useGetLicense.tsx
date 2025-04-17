import { useEffect, useState } from 'react';
import { apiClient } from '../network';
import { LicenseType } from 'types/LicenseType';
import { useNotifications } from 'components/WithNotifications';

const useGetLicense = (slug: string) => {
    const [isLoading, setIsLoading] = useState(true);
    const [license, setLicense] = useState<LicenseType | null>(null);
    const notifications = useNotifications();

    useEffect(() => {
        const getLicense = async () => {
            try {
                const response = await apiClient.get('/api/license/' + slug);
                setLicense(response.data);
                setIsLoading(false);
            } catch (error) {
                setIsLoading(false);
                notifications.show('Could not load license information.', { variant: 'error' });
            }
        };

        getLicense();
    }, []); // Only run once on mount

    return { isLoading, license };
};

export default useGetLicense;
