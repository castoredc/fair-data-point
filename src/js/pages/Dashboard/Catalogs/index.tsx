import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import {Button, Heading, LoadingOverlay, Stack} from "@castoredc/matter";
import {RouteComponentProps} from 'react-router-dom';
import ListItem from "components/ListItem";
import DocumentTitle from "components/DocumentTitle";

interface CatalogsProps extends RouteComponentProps<any> {
}

interface CatalogsState {
    catalogs: any,
    isLoading: boolean,
}

export default class Catalogs extends Component<CatalogsProps, CatalogsState> {
    constructor(props) {
        super(props);

        this.state = {
            catalogs: [],
            isLoading: false,
        };
    }

    getCatalogs = () => {
        this.setState({
            isLoading: true,
        });

        axios.get('/api/catalog')
            .then((response) => {
                this.setState({
                    catalogs: response.data,
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
                    toast.error(<ToastContent type="error" message="An error occurred while loading your catalogs"/>);
                }
            });
    };

    componentDidMount() {
        this.getCatalogs();
    }

    render() {
        const {history} = this.props;
        const {isLoading, catalogs} = this.state;

        return <div>
            <DocumentTitle title="Data models" />

            {isLoading && <LoadingOverlay accessibleLabel="Loading catalogs"/>}

            <Stack distribution="equalSpacing">
                <Heading type="Section">My catalogs</Heading>

                <Button buttonType="primary" onClick={() => history.push('/dashboard/catalogs/add')}>
                    Add catalog
                </Button>
            </Stack>

            <div>
                {catalogs.map((catalog) => {
                    return <ListItem
                        selectable={false}
                        link={`/dashboard/catalogs/${catalog.id}`} title={catalog.title}
                    />
                })}
            </div>
        </div>;
    }
}
