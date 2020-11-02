import React, {Component} from "react";
import MetadataForm from "./MetadataForm";
import OntologyConceptFormBlock from "../OntologyConceptFormBlock";
import Input from "../../Input";

export default class DatasetMetadataForm extends Component {
    render() {
        const { dataset, onSave } = this.props;

        return <MetadataForm type="dataset" object={dataset} onSave={onSave}
                             defaultData={defaultData}
        >
            {(handleChange, data, validation) => (<div>
                <OntologyConceptFormBlock
                    label="Themes"
                    value={data.theme}
                    name="theme"
                    handleChange={handleChange}
                />
            </div>)}
        </MetadataForm>;
    }
}

const defaultData = {
    'theme': [],
};