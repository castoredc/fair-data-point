import React, { Component } from 'react';
import CatalogForm from 'components/Form/Admin/CatalogForm';
import { AuthorizedRouteComponentProps } from 'components/Route';
import SelectPage from 'components/SelectPage';

interface AddCatalogProps extends AuthorizedRouteComponentProps {
}

class AddCatalog extends Component<AddCatalogProps> {
    render() {
        const { history } = this.props;
        return (
            <SelectPage
                title="Add a catalog"
                backButton={{
                    label: 'Back to catalogs',
                    to: '/dashboard/catalogs',
                }}
                history={history}
            >
                <CatalogForm history={history} />
            </SelectPage>
        );
    }
}

export default AddCatalog;