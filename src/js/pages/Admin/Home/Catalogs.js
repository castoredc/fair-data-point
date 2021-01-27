import React, {Component} from "react";
import axios from "axios";
import {localizedText} from "../../../util";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {Button, CellText, DataGrid, Stack, ViewHeader} from "@castoredc/matter";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import AddCatalogModal from "../../../modals/AddCatalogModal";
import DocumentTitle from "../../../components/DocumentTitle";
import DataGridContainer from "../../../components/DataTable/DataGridContainer";

export default class Catalogs extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            hasError:  false,
            catalogs:  {},
            showModal: false,
        };
    }

    componentDidMount() {
        axios.get('/api/catalog')
            .then((response) => {
                this.setState({
                    catalogs:  response.data,
                    isLoading: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the catalogs';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    }

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

    onRowClick = (rowId) => {
        const {catalogs} = this.state;
        const {history} = this.props;

        const catalog = catalogs[rowId];
        history.push(`/admin/catalog/${catalog.slug}`)
    };

    render() {
        const {catalogs, isLoading, showModal} = this.state;

        if (isLoading) {
            return <InlineLoader/>;
        }

        const columns = [
            {
                Header: 'Title',
                accessor: 'title',
                resizable: true,
                template: 'text',
            },
            {
                Header: 'Description',
                accessor: 'description',
                resizable: true,
                template: 'text',
            },
        ];

        const rows = catalogs.map((item) => {
            return {
                title: <CellText>
                    {item.hasMetadata ? localizedText(item.metadata.title, 'en') : '(no title)'}
                </CellText>,
                description: <CellText>
                    {item.hasMetadata ? localizedText(item.metadata.description, 'en') : ''}
                </CellText>,
            };
        });

        return <div className="PageContainer">
            <DocumentTitle title="FDP Admin | Catalogs"/>
            <AddCatalogModal
                show={showModal}
                handleClose={this.closeModal}
            />
            <div className="Page">
                <div className="PageTitle">
                    <ViewHeader>Catalogs</ViewHeader>
                </div>

                <div className="PageBody">
                    <div className="PageButtons">
                        <Stack distribution="trailing" alignment="end">
                            <Button icon="add" onClick={this.openModal}>New catalog</Button>
                        </Stack>
                    </div>

                    <DataGridContainer fullHeight isLoading={isLoading}>
                        <DataGrid
                            accessibleName="Catalogs"
                            emptyStateContent="No catalogs found"
                            onClick={this.onRowClick}
                            rows={rows}
                            columns={columns}
                        />
                    </DataGridContainer>
                </div>
            </div>
        </div>;
    }
}