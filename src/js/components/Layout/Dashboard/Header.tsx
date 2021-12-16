import React, {FC, ReactElement} from 'react';
import './Dashboard.scss';
import {Stack, StackItem} from "@castoredc/matter";
import {toRem} from "@castoredc/matter-utils";

type HeaderProps = {
    title: string,
    badge?: ReactElement
}

const Header: FC<HeaderProps> = ({ title, badge, children }) => {
    return <div className="DashboardHeader" style={{
        width: toRem(960),
        maxWidth: '100%',
        paddingTop: '3.2rem'
    }}>
        <Stack distribution="equalSpacing">
            <StackItem className="HeaderTitle">
                <Stack>
                    <h2>{title}</h2>
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
