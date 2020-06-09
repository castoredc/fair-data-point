import React, {Component} from "react";
import {localizedText} from "../../../util";

export default class CatalogDetails extends Component {
    render() {
        const { catalog } = this.props;

        return <div>
            {catalog.description && <div> {localizedText(catalog.description, 'en', true)}</div>}
        </div>;
    }

}