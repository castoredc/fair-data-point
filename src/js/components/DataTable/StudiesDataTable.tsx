import React, { Component, createRef } from 'react';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { CellText, DataGrid, Heading, Icon, IconCell, LoadingOverlay } from '@castoredc/matter';
import './DataTable.scss';
import DataGridHelper from './DataGridHelper';
import DataGridContainer from './DataGridContainer';
import { apiClient } from 'src/js/network';
import { localizedText } from '../../util';

interface StudiesDataTableProps {
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

export default class StudiesDataTable extends Component<StudiesDataTableProps, StudiesDataTableState> {
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
        const { catalog, hideCatalog } = this.props;

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
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    handlePagination = (paginationCount: { currentPage: number; pageSize: number }) => {
        this.setState(
            prevState => ({
                pagination: {
                    ...prevState.pagination,
                    currentPage: paginationCount.currentPage + 1,
                    perPage: paginationCount.pageSize,
                },
            }),
            this.getStudies
        );
    };

    handleClick = (rowId: string) => {
        const { studies } = this.state;
        const { history, onClick } = this.props;

        const study = studies[rowId];

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

        const columns = [
            {
                Header: 'Name',
                accessor: 'title',
                width: 300,
            },
            {
                Header: 'Description',
                accessor: 'description',
                width: 500,
            },
            {
                Header: <Icon description="Published" type="view" />,
                accessor: 'published',
                disableResizing: true,
                width: 32,
            },
        ];

        const rows = studies.map((item, index) => ({
            __rowId: String(index),
            title: <CellText>{item.hasMetadata ? localizedText(item.metadata.title, 'en') : '(no title)'}</CellText>,
            description: <CellText>{item.hasMetadata ? localizedText(item.metadata.description, 'en') : ''}</CellText>,
            published: item.published ? <IconCell icon={{ type: 'view' }} /> : undefined,
        }));

        const rowState = studies.reduce((acc, item, index) => {
            acc[index] = {
                disabled: item.catalogs.some((studyCatalog: any) => studyCatalog.slug === hideCatalog),
            };
            return acc;
        }, {} as Record<number, { disabled: boolean }>);

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
                            accessibleName="Studies"
                            emptyStateContent="No studies found"
                            onClick={this.handleClick}
                            rows={rows}
                            columns={columns}
                            rowState={rowState}
                        />
                    </DataGridContainer>
                </div>
            </div>
        );
    }
}
