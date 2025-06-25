import React, { Component } from 'react';
import Button from '@mui/material/Button';
import LoadingOverlay from 'components/LoadingOverlay';
import ListItem from 'components/ListItem';
import { localizedText } from '../../../util';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { apiClient } from 'src/js/network';
import SelectPage from 'components/SelectPage';
import Stack from '@mui/material/Stack';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import NoResults from 'components/NoResults';

interface AddStudyProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
}

interface AddStudyState {
    studies: any;
    isLoading: boolean;
    selectedStudyId: string | null;
    catalog: any;
    submitDisabled: boolean;
}

class AddStudy extends Component<AddStudyProps, AddStudyState> {
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
        const { match, notifications } = this.props;

        apiClient
            .get('/api/catalog/' + match.params.catalog)
            .then(response => {
                this.setState(
                    {
                        catalog: response.data,
                    },
                    this.getStudies,
                );
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred while retrieving information about the catalog', { variant: 'error' });
                }
            });
    };

    getStudies = () => {
        const { notifications } = this.props;

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
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
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
        const { history, match, notifications } = this.props;
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
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else if (error.response && typeof error.response.data.error !== 'undefined') {
                    this.setState({
                        submitDisabled: false,
                    });
                    notifications.show(error.response.data.error, { variant: 'error' });
                }
            });
    };

    render() {
        const { isLoading, studies, selectedStudyId, catalog, submitDisabled } = this.state;
        const { history } = this.props;

        const selectedStudy = selectedStudyId ? studies.find(study => study.sourceId == selectedStudyId) : null;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading studies" />;
        }

        if (selectedStudy) {
            return (
                <SelectPage
                    title="Add a study"
                    description={`Please choose an item from your list of studies that you’d like to include in the ${localizedText(
                        catalog.metadata.title,
                        'en',
                    )}.`}
                    backButton={{
                        to: () => this.handleStudySelect(null),
                        label: 'Back to study list',
                    }}
                    history={history}
                >
                    <div>
                        <ListItem key={selectedStudy.sourceId} title={selectedStudy.name}
                                  active={true} icon="study" />

                        <Stack direction="row" sx={{ justifyContent: 'flex-end', mt: 2 }}>
                            <Button disabled={submitDisabled} onClick={this.importStudy} variant="contained">
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
                    'en',
                )}.`}
                backButton={{
                    to: '/dashboard/studies/add',
                    label: 'Back to catalogs',
                }}
                history={history}
            >
                {studies.length > 0 ? (
                    studies.map(study => {
                        return (
                            <ListItem
                                key={study.sourceId}
                                title={study.name}
                                onClick={() => this.handleStudySelect(study.sourceId)}
                                icon="study"
                            />
                        );
                    })
                ) : (
                    <NoResults>No studies found.</NoResults>
                )}
            </SelectPage>
        );
    }
}

export default withNotifications(AddStudy);