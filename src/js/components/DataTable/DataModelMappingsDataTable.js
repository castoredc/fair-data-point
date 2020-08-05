import React, {Component} from "react";
import axios from "axios";
import InlineLoader from "../LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import {DataTable, Pagination} from "@castoredc/matter";
import {classNames, ucfirst} from "../../util";
import {DataType, ValueType} from "../MetadataItem/EnumMappings";

export default class DataModelMappingsDataTable extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingMappings: true,
            hasLoadedMappings: false,
            mappings:          [],
            pagination:        {
                currentPage:  1,
                start:        1,
                perPage:      25,
                totalResults: null,
                totalPages:   null,
            },
        };

        this.tableRef = React.createRef();
    }

    componentDidMount() {
        const {type} = this.props;

        this.getMappings(type);
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {lastHandledMapping, versionId, type} = this.props;

        if (type !== prevProps.type || versionId !== prevProps.versionId || lastHandledMapping !== prevProps.lastHandledMapping) {
            this.getMappings(type);
        }
    }

    getMappings = (type) => {
        const {pagination, hasLoadedMappings} = this.state;
        const {dataset, distribution, versionId} = this.props;

        this.setState({
            isLoadingMappings: true,
        });

        const filters = {
            page:    pagination.currentPage,
            perPage: pagination.perPage,
        };

        // if(hasLoadedMappings) {
        //     window.scrollTo(0, this.tableRef.current.offsetTop - 35);
        // }

        axios.get('/api/dataset/' + dataset + '/distribution/' + distribution.slug + '/contents/rdf/v/' + versionId + '/' + type, {params: filters})
            .then((response) => {
                this.setState({
                    mappings:          response.data.results,
                    pagination:        {
                        currentPage:  response.data.currentPage,
                        perPage:      response.data.perPage,
                        start:        response.data.start,
                        totalResults: response.data.totalResults,
                        totalPages:   response.data.totalPages,
                    },
                    isLoadingMappings: false,
                    hasLoadedMappings: type,
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
        const {pagination} = this.state;

        this.setState({
            pagination: {
                ...pagination,
                currentPage: paginationCount.currentPage,
                perPage:     paginationCount.pageLimit,
            },
        }, () => {
            this.getMappings();
        });
    };

    handleClick = (event, rowID, index) => {
        const {mappings} = this.state;
        const {onClick} = this.props;

        if (typeof index !== "undefined" && mappings.length > 0)
        {
            const mapping = mappings.find((item) => item.id === rowID);
            onClick(mapping);
        }
    };

    render() {
        const {mappings, isLoadingMappings, hasLoadedMappings, pagination} = this.state;
        const {type} = this.props;

        if (!hasLoadedMappings || hasLoadedMappings !== type) {
            return <InlineLoader/>;
        }

        let rows = null;

        if (type === 'node') {
            rows = new Map(mappings.map((item) => {
                return [
                    item.id,
                    {
                        cells: [
                            !item.element ? {
                                type: 'errorCircledInverted',
                            } : undefined,
                            item.node.title,
                            ValueType[item.node.value.value],
                            DataType[item.node.value.dataType],
                            item.node.repeated ? {
                                type: 'tickSmall',
                            } : undefined,
                            item.element ? item.element.label : '',
                        ],
                    },
                ];
            }));
        } else if (type === 'module') {
            rows = new Map(mappings.map((item) => {
                return [
                    item.id,
                    {
                        cells: [
                            !item.element ? {
                                type: 'errorCircledInverted',
                            } : undefined,
                            item.module.displayName,
                            item.element ? item.element.label : '',
                            item.element ? ucfirst(item.element.structureType) : '',
                        ],
                    },
                ];
            }));
        } else {
            return null;
        }

        return <>
            <div className={classNames('SelectableDataTable FullHeightDataTable', isLoadingMappings && 'Loading')}
                 ref={this.tableRef}>
                <div className="DataTableWrapper">
                    <DataTable
                        emptyTableMessage="No mappings found"
                        highlightRowOnHover
                        cellSpacing="default"
                        onClick={this.handleClick}
                        rows={rows}
                        structure={structure[type]}
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
        </>;
    }
}

const structure = {
    node:   {
        mapped:        {
            header:    '',
            resizable: true,
            template:  'icon',
        },
        title:         {
            header:    'Title',
            resizable: true,
            template:  'text',
        },
        valueType:     {
            header:    'Value type',
            resizable: true,
            template:  'text',
        },
        dataType:      {
            header:    'Data type',
            resizable: true,
            template:  'text',
        },
        repeated:      {
            header:   'Repeated',
            icon:     'copy',
            template: 'icon',
        },
        mappedElement: {
            header:    'Mapped field',
            resizable: true,
            template:  'text',
        },
    },
    module: {
        mapped:            {
            header:    '',
            resizable: true,
            template:  'icon',
        },
        title:             {
            header:    'Title',
            resizable: true,
            template:  'text',
        },
        mappedElement:     {
            header:    'Mapped',
            resizable: true,
            template:  'text',
        },
        mappedElementType: {
            header:    'Type',
            resizable: true,
            template:  'text',
        },
    },
};