import React, { Component } from 'react';
import { localizedText } from '../../../util';
import LoadingOverlay from 'components/LoadingOverlay';
import DocumentTitle from 'components/DocumentTitle';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { apiClient } from 'src/js/network';
import MetadataForm from 'components/Form/Metadata/MetadataForm';
import DashboardPage from 'components/Layout/Dashboard/DashboardPage';
import DashboardSideBar from 'components/SideBar/DashboardSideBar';
import Body from 'components/Layout/Dashboard/Body';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import Header from 'components/Layout/Dashboard/Header';
import PageBody from 'components/Layout/Dashboard/PageBody';

interface FAIRDataPointProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
}

interface FAIRDataPointState {
    fdp: any;
    isLoading: boolean;
}

class FAIRDataPoint extends Component<FAIRDataPointProps, FAIRDataPointState> {
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
        const { notifications } = this.props;

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
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred while loading the FAIR Data Point information', { variant: 'error' });
                }
            });
    };

    render() {
        const { fdp, isLoading } = this.state;
        const { location, history, user } = this.props;

        const title = fdp ? (fdp.hasMetadata ? localizedText(fdp.metadata.title, 'en') : 'FAIR Data Point') : 'FAIR Data Point';

        return (
            <DashboardPage>
                <DocumentTitle title="FAIR Data Point" />

                {isLoading && <LoadingOverlay accessibleLabel="Loading FAIR Data Point information" />}

                <DashboardSideBar location={location} history={history} user={user} />

                <Body>
                    <Header title={title} />
                    <PageBody>
                        {fdp && <MetadataForm type="fdp" object={fdp} onSave={this.getFairDataPoint}
                                              onCreate={this.getFairDataPoint} />}
                    </PageBody>
                </Body>
            </DashboardPage>
        );
    }
}

export default withNotifications(FAIRDataPoint);