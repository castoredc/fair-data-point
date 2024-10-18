import React from 'react';
import Prism from 'prismjs/components/prism-core';
import 'prismjs/themes/prism.css';
import './Highlight.scss';
import { turtle } from './turtle';

interface HighlightProps {
    content: string;
}

const Highlight: React.FC<HighlightProps> = ({ content }) => {
    // Perform syntax highlighting
    let highlighted = Prism.highlight(content, turtle, 'turtle');

    // Replace specific tokens with custom HTML elements
    highlighted = highlighted.replace(/##record_id##/gm, '<span class="Record"><span>Record ID</span></span>');
    highlighted = highlighted.replace(/##record\[([^#]*)]##/gm, '<span class="Record"><span>Resource URL</span></span>');
    highlighted = highlighted.replace(/##record##/gm, '<span class="Record"><span>Resource URL</span></span>');
    highlighted = highlighted.replace(/##instance_id##/gm, '<span class="Instance"><span>Instance ID</span></span>');
    highlighted = highlighted.replace(/##([^#]*)##/gm, '<span class="Variable"><span>$1</span></span>');

    return <div className="Highlight" dangerouslySetInnerHTML={{ __html: highlighted }} />;
};

export default Highlight;