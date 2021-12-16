import React, {Component} from 'react';
import {ActionsCell, Button, CellText, DataGrid, LoadingOverlay, Stack} from "@castoredc/matter";
import '../Form.scss'
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import axios from "axios";
import Avatar from "react-avatar";
import ConfirmModal from "../../../modals/ConfirmModal";
import OrganizationModal from "../../../modals/OrganizationModal";
import {OrganizationType} from "../../../types/OrganizationType";

type OrganizationsFormProps = {
    studyId: string
}

type OrganizationsFormState = {
    organizations: any,
    countries: any,
    isLoading: boolean,
    openModal: boolean,
    openRemoveModal: boolean,
    selectedOrganization: OrganizationType | null,
}

export default class OrganizationsForm extends Component<OrganizationsFormProps, OrganizationsFormState> {
    constructor(props) {
        super(props);

        this.state = {
            organizations: [],
            countries:      [],
            isLoading:      false,
            openModal: false,
            openRemoveModal: false,
            selectedOrganization: null,
        };
    }

    getCountries = () => {
        axios.get('/api/countries')
            .then((response) => {
                this.setState({
                    countries: response.data,
                });
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }
            });
    };

    getCenters = () => {
        this.setState({
            isLoading: true,
        });

        axios.get('/api/study/' + this.props.studyId + '/centers')
            .then((response) => {
                this.setState({
                    organizations: response.data,
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
        this.getCountries();
        this.getCenters();
    }

    handleNewOrganization = () => {
        this.setState({
            openModal: true
        });
    }

    handleRemove = (organization) => {
        this.setState({
            openRemoveModal: true,
            selectedOrganization: organization
        });
    }

    removeOrganization = () => {
        const { studyId } = this.props;
        const { selectedOrganization } = this.state;

        this.closeModal();

        if(selectedOrganization) {
            axios.post('/api/study/' + studyId + '/centers/remove', selectedOrganization)
                .then((response) => {
                    toast.success(<ToastContent type="success" message={`${selectedOrganization.name} was successfully removed`}/>, {
                        position: "top-right",
                    });

                    this.getCenters();
                })
                .catch((error) => {
                    if (error.response && typeof error.response.data.error !== "undefined") {
                        toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                    } else {
                        toast.error(<ToastContent type="error" message="An error occurred"/>);
                    }
                });
        }
    };

    closeModal = () => {
        this.setState({
            openModal: false,
            openRemoveModal: false,
            selectedOrganization: null,
        }, () => {
            this.getCenters();
        });
    }

    render() {
        const { studyId } = this.props;
        const {organizations, countries, isLoading, openModal, openRemoveModal, selectedOrganization} = this.state;

        if(isLoading) {
            return <LoadingOverlay accessibleLabel="Loading organizations" />;
        }

        const organizationRows = organizations.map((organization) => {
            return {
                name: <CellText>
                    <Stack wrap={false}>
                        <Avatar name={organization.name} size="35px" round />
                        <span>
                            {organization.name}
                        </span>
                    </Stack>
                </CellText>,
                location: <CellText>{organization.city}, {organization.country}</CellText>,
                menu: <ActionsCell items={[{destination: () => this.handleRemove(organization), label: 'Remove center'}]} />,
            }
        });

        return <div>
            <OrganizationModal
                countries={countries}
                open={openModal}
                onClose={this.closeModal}
                studyId={studyId}
            />

            <ConfirmModal
                title="Remove organization"
                action="Remove organization"
                variant="primary"
                onConfirm={this.removeOrganization}
                onCancel={this.closeModal}
                show={openRemoveModal}
            >
                Are you sure you want remove the <strong>{selectedOrganization && selectedOrganization.name}</strong> center?
            </ConfirmModal>

            <Stack distribution="trailing" alignment="end">
                <Button buttonType="secondary" icon="add" onClick={this.handleNewOrganization}>
                    Add center
                </Button>
            </Stack>


            <DataGrid
                accessibleName="Organizations"
                emptyStateContent="No organizations found"
                rows={organizationRows}
                anchorRightColumns={1}
                columns={[
                    {
                        Header: 'Name',
                        accessor: 'name',
                        width: 280
                    },
                    {
                        Header: 'Location',
                        accessor: 'location',
                        width: 300
                    },
                    {
                        accessor: 'menu',
                        disableGroupBy: true,
                        disableResizing: true,
                        isInteractive: true,
                        isSticky: true,
                        maxWidth: 34,
                        minWidth: 34,
                        width: 34
                    }
                ]}
            />
        </div>;
    }
}