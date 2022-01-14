import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import {Button, ChoiceOption, LoadingOverlay, Pagination, Space} from "@castoredc/matter";
import {RouteComponentProps} from 'react-router-dom';
import ListItem from "components/ListItem";
import DocumentTitle from "components/DocumentTitle";
import DataGridHelper from "components/DataTable/DataGridHelper";
import {isAdmin} from "utils/PermissionHelper";
import Header from "components/Layout/Dashboard/Header";
import ScrollShadow from "components/ScrollShadow";

interface StudiesProps extends RouteComponentProps<any> {
    user: any,
}

interface StudiesState {
    studies: any,
    isLoading: boolean,
    pagination: any,
    viewAll: boolean,
}

export default class Studies extends Component<StudiesProps, StudiesState> {
    constructor(props) {
        super(props);

        this.state = {
            studies: [],
            isLoading: false,
            viewAll: isAdmin(props.user),
            pagination: DataGridHelper.getDefaultState(25),
        };
    }

    getStudies = () => {
        const {viewAll, pagination} = this.state;

        this.setState({
            isLoading: true,
        });

        axios.get(viewAll ? '/api/study' : '/api/study/my', {
            params: {
                page: pagination.currentPage,
                perPage: pagination.perPage,
            }
        })
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

    handleView = () => {
        const {viewAll} = this.state;

        this.setState({
            viewAll: !viewAll
        }, () => {
            this.getStudies();
        });
    }

    handlePagination = (paginationCount) => {
        const {pagination} = this.state;

        this.setState({
            pagination: {
                ...pagination,
                currentPage: paginationCount.currentPage + 1,
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
        const {history, user} = this.props;
        const {isLoading, studies, pagination, viewAll} = this.state;

        return <div className="DashboardTab">
            <DocumentTitle title="Studies"/>

            {isLoading && <LoadingOverlay accessibleLabel="Loading studies"/>}

            <Space bottom="comfortable"/>

            <Header
                title="My studies"
                type="Section"
                badge={isAdmin(user) ? <ChoiceOption
                    labelText="View all studies"
                    checked={viewAll}
                    onChange={this.handleView}
                /> : undefined}
            >
                <Button buttonType="primary" onClick={() => history.push('/dashboard/studies/add')}>
                    Add study
                </Button>
            </Header>

            {/*<div className="DashboardList">*/}
            <ScrollShadow className="DashboardList">
                {studies.map((study) => {
                    return <ListItem
                        key={study.id}
                        selectable={false}
                        link={`/dashboard/studies/${study.id}`}
                        title={study.hasMetadata ? study.metadata.briefName : study.name}
                    />
                })}
            </ScrollShadow>
            {/*</div>*/}

            <div className="DashboardFooter">
                {pagination && <Pagination
                    accessibleName="Pagination"
                    onChange={this.handlePagination}
                    pageSize={pagination.perPage}
                    currentPage={pagination.currentPage - 1}
                    totalItems={pagination.totalResults}
                />}
            </div>
        </div>;
    }
}
