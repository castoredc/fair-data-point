import { useState, useEffect } from 'react';
import axios from 'axios';
import jsonld from 'jsonld';

interface Context {
    [key: string]: string;
}

interface JsonLdData {
    [key: string]: string;
}

const useJsonLdRepresentation = (endpoint: string, context: Context) => {
    const [isLoading, setIsLoading] = useState(true);
    const [data, setData] = useState<JsonLdData>({});
    const [error, setError] = useState<Error | null>(null);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await axios.get(endpoint);
                const jsonData = response.data;

                // Compact the JSON-LD data using the provided context
                const compacted = await jsonld.compact(jsonData, context);

                const extractedData: JsonLdData = {};
                for (const key in context) {
                    extractedData[key] = compacted[key] || compacted['@graph'][0][key] || null;
                }

                setData(extractedData);
                setIsLoading(false);
            } catch (err) {
                setError(err as Error);
                setIsLoading(false);
            }
        };

        fetchData().catch(console.error); // Handle the promise from fetchData
    }, [endpoint, context]);

    return { data, isLoading, error };
};

export default useJsonLdRepresentation;