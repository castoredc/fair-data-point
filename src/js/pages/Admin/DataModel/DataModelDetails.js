import React, {Component} from "react";

export default class DataModelDetails extends Component {
    render() {
        const { dataModel } = this.props;

        return <div>
            {dataModel.description && <div>{dataModel.description}</div>}
        </div>;
    }

}