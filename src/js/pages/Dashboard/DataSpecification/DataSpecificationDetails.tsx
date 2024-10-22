import { AuthorizedRouteComponentProps } from 'components/Route';
import React, { FC } from 'react';
import DataModelForm from 'components/Form/DataSpecification/DataModelForm';
import MetadataModelForm from 'components/Form/DataSpecification/MetadataModelForm';

interface DataSpecificationProps extends AuthorizedRouteComponentProps {
    type: string;
    dataSpecification: any;
}

const DataSpecificationDetails: FC<DataSpecificationProps> = ({ type, dataSpecification, history }) => {
    if (type === 'data-model') {
        return <DataModelForm history={history} dataModel={dataSpecification} />;
    }

    if (type === 'metadata-model') {
        return <MetadataModelForm history={history} metadataModel={dataSpecification} />;
    }

    return null;
};

export default DataSpecificationDetails;
