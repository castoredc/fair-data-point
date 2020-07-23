import React, {Component} from 'react'
import Highlight from "../Highlight";
import {Tabs} from "@castoredc/matter";
import Alert from "../Alert";

export default class DataModelModulePreview extends Component {
    constructor(props) {
        super(props);
        this.state = {
            selectedTab: 'rdf',
        };
    }

    changeTab = (tabIndex) => {
        this.setState({
            selectedTab: tabIndex,
        });
    };

    renderDependencies = (dependencies) => {
        return dependencies.map((dependency, index) => {
            if (dependency.type === 'group') {
                return <span className="DependencyGroup" key={index}>{this.renderDependencies(dependency.rules)}</span>;
            } else if (dependency.type === 'combinator') {
                return <span className="DependencyCombinator" key={index}>{dependency.text}</span>;
            } else if (dependency.type === 'rule') {
                return <span className="DependencyRule" key={index}>{dependency.text}</span>;
            }
        })
    };

    render() {
        const {selectedTab} = this.state;
        const {repeated, dependent, dependencies, rdf} = this.props;

        let alerts = <>
            {repeated && <Alert
                variant="info"
                icon="copy">
                This module is repeated for every instance of a specific survey or report
            </Alert>}
            {dependent && <Alert
                variant="info"
                icon="decision">
                This module is dependent and will only be rendered when:

                <div className="DependencyDescription">{this.renderDependencies(dependencies.description)}</div>
            </Alert>}
        </>;

        return <Tabs
            onChange={this.changeTab}
            selected={selectedTab}
            tabs={{
                rdf: {
                    title:   'RDF',
                    content: <>
                                 {alerts}
                                 {rdf !== '' ? <Highlight content={rdf}/> :
                                     <div className="NoResults">There is no preview available.</div>}
                             </>,
                },
            }}
        />;
    }
}