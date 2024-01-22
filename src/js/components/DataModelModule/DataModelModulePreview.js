import React, { Component } from 'react';
import Highlight from '../Highlight';
import { Banner } from '@castoredc/matter';
import ScrollShadow from '../ScrollShadow';
import VisNetwork from '../Visualization/Network';
import { CopyIcon, DecisionIcon } from '@castoredc/matter-icons';
import PageTabs from 'components/PageTabs';

export default class DataModelModulePreview extends Component {
    constructor(props) {
        super(props);
        this.state = {
            selectedTab: 'visualization',
        };
    }

    changeTab = tabIndex => {
        this.setState({
            selectedTab: tabIndex,
        });
    };

    renderDependencies = dependencies => {
        return dependencies.map((dependency, index) => {
            if (dependency.type === 'group') {
                return (
                    <span className="DependencyGroup" key={index}>
                        {this.renderDependencies(dependency.rules)}
                    </span>
                );
            } else if (dependency.type === 'combinator') {
                return (
                    <span className="DependencyCombinator" key={index}>
                        {dependency.text}
                    </span>
                );
            } else if (dependency.type === 'rule') {
                return (
                    <span className="DependencyRule" key={index}>
                        {dependency.text}
                    </span>
                );
            }
        });
    };

    render() {
        const { selectedTab } = this.state;
        const { repeated, dependent, dependencies, rdf, visualization } = this.props;

        let alerts = (
            <div className="DataModelModuleAlerts">
                {repeated && (
                    <Banner
                        compact
                        customIcon={<CopyIcon />}
                        description="This group is repeated for every instance of a specific survey or report"
                    />
                )}
                {dependent && (
                    <Banner
                        compact
                        customIcon={<DecisionIcon />}
                        description={
                            <>
                                This group is dependent and will only be rendered when:
                                <div className="DependencyDescription">{this.renderDependencies(dependencies.description)}</div>
                            </>
                        }
                    />
                )}
            </div>
        );

        const nodes = visualization.nodes.map(node => {
            node.shape = 'box';
            node.margin = 10;

            if (node.type === 'value') {
                node.shape = 'ellipse';
                node.margin = 20;

                node.color = {
                    border: '#000000',
                    background: '#ffffff',
                };
            }

            if (node.type === 'record') {
                node.shape = 'circle';
            }

            if (node.type === 'external') {
                node.color = {
                    border: '#c4474d',
                    background: '#ffcbcb',
                };
            }

            if (node.type === 'internal') {
                node.color = {
                    border: '#efcc6f',
                    background: '#ffe5c8',
                };
            }

            node.font = {
                color: '#1b2c4b',
                size: 13,
                face: 'Lato',
                multi: 'html',
            };

            return node;
        });

        const edges = visualization.edges.map(edge => {
            edge.font = {
                color: '#1b2c4b',
                size: 13,
                face: 'Lato',
            };

            edge.color = '#000000';

            edge.smooth = {
                enabled: false,
            };

            edge.length = 200;

            return edge;
        });

        return (
            <PageTabs
                    onChange={this.changeTab}
                    selected={selectedTab}
                    tabs={{
                        visualization: {
                            title: 'Visualization',
                            content: (
                                <div className="FullHeightPageTab">
                                    {alerts}
                                    {rdf !== '' ? (
                                        <VisNetwork className="FullHeightNetwork" nodes={nodes} edges={edges} />
                                    ) : (
                                        <div className="NoResults">There is no preview available.</div>
                                    )}
                                </div>
                            ),
                        },
                        rdf: {
                            title: 'RDF',
                            content: (
                                <>
                                    {alerts}
                                    {rdf !== '' ? (
                                        <ScrollShadow>
                                            <Highlight content={rdf} />
                                        </ScrollShadow>
                                    ) : (
                                        <div className="NoResults">There is no preview available.</div>
                                    )}
                                </>
                            ),
                        },
                    }}
                />
        );
    }
}
