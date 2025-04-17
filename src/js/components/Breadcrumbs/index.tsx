import React from 'react';

import './Breadcrumbs.scss';
import Breadcrumb from './Breadcrumb';
import { localizedText } from '../../util';
import { BreadcrumbType } from 'types/BreadcrumbType';
import { Breadcrumbs as MuiBreadcrumbs, Container } from '@mui/material';
import NavigateNextIcon from '@mui/icons-material/NavigateNext';

interface BreadcrumbsProps {
    breadcrumbs: BreadcrumbType[];
}

const Breadcrumbs: React.FC<BreadcrumbsProps> = ({ breadcrumbs }) => {
    return (
        <Container>
            <MuiBreadcrumbs
                separator={<NavigateNextIcon fontSize="small" />}
            >
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
            </MuiBreadcrumbs>
        </Container>
    );
};

export default Breadcrumbs;
