import React, {Component} from 'react'
import './StudyStructure.scss';
import {ValidatorForm} from "react-form-validator-core";
import RadioGroup from "../Input/RadioGroup";
import FormItem from "../Form/FormItem";
import Dropdown from "../Input/Dropdown";
import {Button} from "@castoredc/matter";

export default class StructureTypes extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: {
                type:    (props.mapping && props.mapping.element) ? props.mapping.element.structureType : null,
                element: (props.mapping && props.mapping.element) ? props.mapping.element.id : null,
            },
        };
    }

    componentDidUpdate(prevProps) {
        const {mapping} = this.props;

        if (mapping !== prevProps.mapping) {
            const newData = mapping ? {
                type:    mapping.element.structureType,
                element: mapping.element.id,
            } : null;

            this.state = {
                data: newData,
            };
        }
    }

    handleTypeChange = (event) => {
        this.setState({
            data: {
                type:    event.target.value,
                element: '',
            },
        });
    };

    handleChange = (event) => {
        const {data} = this.state;

        this.setState({
            data: {
                ...data,
                [event.target.name]: event.target.value,
            },
        });
    };

    handleSubmit = () => {
        const {mapping, onSelect} = this.props;
        const {data} = this.state;

        if (this.form.isFormValid()) {
            onSelect({
                module:        mapping.module.id,
                structureType: data.type,
                element:       data.element,
            });
        }
    };

    render() {
        const {structure, mapping} = this.props;
        const {data} = this.state;

        const structureItems = (data.type !== '' && data.type !== null) ? structure[data.type] : [];

        const options = structureItems.map((item) => {
            return {label: item.name, value: item.id};
        });

        const required = "This field is required";

        return <ValidatorForm
            ref={node => (this.form = node)}
            onSubmit={this.handleSubmit}
            method="post"
        >
            <FormItem label="Type">
                <RadioGroup
                    validators={['required']}
                    errorMessages={[required]}
                    options={[
                        {value: 'report', label: 'Report'},
                        {value: 'survey', label: 'Survey'},
                    ]}
                    onChange={this.handleTypeChange}
                    value={data.type}
                    name="type"
                    variant="horizontal"
                />
            </FormItem>

            <FormItem label="Element">
                <Dropdown
                    validators={['required']}
                    errorMessages={[required]}
                    options={options}
                    name="element"
                    onChange={(e) => {
                        this.handleChange({target: {name: 'element', value: e.value}})
                    }}
                    value={options.filter(({value}) => value === data.element)}
                    menuPosition="fixed"
                />
            </FormItem>


            <Button type="submit">
                {(mapping && mapping.element) ? 'Edit mapping' : 'Add mapping'}
            </Button>
        </ValidatorForm>
    }

}