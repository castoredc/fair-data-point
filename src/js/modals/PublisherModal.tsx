import React, {Component} from 'react'
import {Choice, Modal} from "@castoredc/matter";
import PersonForm from "components/Form/Agent/PersonForm";
import OrganizationForm from "components/Form/Agent/OrganizationForm";

type PublisherModalProps = {
    open: boolean,
    onClose: () => void,
    handleSave: (publisher) => void,
    countries: any,
}

type PublisherModalState = {
    type: string,
}

export default class PublisherModal extends Component<PublisherModalProps, PublisherModalState> {
    constructor(props) {
        super(props);

        this.state = {
            type: 'person',
        };
    }

    handleTypeChange = (event) => {
        this.setState({
            type: event.target.value,
        });
    };

    handleSubmit = (values, {setSubmitting}) => {
        const {handleSave} = this.props;
        const {type} = this.state;

        handleSave({
            type: type,
            [type]: values
        });

        setSubmitting(false);
    }

    render() {
        const {open, onClose, countries} = this.props;
        const {type} = this.state;

        const title = 'Add publisher';

        return <Modal
            open={open}
            title={title}
            accessibleName={title}
            onClose={onClose}
        >
            <Choice
                labelText="Type"
                options={[
                    {
                        labelText: 'Person',
                        value: 'person',
                        checked: type === 'person',
                    },
                    {
                        labelText: 'Organization',
                        value: 'organization',
                        checked: type === 'organization',
                    },
                ]}
                name="type"
                collapse={true}
                onChange={this.handleTypeChange}
            />

            {type === 'person' && <PersonForm handleSubmit={this.handleSubmit}/>}

            {type === 'organization' && <OrganizationForm countries={countries} handleSubmit={this.handleSubmit}/>}
        </Modal>
    }
}