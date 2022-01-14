import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import {Button, Heading, LoadingOverlay, Separator, Space, Stack, StackItem} from "@castoredc/matter";
import {RouteComponentProps} from 'react-router-dom';
import ListItem from "components/ListItem";
import {localizedText} from "../../../util";
import {toRem} from "@castoredc/matter-utils";
import DocumentTitle from "components/DocumentTitle";
import BackButton from "components/BackButton";

interface AddStudyProps extends RouteComponentProps<any> {
}

interface AddStudyState {
    studies: any,
    isLoading: boolean,
    selectedStudyId: string | null,
    catalog: any,
    submitDisabled: boolean
}

export default class AddStudy extends Component<AddStudyProps, AddStudyState> {
    constructor(props) {
        super(props);

        this.state = {
            studies: [],
            isLoading: true,
            selectedStudyId: null,
            catalog: null,
            submitDisabled: false
        };
    }

    getCatalog = () => {
        const {match} = this.props;

        axios.get('/api/catalog/' + match.params.catalog)
            .then((response) => {
                this.setState({
                    catalog: response.data,
                }, this.getStudies);
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error"
                                              message="An error occurred while retrieving information about the catalog"/>);
                }
            });
    };

    getStudies = () => {
        axios.get('/api/castor/studies')
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
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }
            });
    };

    componentDidMount() {
        const {match} = this.props;

        this.getCatalog();
    }

    handleStudySelect = (studyId: string | null) => {
        this.setState({
            selectedStudyId: studyId,
        })
    };

    importStudy = () => {
        const {history, match} = this.props;
        const {selectedStudyId} = this.state;

        this.setState({
            submitDisabled: true,
        });

        axios.post('/api/catalog/' + match.params.catalog + '/study/import', {
            studyId: selectedStudyId,
        })
            .then((response) => {
                history.push(`/dashboard/studies/${response.data.id}/`);
            })
            .catch((error) => {
                if (error.response && error.response.status === 409) {
                    this.setState({
                        submitDisabled: false,
                    });
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else if (error.response && typeof error.response.data.error !== "undefined") {
                    this.setState({
                        submitDisabled: false,
                    });
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                }
            })
    };

    render() {
        const {isLoading, studies, selectedStudyId, catalog, submitDisabled} = this.state;

        const selectedStudy = selectedStudyId ? studies.find((study) => study.sourceId == selectedStudyId) : null;

        if(isLoading) {
            return <LoadingOverlay accessibleLabel="Loading studies"/>;
        }

        return <div style={{marginLeft: 'auto', marginRight: 'auto'}}>
            <DocumentTitle title="Add a study" />

            <Stack distribution="center">
                <StackItem style={{width: toRem(480), marginTop: '3.2rem'}}>
                    <BackButton to="/dashboard/studies/add">Back to catalogs</BackButton>

                    <Heading type="Section">Choose a Study</Heading>

                    <p>
                        {`Please choose an item from your list of studies that you’d like to include in the ${localizedText(catalog.metadata.title, 'en')}.`}
                    </p>

                    <Separator />

                    {(studies.length > 0 && selectedStudy) && <div>
                        <ListItem key={selectedStudy.sourceId}
                                  title={selectedStudy.name}
                                  selectable={true}
                                  active={true}
                                  icon="study"
                        />

                        <Stack distribution="trailing" alignment="end">
                            <Button buttonType="contentOnly" onClick={() => this.handleStudySelect(null)} style={{ padding: 0 }}>
                                Select another study
                            </Button>
                        </Stack>

                        <Space top="condensed" />

                        <Stack distribution="center">
                            <Button disabled={submitDisabled} onClick={this.importStudy}>Next</Button>
                        </Stack>
                    </div>}

                    {(studies.length > 0 && !selectedStudy) && studies.map((study) => {
                            return <ListItem key={study.sourceId}
                                             title={study.name}
                                             selectable={true}
                                             onClick={() => this.handleStudySelect(study.sourceId)}
                                             icon="study"
                            />
                        },
                    )}

                    {studies.length == 0 && <div className="NoResults">No studies found.</div>}
                </StackItem>
            </Stack>
        </div>;
    }
}
