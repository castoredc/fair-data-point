import React, { FC, useState } from 'react';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { isGranted } from 'utils/PermissionHelper';
import PermissionEditor from 'components/PermissionEditor';
import { AccessData, Edit, Manage, View } from 'components/PermissionEditor/Permissions';
import NoPermission from 'pages/ErrorPages/NoPermission';
import { UserType } from 'types/UserType';
import PageTabs from 'components/PageTabs';

interface PermissionsProps {
    getDistribution: () => void;
    distribution: any;
    user: UserType | null;
    contents: any;
}

const Permissions: FC<PermissionsProps> = ({ distribution, getDistribution, user, contents }) => {
    if (!isGranted('manage', distribution.permissions)) {
        return <NoPermission text="You do not have access to this page" />;
    }

    const [selectedTab, setSelectedTab] = useState('metadata');

    return (
        <PageBody>
            <PageTabs
                selected={selectedTab}
                onChange={setSelectedTab}
                tabs={{
                    metadata: {
                        title: 'Metadata',
                        content: (
                            <div className="FullHeightPageTab">
                                <PermissionEditor
                                    getObject={getDistribution}
                                    type="distribution"
                                    object={distribution}
                                    user={user}
                                    permissions={[View, Edit, Manage]}
                                />
                            </div>
                        ),
                    },
                    data: {
                        title: 'Data Access',
                        content: (
                            <div className="FullHeightPageTab">
                                <PermissionEditor
                                    getObject={getDistribution}
                                    type="distributionContents"
                                    object={contents}
                                    user={user}
                                    permissions={[AccessData]}
                                />
                            </div>
                        ),
                    },
                }}
            />
        </PageBody>
    );
};

export default Permissions;
