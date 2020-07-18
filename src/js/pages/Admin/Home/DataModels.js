import React, {Component} from "react";
import axios from "axios";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import {Button, DataTable, Stack, ViewHeader} from "@castoredc/matter";
import AddDataModelModal from "../../../modals/AddDataModelModal";

export default class DataModels extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDataModels: true,
            hasLoadedDataModels: false,
            dataModels:          [],
            showModal:           false,
        };
    }

    componentDidMount() {
        this.getDataModels();
    }

    getDataModels = () => {
        this.setState({
            isLoadingDataModels: true,
        });

        axios.get('/api/model')
            .then((response) => {
                this.setState({
                    dataModels:          response.data,
                    isLoadingDataModels: false,
                    hasLoadedDataModels: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDataModels: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the data models';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    openModal = () => {
        this.setState({
            showModal: true,
        });
    };

    closeModal = () => {
        this.setState({
            showModal: false,
        });
    };

    render() {
        const {dataModels, isLoadingDataModels, showModal} = this.state;
        const {history} = this.props;

        if (isLoadingDataModels) {
            return <InlineLoader/>;
        }

        return <div className="PageContainer">
            <AddDataModelModal
                show={showModal}
                handleClose={this.closeModal}
            />
            <div className="Page">
                <div className="PageTitle">
                    <ViewHeader>Data models</ViewHeader>
                </div>

                <div className="PageBody">
                    <div className="PageButtons">
                        <Stack distribution="trailing" alignment="end">
                            <Button icon="add" onClick={this.openModal}>New data model</Button>
                        </Stack>
                    </div>

                    <div className="SelectableDataTable FullHeightDataTable">
                        <div className="DataTableWrapper">
                            <DataTable
                                emptyTableMessage="No data models found"
                                highlightRowOnHover
                                cellSpacing="default"
                                onClick={(event, rowID, index) => {
                                    history.push(`/admin/model/${dataModels[index].id}`)
                                }}
                                rows={dataModels.map((item) => {
                                    return [item.title];
                                })}
                                structure={{
                                    title: {
                                        header:    'Title',
                                        resizable: true,
                                        template:  'text',
                                    },
                                }}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>;
    }
}