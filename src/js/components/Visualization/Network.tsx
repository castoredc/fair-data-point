import React, { useEffect, useRef } from 'react';
import { Network } from 'vis-network';
import { classNames } from '../../util';
import './Network.scss';

type VisNetworkProps = {
    className?: string;
    nodes: any[];
    edges: any[];
};

const VisNetwork: React.FC<VisNetworkProps> = ({ className, nodes, edges }) => {
    const appRef = useRef<HTMLDivElement>(null);
    const networkRef = useRef<Network | null>(null);

    useEffect(() => {
        renderNetwork();
    }, [nodes, edges]); // Run effect when nodes or edges change

    const renderNetwork = () => {
        if (!appRef.current) return;

        const data = {
            nodes: nodes,
            edges: edges,
        };

        const options = {
            autoResize: true,
            height: '100%',
            width: '100%',
            layout: {
                randomSeed: 1,
                improvedLayout: true,
                hierarchical: {
                    levelSeparation: 150,
                    treeSpacing: 200,
                    blockShifting: true,
                    edgeMinimization: true,
                    parentCentralization: true,
                    direction: 'UD',
                    nodeSpacing: 300,
                    sortMethod: 'directed',
                },
            },
            interaction: {
                dragNodes: false,
                hover: true,
            },
            physics: {
                hierarchicalRepulsion: {
                    nodeDistance: 180,
                },
            },
        };

        networkRef.current = new Network(appRef.current, data, options);
    };

    return <div className={classNames('Network', className)} ref={appRef} />;
};

export default VisNetwork;