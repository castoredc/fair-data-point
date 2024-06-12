import React, { useEffect, useState } from 'react';
import { apiClient } from '../network';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Fdp } from 'types/Resources';
import { CountryType } from 'types/CountryType';

const useGetCountry = (code: string) => {
    const [isLoading, setIsLoading] = useState(true);
    const [country, setCountry] = useState<CountryType | null>(null);

    useEffect(() => {
        const getCountry = async () => {
            try {
                const response = await apiClient.get('/api/country/' + code);
                setCountry(response.data);
                setIsLoading(false);
            } catch (error) {
                setIsLoading(false);
                toast.error(<ToastItem type="error" title="Could not load country information." />);
            }
        };

        getCountry();
    }, []); // Only run once on mount

    return { isLoading, country };
}

export default useGetCountry