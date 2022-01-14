import React, {Component} from "react";
import {RouteComponentProps} from 'react-router-dom';
import DocumentTitle from "components/DocumentTitle";
import {toRem} from "@castoredc/matter-utils";
import {Heading, Stack, StackItem} from "@castoredc/matter";
import DataModelForm from "components/Form/Data/DataModelForm";
import BackButton from "components/BackButton";

interface AddDataModelProps extends RouteComponentProps<any> {
}

export default class AddDataModel extends Component<AddDataModelProps> {
    render() {
        const {history} = this.props;
        return <div style={{marginLeft: 'auto', marginRight: 'auto'}}>
            <DocumentTitle title="Add a data model" />

            <Stack distribution="center">
                <StackItem style={{width: toRem(480), marginTop: '3.2rem'}}>
                    <BackButton to="/dashboard/data-models">Back to data models</BackButton>

                    <Heading type="Section">Add a data model</Heading>

                    <DataModelForm history={history} />
                </StackItem>
            </Stack>
        </div>;
    }
}
