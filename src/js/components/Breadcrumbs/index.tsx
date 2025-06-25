import React from 'react';

import './Breadcrumbs.scss';
import Breadcrumb from './Breadcrumb';
import { localizedText } from '../../util';
import { BreadcrumbType } from 'types/BreadcrumbType';

interface BreadcrumbsProps {
    breadcrumbs: BreadcrumbType[];
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
                        title={localizedText(crumb.title, 'en')}
                    />
                ))}
            </div>
        </div>
    );
};

export default Breadcrumbs;
