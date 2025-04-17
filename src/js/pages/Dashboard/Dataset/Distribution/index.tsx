import React, { Component } from 'react';
import Button from '@mui/material/Button';
import LoadingOverlay from 'components/LoadingOverlay';
import { Route, Switch } from 'react-router-dom';
import DocumentTitle from 'components/DocumentTitle';
import { localizedText } from '../../../../util';
import Header from 'components/Layout/Dashboard/Header';
import Body from 'components/Layout/Dashboard/Body';
import SideBar from 'components/SideBar';
import NotFound from 'pages/ErrorPages/NotFound';
import DistributionSubset from 'pages/Dashboard/Dataset/Distribution/DistributionSubset';
import DistributionLog from 'pages/Dashboard/Dataset/Distribution/DistributionLog';
import DistributionLogs from 'pages/Dashboard/Dataset/Distribution/DistributionLogs';
import Details from 'pages/Dashboard/Dataset/Distribution/Details';
import DistributionContentsCsv from 'pages/Dashboard/Dataset/Distribution/DistributionContentsCsv';
import DistributionContentsRdf from 'pages/Dashboard/Dataset/Distribution/DistributionContentsRdf';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { isGranted } from 'utils/PermissionHelper';
import Permissions from 'pages/Dashboard/Dataset/Distribution/Permissions';
import NoPermission from 'pages/ErrorPages/NoPermission';
import { apiClient } from 'src/js/network';
import MetadataForm from 'components/Form/Metadata/MetadataForm';
import PageBody from 'components/Layout/Dashboard/PageBody';
import OpenInNewIcon from '@mui/icons-material/OpenInNew';
import DashboardPage from 'components/Layout/Dashboard/DashboardPage';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import DescriptionIcon from '@mui/icons-material/Description';
import GroupsIcon from '@mui/icons-material/Groups';
import FileOpenIcon from '@mui/icons-material/FileOpen';
import HighlightAltIcon from '@mui/icons-material/HighlightAlt';
import SyncAltIcon from '@mui/icons-material/SyncAlt';
import ListIcon from '@mui/icons-material/List';
import NotesIcon from '@mui/icons-material/Notes';

interface DistributionProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
    dataset: any;
}

interface DistributionState {
    distribution: any;
    contents: any;
    isLoading: boolean;
}

class Distribution extends Component<DistributionProps, DistributionState> {
    constructor(props) {
        super(props);

        this.state = {
            distribution: null,
            contents: null,
            isLoading: true,
        };
    }

