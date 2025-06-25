const DataGridHelper = {
    getDefaultState: function(perPage) {
        return {
            currentPage: 0,
            start: 1,
            perPage: perPage,
            totalResults: 0,
            totalPages: 1,
        };
    },

    parseResults: function(data) {
        return {
            currentPage: data.currentPage,
            perPage: data.perPage,
            start: data.start,
            totalResults: data.totalResults,
            totalPages: data.totalPages,
        };
    },
};

export default DataGridHelper;
