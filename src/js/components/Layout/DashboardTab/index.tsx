import React, { FC } from 'react';
import './DashboardTab.scss';

type DashboardTabProps = {
    children: React.ReactNode;
};

const DashboardTab: FC<DashboardTabProps> = ({ children }) => {
    return <div className="DashboardTab">{children}</div>;
};

export default DashboardTab;
