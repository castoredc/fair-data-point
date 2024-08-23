import React from 'react';

import './Breadcrumbs.scss';
import Breadcrumb from './Breadcrumb';
import { localizedText } from '../../util';

interface BreadcrumbProps {
    type: string;
    path: string;
    state?: any;
    title: string;
}

interface BreadcrumbsProps {
    breadcrumbs: BreadcrumbProps[];
}

const Breadcrumbs: React.FC<BreadcrumbsProps> = ({ breadcrumbs }) => {
    return (
        <div className="Breadcrumbs">
            <div className="container">
                {breadcrumbs.map(crumb => (
                    <Breadcrumb
                        key={crumb.type}
                        to={{
                            pathname: crumb.path,
                            state: crumb.state,
                        }}
                    >
                        {localizedText(crumb.title, 'en')}
                    </Breadcrumb>
                ))}
            </div>
        </div>
    );
};

export default Breadcrumbs;