import React, { Component } from 'react';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Button, LoadingOverlay, Space, Stack } from '@castoredc/matter';
import ListItem from 'components/ListItem';
import { localizedText } from '../../../util';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { apiClient } from 'src/js/network';
import SelectPage from 'components/SelectPage';

interface AddStudyProps extends AuthorizedRouteComponentProps {}

interface AddStudyState {
    studies: any;
    isLoading: boolean;
    selectedStudyId: string | null;
    catalog: any;
    submitDisabled: boolean;
}

export default class AddStudy extends Component<AddStudyProps, AddStudyState> {
    constructor(props) {
        super(props);

        this.state = {
            studies: [],
            isLoading: true,
            selectedStudyId: null,
            catalog: null,
            submitDisabled: false,
        };
    }

    getCatalog = () => {
        const { match } = this.props;

        apiClient
            .get('/api/catalog/' + match.params.catalog)
            .then(response => {
                this.setState(
                    {
                        catalog: response.data,
                    },
                    this.getStudies
                );
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== 'undefined') {
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred while retrieving information about the catalog" />);
                }
            });
    };

    getStudies = () => {
        apiClient
            .get('/api/castor/studies')
            .then(response => {
                this.setState({
                    studies: response.data,
                    isLoading: false,
                });
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== 'undefined') {
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }
            });
    };

    componentDidMount() {
        const { match } = this.props;

        this.getCatalog();
    }

    handleStudySelect = (studyId: string | null) => {
        this.setState({
            selectedStudyId: studyId,
        });
    };

    importStudy = () => {
        const { history, match } = this.props;
        const { selectedStudyId } = this.state;

        this.setState({
            submitDisabled: true,
        });

        apiClient
            .post('/api/catalog/' + match.params.catalog + '/study/import', {
                studyId: selectedStudyId,
            })
            .then(response => {
                history.push(`/dashboard/studies/${response.data.id}/`);
            })
            .catch(error => {
                if (error.response && error.response.status === 409) {
                    this.setState({
                        submitDisabled: false,
                    });
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                } else if (error.response && typeof error.response.data.error !== 'undefined') {
                    this.setState({
                        submitDisabled: false,
                    });
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                }
            });
    };

    render() {
        const { isLoading, studies, selectedStudyId, catalog, submitDisabled } = this.state;

        const selectedStudy = selectedStudyId ? studies.find(study => study.sourceId == selectedStudyId) : null;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading studies" />;
        }

        if(selectedStudy) {
            return (
                <SelectPage
                    title="Add a study"
                    description={`Please choose an item from your list of studies that you’d like to include in the ${localizedText(
                        catalog.metadata.title,
                        'en'
                    )}.`}
                    backButton={{
                        to: () => this.handleStudySelect(null),
                        label: 'Back to study list',
                    }}
                >
                    <div>
                        <ListItem key={selectedStudy.sourceId} title={selectedStudy.name} selectable={true} active={true} icon="study" />

                        <Stack distribution="center">
                            <Button disabled={submitDisabled} onClick={this.importStudy}>
                                Next
                            </Button>
                        </Stack>
                    </div>
                </SelectPage>
            );
        }


        return (
            <SelectPage
                title="Add a study"
                description={`Please choose an item from your list of studies that you’d like to include in the ${localizedText(
                    catalog.metadata.title,
                    'en'
                )}.`}
                backButton={{
                    to: '/dashboard/studies/add',
                    label: 'Back to catalogs',
                }}
            >
                {studies.length > 0 ? (
                    studies.map(study => {
                        return (
                            <ListItem
                                key={study.sourceId}
                                title={study.name}
                                selectable={true}
                                onClick={() => this.handleStudySelect(study.sourceId)}
                                icon="study"
                            />
                        );
                    })
                ) : <div className="NoResults">No studies found.</div> }
            </SelectPage>
        );
    }
}
