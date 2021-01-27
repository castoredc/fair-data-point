import React, {Component} from "react";
import axios from "axios";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import {Button, CellText, DataGrid, Stack, ViewHeader} from "@castoredc/matter";
import AddDataDictionaryModal from "../../../modals/AddDataDictionaryModal";
import DocumentTitle from "../../../components/DocumentTitle";

export default class DataDictionaries extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDataDictionaries: true,
            hasLoadedDataDictionaries: false,
            dataDictionaries:          [],
            showModal:           false,
        };
    }

    componentDidMount() {
        this.getDataDictionaries();
    }

    getDataDictionaries = () => {
        this.setState({
            isLoadingDataDictionaries: true,
        });

        axios.get('/api/dictionary')
            .then((response) => {
                this.setState({
                    dataDictionaries:          response.data,
                    isLoadingDataDictionaries: false,
                    hasLoadedDataDictionaries: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDataDictionaries: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the data dictionaries';
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

    handleClick = (rowId) => {
        const {dataDictionaries} = this.state;
        const {history} = this.props;

        const dataDictionary = dataDictionaries[rowId];

        history.push(`/admin/dictionary/${dataDictionary.id}`);
    };

    render() {
        const {dataDictionaries, isLoadingDataDictionaries, showModal} = this.state;
        const {history} = this.props;

        if (isLoadingDataDictionaries) {
            return <InlineLoader/>;
        }

        const columns = [
            {
                Header: 'Title',
                accessor: 'title',
            }
        ];

        const rows = dataDictionaries.map((item) => {
            return {
                title: <CellText>{item.title}</CellText>,
            }
        });

        return <div className="PageContainer">
            <DocumentTitle title="FDP Admin | Data Dictionaries" />
            <AddDataDictionaryModal
                show={showModal}
                handleClose={this.closeModal}
            />
            <div className="Page">
                <div className="PageTitle">
                    <ViewHeader>Data dictionaries</ViewHeader>
                </div>

                <div className="PageBody">
                    <div className="PageButtons">
                        <Stack distribution="trailing" alignment="end">
                            <Button icon="add" onClick={this.openModal}>New data dictionary</Button>
                        </Stack>
                    </div>

                    <div className="SelectableDataTable FullHeightDataTable">
                        <div className="DataTableWrapper">
                            <DataGrid
                                accessibleName="Data dictionaries"
                                emptyStateContent="No data dictionaries found"
                                onClick={this.handleClick}
                                rows={rows}
                                columns={columns}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>;
    }
}