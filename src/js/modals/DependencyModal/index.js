import React, {Component} from 'react'
import Modal from "../Modal";
import ModuleDependencyEditor from "../../components/DependencyEditor/ModuleDependencyEditor";
import {formatQuery} from "react-querybuilder";
import Alert from "../../components/Alert";

export default class DependencyModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            query: '',
            lengthValid: true
        }
    }

    shouldComponentUpdate(nextProps, nextState) {
        return (this.props.show !== nextProps.show || this.state.lengthValid !== nextState.lengthValid);
    }

    handleChange = (query) => {
        this.setState({
            query: query,
        });
    };

    handleSave = (valid) => {
        const {query} = this.state;
        const {save} = this.props;

        const sqlQuery = formatQuery(query, 'sql');
        const replaced = sqlQuery.replace(/\(|\)|and|or| /ig, '');

        if(replaced.length === 0) {
            this.setState({
                lengthValid: false
            });
        }
        else if (valid) {
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
        const {show, handleClose, valueNodes, prefixes, dependencies} = this.props;
        const {lengthValid} = this.state;

        return <Modal
            show={show}
            handleClose={this.handleClose}
            title="Edit dependencies"
            closeButton
            className="DependencyModal"
        >
            {!lengthValid && <Alert variant="error" icon="errorCircled">
                <strong>An error occurred</strong>
                There were no dependency conditions found, please add one or more conditions
            </Alert>}

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