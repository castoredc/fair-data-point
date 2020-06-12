import React, {Component} from "react";

export default class DatasetDetails extends Component {
    render() {
        const { dataset } = this.props;

        return <div>
            {dataset.slug}
        </div>;
    }

}