import React, {Component} from 'react';
import {ActionsCell, Button, CellText, DataGrid, LoadingOverlay, Stack} from "@castoredc/matter";

import '../Form.scss'
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import axios from "axios";
import Avatar from 'react-avatar';
import {PersonType} from "../../../types/PersonType";
import ContactModal from "../../../modals/ContactModal";
import ConfirmModal from "../../../modals/ConfirmModal";

type ContactsFormProps = {
    studyId: string
}

type ContactsFormState = {
    contacts: any,
    isLoading: boolean,
    openModal: boolean,
    openRemoveModal: boolean,
    selectedContact: PersonType | null,
}

export default class ContactsForm extends Component<ContactsFormProps, ContactsFormState> {
    constructor(props) {
        super(props);

        this.state = {
            contacts: [],
            isLoading: false,
            openModal: false,
            openRemoveModal: false,
            selectedContact: null,
        };
    }

    getContacts = () => {
        const {studyId} = this.props;

        this.setState({
            isLoading: true,
        });

        axios.get('/api/study/' + studyId + '/team')
            .then((response) => {
                this.setState({
                    contacts: response.data,
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
        this.getContacts();
    }

    handleNewContact = () => {
        this.setState({
            openModal: true
        });
    }

    handleRemove = (contact) => {
        this.setState({
            openRemoveModal: true,
            selectedContact: contact
        });
    }

    removeContact = () => {
        const {studyId} = this.props;
        const {selectedContact} = this.state;

        this.closeModal();

        if (selectedContact) {
            const name = [selectedContact.firstName, selectedContact.middleName, selectedContact.lastName].filter(Boolean).join(' ');

            axios.post('/api/study/' + studyId + '/team/remove', selectedContact)
                .then((response) => {
                    toast.success(<ToastContent type="success"
                                                message={`${name} was successfully removed as study contact`}/>, {
                        position: "top-right",
                    });

                    this.getContacts();
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
            selectedContact: null,
        }, () => {
            this.getContacts();
        });
    }

    render() {
        const {studyId} = this.props;
        const {contacts, isLoading, openModal, openRemoveModal, selectedContact} = this.state;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading contacts"/>;
        }

        const contactRows = contacts.map((contact) => {
            const name = [contact.firstName, contact.middleName, contact.lastName].filter(Boolean).join(' ');

            return {
                name: <CellText>
                    <Stack wrap={false}>
                        <Avatar name={name} size="35px" round/>
                        <span>
                            {name}
                        </span>
                    </Stack>
                </CellText>,
                email: <CellText>{contact.email}</CellText>,
                orcid: <CellText>{contact.orcid}</CellText>,
                menu: <ActionsCell items={[{destination: () => this.handleRemove(contact), label: 'Remove contact'}]}/>,
            }
        });

        const name = selectedContact && [selectedContact.firstName, selectedContact.middleName, selectedContact.lastName].filter(Boolean).join(' ');

        return <div>
            <ContactModal open={openModal} email={selectedContact ? selectedContact.email : undefined}
                          onClose={this.closeModal} studyId={studyId}/>

            <ConfirmModal
                title="Remove contact"
                action="Remove contact"
                variant="primary"
                onConfirm={this.removeContact}
                onCancel={this.closeModal}
                show={openRemoveModal}
            >
                Are you sure you want remove <strong>{selectedContact && name}</strong> as study contact?
            </ConfirmModal>

            <Stack distribution="trailing" alignment="end">
                <Button buttonType="secondary" icon="add" onClick={this.handleNewContact}>
                    Add contact
                </Button>
            </Stack>

            <DataGrid
                accessibleName="Contacts"
                emptyStateContent="No contacts found"
                rows={contactRows}
                anchorRightColumns={1}
                columns={[
                    {
                        Header: 'Name',
                        accessor: 'name',
                        width: 280
                    },
                    {
                        Header: 'Email',
                        accessor: 'email',
                        width: 300
                    },
                    {
                        Header: 'ORCID',
                        accessor: 'orcid',
                        width: 250
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