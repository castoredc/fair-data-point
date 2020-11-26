import React, {Component} from 'react'
import FormItem from "../../components/Form/FormItem";
import {Button} from "@castoredc/matter";
import RadioGroup from "../../components/Input/RadioGroup";
import Modal from "../Modal";
import {ValidatorForm} from "react-form-validator-core";

export default class DataDictionaryVersionModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
                versionType: null
            },
            newVersion: null
        };
    }

    handleChange = (event) => {
        const { data } = this.state;
        const { latestVersion } = this.props;
        const versionType = event.target.value;

        const parsedVersion = latestVersion.split('.');

        const major = parseInt(parsedVersion[0]);
        const minor = parseInt(parsedVersion[1]);
        const patch = parseInt(parsedVersion[2]);

        let newVersion = '';

        if(versionType === 'major') {
            newVersion = (major + 1) + '.' + 0 + '.' + 0;
        } else if(versionType === 'minor') {
            newVersion = major + '.' + (minor + 1) + '.' + 0;
        } else if(versionType === 'patch') {
            newVersion = major + '.' + minor + '.' + (patch + 1);
        }

        this.setState({
            data: {
                versionType: versionType
            },
            newVersion: newVersion
        });
    };

    handleSubmit = () => {
        const {onSave} = this.props;
        const {data} = this.state;

        if (this.form.isFormValid()) {
            onSave(data.versionType)
        }
    };

    render() {
        const { show, handleClose, onSave, latestVersion } = this.props;
        const { data, newVersion } = this.state;

        const required = "This field is required";

        return <Modal
            show={show}
            className="DataDictionaryVersionModal"
            handleClose={handleClose}
            title="Create version"
            closeButton
            footer={(
                <Button type="submit" disabled={data.versionType === null} onClick={() => this.form.submit()}>
                    Create version
                </Button>
            )}
        >
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                <FormItem label="Please indicate to what extent you are making changes in the data dictionary">
                    <RadioGroup
                        validators={['required']}
                        errorMessages={[required]}
                        options={versionTypes}
                        value={data.versionType}
                        name="versionType"
                        onChange={this.handleChange}
                    />
                </FormItem>

                <FormItem label="Latest version">
                    {latestVersion}
                </FormItem>

                {newVersion && <FormItem label="New version">
                    {newVersion}
                </FormItem>}
            </ValidatorForm>
        </Modal>
    }
}

const versionTypes = [
    { value: 'major', label: 'Major changes' },
    { value: 'minor', label: 'Minor changes' },
    { value: 'patch', label: 'Patch' },
];