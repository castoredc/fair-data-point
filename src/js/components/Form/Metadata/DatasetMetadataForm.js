import React, {Component} from "react";
import MetadataForm from "./MetadataForm";
import OntologyConceptFormBlock from "../OntologyConceptFormBlock";
import FormItem from "../FormItem";
import LocalizedTextInput from "../../Input/LocalizedTextInput";

export default class DatasetMetadataForm extends Component {
    render() {
        const { dataset, onSave } = this.props;

        return <MetadataForm type="dataset" object={dataset} onSave={onSave}
                             defaultData={defaultData}
        >
            {(validation, languages) => (<div>
                {/*<FormItem label="Keywords">*/}
                {/*    <LocalizedTextInput*/}
                {/*        name="keyword"*/}
                {/*        onChange={handleChange}*/}
                {/*        value={data.keyword}*/}
                {/*        serverError={validation.keyword}*/}
                {/*        languages={languages}*/}
                {/*    />*/}
                {/*</FormItem>*/}

                {/*<OntologyConceptFormBlock*/}
                {/*    label="Themes"*/}
                {/*    value={data.theme}*/}
                {/*    name="theme"*/}
                {/*    handleChange={handleChange}*/}
                {/*/>*/}
            </div>)}
        </MetadataForm>;
    }
}

const defaultData = {
    'theme': [],
    'keyword': null,
};