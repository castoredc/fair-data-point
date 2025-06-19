import React, { Component } from 'react';
import { Box, Container } from '@mui/material';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { DashboardRoutes } from 'pages/Dashboard/DashboardRoutes';

interface DashboardProps extends AuthorizedRouteComponentProps {
}

class Dashboard extends Component<DashboardProps> {
    render() {
        const { history, user } = this.props;

        return (
            <Box sx={{ 
                display: 'flex',
                flexDirection: 'column',
                minHeight: '100vh'
            }}>
                <Container maxWidth={false}>
                    <DashboardRoutes user={user} />
                </Container>
            </Box>
        );
    }
}

export default Dashboard;