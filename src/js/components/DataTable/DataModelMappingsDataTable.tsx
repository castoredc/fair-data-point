import React, { Component, RefObject } from 'react';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { CellText, DataGrid, Icon, IconCell, LoadingOverlay, TextStyle } from '@castoredc/matter';
import { DataType, ValueType } from '../MetadataItem/EnumMappings';
import DataGridHelper from './DataGridHelper';
import DataGridContainer from './DataGridContainer';
import { apiClient } from 'src/js/network';

interface Mapping {
    node?: {
        title: string;
        value: {
            value: string;
            dataType: string;
        };
        repeated?: boolean;
    };
    elements?: { label: string }[];
    module?: {
        displayName: string;
    };
    element?: {
        label: string;
        structureType: string;
    };
    transformed?: boolean;
}

interface Pagination {
    currentPage: number;
    perPage: number;
}

interface DataModelMappingsDataTableProps {
    type: 'node' | 'module';
    dataset: string;
    distribution: { slug: string };
    versionId: string;
    lastHandledMapping?: any;
    onClick: (mapping: Mapping) => void;
}

interface DataModelMappingsDataTableState {
    isLoadingMappings: boolean;
    hasLoadedMappings: boolean | string;
    mappings: Mapping[];
    pagination: Pagination;
}

export default class DataModelMappingsDataTable extends Component<DataModelMappingsDataTableProps, DataModelMappingsDataTableState> {
    private tableRef: RefObject<HTMLDivElement>;

    constructor(props: DataModelMappingsDataTableProps) {
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

    componentDidUpdate(prevProps: DataModelMappingsDataTableProps) {
        const { lastHandledMapping, versionId, type } = this.props;

        if (type !== prevProps.type || versionId !== prevProps.versionId || lastHandledMapping !== prevProps.lastHandledMapping) {
            this.getMappings(type);
        }
    }

    getMappings = (type: 'node' | 'module') => {
        const { pagination } = this.state;
        const { dataset, distribution, versionId } = this.props;

        this.setState({
            isLoadingMappings: true,
        });

        const filters = {
            page: pagination.currentPage,
            perPage: pagination.perPage,
        };

        apiClient
            .get(`/api/dataset/${dataset}/distribution/${distribution.slug}/contents/rdf/v/${versionId}/${type}`, {
                params: filters,
            })
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
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    handlePagination = (paginationCount: { currentPage: number; pageSize: number }) => {
        const { pagination } = this.state;

        this.setState(
            {
                pagination: {
                    ...pagination,
                    currentPage: paginationCount.currentPage + 1,
                    perPage: paginationCount.pageSize,
                },
            },
            () => {
                this.getMappings(this.props.type);
            }
        );
    };

    handleClick = (rowId: string) => {
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

        let rows: any[] = [];

        if (type === 'node') {
            rows = mappings.map((item, index) => {
                const valueType = item.node?.value.value ? ValueType[item.node?.value.value] : '';
                const dataType = item.node?.value.dataType ? DataType[item.node?.value.dataType] : '';

                return {
                    mapped: !item.elements ? <IconCell key={index} icon={{ type: 'errorCircledInverted' }} /> : undefined,
                    title: <CellText key={index}>{item.node?.title}</CellText>,
                    valueType: <CellText key={index}>{valueType}</CellText>,
                    dataType: <CellText key={index}>{dataType}</CellText>,
                    repeated: item.node?.repeated ? <IconCell key={index} icon={{ type: 'tickSmall' }} /> : undefined,
                    ...(item.transformed && {
                        mappedElement: (
                            <CellText key={index}>
                                <TextStyle variation="italic">Transformed value</TextStyle>
                            </CellText>
                        ),
                    }),
                    ...(!item.transformed && {
                        mappedElement: <CellText key={index}>{item.elements ? item.elements[0].label : ''}</CellText>,
                    }),
                };
            });
        } else if (type === 'module') {
            rows = mappings.map((item, index) => ({
                mapped: !item.element ? <IconCell key={index} icon={{ type: 'errorCircledInverted' }} /> : undefined,
                title: <CellText key={index}>{item.module?.displayName}</CellText>,
                mappedElement: <CellText key={index}>{item.element ? item.element.label : ''}</CellText>,
                mappedElementType: <CellText key={index}>{item.element ? item.element.structureType : ''}</CellText>,
            }));
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
