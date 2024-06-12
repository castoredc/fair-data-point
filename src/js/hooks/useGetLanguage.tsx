import React, { useEffect, useState } from 'react';
import { apiClient } from '../network';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Fdp } from 'types/Resources';
import { LanguageType } from 'types/LanguageType';

const useGetLanguage = (code: string) => {
    const [isLoading, setIsLoading] = useState(true);
    const [language, setLanguage] = useState<LanguageType | null>(null);

    useEffect(() => {
        const getLanguage = async () => {
            try {
                const response = await apiClient.get('/api/language/' + code);
                setLanguage(response.data);
                setIsLoading(false);
            } catch (error) {
                setIsLoading(false);
                toast.error(<ToastItem type="error" title="Could not load language information." />);
            }
        };

        getLanguage();
    }, []); // Only run once on mount

    return { isLoading, language };
}

export default useGetLanguage