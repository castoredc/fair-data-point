import React, {Component} from "react";
import axios from "axios";
import {classNames, localizedText} from "../../../util";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {Button, DataTable, Stack, ViewHeader} from "@castoredc/matter";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import AddCatalogModal from "../../../modals/AddCatalogModal";
import DocumentTitle from "../../../components/DocumentTitle";

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

    onRowClick = (event, rowID, index) => {
        const {catalogs} = this.state;
        const {history} = this.props;

        if (typeof index !== "undefined") {
            const catalog = catalogs.find((item) => item.id === rowID);
            history.push(`/admin/catalog/${catalog.slug}`)
        }
    };

    render() {
        const {catalogs, isLoading, showModal} = this.state;

        if (isLoading) {
            return <InlineLoader/>;
        }

        const rows = new Map(catalogs.map((item) => {
            return [
                item.id,
                {
                    cells: [
                        item.hasMetadata ? localizedText(item.metadata.title, 'en') : '(no title)',
                        item.hasMetadata ? localizedText(item.metadata.description, 'en') : '',
                    ],
                }];
        }));

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

                    <div className={classNames('SelectableDataTable FullHeightDataTable', isLoading && 'Loading')}>
                        <div className="DataTableWrapper">
                            <DataTable
                                emptyTableMessage="No catalogs found"
                                highlightRowOnHover
                                cellSpacing="default"
                                onClick={this.onRowClick}
                                rows={rows}
                                structure={{
                                    title: {
                                        header:    'Name',
                                        resizable: true,
                                        template:  'text',
                                    },
                                    type:  {
                                        header:    'Description',
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