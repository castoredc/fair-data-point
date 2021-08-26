import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import {Button, Heading, LoadingOverlay, Stack} from "@castoredc/matter";
import {RouteComponentProps} from 'react-router-dom';
import ListItem from "components/ListItem";
import DocumentTitle from "components/DocumentTitle";

interface DataModelsProps extends RouteComponentProps<any> {
}

interface DataModelsState {
    dataModels: any,
    isLoading: boolean,
}

export default class DataModels extends Component<DataModelsProps, DataModelsState> {
    constructor(props) {
        super(props);

        this.state = {
            dataModels: [],
            isLoading: false,
        };
    }

    getDataModels = () => {
        this.setState({
            isLoading: true,
        });

        axios.get('/api/model/my')
            .then((response) => {
                this.setState({
                    dataModels: response.data,
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
                    toast.error(<ToastContent type="error" message="An error occurred while loading your data models"/>);
                }
            });
    };

    componentDidMount() {
        this.getDataModels();
    }

    render() {
        const {history} = this.props;
        const {isLoading, dataModels} = this.state;

        return <div>
            <DocumentTitle title="Data models" />

            {isLoading && <LoadingOverlay accessibleLabel="Loading data models"/>}

            <Stack distribution="equalSpacing">
                <Heading type="Section">My data models</Heading>

                <Button buttonType="primary" onClick={() => history.push('/dashboard/data-models/add')}>
                    Add data model
                </Button>
            </Stack>

            <div>
                {dataModels.map((model) => {
                    return <ListItem
                        selectable={false}
                        link={`/dashboard/data-models/${model.id}`} title={model.title}
                    />
                })}
            </div>
        </div>;
    }
}
