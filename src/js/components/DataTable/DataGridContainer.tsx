import React, { FC } from 'react';
import LoadingOverlay from 'components/LoadingOverlay';
import { TablePagination } from '@mui/material';

interface DataGridContainerProps {
    fullHeight?: boolean;
    isLoading?: boolean;
    inFlexContainer?: boolean;
    pagination?: any;
    children: React.ReactNode;
    handlePageChange?: (currentPage: number, pageSize: number) => void;
    forwardRef?: any;
}

const DataGridContainer: FC<DataGridContainerProps> = ({
                                                           fullHeight = false,
                                                           isLoading = false,
                                                           inFlexContainer = false,
                                                           children,
                                                           pagination,
                                                           handlePageChange,
                                                           forwardRef,
                                                       }) => {
    const grid = (
        <div
            style={{
                flex: 1,
                overflow: 'auto',
                ...(isLoading && { opacity: 0.5 }),
            }}
        >
            {children}
        </div>
    );

    if (fullHeight) {
        return (
            <div
                style={{
                    display: 'flex',
                    flexFlow: 'column',
                    height: '100%',
                    position: 'relative',
                    ...(inFlexContainer && { flex: 1 }),
                }}
                ref={forwardRef}
            >
                {isLoading && <LoadingOverlay accessibleLabel="Loading" />}

                {grid}

                {pagination && (
                    <TablePagination
                        onPageChange={handlePageChange ? (event, page) => {
                            handlePageChange(page, pagination.perPage);
                        } : () => {
                        }}
                        onRowsPerPageChange={handlePageChange ? (event) => {
                            handlePageChange(pagination.currentPage, parseInt(event.target.value));
                        } : () => {
                        }}
                        rowsPerPage={pagination.perPage}
                        page={pagination.currentPage - 1}
                        count={pagination.totalResults}
                    />
                )}
            </div>
        );
    }

    return grid;
};

export default DataGridContainer;
