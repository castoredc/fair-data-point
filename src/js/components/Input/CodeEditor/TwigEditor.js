import React, {Component} from 'react';
import './CodeEditor.scss'
import CodeMirror from 'codemirror';
import {FormLabel} from "@castoredc/matter";
require('codemirror/mode/twig/twig');
require('codemirror/addon/display/autorefresh');

export default class TwigEditor extends Component {
    constructor(props) {
        super(props);
        this.ref = React.createRef();
    }

    componentDidMount() {
        const { onChange } = this.props;

        this.codeMirror = CodeMirror.fromTextArea(
            this.ref.current,
            {
                mode: 'twig',
                autoRefresh: true
            }
        );

        this.codeMirror.on('change', editor => {
            onChange(editor.getValue());
        })
    }

    render() {
        const { label, value } = this.props;

        return <div className="TwigEditor">
            {label && <FormLabel>{label}</FormLabel>}
            <textarea ref={this.ref} autoComplete="off">{value}</textarea>
        </div>;
    }
}