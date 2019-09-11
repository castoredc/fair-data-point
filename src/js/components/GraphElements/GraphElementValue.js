import React, {Component} from 'react'
import GraphElementLabel from "./GraphElementLabel";

class GraphElementValue extends Component {
    constructor (props) {
        super(props);
        this.state = {
            isLoading: false,
            label: ''
        };
    }
    componentDidMount() {
    }
    render() {
        const { isIri, isLocal, type, value } = this.props;

        if(isIri)
        {
            return <div className="GraphElementValue ValueUrlLocal">
                <GraphElementLabel iri={value} short={value} asLink={true} external={!isLocal} />
            </div>
        }

        return <div className="GraphElementValue">
            {value}
        </div>
    }
}

export default GraphElementValue
