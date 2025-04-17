import React, { useState } from 'react';
import Highlight from '../Highlight';
import ScrollShadow from '../ScrollShadow';
import VisNetwork from '../Visualization/Network';
import AltRouteIcon from '@mui/icons-material/AltRoute';
import PageTabs from 'components/PageTabs';
import { DependenciesType, DependencyDescription, DependencyGroupType } from 'types/ModuleType';
import { Alert } from '@mui/material';
import NoResults from 'components/NoResults';

import ContentCopyIcon from '@mui/icons-material/ContentCopy';

type Visualization = {
    nodes: Node[];
    edges: Edge[];
};

type Node = {
    id: string;
    label: string;
    type: string;
    color?: {
        border: string;
        background: string;
    };
    shape?: string;
    margin?: number;
    font?: {
        color: string;
        size: number;
        face: string;
        multi?: string;
    };
};

type Edge = {
    from: string;
    to: string;
    font: {
        color: string;
        size: number;
        face: string;
    };
    color: string;
    smooth?: {
        enabled: boolean;
    };
    length: number;
};

type Props = {
    repeated: boolean;
    dependent: boolean;
    dependencies?: DependencyGroupType;
    rdf: string;
    visualization: Visualization;
};

const DataSpecificationModulePreview: React.FC<Props> = ({ repeated, dependent, dependencies, rdf, visualization }) => {
    const [selectedTab, setSelectedTab] = useState('visualization');

    const changeTab = (tabIndex: string) => {
        setSelectedTab(tabIndex);
    };

    const renderDependencies = (dependencies: DependenciesType[] | DependencyDescription[]): React.ReactNode => {
        return dependencies.map((dependency, index) => {
            if (dependency.type === 'group') {
                return (
                    <span className="DependencyGroup" key={index}>
                        {dependency.rules !== undefined && renderDependencies(dependency.rules)}
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

    const alerts = (
        <div className="DataModelModuleAlerts">
            {repeated && (
                <Alert severity="info" icon={<ContentCopyIcon />}>
                    This group is repeated for every instance of a specific
                    survey or report</Alert>
            )}
            {dependent && (
                <Alert
                    severity="info"
                    icon={<AltRouteIcon />}
                >
                    This group is dependent and will only be rendered when:
                    <div
                        className="DependencyDescription">{dependencies && renderDependencies(dependencies.description)}</div>
                </Alert>
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
            onChange={changeTab}
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
                                <NoResults>There is no preview available.</NoResults>
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
                                <NoResults>There is no preview available.</NoResults>
                            )}
                        </>
                    ),
                },
            }}
        />
    );
};

export default DataSpecificationModulePreview;
