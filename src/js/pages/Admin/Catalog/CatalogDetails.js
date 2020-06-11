import React, {Component} from "react";

export default class CatalogDetails extends Component {
    render() {
        const { catalog } = this.props;

        return <div>
            {catalog.slug}
        </div>;
    }

}