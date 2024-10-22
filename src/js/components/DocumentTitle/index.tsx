import React, { useEffect } from 'react';

interface DocumentTitleProps {
    title: string;
}

const DocumentTitle: React.FC<DocumentTitleProps> = ({ title }) => {
    useEffect(() => {
        document.title = title;
    }, [title]);

    return null;
};

export default DocumentTitle;
