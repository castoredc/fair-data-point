import React, { Component } from 'react';
import { Redirect, Route, Switch } from 'react-router-dom';
import DocumentTitle from 'components/DocumentTitle';
import { localizedText } from '../../../../util';
import Header from 'components/Layout/Dashboard/Header';
import Body from 'components/Layout/Dashboard/Body';
import Annotations from 'pages/Dashboard/Studies/Study/Annotations';
import { isGranted } from 'utils/PermissionHelper';
import Datasets from 'pages/Dashboard/Studies/Study/Datasets';
import SideBar from 'components/SideBar';
import NotFound from 'pages/ErrorPages/NotFound';
import { AuthorizedRouteComponentProps } from 'components/Route';
import NoPermission from 'pages/ErrorPages/NoPermission';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';
import MetadataForm from 'components/Form/Metadata/MetadataForm';
import Button from '@mui/material/Button';
import OpenInNewIcon from '@mui/icons-material/OpenInNew';
import LoadingOverlay from 'components/LoadingOverlay';
import Alert from '@mui/material/Alert';
import DashboardPage from 'components/Layout/Dashboard/DashboardPage';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import DescriptionIcon from '@mui/icons-material/Description';
import DatasetIcon from '@mui/icons-material/Dataset';
import MoreIcon from '@mui/icons-material/More';

interface StudyProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
}

interface StudyState {
    study: any;
    isLoading: boolean;
}

class Study extends Component<StudyProps, StudyState> {
    constructor(props) {
        super(props);

        this.state = {
            study: null,
            isLoading: true,
        };
    }

    getStudy = () => {
        this.setState({
            isLoading: true,
        });

        const { match, notifications } = this.props;

        apiClient
            .get('/api/study/' + match.params.study)
            .then(response => {
                this.setState({
                    study: response.data,
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
                    notifications.show('An error occurred while loading the study', { variant: 'error' });
                }
            });
    };

    componentDidMount() {
        this.getStudy();
    }

    render() {
        const { history, location, match } = this.props;
        const { isLoading, study } = this.state;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading study" />;
        }

        if (!isGranted('edit', study.permissions)) {
            return <NoPermission text="You do not have permission to edit this study" />;
        }

        let title = study.hasMetadata ? localizedText(study.metadata.title, 'en') : study.name;

        if (title === '') {
            title = 'Untitled study';
        }

        return (
            <DashboardPage>
                <DocumentTitle title={title} />

                <SideBar
                    back={{
                        to: '/dashboard/studies',
                        title: 'Back to study list',
                    }}
                    location={location}
                    items={[
                        {
                            to: '/dashboard/studies/' + study.id + '/metadata',
                            exact: true,
                            title: 'Metadata',
                            icon: <DescriptionIcon />,
                        },
                        {
                            type: 'separator',
                        },
                        {
                            to: '/dashboard/studies/' + study.id + '/annotations',
                            exact: true,
                            title: 'Annotations',
                            icon: <MoreIcon />,
                        },
                        {
                            type: 'separator',
                        },
                        {
                            to: '/dashboard/studies/' + study.id + '/datasets',
                            exact: true,
                            title: 'Datasets',
                            icon: <DatasetIcon />,
                        },
                    ]}
                    history={history}
                />

                <Body>
                    <Header title={title}>
                        {study.hasMetadata && (
                            <Button variant="text" startIcon={<OpenInNewIcon />} href={`/study/${study.slug}`}
                                    target="_blank">
                                View
                            </Button>
                        )}
                    </Header>

                    <Switch>
                        <Redirect exact from="/dashboard/studies/:study" to="/dashboard/studies/:study/metadata" />
                        <Route
                            path="/dashboard/studies/:study/metadata"
                            exact
                            render={props => (
                                <PageBody>
                                    <MetadataForm type="study" object={study} onCreate={this.getStudy}
                                                  onSave={this.getStudy} />
                                </PageBody>
                            )}
                        />
                        <Route
                            path="/dashboard/studies/:study/annotations"
                            exact
                            render={props =>
                                isGranted('edit_source_system', study.permissions) ? (
                                    <PageBody>
                                        <Annotations studyId={study.id} />
                                    </PageBody>
                                ) : (
                                    <PageBody>
                                        <Alert severity="error">
                                            You do not have permission to access this study in Castor
                                        </Alert>
                                    </PageBody>
                                )
                            }
                        />
                        <Route
                            path="/dashboard/studies/:study/datasets"
                            exact
                            render={props => (
                                <PageBody>
                                    <Datasets studyId={study.id} history={history} />
                                </PageBody>
                            )}
                        />
                        <Route component={NotFound} />
                    </Switch>
                </Body>
            </DashboardPage>
        );
    }
}

export default withNotifications(Study);