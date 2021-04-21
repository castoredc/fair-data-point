import React, {Component} from "react";
import axios from "axios";
import InlineLoader from "../LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import {CellText, DataGrid} from "@castoredc/matter";
import './DataTable.scss';
import DataGridHelper from "./DataGridHelper";
import DataGridContainer from "./DataGridContainer";
import {localizedText} from "../../util";

export default class CatalogsDataTable extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingCatalogs: true,
            hasLoadedCatalogs: false,
            catalogs: [],
            pagination: DataGridHelper.getDefaultState(25),
        };

        this.tableRef = React.createRef();
    }

    componentDidMount() {
        this.getCatalogs();
    }

    getCatalogs = () => {
        const {pagination, hasLoadedCatalogs} = this.state;
        const {catalog} = this.props;

        this.setState({
            isLoadingCatalogs: true,
        });

        let filters = {
            page : pagination.currentPage,
            perPage : pagination.perPage,
        };

        if (hasLoadedCatalogs) {
            window.scrollTo(0, this.tableRef.current.offsetTop - 35);
        }

        axios.get('/api/catalog/', {params: filters})
            .then((response) => {
                this.setState({
                    catalogs: response.data.results,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoadingCatalogs: false,
                    hasLoadedCatalogs: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingCatalogs: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the catalogs';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    handlePagination = (paginationCount) => {
        const {pagination} = this.state;

        this.setState({
            pagination: {
                ...pagination,
                currentPage: paginationCount.currentPage,
                perPage: paginationCount.pageLimit,
            },
        }, () => {
            this.getCatalogs();
        });
    };

    handleClick = (rowId) => {
        const {catalogs} = this.state;
        const {history, onClick} = this.props;

        const catalog = catalogs[rowId];

        if (onClick) {
            onClick(catalog);
        } else {
            history.push(`/admin/catalog/${catalog.slug}`);
        }
    };

    render() {
        const {catalogs, isLoadingCatalogs, hasLoadedCatalogs, pagination} = this.state;

        const hasLoaded = (hasLoadedCatalogs);

        if (!hasLoaded) {
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

        return <div className="DataTableContainer">
            <div className="TableCol">
                <DataGridContainer
                    pagination={pagination}
                    handlePageChange={this.handlePagination}
                    fullHeight
                    isLoading={isLoadingCatalogs}
                    ref={this.tableRef}
                >
                    <DataGrid
                        accessibleName="Catalogs"
                        emptyStateContent="No catalogs found"
                        onClick={this.handleClick}
                        rows={rows}
                        columns={columns}
                    />
                </DataGridContainer>
            </div>
        </div>;
    }
}