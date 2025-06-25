import React from 'react';
import Breadcrumb from './Breadcrumb';
import { localizedText } from '../../util';
import { BreadcrumbType } from 'types/BreadcrumbType';
import { Breadcrumbs as MuiBreadcrumbs, Container, Paper } from '@mui/material';
import NavigateNextIcon from '@mui/icons-material/NavigateNext';

interface BreadcrumbsProps {
    breadcrumbs: BreadcrumbType[];
}

const Breadcrumbs: React.FC<BreadcrumbsProps> = ({ breadcrumbs }) => {
    return (
        <Paper elevation={0} sx={{ bgcolor: 'grey.100', py: 1, borderRadius: 0 }}>
            <Container>
                <MuiBreadcrumbs
                    separator={<NavigateNextIcon fontSize="small" />}
                    sx={{ '& .MuiBreadcrumbs-li': { display: 'flex' } }}
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
        </Paper>
    );
};

export default Breadcrumbs;
