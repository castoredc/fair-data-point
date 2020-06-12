import React, {Component} from 'react'
import Toggle from "../Toggle";
import Highlight from "../Highlight";

export default class DataModelModulePreview extends Component {
    render() {
        const { title, order, rdf } = this.props;

        return <div className="DataModelModulePreview">
            <Toggle title={`Module ${order}. ${title}`}>
                <Highlight content={rdf} />
            </Toggle>
        </div>;
    }
}