import React, { FC, ReactElement } from 'react';
// import './Dashboard.scss';
import { Heading, Stack, StackItem } from '@castoredc/matter';
import { HeadingType } from '@castoredc/matter/lib/types/types/heading';

type DashboardTabHeader = {
    title: string;
    badge?: ReactElement;
    type?: HeadingType;
};

const DashboardTabHeader: FC<DashboardTabHeader> = ({ title, badge, children, type }) => {
    return (
        <div className="DashboardTabHeader">
            <Stack distribution="equalSpacing">
                <StackItem className="HeaderTitle">
                    <Stack>
                        <Heading type={type ?? 'Subsection'} style={{ margin: 0 }}>
                            {title}
                        </Heading>
                        {badge && badge}
                    </Stack>
                </StackItem>
                <StackItem className="HeaderActions">{children}</StackItem>
            </Stack>
        </div>
    );
};

export default DashboardTabHeader;
