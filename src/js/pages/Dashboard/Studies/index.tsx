import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import {Button, Heading, LoadingOverlay, Stack} from "@castoredc/matter";
import {RouteComponentProps} from 'react-router-dom';
import ListItem from "components/ListItem";
import DocumentTitle from "components/DocumentTitle";

interface StudiesProps extends RouteComponentProps<any> {
}

interface StudiesState {
    studies: any,
    isLoading: boolean,
}

export default class Studies extends Component<StudiesProps, StudiesState> {
    constructor(props) {
        super(props);

        this.state = {
            studies: [],
            isLoading: false,
        };
    }

    getStudies = () => {
        this.setState({
            isLoading: true,
        });

        axios.get('/api/study/my')
            .then((response) => {
                this.setState({
                    studies: response.data,
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

    componentDidMount() {
        this.getStudies();
    }

    render() {
        const {history} = this.props;
        const {isLoading, studies} = this.state;

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
            </div>
        </div>;
    }
}
