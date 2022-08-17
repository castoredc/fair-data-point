import React, { Component } from 'react';
import { toast } from 'react-toastify';
import ToastContent from '../ToastContent';
import { CellText, DataGrid, Icon, IconCell, LoadingOverlay, TextStyle } from '@castoredc/matter';
import { DataType, ValueType } from '../MetadataItem/EnumMappings';
import DataGridHelper from './DataGridHelper';
import DataGridContainer from './DataGridContainer';
import { apiClient } from 'src/js/network';

export default class DataModelMappingsDataTable extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingMappings: true,
            hasLoadedMappings: false,
            mappings: [],
            pagination: DataGridHelper.getDefaultState(25),
        };

        this.tableRef = React.createRef();
    }

    componentDidMount() {
        const { type } = this.props;

        this.getMappings(type);
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const { lastHandledMapping, versionId, type } = this.props;

        if (type !== prevProps.type || versionId !== prevProps.versionId || lastHandledMapping !== prevProps.lastHandledMapping) {
            this.getMappings(type);
        }
    }

    getMappings = type => {
        const { pagination, hasLoadedMappings } = this.state;
        const { dataset, distribution, versionId } = this.props;

        this.setState({
            isLoadingMappings: true,
        });

        const filters = {
            page: pagination.currentPage,
            perPage: pagination.perPage,
        };

        apiClient
            .get('/api/dataset/' + dataset + '/distribution/' + distribution.slug + '/contents/rdf/v/' + versionId + '/' + type, { params: filters })
            .then(response => {
                this.setState({
                    mappings: response.data.results,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoadingMappings: false,
                    hasLoadedMappings: type,
                });
            })
            .catch(error => {
                this.setState({
                    isLoadingMappings: false,
                });

                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the mappings';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    handlePagination = paginationCount => {
        const { pagination } = this.state;

        this.setState(
            {
                pagination: {
                    ...pagination,
                    currentPage: paginationCount.currentPage + 1,
                    perPage: paginationCount.pageLimit,
                },
            },
            () => {
                this.getMappings();
            }
        );
    };

    handleClick = rowId => {
        const { mappings } = this.state;
        const { onClick } = this.props;

        const mapping = mappings[rowId];
        onClick(mapping);
    };

    render() {
        const { mappings, isLoadingMappings, hasLoadedMappings, pagination } = this.state;
        const { type } = this.props;

        if (!hasLoadedMappings || hasLoadedMappings !== type) {
            return <LoadingOverlay accessibleLabel="Loading mappings" />;
        }

        let rows = null;

        if (type === 'node') {
            rows = mappings.map(item => {
                return {
                    mapped: !item.elements ? <IconCell icon={{ type: 'errorCircledInverted' }} /> : undefined,
                    title: <CellText>{item.node.title}</CellText>,
                    valueType: <CellText>{ValueType[item.node.value.value]}</CellText>,
                    dataType: <CellText>{DataType[item.node.value.dataType]}</CellText>,
                    repeated: item.node.repeated ? <IconCell icon={{ type: 'tickSmall' }} /> : undefined,
                    ...(item.transformed && {
                        mappedElement: (
                            <CellText>
                                <TextStyle variation="italic">Transformed value</TextStyle>
                            </CellText>
                        ),
                    }),
                    ...(!item.transformed && {
                        mappedElement: <CellText>{item.elements ? item.elements[0].label : ''}</CellText>,
                    }),
                };
            });
        } else if (type === 'module') {
            rows = mappings.map(item => {
                return {
                    mapped: !item.element ? <IconCell icon={{ type: 'errorCircledInverted' }} /> : undefined,
                    title: <CellText>{item.module.displayName}</CellText>,
                    mappedElement: <CellText>{item.element ? item.element.label : ''}</CellText>,
                    mappedElementType: <CellText>{item.element ? item.element.structureType : ''}</CellText>,
                };
            });
        } else {
            return null;
        }

        return (
            <DataGridContainer
                pagination={pagination}
                handlePageChange={this.handlePagination}
                fullHeight
                isLoading={isLoadingMappings}
                forwardRef={this.tableRef}
            >
                <DataGrid
                    accessibleName="Mappings"
                    emptyStateContent="No mappings found"
                    onClick={this.handleClick}
                    rows={rows}
                    columns={columns[type]}
                />
            </DataGridContainer>
        );
    }
}

const columns = {
    node: [
        {
            Header: '',
            accessor: 'mapped',
            disableResizing: true,
            width: 32,
        },
        {
            Header: 'Title',
            accessor: 'title',
        },
        {
            Header: 'Value type',
            accessor: 'valueType',
        },
        {
            Header: 'Data type',
            accessor: 'dataType',
        },
        {
            Header: <Icon description="Repeated" type="copy" />,
            accessor: 'repeated',
            disableResizing: true,
            width: 32,
        },
        {
            Header: 'Mapped field',
            accessor: 'mappedElement',
        },
    ],
    module: [
        {
            Header: '',
            accessor: 'mapped',
            disableResizing: true,
            width: 32,
        },
        {
            Header: 'Title',
            accessor: 'title',
        },
        {
            Header: 'Mapped',
            accessor: 'mappedElement',
        },
        {
            Header: 'Type',
            accessor: 'mappedElementType',
        },
    ],
};
