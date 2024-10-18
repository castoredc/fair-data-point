import React, { useMemo } from 'react';
import { CellText, DataGrid, Link } from '@castoredc/matter';

type SPARQLDataTableProps = {
    vars: string[];
    bindings: any[];
    prefixes?: any[];
    fullUrl: string;
};

const SPARQLDataTable: React.FC<SPARQLDataTableProps> = ({ vars, bindings, prefixes, fullUrl }) => {
    const getColumns = () => {
        return vars.map(variable => ({
            Header: variable,
            accessor: variable,
        }));
    };

    const getRows = () => {
        return bindings.map((binding, rowId) => {
            const row: Record<string, JSX.Element> = {};

            vars.forEach(sparqlVar => {
                row[sparqlVar] = (
                    <CellText key={sparqlVar}>
                        {sparqlVar in binding ? getCellContent(binding, sparqlVar) : ''}
                    </CellText>
                );
            });

            return row;
        });
    };

    const getUriLinkFromBinding = (binding: any) => {
        const href = binding.value;
        let visibleString = href;
        let prefixed = false;

        if (prefixes) {
            for (const prefixLabel in prefixes) {
                if (visibleString.indexOf(prefixes[prefixLabel]) === 0) {
                    visibleString = `${prefixLabel}:${href.substring(prefixes[prefixLabel].length)}`;
                    prefixed = true;
                    break;
                }
            }
        }

        if (visibleString.indexOf(fullUrl) === 0) {
            visibleString = `:${href.substring(fullUrl.length)}`;
            prefixed = true;
        }

        return (
            <span>
                {prefixed ? '' : '<'}
                <Link className="iri" href={href} target="_blank">
                    {visibleString}
                </Link>
                {prefixed ? '' : '>'}
            </span>
        );
    };

    const getCellContent = (binding: any, sparqlVar: string) => {
        const currentBinding = binding[sparqlVar];
        let content = <span></span>;

        if (currentBinding.type === 'uri') {
            content = getUriLinkFromBinding(currentBinding);
        } else {
            content = <span className="nonIri">{formatLiteral(currentBinding)}</span>;
        }

        return content;
    };

    const formatLiteral = (literalBinding: any) => {
        let stringRepresentation = literalBinding.value;

        if (literalBinding['xml:lang']) {
            stringRepresentation = (
                <span>
                    {stringRepresentation} <sup>{literalBinding['xml:lang']}</sup>
                </span>
            );
        } else if (literalBinding.datatype) {
            const dataType = getUriLinkFromBinding({
                type: 'uri',
                value: literalBinding.datatype,
            });

            stringRepresentation = (
                <span>
                    {stringRepresentation} <sup className="DataType">^^{dataType}</sup>
                </span>
            );
        }

        return stringRepresentation;
    };

    const rows = useMemo(() => getRows(), [bindings, vars]);
    const columns = useMemo(() => getColumns(), [vars]);

    return (
        <div className="DataTableWrapper">
            <DataGrid accessibleName="SPARQL query results" columns={columns} rows={rows} />
        </div>
    );
};

export default SPARQLDataTable;