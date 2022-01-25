import React, {FC} from "react";
import DocumentTitle from "components/DocumentTitle";
import {Banner, Heading, Stack, StackItem} from "@castoredc/matter";
import {toRem} from "@castoredc/matter-utils";
import BackButton from "components/BackButton";
import {useHistory} from "react-router-dom";

interface NoPermissionProps {
    text: string,
}

const NoPermission: FC<NoPermissionProps> = ({ text }) => {
    return <div style={{marginLeft: 'auto', marginRight: 'auto'}}>
        <DocumentTitle title="Unauthorized"/>

        <Stack distribution="center">
            <StackItem style={{width: toRem(480), marginTop: '3.2rem'}}>
                <BackButton returnButton>Back to previous page</BackButton>

                <Banner type="error" title={text}/>
            </StackItem>
        </Stack>
    </div>;
}
    export default NoPermission;
