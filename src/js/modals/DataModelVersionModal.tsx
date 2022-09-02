import React, { Component } from 'react';
import FormItem from 'components/Form/FormItem';
import { Button, Choice, Modal } from '@castoredc/matter';

type DataModelVersionModalProps = {
    latestVersion: string;
    show: boolean;
    handleClose: () => void;
    handleSave: (versionType: string) => void;
};

type DataModelVersionModalState = {
    versionType: string | null;
    newVersion: string | null;
};

export default class DataModelVersionModal extends Component<DataModelVersionModalProps, DataModelVersionModalState> {
    constructor(props) {
        super(props);

        this.state = {
            versionType: null,
            newVersion: null,
        };
    }

    handleChange = event => {
        const { latestVersion } = this.props;
        const versionType = event.target.value;

        const parsedVersion = latestVersion.split('.');

        const major = parseInt(parsedVersion[0]);
        const minor = parseInt(parsedVersion[1]);
        const patch = parseInt(parsedVersion[2]);

        let newVersion = '';

        if (versionType === 'major') {
            newVersion = major + 1 + '.' + 0 + '.' + 0;
        } else if (versionType === 'minor') {
            newVersion = major + '.' + (minor + 1) + '.' + 0;
        } else if (versionType === 'patch') {
            newVersion = major + '.' + minor + '.' + (patch + 1);
        }

        this.setState({
            versionType: versionType,
            newVersion: newVersion,
        });
    };

    render() {
        const { show, handleClose, handleSave, latestVersion } = this.props;
        const { versionType, newVersion } = this.state;

        return (
            <Modal open={show} onClose={handleClose} title="Create version" accessibleName="Create version">
                <Choice
                    options={[
                        { value: 'major', labelText: 'Major changes' },
                        { value: 'minor', labelText: 'Minor changes' },
                        { value: 'patch', labelText: 'Patch' },
                    ]}
                    value={versionType ? versionType : undefined}
                    name="versionType"
                    onChange={this.handleChange}
                    labelText="Please indicate to what extent you are making changes in the data model"
                />

                <FormItem label="Latest version">{latestVersion}</FormItem>

                {newVersion && <FormItem label="New version">{newVersion}</FormItem>}

                <Button
                    type="submit"
                    disabled={versionType === null}
                    onClick={() => {
                        handleSave(versionType ? versionType : '');
                    }}
                >
                    Save metadata
                </Button>
            </Modal>
        );
    }
}
