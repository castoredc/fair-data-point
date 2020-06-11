import React, {Component} from 'react';
import {Button} from "@castoredc/matter/lib/matter.esm";
import Row from 'react-bootstrap/Row'
import Col from 'react-bootstrap/Col'
import '../Form.scss'
import MetadataForm from "./MetadataForm";
import FormItem from "../FormItem";
import Input from "../../Input";

export default class CatalogMetadataForm extends Component {
    render() {
        const { catalog, onSave } = this.props;

        return (
            <MetadataForm
                type="catalog"
                object={catalog}
                onSave={onSave}
                defaultData={defaultData}
            >{(handleChange, data, validation) => (<div>
                <FormItem label="Homepage">
                    <Input
                        name="homepage"
                        onChange={handleChange}
                        value={data.homepage}
                        serverError={validation.homepage}
                    />
                </FormItem>

                <FormItem label="Logo">
                    <Input
                        name="logo"
                        onChange={handleChange}
                        value={data.logo}
                        serverError={validation.logo}
                    />
                </FormItem>
            </div>)}
            </MetadataForm>
        );
    }
}

const defaultData = {
    'homepage': '',
    'logo' : '',
};