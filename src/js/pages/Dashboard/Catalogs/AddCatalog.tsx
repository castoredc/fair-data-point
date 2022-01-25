import React, {Component} from "react";
import CatalogForm from "components/Form/Admin/CatalogForm";
import DocumentTitle from "components/DocumentTitle";
import {toRem} from "@castoredc/matter-utils";
import {Heading, Stack, StackItem} from "@castoredc/matter";
import BackButton from "components/BackButton";
import {AuthorizedRouteComponentProps} from "components/Route";

interface AddCatalogProps extends AuthorizedRouteComponentProps {
}

export default class AddCatalog extends Component<AddCatalogProps> {
    render() {
        const {history} = this.props;
        return <div style={{marginLeft: 'auto', marginRight: 'auto'}}>
            <DocumentTitle title="Add a catalog"/>

            <Stack distribution="center">
                <StackItem style={{width: toRem(480), marginTop: '3.2rem'}}>
                    <BackButton to="/dashboard/catalogs">Back to catalogs</BackButton>

                    <Heading type="Section">Add a catalog</Heading>

                    <CatalogForm history={history}/>
                </StackItem>
            </Stack>
        </div>;
    }
}
