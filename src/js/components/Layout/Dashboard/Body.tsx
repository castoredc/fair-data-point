import React, {FC} from 'react';
import './Dashboard.scss';
import {Stack, StackItem} from "@castoredc/matter";
import {toRem} from "@castoredc/matter-utils";

type BodyProps = {}

const Body: FC<BodyProps> = ({children}) => {
    return <div className="DashboardBody">
        <Stack distribution="center">
            <StackItem style={{
                width: toRem(960),
                // marginTop: '3.2rem',
                overflow: 'hidden',
                height: '100%'
            }}>
                {children}
            </StackItem>
        </Stack>
    </div>;
}

export default Body;
