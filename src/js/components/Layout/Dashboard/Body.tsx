import React, { FC } from 'react';
import './Dashboard.scss';

type BodyProps = {
    children: React.ReactNode;
};

const Body: FC<BodyProps> = ({ children }) => {
    return (
        <div className="DashboardBody">
            {children}
        </div>
    );
};

export default Body;
