import React, { Component } from 'react';
import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';
import LoadingOverlay from 'components/LoadingOverlay';
import ListItem from 'components/ListItem';
import DataGridHelper from 'components/DataTable/DataGridHelper';
import { localizedText } from '../../../util';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { isGranted } from 'utils/PermissionHelper';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';
import Stack from '@mui/material/Stack';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface DistributionsProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
}

interface DistributionsState {
    distributions: any;
    isLoading: boolean;
    pagination: any;
}

class Distributions extends Component<DistributionsProps, DistributionsState> {
    constructor(props) {
        super(props);

        this.state = {
            isLoading: true,
            distributions: [],
            pagination: DataGridHelper.getDefaultState(25),
        };
    }

    componentDidMount() {
        this.getDistributions();
    }

    getDistributions = () => {
        const { match, notifications } = this.props;
        this.setState({
            isLoading: true,
        });

        apiClient
            .get('/api/dataset/' + match.params.dataset + '/distribution')
            .then(response => {
                this.setState({
                    distributions: response.data.results,
                    pagination: DataGridHelper.parseResults(response.data),
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
                        : 'An error occurred while loading the distributions';
                notifications.show(message, { variant: 'error' });
            });
    };

    render() {
        const { isLoading, distributions } = this.state;
        const { match, history } = this.props;

        const mainUrl = match.params.study
            ? `/dashboard/studies/${match.params.study}/datasets/${match.params.dataset}`
            : `/dashboard/catalogs/${match.params.catalog}/datasets/${match.params.dataset}`;

        return (
            <PageBody>
                {isLoading && <LoadingOverlay accessibleLabel="Loading studies" />}

                <Stack direction="row" sx={{ justifyContent: 'flex-end' }}>
                    <Button
                        startIcon={<AddIcon />}
                        className="AddButton"
                        disabled={isLoading}
                        onClick={() => history.push(`${mainUrl}/distributions/add`)}
                        variant="contained"
                    >
                        New distribution
                    </Button>
                </Stack>

                <div>
                    {distributions.length === 0 &&
                        <div className="NoResults">This study does not have distributions.</div>}

                    {distributions.map(distribution => {
                        let title = distribution.hasMetadata ? localizedText(distribution.metadata.title, 'en') : 'Untitled distribution';

                        if (title === '') {
                            title = 'Untitled distribution';
                        }

                        return (
                            <ListItem
                                selectable={false}
                                disabled={!isGranted('edit', distribution.permissions)}
                                link={`${mainUrl}/distributions/${distribution.slug}`}
                                title={title}
                            />
                        );
                    })}
                </div>
            </PageBody>
        );
    }
}

export default withNotifications(Distributions);