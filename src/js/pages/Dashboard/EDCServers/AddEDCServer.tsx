import React, {Component} from "react";
import CatalogForm from "components/Form/Admin/CatalogForm";
import DocumentTitle from "components/DocumentTitle";
import {toRem} from "@castoredc/matter-utils";
import {Heading, Stack, StackItem} from "@castoredc/matter";
import BackButton from "components/BackButton";
import {AuthorizedRouteComponentProps} from "components/Route";

interface AddEDCServerProps extends AuthorizedRouteComponentProps {
}

export default class AddEDCServer extends Component<AddEDCServerProps> {
    render() {
        const {history} = this.props;
        return <div style={{marginLeft: 'auto', marginRight: 'auto'}}>
            <DocumentTitle title="Add an EDC server"/>

            <Stack distribution="center">
                <StackItem style={{width: toRem(480), marginTop: '3.2rem'}}>
                    <BackButton to="/dashboard/edc-servers">Back to EDC servers</BackButton>

                    <Heading type="Section">Add an EDC server</Heading>

                    <CatalogForm history={history}/>
                </StackItem>
            </Stack>
        </div>;
    }
}
