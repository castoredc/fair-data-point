import React from 'react';
import { OpenNewWindowIcon } from '@castoredc/matter-icons';

export const Node = (title: string, type: string, value: any, repeated: boolean) => {
    return (
        <div>
            {title}
            {type === 'internal' && <div className="NodeInfo Slug">{repeated ? <span>/{value}/[instance_id]</span> : <span>/{value}</span>}</div>}
            {type === 'external' && (
                <div className="NodeInfo PrefixedUri">
                    <span>
                        {value.prefixedValue !== null ? value.prefixedValue : `...:${value.base}`}
                        &nbsp;
                        <OpenNewWindowIcon width="8px" height="8px" />
                    </span>
                </div>
            )}
            {type === 'value' && (
                <div className="NodeInfo Value">
                    <span>
                        {value.value === 'annotated' ? 'Annotated value' : `Plain value (${value.dataType})`}
                        {repeated && ' - repeated'}
                    </span>
                </div>
            )}
            {type === 'literal' && (
                <div className="NodeInfo Literal">
                    <span>
                        {value.value} ({value.dataType})
                    </span>
                </div>
            )}
            {type === 'children' && (
                <div className="NodeInfo Literal">
                    <span>
                        Children of type
                        &nbsp;
                        <OpenNewWindowIcon width="8px" height="8px" />
                    </span>
                </div>
            )}
            {type === 'parents' && (
                <div className="NodeInfo Literal">
                    <span>
                        Parents of type
                        &nbsp;
                        <OpenNewWindowIcon width="8px" height="8px" />
                    </span>
                </div>
            )}
        </div>
    );
};