import React, {FC, ReactElement} from 'react';
import './Dashboard.scss';
import {Heading, Stack, StackItem} from "@castoredc/matter";
import {toRem} from "@castoredc/matter-utils";
import {HeadingType} from "@castoredc/matter/lib/types/types/heading";

type HeaderProps = {
    title: string,
    badge?: ReactElement,
    type?: HeadingType
}

const Header: FC<HeaderProps> = ({title, badge, children, type}) => {
    return <div className="DashboardHeader" style={{
        width: toRem(960),
        maxWidth: '100%',
    }}>
        <Stack distribution="equalSpacing">
            <StackItem className="HeaderTitle">
                <Stack>
                    <Heading type={type ?? 'Subsection'} style={{margin: 0}}>{title}</Heading>
                    {badge && badge}
                </Stack>
            </StackItem>
            <StackItem className="HeaderActions">
                {children}
            </StackItem>
        </Stack>
    </div>;
}

export default Header;
