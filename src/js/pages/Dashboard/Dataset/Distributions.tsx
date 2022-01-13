import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "components/ToastContent";
import {Button, LoadingOverlay, Stack} from "@castoredc/matter";
import ListItem from "components/ListItem";
import DataGridHelper from "components/DataTable/DataGridHelper";
import {localizedText} from "../../../util";
import {RouteComponentProps} from "react-router-dom";

interface DistributionsProps extends RouteComponentProps<any> {
}

interface DistributionsState {
    distributions: any,
    isLoading: boolean,
    pagination: any,
}

export default class Distributions extends Component<DistributionsProps, DistributionsState> {
    constructor(props) {
        super(props);

        this.state = {
            isLoading: true,
            distributions: [],
            pagination: DataGridHelper.getDefaultState(25),
        };
    }

    componentDidMount() {
        this.getDistributions();
    }

    getDistributions = () => {
        const {match} = this.props;
        this.setState({
            isLoading: true,
        });

        axios.get('/api/dataset/' + match.params.dataset + '/distribution')
            .then((response) => {
                this.setState({
                    distributions: response.data.results,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoading: false
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the distributions';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    render() {
        const {isLoading, distributions} = this.state;
        const {match, history} = this.props;

        const mainUrl = match.params.study ? `/dashboard/studies/${match.params.study}/datasets/${match.params.dataset}` :`/dashboard/catalogs/${match.params.catalog}/datasets/${match.params.dataset}`;

        return <div>
            {isLoading && <LoadingOverlay accessibleLabel="Loading studies"/>}

            <Stack distribution="trailing" alignment="end">
                <Button icon="add" className="AddButton" disabled={isLoading} onClick={() => history.push(`${mainUrl}/distributions/add`)}>New distribution</Button>
            </Stack>

            <div>
                {distributions.length === 0 && <div className="NoResults">This study does not have distributions.</div>}

                {distributions.map((distribution) => {
                    return <ListItem
                        selectable={false}
                        link={`${mainUrl}/distributions/${distribution.slug}`} title={distribution.hasMetadata ? localizedText(distribution.metadata.title, 'en') : 'Untitled distribution'}
                    />
                })}
            </div>
        </div>;
    }
}
