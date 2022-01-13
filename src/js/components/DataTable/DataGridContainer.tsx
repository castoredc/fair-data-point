import React, {FC} from "react";
import {LoadingOverlay, Pagination} from "@castoredc/matter";
import {PaginationState} from "@castoredc/matter/lib/types/src/Pagination/Pagination";

interface DataGridContainerProps {
    fullHeight?: boolean,
    isLoading?: boolean,
    inFlexContainer?: boolean,
    pagination?: any,
    children: React.ReactNode,
    handlePageChange?: (value: PaginationState) => void,
    ref: any,
}

const DataGridContainer: FC<DataGridContainerProps> = (({
                                                            fullHeight = false,
                                                            isLoading = false,
                                                            inFlexContainer = false,
                                                            children,
                                                            pagination,
                                                            handlePageChange, ref
                                                        }) => {
    const grid = <div style={{
        flex: 1,
        overflow: 'auto',
        ...(isLoading && {opacity: 0.5})
    }}>
        {children}
    </div>;

    if (fullHeight) {
        return <div style={{
            display: 'flex',
            flexFlow: 'column',
            height: '100%',
            position: 'relative',
            ...(inFlexContainer && {flex: 1})
        }} ref={ref}>
            {isLoading && <LoadingOverlay accessibleLabel="Loading"/>}

            {grid}

            {pagination && <Pagination
                accessibleName="Pagination"
                onChange={handlePageChange ? handlePageChange : () => null}
                pageSize={pagination.perPage}
                currentPage={pagination.currentPage - 1}
                totalItems={pagination.totalResults}
            />}
        </div>
    }

    return grid;
});

export default DataGridContainer;