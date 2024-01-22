import React, { Component } from 'react';
import { localizedText } from '../../../util';
import { LoadingOverlay } from '@castoredc/matter';
import DocumentTitle from 'components/DocumentTitle';
import FAIRDataPointMetadataForm from 'components/Form/Metadata/FAIRDataPointMetadataForm';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { apiClient } from 'src/js/network';
import DashboardTab from 'components/Layout/DashboardTab';
import DashboardTabHeader from 'components/Layout/DashboardTab/DashboardTabHeader';

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
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred while loading the FAIR Data Point information" />);
                }
            });
    };

    render() {
        const { fdp, isLoading } = this.state;

        const title = fdp ? (fdp.hasMetadata ? localizedText(fdp.metadata.title, 'en') : 'FAIR Data Point') : 'FAIR Data Point';

        return (
            <DashboardTab>
                <DocumentTitle title="FAIR Data Point" />

                {isLoading && <LoadingOverlay accessibleLabel="Loading FAIR Data Point information" />}

                <DashboardTabHeader type="Section" title={title} />

                {fdp && <FAIRDataPointMetadataForm fdp={fdp} onSave={this.getFairDataPoint} />}
            </DashboardTab>
        );
    }
}
