import React, {Component} from "react";
import axios from "axios/index";
import {Col, Row} from "react-bootstrap";
import InlineLoader from "../LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import {Button, DataTable, Pagination} from "@castoredc/matter";
import {MethodType, StudyType} from "../MetadataItem/EnumMappings";
import Filters from "../Filters";
import {classNames, localizedText} from "../../util";

export default class DatasetsDataTable extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDatasets: true,
            hasLoadedDatasets: false,
            datasets:          [],
            pagination:       {
                currentPage: 1,
                start: 0,
                perPage: 25,
                totalResults: null,
                totalPages: null
            },
        };

        this.tableRef = React.createRef();
    }

    componentDidMount() {
        this.getDatasets();
    }

    getDatasets = () => {
        const { pagination, hasLoadedDatasets } = this.state;
        const { catalog } = this.props;

        this.setState({
            isLoadingDatasets: true,
        });

        const filters = {
            page: pagination.currentPage,
            perPage: pagination.perPage
        };

        if(hasLoadedDatasets) {
            window.scrollTo(0, this.tableRef.current.offsetTop - 35);
        }

        axios.get(catalog ? '/api/catalog/' + catalog.slug + '/dataset' : '/api/dataset', {params: filters})
            .then((response) => {
                this.setState({
                    datasets:          response.data.results,
                    pagination:       {
                        currentPage: response.data.currentPage,
                        perPage: response.data.perPage,
                        start: response.data.start,
                        totalResults: response.data.totalResults,
                        totalPages: response.data.totalPages
                    },
                    isLoadingDatasets: false,
                    hasLoadedDatasets: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDatasets: false,
                });

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the datasets';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    handlePagination = (paginationCount) => {
        const { pagination } = this.state;

        this.setState({
            pagination: {
                ...pagination,
                currentPage: paginationCount.currentPage,
                perPage: paginationCount.pageLimit
            }
        }, () => {
            this.getDatasets();
        });
    };

    render() {
        const { datasets, isLoadingDatasets, hasLoadedDatasets, pagination } = this.state;
        const { history, catalog } = this.props;

        if(!hasLoadedDatasets) {
            return <Row>
                <Col>
                    <InlineLoader />
                </Col>
            </Row>;
        }

        return <Row className="FillHeight">
        <Col sm={12} className="Page">
            <div className={classNames('SelectableDataTable FullHeightDataTable', isLoadingDatasets && 'Loading')} ref={this.tableRef}>
                <div className="DataTableWrapper">
                    <DataTable
                        emptyTableMessage="No datasets found"
                        highlightRowOnHover
                        cellSpacing="default"
                        onClick={(event, rowID, index) => {
                            if(typeof index !== "undefined") {
                                history.push('/admin/' + (catalog ? 'catalog/' + catalog.slug : '') + '/dataset/' + datasets[index].slug)
                            }
                        }}
                        rows={datasets.map((item) => {
                            return [
                                item.hasMetadata ? localizedText(item.metadata.title, 'en') : '(no title)',
                                item.hasMetadata ? localizedText(item.metadata.description, 'en') : '',
                                item.hasMetadata ? item.metadata.language : '',
                                item.hasMetadata ? item.metadata.license : '',
                                item.published ? {
                                    type: 'view'
                                } : undefined,
                            ];
                        })}
                        structure={{
                            title: {
                                header:    'Title',
                                resizable: true,
                                template:  'text',
                            },
                            description: {
                                header:    'Description',
                                resizable: true,
                                template:  'text',
                            },
                            language: {
                                header:    'Language',
                                resizable: true,
                                template:  'text',
                            },
                            license: {
                                header:    'License',
                                resizable: true,
                                template:  'text',
                            },
                            published: {
                                header:    'Published',
                                icon:      'view',
                                template:  'icon'
                            },
                        }}
                    />
                </div>

                <Pagination
                    accessibleName="Pagination"
                    onChange={this.handlePagination}
                    pageLimit={pagination.perPage}
                    start={pagination.start}
                    totalItems={pagination.totalResults}
                />

            </div>
        </Col>
    </Row>;
    }
}