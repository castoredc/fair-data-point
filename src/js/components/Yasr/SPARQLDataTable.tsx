import React, { useMemo } from 'react';
import DataGrid from 'components/DataTable/DataGrid';
import { GridColDef } from '@mui/x-data-grid';

type SPARQLDataTableProps = {
    vars: string[];
    bindings: any[];
    prefixes?: { [key: string]: string };
    fullUrl?: string;
};

const SPARQLDataTable: React.FC<SPARQLDataTableProps> = ({ vars, bindings, prefixes, fullUrl }) => {
    const getColumns = (): GridColDef[] => {
        return vars.map(variable => ({
            headerName: variable,
            field: variable,
        }));
    };

    const getRows = () => {
        return bindings.map((binding, rowId) => {
            const row = {};

            vars.forEach(sparqlVar => {
                row[sparqlVar] = sparqlVar in binding ? getCellContent(binding, sparqlVar) : '';
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

        if (fullUrl && visibleString.indexOf(fullUrl) === 0) {
            visibleString = `:${href.substring(fullUrl.length)}`;
            prefixed = true;
        }

        return (
            <span>
                {prefixed ? '' : '<'}
                <a className="iri" href={href} target="_blank">
                    {visibleString}
                </a>
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
            content = formatLiteral(currentBinding);
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
            <DataGrid disableRowSelectionOnClick accessibleName="SPARQL query results" columns={columns} rows={rows} />
        </div>
    );
};

export default SPARQLDataTable;