    getDistribution = () => {
        this.setState({
            isLoading: true,
        });

        const { match, notifications } = this.props;

        apiClient
            .get('/api/dataset/' + match.params.dataset + '/distribution/' + match.params.distribution)
            .then(response => {
                this.setState(
                    {
                        distribution: response.data,
                    },
                    this.getContents,
                );
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred while loading your distribution', { variant: 'error' });
                }
            });
    };

    getContents = () => {
        const { match, notifications } = this.props;

        apiClient
            .get('/api/dataset/' + match.params.dataset + '/distribution/' + match.params.distribution + '/contents')

            .then(response => {
                this.setState({
                    contents: response.data,
                    isLoading: false,
                });
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the distribution';
                notifications.show(message, { variant: 'error' });
            });
    };

    componentDidMount() {
        this.getDistribution();
    }

    render() {
        const { history, location, match, user } = this.props;
        const { isLoading, distribution, contents } = this.state;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading distribution" />;
        }

        if (!isGranted('edit', distribution.permissions)) {
            return <NoPermission text="You do not have permission to edit this distribution" />;
        }

        const catalog = match.params.catalog;
        const study = match.params.study;
        const dataset = match.params.dataset;

        let title = distribution.hasMetadata ? localizedText(distribution.metadata.title, 'en') : 'Untitled distribution';

        if (title === '') {
            title = 'Untitled distribution';
        }

        const mainUrl = match.params.study
            ? '/dashboard/studies/' + match.params.study + '/datasets/' + dataset
            : '/dashboard/catalogs/' + match.params.catalog + '/datasets/' + dataset;

        let sidebarItems = [
            {
                to: mainUrl + '/distributions/' + distribution.slug,
                exact: true,
                title: 'Distribution',
                icon: <FileOpenIcon />,
            },
            {
                to: mainUrl + '/distributions/' + distribution.slug + '/metadata',
                exact: true,
                title: 'Metadata',
                icon: <DescriptionIcon />,
            },
            ...(isGranted('manage', distribution.permissions)
                ? [
                    {
                        to: mainUrl + '/distributions/' + distribution.slug + '/permissions',
                        exact: true,
                        title: 'Permissions',
                        icon: <GroupsIcon />,
                    },
                ]
                : []),
            {
                to: mainUrl + '/distributions/' + distribution.slug + '/subset',
                exact: true,
                title: 'Subset',
                icon: <HighlightAltIcon />,
            },
            {
                type: 'separator',
            },
        ];

        if (distribution.type === 'rdf') {
            sidebarItems.push({
                to: mainUrl + '/distributions/' + distribution.slug + '/contents',
                exact: true,
                title: 'Mappings',
                icon: <SyncAltIcon />,
            });

            if (distribution.cached) {
                sidebarItems.push({
                    to: mainUrl + '/distributions/' + distribution.slug + '/log',
                    exact: true,
                    title: 'Log',
                    icon: <NotesIcon />,
                });
            }
        } else if (distribution.type === 'csv') {
            sidebarItems.push({
                to: mainUrl + '/distributions/' + distribution.slug + '/contents',
                exact: true,
                title: 'Contents',
                icon: <ListIcon />,
            });
        }

        return (
            <DashboardPage>
                <DocumentTitle title={title} />

                <SideBar
                    back={{
                        to: mainUrl,
                        title: 'Back to dataset',
                    }}
                    location={location}
                    items={sidebarItems}
                    history={history}
                />

                <Body>
                    <Header title={title} fullWidth={true}>
                        <Button
                            variant="text"
                            startIcon={<OpenInNewIcon />}
                            href={`/fdp/dataset/${dataset}/distribution/${distribution.slug}`}
                            target="_blank"
                        >
                            View
                        </Button>
                    </Header>

                    <Switch>
                        <Route
                            path={[
                                '/dashboard/studies/:study/datasets/:dataset/distributions/:distribution',
                                '/dashboard/catalogs/:catalog/datasets/:dataset/distributions/:distribution',
                            ]}
                            exact
                            render={props => (
                                <Details {...props} user={user} catalog={catalog} study={study} dataset={dataset}
                                         distribution={distribution} />
                            )}
                        />

                        <Route
                            path={[
                                '/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/metadata',
                                '/dashboard/catalogs/:catalog/datasets/:dataset/distributions/:distribution/metadata',
                            ]}
                            exact
                            render={props => (
                                <PageBody>
                                    <MetadataForm
                                        type="distribution"
                                        object={distribution}
                                        onCreate={this.getDistribution}
                                        onSave={this.getDistribution}
                                    />
                                </PageBody>
                            )}
                        />

                        <Route
                            path={[
                                '/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/permissions',
                                '/dashboard/catalogs/:catalog/datasets/:dataset/distributions/:distribution/permissions',
                            ]}
                            exact
                            render={() => (
                                <Permissions contents={contents} distribution={distribution}
                                             getDistribution={this.getDistribution} user={user} />
                            )}
                        />

                        <Route
                            path={[
                                '/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/contents',
                                '/dashboard/catalogs/:catalog/datasets/:dataset/distributions/:distribution/contents',
                            ]}
                            exact
                            render={() => {
                                if (distribution.type === 'csv') {
                                    return (
                                        <DistributionContentsCsv
                                            contents={contents}
                                            catalog={catalog}
                                            distribution={distribution}
                                            dataset={dataset}
                                        />
                                    );
                                } else if (distribution.type === 'rdf') {
                                    return <DistributionContentsRdf dataset={dataset} distribution={distribution} />;
                                }

                                return <div className="NoResults">This distribution does not have contents.</div>;
                            }}
                        />

                        <Route
                            path={[
                                '/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/log/:log',
                                '/dashboard/catalogs/:catalog/datasets/:dataset/distributions/:distribution/log/:log',
                            ]}
                            exact
                            render={props => <DistributionLog {...props} dataset={dataset}
                                                              distribution={distribution} />}
                        />

                        <Route
                            path={[
                                '/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/log',
                                '/dashboard/catalogs/:catalog/datasets/:dataset/distributions/:distribution/log',
                            ]}
                            exact
                            render={props => (
                                <DistributionLogs {...props} catalog={catalog} study={study} dataset={dataset}
                                                  distribution={distribution} />
                            )}
                        />

                        <Route
                            path={[
                                '/dashboard/studies/:study/datasets/:dataset/distributions/:distribution/subset',
                                '/dashboard/catalogs/:catalog/datasets/:dataset/distributions/:distribution/subset',
                            ]}
                            exact
                            render={props => <DistributionSubset {...props} dataset={dataset}
                                                                 distribution={distribution} />}
                        />

                        <Route component={NotFound} />
                    </Switch>
                </Body>
            </DashboardPage>
        );
    }
}

export default withNotifications(Distribution);