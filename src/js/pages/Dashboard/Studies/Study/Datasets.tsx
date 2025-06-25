import React, { Component } from 'react';
import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';
import LoadingOverlay from 'components/LoadingOverlay';
import DataGridHelper from 'components/DataTable/DataGridHelper';
import * as H from 'history';
import { localizedText } from '../../../../util';
import { isGranted } from 'utils/PermissionHelper';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';
import Stack from '@mui/material/Stack';
import { Box } from '@mui/material';
import DataGrid from 'components/DataTable/DataGrid';
import { GridColDef } from '@mui/x-data-grid';
import NoResults from 'components/NoResults';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface DatasetsProps extends ComponentWithNotifications {
    studyId: string;
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
        const { studyId, notifications } = this.props;
        this.setState({
            isLoading: true,
        });

        apiClient
            .get('/api/study/' + studyId + '/dataset')
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

    handleCreate = () => {
        const { studyId, history, notifications } = this.props;

        this.setState({
            isLoading: true,
        });

        apiClient
            .post('/api/study/' + studyId + '/dataset')
            .then(response => {
                this.setState({
                    isLoading: false,
                });

                history.push(`/dashboard/studies/${studyId}/datasets/${response.data.slug}`);
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while creating a new dataset';
                notifications.show(message, { variant: 'error' });
            });
    };

    render() {
        const { isLoading, datasets } = this.state;
        const { studyId, history } = this.props;

        const columns: GridColDef[] = [
            {
                headerName: 'Title',
                field: 'title',
                flex: 1,
                minWidth: 200,
            },
        ];

        const rows = datasets.map(dataset => ({
            id: dataset.id,
            title: dataset.hasMetadata ? localizedText(dataset.metadata.title, 'en') || 'Untitled dataset' : 'Untitled dataset',
            slug: dataset.slug,
        }));

        return (
            <PageBody>
                {isLoading && <LoadingOverlay accessibleLabel="Loading datasets" />}

                <Stack direction="row" justifyContent="flex-end" mb={2}>
                    <Button
                        startIcon={<AddIcon />}
                        disabled={isLoading}
                        onClick={this.handleCreate}
                        variant="contained"
                    >
                        New dataset
                    </Button>
                </Stack>

                <Box height={400} width="100%">
                    <DataGrid
                        disableRowSelectionOnClick
                        rows={rows}
                        columns={columns}
                        accessibleName="Datasets"
                        emptyStateContent={() => <NoResults>This study does not have datasets.</NoResults>}
                        onRowClick={(params) => {
                            history.push(`/dashboard/studies/${studyId}/datasets/${params.row.slug}`);
                        }}
                    />
                </Box>
            </PageBody>
        );
    }
}

export default withNotifications(Datasets);