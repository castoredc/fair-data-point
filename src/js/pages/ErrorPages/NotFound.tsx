import React from "react";
import DocumentTitle from "components/DocumentTitle";
import {Banner, Stack, StackItem} from "@castoredc/matter";
import {toRem} from "@castoredc/matter-utils";
import BackButton from "components/BackButton";

export default () =>
    <div style={{marginLeft: 'auto', marginRight: 'auto'}}>
        <DocumentTitle title="Page not found"/>

        <Stack distribution="center">
            <StackItem style={{width: toRem(480), marginTop: '3.2rem'}}>
                <BackButton returnButton>Back to previous page</BackButton>

                <Banner type="information" title="We could not find this page"/>
            </StackItem>
        </Stack>
    </div>;
