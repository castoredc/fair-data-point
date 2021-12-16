import React from "react";
import {LoadingOverlay, Pagination} from "@castoredc/matter";
import InlineLoader from "../LoadingScreen/InlineLoader";

export default React.forwardRef(({
                                     fullHeight = false,
                                     isLoading = false,
                                     inFlexContainer = false,
                                     children,
                                     pagination,
                                     handlePageChange
                                 }, ref) => {
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
                onChange={handlePageChange}
                pageSize={pagination.perPage}
                currentPage={pagination.currentPage - 1}
                totalItems={pagination.totalResults}
            />}
        </div>
    }

    return grid;
});