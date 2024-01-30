import React, { Component } from 'react';
import MetadataModelForm from 'components/Form/DataSpecification/MetadataModelForm';
import { AuthorizedRouteComponentProps } from 'components/Route';
import SelectPage from 'components/SelectPage';

interface AddMetadataModelProps extends AuthorizedRouteComponentProps {}

export default class AddMetadataModel extends Component<AddMetadataModelProps> {
    render() {
        const { history } = this.props;
        return (
            <SelectPage
                title="Add a metadata model"
                backButton={{
                    label: "Back to metadata models",
                    to: "/dashboard/metadata-models"
                }}
            >
                <MetadataModelForm history={history} />
            </SelectPage>
        );
    }
}
