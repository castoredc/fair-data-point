import React, {Component} from "react";

export default class DataDictionaryDetails extends Component {
    render() {
        const { dataDictionary } = this.props;

        return <div className="PageBody">
            {dataDictionary.description && <div>{dataDictionary.description}</div>}
        </div>;
    }

}