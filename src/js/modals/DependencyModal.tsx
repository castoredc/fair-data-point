import React, {Component} from 'react'
import ModuleDependencyEditor from "components/DependencyEditor/ModuleDependencyEditor";
import {formatQuery} from "react-querybuilder";
import {Banner, Modal} from "@castoredc/matter";
import {PrefixType} from "types/PrefixType";
import {NodeType} from "types/NodeType";
import {DependenciesType} from "types/ModuleType";
import {RuleGroupType} from "react-querybuilder/types/types";

type DependencyModalProps = {
    show: boolean,
    save: (query) => void,
    handleClose: () => void,
    valueNodes: NodeType[],
    prefixes: PrefixType[],
    dependencies: DependenciesType[] | null,
};

type DependencyModalState = {
    query: RuleGroupType | null,
    lengthValid: boolean;
};

export default class DependencyModal extends Component<DependencyModalProps, DependencyModalState> {
    constructor(props) {
        super(props);

        this.state = {
            query: null,
            lengthValid: true
        }
    }

    shouldComponentUpdate(nextProps, nextState) {
        const {show} = this.props;
        const {lengthValid} = this.state;

        return (show !== nextProps.show || lengthValid !== nextState.lengthValid);
    }

    handleChange = (query) => {
        this.setState({
            query: query,
        });
    };

    handleSave = (valid) => {
        const {query} = this.state;
        const {save} = this.props;

        if(query === null) {
            return;
        }

        const sqlQuery = formatQuery(query, 'sql') as string;
        const replaced = sqlQuery.replace(/\(|\)|and|or| /ig, '');

        if (replaced.length === 0) {
            this.setState({
                lengthValid: false
            });
        } else if (valid) {
            this.setState({
                lengthValid: true
            });

            save(query);
        }
    };

    handleClose = () => {
        const {handleClose} = this.props;

        this.setState({
            lengthValid: true
        }, () => {
            handleClose();
        });
    };

    render() {
        const {show, valueNodes, prefixes, dependencies} = this.props;
        const {lengthValid} = this.state;

        return <Modal
            open={show}
            onClose={this.handleClose}
            title="Edit dependencies"
            accessibleName="Edit dependencies"
        >
            {!lengthValid && <Banner
                type="error"
                title="An error occurred"
                description="There were no dependency conditions found, please add one or more conditions"
            />}

            <ModuleDependencyEditor
                valueNodes={valueNodes}
                prefixes={prefixes}
                value={dependencies}
                handleChange={this.handleChange}
                save={this.handleSave}
            />
        </Modal>
    }
}