import React, { Component } from 'react';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Button, LoadingOverlay, Stack } from '@castoredc/matter';
import ListItem from 'components/ListItem';
import DataGridHelper from 'components/DataTable/DataGridHelper';
import { localizedText } from '../../../util';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { isGranted } from 'utils/PermissionHelper';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';

interface DistributionsProps extends AuthorizedRouteComponentProps {}

interface DistributionsState {
    distributions: any;
    isLoading: boolean;
    pagination: any;
}

export default class Distributions extends Component<DistributionsProps, DistributionsState> {
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
        const { match } = this.props;
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
                toast.error(<ToastItem type="error" title={message} />);
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

                <Stack distribution="trailing" alignment="end">
                    <Button icon="add" className="AddButton" disabled={isLoading} onClick={() => history.push(`${mainUrl}/distributions/add`)}>
                        New distribution
                    </Button>
                </Stack>

                <div>
                    {distributions.length === 0 && <div className="NoResults">This study does not have distributions.</div>}

                    {distributions.map(distribution => {
                        return (
                            <ListItem
                                selectable={false}
                                disabled={!isGranted('edit', distribution.permissions)}
                                link={`${mainUrl}/distributions/${distribution.slug}`}
                                title={distribution.hasMetadata ? localizedText(distribution.metadata.title, 'en') : 'Untitled distribution'}
                            />
                        );
                    })}
                </div>
            </PageBody>
        );
    }
}
