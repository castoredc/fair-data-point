import React, {Component, createRef} from "react";
import {Network} from "vis-network";
import {classNames} from "../../util";
import './Network.scss';

class VisNetwork extends Component {
    constructor(props) {
        super(props);
        this.network = {};
        this.appRef = createRef();
    }

    componentDidMount() {
        this.renderNetwork();
    }

    componentDidUpdate(prevProps) {
        if (this.props.nodes !== prevProps.nodes || this.props.edges !== prevProps.edges) {
            this.renderNetwork();
        }
    }

    renderNetwork = () => {
        const { nodes, edges } = this.props;

        const data = {
            nodes: nodes,
            edges: edges
        };

        const options = {
            autoResize: true,
            height: '100%',
            width: '100%',
            layout: {
                randomSeed: 1,
                improvedLayout: true,
            },
            interaction: {
                dragNodes: false,
                hover: true,
            },
            physics: {
                enabled: true,
                barnesHut: {
                    avoidOverlap: .5
                },
            }
        };

        this.network = new Network(this.appRef.current, data, options);
    };

    render() {
        const {className} = this.props;
        return (
            <div className={classNames('Network', className)} ref={this.appRef} />
        );
    }
}

export default VisNetwork;