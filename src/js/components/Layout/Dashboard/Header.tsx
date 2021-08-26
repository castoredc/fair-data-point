import React, {FC, ReactElement} from 'react';
import './Dashboard.scss';
import {Stack, StackItem} from "@castoredc/matter";
import {toRem} from "@castoredc/matter-utils";

type HeaderProps = {
    title: string,
    badge?: ReactElement
}

const Header: FC<HeaderProps> = ({ title, badge, children }) => {
    return <Stack distribution="center">
        <StackItem className="DashboardHeader" style={{width: toRem(960), marginTop: '3.2rem'}}>
        <div className="HeaderTitle">
            <h2>{title}</h2>
            {badge && badge}
        </div>
        <div className="HeaderActions">
            {children}
        </div>
        </StackItem>
    </Stack>;
}

export default Header;
