import React, { Component } from 'react';
import ModuleDependencyEditor from 'components/DependencyEditor/ModuleDependencyEditor';
import { formatQuery } from 'react-querybuilder';
import { PrefixType } from 'types/PrefixType';
import { NodeType } from 'types/NodeType';
import { DependencyGroupType } from 'types/ModuleType';
import { RuleGroupType } from 'react-querybuilder/types/types';
import Modal from 'components/Modal';
import Alert from '@mui/material/Alert';
import { AlertTitle } from '@mui/material';

type DependencyModalProps = {
    show: boolean;
    save: (query) => void;
    handleClose: () => void;
    valueNodes: NodeType[];
    prefixes: PrefixType[];
    dependencies: DependencyGroupType | null;
    modelType: string;
};

type DependencyModalState = {
    query: RuleGroupType | null;
    lengthValid: boolean;
};

class DependencyModal extends Component<DependencyModalProps, DependencyModalState> {
    constructor(props) {
        super(props);

        this.state = {
            query: null,
            lengthValid: true,
        };
    }

    shouldComponentUpdate(nextProps, nextState) {
        const { show } = this.props;
        const { lengthValid } = this.state;

        return show !== nextProps.show || lengthValid !== nextState.lengthValid;
    }

    handleChange = query => {
        this.setState({
            query: query,
        });
    };

    handleSave = valid => {
        const { query } = this.state;
        const { save } = this.props;

        if (query === null) {
            return;
        }

        const sqlQuery = formatQuery(query, 'sql') as string;
        const replaced = sqlQuery.replace(/\(|\)|and|or| /gi, '');

        if (replaced.length === 0) {
            this.setState({
                lengthValid: false,
            });
        } else if (valid) {
            this.setState({
                lengthValid: true,
            });

            save(query);
        }
    };

    handleClose = () => {
        const { handleClose } = this.props;

        this.setState(
            {
                lengthValid: true,
            },
            () => {
                handleClose();
            },
        );
    };

    render() {
        const { modelType, show, valueNodes, prefixes, dependencies } = this.props;
        const { lengthValid } = this.state;

        return (
            <Modal open={show} onClose={this.handleClose} title="Edit dependencies">
                {!lengthValid && (
                    <Alert severity="error">
                        <AlertTitle>An error occurred</AlertTitle>
                        description="There were no dependency conditions found, please add one or more conditions
                    </Alert>
                )}

                <ModuleDependencyEditor
                    modelType={modelType}
                    valueNodes={valueNodes}
                    prefixes={prefixes}
                    value={dependencies}
                    handleChange={this.handleChange}
                    save={this.handleSave}
                />
            </Modal>
        );
    }
}

export default DependencyModal;