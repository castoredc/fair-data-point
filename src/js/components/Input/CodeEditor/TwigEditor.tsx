import React, { Component, createRef } from 'react';
import './CodeEditor.scss';
import CodeMirror, { EditorFromTextArea } from 'codemirror';
import { FormLabel } from '@mui/material';

require('codemirror/mode/twig/twig');
require('codemirror/addon/display/autorefresh');

interface TwigEditorProps {
    label?: string;
    value: string;
    onChange: (value: string) => void;
}

class TwigEditor extends Component<TwigEditorProps> {
    private ref = createRef<HTMLTextAreaElement>();
    private codeMirror!: EditorFromTextArea;

    componentDidMount() {
        const { onChange } = this.props;

        if (this.ref.current) {
            this.codeMirror = CodeMirror.fromTextArea(this.ref.current, {
                mode: 'twig',
                autoRefresh: true,
            });

            this.codeMirror.on('change', editor => {
                onChange(editor.getValue());
            });
        }
    }

    render() {
        const { label, value } = this.props;

        return (
            <div className="TwigEditor">
                {label && <FormLabel>{label}</FormLabel>}
                <textarea ref={this.ref} autoComplete="off" defaultValue={value} />
            </div>
        );
    }
}

export default TwigEditor;