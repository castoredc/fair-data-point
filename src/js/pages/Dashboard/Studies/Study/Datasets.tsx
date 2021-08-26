import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../../components/ToastContent";
import {Button, Heading, LoadingOverlay, Stack} from "@castoredc/matter";
import ListItem from "components/ListItem";
import DataGridHelper from "components/DataTable/DataGridHelper";
import * as H from "history";
import {localizedText} from "../../../../util";

interface DatasetsProps {
    studyId: string,
    history: H.History;
}

interface DatasetsState {
    datasets: any,
    isLoading: boolean,
    pagination: any,
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
        const {studyId} = this.props;
        this.setState({
            isLoading: true,
        });

        axios.get('/api/study/' + studyId + '/dataset')
            .then((response) => {
                this.setState({
                    datasets: response.data.results,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoading: false
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the datasets';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    handleCreate = () => {
        const { studyId, history } = this.props;

        this.setState({
            isLoading: true
        });

        axios.post('/api/study/' + studyId + '/dataset')
            .then((response) => {
                this.setState({
                    isLoading: false
                });

                history.push(`/dashboard/studies/${studyId}/datasets/${response.data.slug}`);
            })
            .catch((error) => {
                this.setState({
                    isLoading: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while creating a new dataset';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        const {isLoading, datasets} = this.state;
        const {studyId} = this.props;

        return <div>
            {isLoading && <LoadingOverlay accessibleLabel="Loading studies"/>}

            <Stack distribution="trailing" alignment="end">
                <Button icon="add" className="AddButton" disabled={isLoading} onClick={this.handleCreate}>New dataset</Button>
            </Stack>

            <div>
                {datasets.length === 0 && <div className="NoResults">This study does not have datasets.</div>}

                {datasets.map((dataset) => {
                    return <ListItem
                        selectable={false}
                        link={`/dashboard/studies/${studyId}/datasets/${dataset.slug}`} title={dataset.hasMetadata ? localizedText(dataset.metadata.title, 'en') : 'Untitled dataset'}
                    />
                })}
            </div>
        </div>;
    }
}
