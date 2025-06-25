import React, { Component } from 'react';
import DataModelForm from 'components/Form/DataSpecification/DataModelForm';
import { AuthorizedRouteComponentProps } from 'components/Route';
import SelectPage from 'components/SelectPage';

interface AddDataModelProps extends AuthorizedRouteComponentProps {}

export default class AddDataModel extends Component<AddDataModelProps> {
    render() {
        const { history } = this.props;
        return (
            <SelectPage
                title="Add a data model"
                backButton={{
                    label: 'Back to data models',
                    to: '/dashboard/data-models',
                }}
                history={history}
            >
                <DataModelForm history={history} />
            </SelectPage>
        );
    }
}
