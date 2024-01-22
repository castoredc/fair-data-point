import React, { Component } from 'react';
import CatalogForm from 'components/Form/Admin/CatalogForm';
import { AuthorizedRouteComponentProps } from 'components/Route';
import SelectPage from 'components/SelectPage';

interface AddCatalogProps extends AuthorizedRouteComponentProps {}

export default class AddCatalog extends Component<AddCatalogProps> {
    render() {
        const { history } = this.props;
        return (
            <SelectPage
                title="Add a catalog"
                backButton={{
                    label: "Back to catalogs",
                    to: "/dashboard/catalogs"
                }}
            >
                <CatalogForm history={history} />
            </SelectPage>
        );
    }
}
