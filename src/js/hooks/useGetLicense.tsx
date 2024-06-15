import React, { useEffect, useState } from 'react';
import { apiClient } from '../network';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Fdp } from 'types/Resources';
import { LicenseType } from 'types/LicenseType';

const useGetLicense = (slug: string) => {
    const [isLoading, setIsLoading] = useState(true);
    const [license, setLicense] = useState<LicenseType | null>(null);

    useEffect(() => {
        const getLicense = async () => {
            try {
                const response = await apiClient.get('/api/license/' + slug);
                setLicense(response.data);
                setIsLoading(false);
            } catch (error) {
                setIsLoading(false);
                toast.error(<ToastItem type="error" title="Could not load license information." />);
            }
        };

        getLicense();
    }, []); // Only run once on mount

    return { isLoading, license };
}

export default useGetLicense