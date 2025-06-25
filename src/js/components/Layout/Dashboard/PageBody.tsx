import React, { FC } from 'react';
import './PageBody.scss';

type PageBodyProps = {
    children: React.ReactNode;
};

const PageBody: FC<PageBodyProps> = ({ children }) => {
    return <div className="PageBody">{children}</div>;
};

export default PageBody;
