import React, { Component } from 'react';
import { localizedText } from '../../../util';
import { Heading, LoadingOverlay } from '@castoredc/matter';
import DocumentTitle from 'components/DocumentTitle';
import FAIRDataPointMetadataForm from 'components/Form/Metadata/FAIRDataPointMetadataForm';
import { toast } from 'react-toastify';
import ToastContent from 'components/ToastContent';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';

interface FAIRDataPointProps extends AuthorizedRouteComponentProps {}

interface FAIRDataPointState {
    fdp: any;
    isLoading: boolean;
}

export default class FAIRDataPoint extends Component<FAIRDataPointProps, FAIRDataPointState> {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            fdp: null,
        };
    }

    componentDidMount() {
        this.getFairDataPoint();
    }

    getFairDataPoint = () => {
        this.setState({
            isLoading: true,
        });

        apiClient
            .get('/api/fdp')
            .then(response => {
                this.setState({
                    fdp: response.data,
                    isLoading: false,
                });
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== 'undefined') {
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred while loading the FAIR Data Point information" />);
                }
            });
    };

    render() {
        const { fdp, isLoading } = this.state;

        const title = fdp ? (fdp.hasMetadata ? localizedText(fdp.metadata.title, 'en') : 'FAIR Data Point') : 'FAIR Data Point';

        return (
            <PageBody>
                <DocumentTitle title="FAIR Data Point" />

                {isLoading && <LoadingOverlay accessibleLabel="Loading FAIR Data Point information" />}

                <Heading type="Section">{title}</Heading>

                {fdp && <FAIRDataPointMetadataForm fdp={fdp} onSave={this.getFairDataPoint} />}
            </PageBody>
        );
    }
}
