import React, {Component} from "react";
import {AppProvider, DataTable, Icon} from "@castoredc/matter";

export default class SPARQLDataTable extends Component {
    constructor(props) {
        super(props);
    }

    getColumns = () => {
        const { vars } = this.props;

        return vars.reduce(function (map, variable) {
            map[variable] = {
                header:    variable,
                resizable: true,
                template:  'text',
            };
            return map;
        }, {});
    };

    getRows = () => {
        const { vars, bindings, prefixes } = this.props;

        let rows = [];

        for (let rowId = 0; rowId < bindings.length; rowId++) {
            const binding = bindings[rowId];
            let row = [];
            for (let colId = 0; colId < vars.length; colId++) {
                const sparqlVar = vars[colId];
                if (sparqlVar in binding) {
                    row.push(this.getCellContent(binding, sparqlVar, prefixes));
                } else {
                    row.push("");
                }
            }
            rows.push(row);
        }
        return rows;
    };

    getUriLinkFromBinding = (binding) => {
        const { prefixes, fullUrl } = this.props;

        const href = binding.value;
        let visibleString = href;
        let prefixed = false;

        if (prefixes) {
            for (const prefixLabel in prefixes) {
                if (visibleString.indexOf(prefixes[prefixLabel]) === 0) {
                    visibleString = prefixLabel + ":" + href.substring(prefixes[prefixLabel].length);
                    prefixed = true;
                    break;
                }
            }
        }

        if (visibleString.indexOf(fullUrl) === 0) {
            visibleString = ":" + href.substring(fullUrl.length);
            prefixed = true;
        }

        return <span>{prefixed ? "" : "<"}<a className='iri' href={href} target="_blank">{visibleString}</a>{prefixed ? "" : ">"}</span>;
    };

    getCellContent = (bindings, sparqlVar) => {
        const binding = bindings[sparqlVar];
        let content = '';
        if (binding.type === "uri") {
            content = this.getUriLinkFromBinding(binding);
        } else {
            content = <span className='nonIri'>{this.formatLiteral(binding)}</span>;
        }
        return content;
    };

    formatLiteral = (literalBinding) => {
        const { prefixes } = this.props;

        let stringRepresentation = literalBinding.value;
        if (literalBinding["xml:lang"]) {
            stringRepresentation = <span>{stringRepresentation} <sup>{literalBinding["xml:lang"]}</sup></span>;
        } else if (literalBinding.datatype) {
            const dataType = this.getUriLinkFromBinding({type: "uri", value: literalBinding.datatype}, prefixes);
            stringRepresentation = <span>{stringRepresentation} <sup>^^{dataType}</sup></span>;
        }
        return stringRepresentation;
    };

    render() {
        const rows = this.getRows();
        const columns = this.getColumns();

        return <div className="QueryResults DataTableWrapper">
                <DataTable structure={columns} rows={rows}/>
            </div>;
    }

}