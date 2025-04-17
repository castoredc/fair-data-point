import React, { FunctionComponent } from 'react';
import BackButton from 'components/BackButton';
import DocumentTitle from 'components/DocumentTitle';
import './SelectPage.scss';
import * as H from 'history';
import { Divider, Typography } from '@mui/material';
import MuiCard from '@mui/material/Card';
import { styled } from '@mui/material/styles';

interface SelectPageProps {
    title: string;
    description?: string;
    children: React.ReactNode;
    backButton: {
        to?: string | (() => void);
        label: string;
    };
    returnButton?: boolean;
    sidebar?: boolean;
    history: H.History;
}

const Card = styled(MuiCard)(({ theme }) => ({
    display: 'flex',
    flexDirection: 'column',
    alignSelf: 'center',
    width: '100%',
    padding: theme.spacing(4),
    gap: theme.spacing(2),
    margin: 'auto',
    marginTop: theme.spacing(4),
    [theme.breakpoints.up('sm')]: {
        maxWidth: '450px',
    },
}));

export const SelectPage: FunctionComponent<SelectPageProps> = ({
                                                                   title,
                                                                   description,
                                                                   children,
                                                                   backButton,
                                                                   history,
                                                               }) => {
    return (
        <Card>
            <DocumentTitle title={title} />
            <div className="SelectPageHeader">
                <BackButton to={backButton.to} history={history}>
                    {backButton.label}
                </BackButton>

                <Typography variant="h4">{title}</Typography>

                {description && <p>{description}</p>}

                <Divider />
            </div>

            <div className="SelectPageContent">{children}</div>
        </Card>
    );
};

export default SelectPage;
