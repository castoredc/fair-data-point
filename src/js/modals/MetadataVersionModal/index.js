import React, {Component} from 'react'
import Modal from "react-bootstrap/Modal";
import FormItem from "../../components/Form/FormItem";
import {Button} from "@castoredc/matter";
import RadioGroup from "../../components/Input/RadioGroup";
import Container from "react-bootstrap/Container";

export default class MetadataVersionModal extends Component {
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
        const { currentVersion } = this.props;
        const versionType = event.target.value;

        const parsedVersion = currentVersion.split('.');

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

    render() {
        const { show, handleClose, onSave, currentVersion } = this.props;
        const { data, newVersion } = this.state;

        return <Modal show={show} onHide={handleClose} className="MetadataVersionModal">
            <Modal.Header closeButton>
                <Modal.Title>Save metadata</Modal.Title>
            </Modal.Header>
            <Modal.Body>
                <Container>
                    <FormItem label="Please indicate to what extent you made changes in the metadata">
                        <RadioGroup
                            options={versionTypes}
                            value={data.versionType}
                            name="versionType"
                            onChange={this.handleChange}
                        />
                    </FormItem>

                    {currentVersion && <FormItem label="Current version">
                        {currentVersion}
                    </FormItem>}

                    {newVersion && <FormItem label="New version">
                        {newVersion}
                    </FormItem>}

                </Container>
            </Modal.Body>
            <Modal.Footer>
                <Button type="submit" disabled={data.versionType === null} onClick={() => {onSave(data.versionType)}}>
                    Save metadata
                </Button>
            </Modal.Footer>
        </Modal>
    }
}

const versionTypes = [
    { value: 'major', label: 'Major changes' },
    { value: 'minor', label: 'Minor changes' },
    { value: 'patch', label: 'Patch' },
];