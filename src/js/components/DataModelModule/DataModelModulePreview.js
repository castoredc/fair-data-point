import React, {Component} from 'react'
import Highlight from "../Highlight";
import {Tabs} from "@castoredc/matter";

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

    render() {
        const {selectedTab} = this.state;
        const {rdf} = this.props;

        return <Tabs
            onChange={this.changeTab}
            selected={selectedTab}
            tabs={{
                rdf: {
                    title:   'RDF',
                    content: rdf !== '' ? <Highlight content={rdf}/> :
                                 <div className="NoResults">There is no preview available.</div>,
                },
            }}
        />;
    }
}