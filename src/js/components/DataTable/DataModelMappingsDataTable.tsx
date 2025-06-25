import React, { Component, createRef } from 'react';
import LoadingOverlay from 'components/LoadingOverlay';
import { DataType, ValueType } from '../MetadataItem/EnumMappings';
import DataGridHelper from './DataGridHelper';
import { apiClient } from 'src/js/network';
import { DataGrid, GridColDef, GridRowParams } from '@mui/x-data-grid';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import CheckIcon from '@mui/icons-material/Check';
import ErrorIcon from '@mui/icons-material/Error';

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
    totalResults: number;
}

interface DataModelMappingsDataTableProps extends ComponentWithNotifications {
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

class DataModelMappingsDataTable extends Component<DataModelMappingsDataTableProps, DataModelMappingsDataTableState> {
    private tableRef: React.RefObject<HTMLDivElement>;

    constructor(props: DataModelMappingsDataTableProps) {
        super(props);
        this.state = {
            isLoadingMappings: true,
            hasLoadedMappings: false,
            mappings: [],
            pagination: DataGridHelper.getDefaultState(25),
        };

        this.tableRef = createRef<HTMLDivElement>();
    }

    componentDidMount() {
        const { type } = this.props;
        this.getMappings(type);
    }

    componentDidUpdate(prevProps: DataModelMappingsDataTableProps) {
        const { versionId, type } = this.props;

        // Only refresh the mappings if the version or type changes
        // Removed lastHandledMapping check to prevent continuous updates
        if (type !== prevProps.type || versionId !== prevProps.versionId) {
            console.log('updated');
            this.getMappings(type);
        }
    }

    getMappings = (type: 'node' | 'module') => {
        const { pagination } = this.state;
        const { dataset, distribution, versionId, notifications } = this.props;

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
                notifications.show(message, { variant: 'error' });
            });
    };

    handlePagination = (model: { page: number; pageSize: number }) => {
        const { pagination } = this.state;
        const newPage = model.page + 1; // Convert 0-based to 1-based

        // Only update if values actually changed
        if (pagination.currentPage !== newPage || pagination.perPage !== model.pageSize) {
            this.setState(
                prevState => ({
                    pagination: {
                        ...prevState.pagination,
                        currentPage: newPage,
                        perPage: model.pageSize,
                    },
                }),
                () => this.getMappings(this.props.type),
            );
        }
    };

    handleClick = (params: GridRowParams) => {
        const { mappings } = this.state;
        const { onClick } = this.props;

        const mapping = mappings[parseInt(params.id as string)];
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
                    id: String(index),
                    mapped: !!item.elements,
                    title: item.node?.title,
                    valueType: valueType,
                    dataType: dataType,
                    repeated: item.node?.repeated ?? false,
                    ...(item.transformed && {
                        mappedElement: 'Transformed value',
                    }),
                    ...(!item.transformed && {
                        mappedElement: item.elements ? item.elements[0].label : '',
                    }),
                };
            });
        } else if (type === 'module') {
            rows = mappings.map((item, index) => ({
                id: String(index),
                mapped: !!item.element,
                title: item.module?.displayName,
                mappedElement: item.element ? item.element.label : '',
                mappedElementType: item.element ? item.element.structureType : '',
            }));
        } else {
            return null;
        }

        return (
            <div className="DataTableContainer">
                <div className="TableCol">
                    <div ref={this.tableRef}>
                        <DataGrid
                            disableRowSelectionOnClick
                            getRowId={(row) => row.id}
                            onRowClick={this.handleClick}
                            rows={rows}
                            columns={columns[type]}
                            paginationMode="server"
                            rowCount={pagination.totalResults}
                            loading={isLoadingMappings}
                            pageSizeOptions={[10, 25, 50]}
                            paginationModel={{
                                page: pagination.currentPage - 1, // Convert 1-based to 0-based
                                pageSize: pagination.perPage,
                            }}
                            onPaginationModelChange={this.handlePagination}
                        />
                    </div>
                </div>
            </div>
        );
    }
}

const columns: { [key: string]: GridColDef[] } = {
    node: [
        {
            headerName: '',
            field: 'mapped',
            resizable: false,
            width: 32,
            renderCell: (params) => {
                return params.row.mapped ? '' : <ErrorIcon />;
            },
        },
        {
            headerName: 'Title',
            field: 'title',
        },
        {
            headerName: 'Value type',
            field: 'valueType',
        },
        {
            headerName: 'Data type',
            field: 'dataType',
        },
        {
            headerName: 'Repeated',
            field: 'repeated',
            resizable: false,
            width: 32,
            renderCell: (params) => {
                return params.row.repeated ? <CheckIcon /> : '';
            },
        },
        {
            headerName: 'Mapped field',
            field: 'mappedElement',
        },
    ],
    module: [
        {
            headerName: '',
            field: 'mapped',
            resizable: false,
            width: 32,
            renderCell: (params) => {
                return params.row.mapped ? '' : <ErrorIcon />;
            },
        },
        {
            headerName: 'Title',
            field: 'title',
        },
        {
            headerName: 'Mapped',
            field: 'mappedElement',
        },
        {
            headerName: 'Type',
            field: 'mappedElementType',
        },
    ],
};

export default withNotifications(DataModelMappingsDataTable);