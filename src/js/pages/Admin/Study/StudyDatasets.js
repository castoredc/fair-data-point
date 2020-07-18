import React, {Component} from "react";
import DatasetsDataTable from "../../../components/DataTable/DatasetsDataTable";
import {Button, Stack} from "@castoredc/matter";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import {Redirect} from "react-router-dom";

export default class StudyDatasets extends Component {
    constructor(props) {
        super(props);
        this.state = {
            newDataset: null,
            isLoading: false
        };
    }

    handleCreate = () => {
        const { study } = this.props;

        this.setState({
            isLoading: true
        });

        axios.post('/api/study/' + study.id + '/dataset')
            .then((response) => {
                this.setState({
                    newDataset: response.data,
                    isLoading: false
                });
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
        const {study, history} = this.props;
        const {newDataset, isLoading} = this.state;

        if(newDataset !== null)
        {
            return <Redirect push to={'/admin/dataset/' + newDataset.slug} />;
        }

        return <div className="PageBody">
            <div className="PageButtons">
                <Stack distribution="trailing" alignment="end">
                        <Button icon="add" className="AddButton" disabled={isLoading} onClick={this.handleCreate}>New dataset</Button>
                </Stack>
            </div>

            <DatasetsDataTable
                history={history}
                study={study}
            />
        </div>;
    }
}