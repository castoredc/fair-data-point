import React, { FunctionComponent } from 'react';
import Studies from 'pages/Dashboard/Studies';
import Catalogs from 'pages/Dashboard/Catalogs';
import DataModels from 'pages/Dashboard/DataModels';
import { isAdmin } from 'utils/PermissionHelper';
import FAIRDataPoint from 'pages/Dashboard/FAIRDataPoint';
import EDCServers from 'pages/Dashboard/EDCServers';
import { AuthorizedRouteComponentProps } from 'components/Route';
import TabsWrapper from 'components/TabsWrapper';
import MetadataModels from 'pages/Dashboard/MetadataModels';

interface DashboardProps extends AuthorizedRouteComponentProps {}
export const DashboardTabs: FunctionComponent<DashboardProps> = ({ history, location, match, user }) => {
    const urls = {
        '/dashboard/studies': 'studies',
        '/dashboard/catalogs': 'catalogs',
        '/dashboard/data-models': 'dataModels',
        '/dashboard/metadata-models': 'metadataModels',
        '/dashboard/fdp': 'fdp',
        '/dashboard/edc-servers': 'edcServers',
    };

    const tabs = {
        studies: {
            content: <Studies history={history} location={location} match={match} user={user} />,
            title: 'Studies',
        },
        catalogs: {
            content: <Catalogs history={history} location={location} match={match} user={user} />,
            title: 'Catalogs',
        },
        dataModels: {
            content: <DataModels history={history} location={location} match={match} user={user} />,
            title: 'Data models',
        },
        metadataModels: {
            content: <MetadataModels history={history} location={location} match={match} user={user} />,
            title: 'Metadata models',
        },
        ...(isAdmin(user) && {
            fdp: {
                content: <FAIRDataPoint history={history} location={location} match={match} user={user} />,
                title: 'FAIR Data Point',
            },
            edcServers: {
                content: <EDCServers history={history} location={location} match={match} user={user} />,
                title: 'EDC Servers',
            },
        }),
    };

    return (
        <div className="DashboardTabs">
            <TabsWrapper
                tabs={tabs}
                selected={urls[location.pathname]}
                onChange={selectedKey => {
                    const newUrl = Object.keys(urls).find(key => urls[key] === selectedKey) ?? '/dashboard/studies';
                    history.push(newUrl);
                }}
            />
        </div>
    );
};
