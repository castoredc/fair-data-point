import React, {FC} from 'react';
import './Dashboard.scss';
import {Stack, StackItem} from "@castoredc/matter";
import {toRem} from "@castoredc/matter-utils";

type BodyProps = {
    children: React.ReactNode,
}

const Body: FC<BodyProps> = ({children}) => {
    return <div className="DashboardBody">
        <Stack distribution="center">
            <StackItem style={{
                width: toRem(960),
                maxWidth: '100%',
                // marginTop: '3.2rem',
                overflow: 'hidden',
                height: '100%',
                padding: '0 3.2rem',
                display: 'flex',
                flexDirection: 'column',
            }}>
                {children}
            </StackItem>
        </Stack>
    </div>;
}

export default Body;
