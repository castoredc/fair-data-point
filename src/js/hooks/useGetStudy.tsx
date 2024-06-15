import React, { useEffect, useState } from 'react';
import { apiClient } from '../network';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Study } from 'types/Resources';

const useGetStudy = (slug: string) => {
    const [isLoading, setIsLoading] = useState(true);
    const [study, setStudy] = useState<Study | null>(null);

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
                toast.error(<ToastItem type="error" title={message} />);
            }
        };

        getStudy();
    }, []); // Only run once on mount

    return { isLoading, study };
}

export default useGetStudy