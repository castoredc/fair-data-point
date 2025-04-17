import React, { Component, createRef } from 'react';
import LoadingOverlay from 'components/LoadingOverlay';
import DataGridHelper from './DataGridHelper';
import DataGridContainer from './DataGridContainer';
import { apiClient } from 'src/js/network';
import { localizedText } from '../../util';
import DataGrid from 'components/DataTable/DataGrid';
import { GridColDef, GridRowParams } from '@mui/x-data-grid';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import VisibilityIcon from '@mui/icons-material/Visibility';
import VisibilityOffIcon from '@mui/icons-material/VisibilityOff';

interface StudiesDataTableProps extends ComponentWithNotifications {
    catalog?: string;
    hideCatalog?: string;
    lastHandledStudy?: any;
    history?: any;
    onClick?: (study: any) => void;
}

interface StudiesDataTableState {
    isLoadingStudies: boolean;
    hasLoadedStudies: boolean;
    studies: any[];
    pagination: any;
}

class StudiesDataTable extends Component<StudiesDataTableProps, StudiesDataTableState> {
    private tableRef: React.RefObject<HTMLDivElement>;

    constructor(props: StudiesDataTableProps) {
        super(props);
        this.state = {
            isLoadingStudies: true,
            hasLoadedStudies: false,
            studies: [],
            pagination: DataGridHelper.getDefaultState(25),
        };

        this.tableRef = createRef<HTMLDivElement>();
    }

    componentDidMount() {
        this.getStudies();
    }

    componentDidUpdate(prevProps: StudiesDataTableProps) {
        const { lastHandledStudy } = this.props;

        if (lastHandledStudy !== prevProps.lastHandledStudy) {
            this.getStudies();
        }
    }

    getStudies = () => {
        const { pagination, hasLoadedStudies } = this.state;
        const { catalog, hideCatalog, notifications } = this.props;

        this.setState({ isLoadingStudies: true });

        let filters = {};
        filters['page'] = pagination.currentPage;
        filters['perPage'] = pagination.perPage;

        if (hideCatalog) {
            filters['hideCatalogs'] = [hideCatalog];
        }

        if (hasLoadedStudies && this.tableRef.current) {
            window.scrollTo(0, this.tableRef.current.offsetTop - 35);
        }

        apiClient
            .get(catalog ? `/api/catalog/${catalog}/study` : '/api/study', { params: filters })
            .then(response => {
                this.setState({
                    studies: response.data.results,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoadingStudies: false,
                    hasLoadedStudies: true,
                });
            })
            .catch(error => {
                this.setState({ isLoadingStudies: false });

                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the studies';
                notifications.show(message, { variant: 'error' });
            });
    };

    handlePagination = (currentPage: number, pageSize: number ) => {
        this.setState(
            prevState => ({
                pagination: {
                    ...prevState.pagination,
                    currentPage: currentPage + 1,
                    perPage: pageSize,
                },
            }),
            this.getStudies,
        );
    };

    handleClick = (params: GridRowParams) => {
        const { studies } = this.state;
        const { history, onClick } = this.props;

        const study = studies[params.id];

        if (onClick) {
            onClick(study);
        } else {
            history.push(`/dashboard/studies/${study.id}`);
        }
    };

    render() {
        const { studies, isLoadingStudies, hasLoadedStudies, pagination } = this.state;
        const { hideCatalog } = this.props;

        if (!hasLoadedStudies) {
            return <LoadingOverlay accessibleLabel="Loading studies" />;
        }

        const columns: GridColDef[] = [
            {
                headerName: 'Name',
                field: 'title',
                width: 300,
            },
            {
                headerName: 'Description',
                field: 'description',
                width: 500,
            },
            {
                headerName: 'Published',
                field: 'published',
                resizable: false,
                width: 32,
                renderCell: (params) => {
                    return params.row.published ? <VisibilityIcon /> : <VisibilityOffIcon />;
                },
            },
        ];

        const rows = studies.filter((item) => {
            return !item.catalogs.some((studyCatalog: any) => studyCatalog.slug === hideCatalog);
        }).map((item, index) => ({
            id: String(index),
            title: item.hasMetadata ? localizedText(item.metadata.title, 'en') : '(no title)',
            description: item.hasMetadata ? localizedText(item.metadata.description, 'en') : '',
            published: item.published,
        }));

        return (
            <div className="DataTableContainer">
                <div className="TableCol">
                    <DataGridContainer
                        pagination={pagination}
                        handlePageChange={this.handlePagination}
                        fullHeight
                        isLoading={isLoadingStudies}
                        forwardRef={this.tableRef}
                    >
                        <DataGrid
                            disableRowSelectionOnClick
                            emptyStateContent="No studies found"
                            onRowClick={this.handleClick}
                            rows={rows}
                            columns={columns}
                        />
                    </DataGridContainer>
                </div>
            </div>
        );
    }
}

export default withNotifications(StudiesDataTable);