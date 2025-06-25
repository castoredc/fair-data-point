import React, { Component } from 'react';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Button, LoadingOverlay, Stack } from '@castoredc/matter';
import ListItem from 'components/ListItem';
import DataGridHelper from 'components/DataTable/DataGridHelper';
import * as H from 'history';
import { localizedText } from '../../../../util';
import { isGranted } from 'utils/PermissionHelper';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';

interface DatasetsProps {
    studyId: string;
    history: H.History;
}

interface DatasetsState {
    datasets: any;
    isLoading: boolean;
    pagination: any;
}

export default class Datasets extends Component<DatasetsProps, DatasetsState> {
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
        const { studyId } = this.props;
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
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    handleCreate = () => {
        const { studyId, history } = this.props;

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
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    render() {
        const { isLoading, datasets } = this.state;
        const { studyId } = this.props;

        return (
            <PageBody>
                {isLoading && <LoadingOverlay accessibleLabel="Loading studies" />}

                <Stack distribution="trailing" alignment="end">
                    <Button icon="add" className="AddButton" disabled={isLoading} onClick={this.handleCreate}>
                        New dataset
                    </Button>
                </Stack>

                <div>
                    {datasets.length === 0 && <div className="NoResults">This study does not have datasets.</div>}

                    {datasets.map(dataset => {
                        let title = dataset.hasMetadata ? localizedText(dataset.metadata.title, 'en') : 'Untitled dataset';

                        if(title === '') {
                            title = 'Untitled dataset'
                        }

                        return (
                            <ListItem
                                key={dataset.id}
                                selectable={false}
                                disabled={!isGranted('edit', dataset.permissions)}
                                link={`/dashboard/studies/${studyId}/datasets/${dataset.slug}`}
                                title={title}
                            />
                        );
                    })}
                </div>
            </PageBody>
        );
    }
}
