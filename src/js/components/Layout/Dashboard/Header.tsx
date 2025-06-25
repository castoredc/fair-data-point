import React, { FC, ReactElement } from 'react';
import './Dashboard.scss';
import { Stack, StackItem, ViewHeader } from '@castoredc/matter';
import { HeadingType } from '@castoredc/matter/lib/types/types/heading';

type HeaderProps = {
    title: string;
    badge?: ReactElement;
    type?: HeadingType;
    fullWidth?: boolean;
};

const Header: FC<HeaderProps> = ({ title, badge, children, type, fullWidth }) => {
    return (
        <div className="DashboardHeader">
            <ViewHeader>
                <Stack distribution="space-between" withoutExternalMargins>
                    <div className="HeaderTitle">
                        {title}
                        {badge && badge}
                    </div>

                    <StackItem className="HeaderActions">{children}</StackItem>
                </Stack>
            </ViewHeader>
        </div>
    );
};

export default Header;
