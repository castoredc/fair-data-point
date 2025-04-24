import SideBar from 'components/SideBar/index';
import { isAdmin } from 'utils/PermissionHelper';
import React from 'react';
import * as H from 'history';
import { UserType } from 'types/UserType';
import InventoryIcon from '@mui/icons-material/Inventory';
import BiotechIcon from '@mui/icons-material/Biotech';
import AccountTreeIcon from '@mui/icons-material/AccountTree';
import HubIcon from '@mui/icons-material/Hub';
import DnsIcon from '@mui/icons-material/Dns';

interface DashboardSideBarProps {
    location: H.Location;
    history: H.History;
    user: UserType | null;
}

const DashboardSideBar: React.FC<DashboardSideBarProps> = ({ location, history, user }) => {
    return <SideBar
        location={location}
        items={[
            {
                to: '/dashboard/studies',
                exact: true,
                title: 'Studies',
                icon: <BiotechIcon />,
            },
            {
                to: '/dashboard/catalogs',
                exact: true,
                title: 'Catalogs',
                icon: <InventoryIcon />,
            },
            {
                to: '/dashboard/data-models',
                exact: true,
                title: 'Data models',
                icon: <AccountTreeIcon />,
            },
            {
                to: '/dashboard/metadata-models',
                exact: true,
                title: 'Metadata models',
                icon: <AccountTreeIcon />,
            },
            ...(isAdmin(user)
                ? [
                    {
                        type: 'separator',
                    },
                    {
                        to: '/dashboard/fdp',
                        exact: true,
                        title: 'FAIR Data Point',
                        icon: <HubIcon />,
                    },
                    {
                        to: '/dashboard/edc-servers',
                        exact: true,
                        title: 'EDC servers',
                        icon: <DnsIcon />,
                    },
                ]
                : []),
        ]}
        history={history}
    />;
};

export default DashboardSideBar;