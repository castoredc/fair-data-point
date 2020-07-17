import React, {Component} from "react";
import axios from "axios";
import {Col, Row} from "react-bootstrap";
import InlineLoader from "../LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import {DataTable, Pagination} from "@castoredc/matter";
import {classNames} from "../../util";
import {DataType, ValueType} from "../MetadataItem/EnumMappings";

export default class DataModelMappingsDataTable extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingMappings: true,
            hasLoadedMappings: false,
            mappings:          [],
            pagination:        {
                currentPage: 1,
                start: 1,
                perPage: 25,
                totalResults: null,
                totalPages: null
            },
        };

        this.tableRef = React.createRef();
    }

    componentDidMount() {
        this.getMappings();
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const { lastHandledMapping, versionId } = this.props;

        if(versionId !== prevProps.versionId || lastHandledMapping !== prevProps.lastHandledMapping) {
            this.getMappings();
        }
    }

    getMappings = () => {
        const { pagination, hasLoadedMappings } = this.state;
        const { dataset, distribution, versionId } = this.props;

        this.setState({
            isLoadingMappings: true,
        });

        const filters = {
            page: pagination.currentPage,
            perPage: pagination.perPage
        };

        if(hasLoadedMappings) {
            window.scrollTo(0, this.tableRef.current.offsetTop - 35);
        }

        axios.get('/api/dataset/' + dataset + '/distribution/' + distribution.slug + '/contents/rdf/v/' + versionId, {params: filters})
            .then((response) => {
                this.setState({
                    mappings:          response.data.results,
                    pagination:       {
                        currentPage: response.data.currentPage,
                        perPage: response.data.perPage,
                        start: response.data.start,
                        totalResults: response.data.totalResults,
                        totalPages: response.data.totalPages
                    },
                    isLoadingMappings: false,
                    hasLoadedMappings: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingMappings: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the mappings';
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
            this.getMappings();
        });
    };

    handleClick = (event, rowID, index) => {
        const { mappings } = this.state;
        const { onClick } = this.props;

        if(typeof index !== "undefined" && mappings.length > 0) {
            onClick(mappings[index]);
        }
    };

    render() {
        const { mappings, isLoadingMappings, hasLoadedMappings, pagination } = this.state;

        if(!hasLoadedMappings) {
            return <Row>
                <Col>
                    <InlineLoader />
                </Col>
            </Row>;
        }

        return <Row className="FillHeight">
        <Col sm={12} className="Page">
            <div className={classNames('SelectableDataTable FullHeightDataTable', isLoadingMappings && 'Loading')} ref={this.tableRef}>
                <div className="DataTableWrapper">
                    <DataTable
                        emptyTableMessage="No mappings found"
                        highlightRowOnHover
                        cellSpacing="default"
                        onClick={this.handleClick}
                        rows={mappings.map((item) => {
                            return [
                                item.node.title,
                                ValueType[item.node.value.value],
                                DataType[item.node.value.dataType],
                                item.node.repeated ? {
                                    type: 'tickSmall'
                                } : undefined,
                                item.element ? item.element.label : ''
                            ];
                        })}
                        structure={{
                            title: {
                                header:    'Title',
                                resizable: true,
                                template:  'text',
                            },
                            valueType: {
                                header:    'Value type',
                                resizable: true,
                                template:  'text',
                            },
                            dataType: {
                                header:    'Data type',
                                resizable: true,
                                template:  'text',
                            },
                            repeated: {
                                header:    'Repeated',
                                resizable: true,
                                template:  'icon',
                            },
                            mappedElement: {
                                header:    'Mapped field',
                                resizable: true,
                                template:  'text',
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