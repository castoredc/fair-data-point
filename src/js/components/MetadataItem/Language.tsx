import React from 'react';

import useGetLanguage from '../../hooks/useGetLanguage';

interface LanguageProps {
    code: string;
}

const Language: React.FC<LanguageProps> = ({ code }) => {
    const { language, isLoading } = useGetLanguage(code);

    if (isLoading || !language) {
        return <div className="Language">&nbsp;</div>;
    }

    return <div className="Language">{language.label}</div>;
};

export default Language;
