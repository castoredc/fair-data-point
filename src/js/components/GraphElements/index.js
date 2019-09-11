import React, {Component} from 'react'
import GraphElement from "./GraphElement";
import Alert from 'react-bootstrap/Alert';
import './GraphElements.scss'

class GraphElements extends Component {
    constructor (props) {
        super(props);
        this.state = {
            isLoading: false
        };
    }
    render() {
        const { graph } = this.props;

        const toGraphElement = (item, index) => {
            return <GraphElement key={index} {... item} />
        };

        return <div className="GraphElements">
            {graph.length > 0 ? graph.map(toGraphElement) :
                <Alert variant="info">
                    <strong>No information found</strong><br/>
                </Alert>}
        </div>
    }
}

export default GraphElements
