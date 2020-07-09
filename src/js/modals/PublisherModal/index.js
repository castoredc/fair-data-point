import React, {Component} from 'react'
import {ValidatorForm} from "react-form-validator-core";
import FormItem from "../../components/Form/FormItem";
import {Button, Stack} from "@castoredc/matter";
import Modal from "../Modal";
import RadioGroup from "../../components/Input/RadioGroup";
import PersonForm from "../../components/Form/Agent/PersonForm";
import OrganizationForm from "../../components/Form/Agent/OrganizationForm";
import ConfirmModal from "../ConfirmModal";

export default class PublisherModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            type: props.type ? props.type : 'person',
            data: props.data ? props.data : defaultData,
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {show, data, type} = this.props;

        if (show !== prevProps.show || data !== prevProps.data) {
            this.setState({
                type: type ? type : 'person',
                data: data ? data : defaultData,
            })
        }
    }

    componentDidMount() {
        ValidatorForm.addValidationRule('isUrl', (value) => {
            var pattern = new RegExp('^((ft|htt)ps?:\\/\\/)?' + // protocol
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name and extension
                '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
                '(\\:\\d+)?' + // port
                '(\\/[-a-z\\d%@_.~+&:]*)*' + // path
                '(\\?[;&a-z\\d%@_.,~+&:=-]*)?' + // query string
                '(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
            return pattern.test(value);
        });
    }

    handleTypeChange = (event) => {
        this.setState({
            type: event.target.value,
        });
    };

    handleDataChange = (data) => {
        this.setState({
            data: data,
        });
    };

    handleSubmit = () => {
        const {save} = this.props;
        const {type, data} = this.state;

        if (this.form.isFormValid()) {
            save({
                type: type,
                ...data
            });
        }
    };

    render() {
        const {show, handleClose, countries, deletePublisher} = this.props;
        const {type, data, isLoading} = this.state;

        return <Modal
            show={show}
            handleClose={handleClose}
            title={data.id ? 'Edit publisher' : 'Add publisher'}
            closeButton
            footer={(
                <Stack>
                <Button type="submit" disabled={isLoading} onClick={() => this.form.submit()}>
                    {data.id ? 'Edit publisher' : 'Add publisher'}
                </Button>
                    {data.id && <ConfirmModal
                        title="Delete publisher"
                        action="Delete publisher"
                        variant="danger"
                        onConfirm={deletePublisher}
                        includeButton={true}
                    >
                        Are you sure you want to delete this publisher?
                    </ConfirmModal>}
                </Stack>
            )}
        >
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                <FormItem label="Type">
                    <RadioGroup
                        options={[
                            {
                                label: 'Person',
                                value: 'person',
                            },
                            {
                                label: 'Organization',
                                value: 'organization',
                            },
                        ]}
                        onChange={this.handleTypeChange}
                        value={type}
                        name="type"
                        variant="horizontal"
                    />
                </FormItem>

                {type === 'person' && <PersonForm data={data} handleDataChange={this.handleDataChange}/>}
                {type === 'organization' &&
                <OrganizationForm data={data} handleDataChange={this.handleDataChange} countries={countries}/>}
            </ValidatorForm>
        </Modal>
    }
}

const defaultData = {
    id: null
};