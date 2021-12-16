import React, {Component} from 'react'
import {Button, Choice, Modal} from "@castoredc/matter";
import FormItem from "components/Form/FormItem";


type MetadataVersionModalProps = {
    currentVersion: string,
    open: boolean,
    onClose: () => void,
    handleSave: (versionType: string) => void,
}

type MetadataVersionModalState = {
    versionType: string | null,
    newVersion: string | null,
}


export default class MetadataVersionModal extends Component<MetadataVersionModalProps, MetadataVersionModalState> {
    constructor(props) {
        super(props);

        this.state = {
            versionType: null,
            newVersion: null
        };
    }

    handleChange = (event) => {
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
            versionType: versionType,
            newVersion: newVersion
        });
    };

    render() {
        const { open, onClose, handleSave, currentVersion } = this.props;
        const { versionType, newVersion } = this.state;

        const title = "Save metadata";

        return <Modal
            open={open}
            title={title}
            accessibleName={title}
            onClose={onClose}
        >
            <Choice
                options={[
                    { value: 'major', labelText: 'Major changes' },
                    { value: 'minor', labelText: 'Minor changes' },
                    { value: 'patch', labelText: 'Patch' },
                ]}
                value={versionType ? versionType : undefined}
                name="versionType"
                onChange={this.handleChange}
                labelText="Please indicate to what extent you made changes in the metadata"
            />

            {currentVersion && <FormItem label="Current version">
                {currentVersion}
            </FormItem>}

            {newVersion && <FormItem label="New version">
                {newVersion}
            </FormItem>}


            <Button type="submit" disabled={versionType === null} onClick={() => {handleSave(versionType ? versionType : '')}}>
                Save metadata
            </Button>
        </Modal>
    }
}