import React, {Component} from "react";
import axios from "axios";
import {classNames, localizedText} from "../../util";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import {Pagination} from "@castoredc/matter";
import InlineLoader from "../LoadingScreen/InlineLoader";
import DatasetListItem from "../ListItem/DatasetListItem";
import DataGridHelper from "../DataTable/DataGridHelper";

export default class DatasetList extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoadingDatasets: true,
            datasets:          null,
            pagination: DataGridHelper.getDefaultState(props.embedded ? 5 : 10),
        };
    }

    componentDidMount() {
        this.getDatasets();
    }

    getDatasets = () => {
        const {pagination} = this.state;
        const {catalog, study} = this.props;

        this.setState({
            isLoadingDatasets: true,
        });

        let filters = {
            page:    pagination.currentPage,
            perPage: pagination.perPage,
        };

        let url = '';

        if(study) {
            url = '/api/study/' + study.id + '/dataset'
        } else if(catalog) {
            url = '/api/catalog/' + catalog.slug + '/dataset'
        }

        axios.get(url, {params: filters})
            .then((response) => {
                const datasets = response.data.results.filter((dataset) => {
                    return dataset.hasMetadata
                });

                this.setState({
                    datasets:          datasets,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoadingDatasets: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDatasets: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the datasets';
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
            this.getDatasets();
        });
    };

    render() {
        const {embedded, pagination, datasets} = this.state;
        const {visible = true, study, state, className} = this.props;

        if(!visible) {
            return null;
        }

        if (datasets === null) {
            return <InlineLoader/>;
        }

        return <div className={classNames('Datasets', className)}>
            {datasets.length > 0 ? <div>
                <div className="Description">
                    Datasets are collections of data which are available for access or download in one or more representations.
                </div>

                {datasets.map((dataset) => {
                    if (dataset.hasMetadata === false) {
                        return null;
                    }
                    return <DatasetListItem key={dataset.id}
                                            newWindow={embedded}
                                            state={state}
                                            link={dataset.relativeUrl}
                                            name={localizedText(dataset.metadata.title, 'en')}
                                            description={localizedText(dataset.metadata.description, 'en')}
                    />
                })}

                <Pagination
                    accessibleName="Pagination"
                    onChange={this.handlePagination}
                    pageLimit={pagination.perPage}
                    start={pagination.start}
                    totalItems={pagination.totalResults}
                />

            </div> : <div className="NoResults">This {study ? 'study' : 'catalog'} does not have any associated datasets.</div>}
        </div>;
    }
}