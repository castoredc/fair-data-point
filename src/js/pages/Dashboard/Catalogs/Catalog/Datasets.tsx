import React, { Component } from 'react';
import Button from '@mui/material/Button';
import LoadingOverlay from 'components/LoadingOverlay';
import ListItem from 'components/ListItem';
import DataGridHelper from 'components/DataTable/DataGridHelper';
import * as H from 'history';
import { localizedText } from '../../../../util';
import { isGranted } from 'utils/PermissionHelper';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';
import AddIcon from '@mui/icons-material/Add';
import Stack from '@mui/material/Stack';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface DatasetsProps extends ComponentWithNotifications {
    catalog: string;
    history: H.History;
}

interface DatasetsState {
    datasets: any;
    isLoading: boolean;
    pagination: any;
}

class Datasets extends Component<DatasetsProps, DatasetsState> {
    constructor(props) {
        super(props);

        this.state = {
            isLoading: true,
            datasets: [],
            pagination: DataGridHelper.getDefaultState(25),
        };
    }

    componentDidMount() {
        this.getDatasets();
    }

    getDatasets = () => {
        const { catalog, notifications } = this.props;
        this.setState({
            isLoading: true,
        });

        apiClient
            .get('/api/catalog/' + catalog + '/dataset')
            .then(response => {
                this.setState({
                    datasets: response.data.results,
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
                        : 'An error occurred while loading the datasets';
                notifications.show(message, { variant: 'error' });
            });
    };

    render() {
        const { isLoading, datasets } = this.state;
        const { catalog, history } = this.props;

        return (
            <PageBody>
                {isLoading && <LoadingOverlay accessibleLabel="Loading studies" />}

                <Stack direction="row" sx={{ justifyContent: 'flex-end' }}>
                    <Button
                        startIcon={<AddIcon />}
                        variant="contained"
                        disabled={isLoading}
                        onClick={() => history.push(`/dashboard/catalogs/${catalog}/datasets/add`)}
                    >
                        Add dataset
                    </Button>
                </Stack>

                <div>
                    {datasets.length === 0 && <div className="NoResults">This study does not have datasets.</div>}

                    {datasets.map(dataset => {
                        let title = dataset.hasMetadata ? localizedText(dataset.metadata.title, 'en') : 'Untitled dataset';

                        if (title === '') {
                            title = 'Untitled dataset';
                        }

                        return (
                            <ListItem
                                key={dataset.id}
                                selectable={false}
                                disabled={!isGranted('edit', dataset.permissions)}
                                link={`/dashboard/catalogs/${catalog}/datasets/${dataset.slug}`}
                                title={title}
                            />
                        );
                    })}
                </div>
            </PageBody>
        );
    }
}

export default withNotifications(Datasets);