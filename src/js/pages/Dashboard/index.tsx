import React, { Component } from 'react';
import { CastorBar } from '@castoredc/matter';
import './Dashboard.scss';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { DashboardRoutes } from 'pages/Dashboard/DashboardRoutes';

interface DashboardProps extends AuthorizedRouteComponentProps {}

export default class Dashboard extends Component<DashboardProps> {
    render() {
        const { history, user } = this.props;

        return (
            <div className="Dashboard">
                <CastorBar
                    items={[
                        {
                            destination: () => history.push('/dashboard/studies'),
                            label: 'Castor',
                            type: 'brand',
                        },
                        {
                            items: [
                                {
                                    isTitle: true,
                                    label: 'Account',
                                },
                                {
                                    destination: '/logout',
                                    icon: 'logOut',
                                    label: 'Log out',
                                },
                            ],
                            label: 'Account',
                            type: 'account',
                        },
                    ]}
                    label="Castor navigation"
                    horizontalNav
                />
                <div className="Main">
                    <DashboardRoutes user={user} />
                </div>
            </div>
        );
    }
}
