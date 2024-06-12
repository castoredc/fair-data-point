import React, { useEffect, useState } from 'react';
import { apiClient } from '../network';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Catalog } from 'types/Resources';

const useGetCatalog = (slug: string) => {
    const [isLoading, setIsLoading] = useState(true);
    const [catalog, setCatalog] = useState<Catalog | null>(null);

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
                toast.error(<ToastItem type="error" title={message} />);
            }
        };

        getCatalog();
    }, []); // Only run once on mount

    return { isLoading, catalog };
}

export default useGetCatalog