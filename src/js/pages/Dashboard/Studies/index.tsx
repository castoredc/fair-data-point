import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import {Button, Heading, LoadingOverlay, Pagination, Stack} from "@castoredc/matter";
import {RouteComponentProps} from 'react-router-dom';
import ListItem from "components/ListItem";
import DocumentTitle from "components/DocumentTitle";
import DataGridHelper from "components/DataTable/DataGridHelper";

interface StudiesProps extends RouteComponentProps<any> {
}

interface StudiesState {
    studies: any,
    isLoading: boolean,
    pagination: any,
}

export default class Studies extends Component<StudiesProps, StudiesState> {
    constructor(props) {
        super(props);

        this.state = {
            studies: [],
            isLoading: false,
            pagination: DataGridHelper.getDefaultState(25),
        };
    }

    getStudies = () => {
        this.setState({
            isLoading: true,
        });

        axios.get('/api/study/my')
            .then((response) => {
                this.setState({
                    studies: response.data.results,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoading: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred while loading your studies"/>);
                }
            });
    };

    handlePagination = (paginationCount) => {
        const {pagination} = this.state;

        this.setState({
            pagination: {
                ...pagination,
                currentPage: paginationCount.currentPage,
                perPage: paginationCount.pageLimit,
            },
        }, () => {
            this.getStudies();
        });
    };


    componentDidMount() {
        this.getStudies();
    }

    render() {
        const {history} = this.props;
        const {isLoading, studies, pagination} = this.state;

        return <div>
            <DocumentTitle title="Studies" />

            {isLoading && <LoadingOverlay accessibleLabel="Loading studies"/>}

            <Stack distribution="equalSpacing">
                <Heading type="Section">My studies</Heading>

                <Button buttonType="primary" onClick={() => history.push('/dashboard/studies/add')}>
                    Add study
                </Button>
            </Stack>

            <div>
                {studies.map((study) => {
                    return <ListItem
                        key={study.id}
                        selectable={false}
                        link={`/dashboard/studies/${study.id}`} title={study.hasMetadata ? study.metadata.briefName : study.name}
                    />
                })}

                {pagination && <Pagination
                    accessibleName="Pagination"
                    onChange={this.handlePagination}
                    pageLimit={pagination.perPage}
                    start={pagination.start}
                    totalItems={pagination.totalResults}
                />}
            </div>
        </div>;
    }
}
