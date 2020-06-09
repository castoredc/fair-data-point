import React, {Component} from "react";
import Input from "../Input";
import {ValidatorForm} from "react-form-validator-core";

import './Filters.scss';
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import FormItem from "../Form/FormItem";
import {MethodType, StudyType} from "../MetadataItem/EnumMappings";
import CheckboxGroup from "../Input/CheckboxGroup";
import InlineLoader from "../LoadingScreen/InlineLoader";
import {classNames} from "../../util";
import Dropdown from "../Input/Dropdown";

export default class Filters extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoadingCountries: true,
            hasLoadedCountries: false,
            data: {
                search: '',
                country: [],
                studyType: [],
                methodType: []
            },
            options: {
                country: [],
                studyType: [],
                methodType: []
            },
            countries: [],
        };

        this.timer = null;
    }

    componentDidMount() {
        const { filters } = this.props;
        const { options } = this.state;

        const studyTypes = filters.studyType.map((studyType) => {
            return {
                value: studyType,
                label: StudyType[studyType]
            }
        });

        const methodTypes = filters.methodType.map((methodType) => {
            return {
                value: methodType,
                label: MethodType[methodType]
            }
        });

        this.setState({
            options: {
                ...options,
                studyType: studyTypes,
                methodType: methodTypes
            },
        }, () => {
            this.getCountries();
        });
    }

    getCountries = () => {
        const { filters } = this.props;
        const { options } = this.state;

        axios.get('/api/countries')
            .then((response) => {
                const countries = response.data;

                const countryOptions = filters.country.map((country) => {
                    return {
                        value: country,
                        label: countries.filter(({value}) => value === country)[0].label,
                    }
                });

                this.setState({
                    options: {
                        ...options,
                        country: countryOptions
                    },
                    isLoadingCountries: false,
                    hasLoadedCountries: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingCountries: false
                });

                if(error.response && typeof error.response.data.error !== "undefined")
                {
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred" />);
                }
            });
    };

    handleChange = (event, callback = (() => {})) => {
        const { data } = this.state;
        const newData = {
            ...data,
            [event.target.name]: event.target.value,
        };

        this.setState({
            data: newData
        });

        clearTimeout(this.timer);
        this.timer = setTimeout(() => {
            this.props.onFilter(newData);
        }, 600);
    };

    handleSelectChange = (name, event) => {
        this.handleChange({
            target: {
                name: name,
                value: event.map((value) => {
                    return value.value
                })
            }
        });
    };

    render() {
        const { style, className, overlay, hidden, filters } = this.props;
        const { isLoadingCountries, data, options } = this.state;

        if(isLoadingCountries)
        {
            return <InlineLoader />;
        }

        const showStudyType = filters.studyType.length > 0;
        const showMethodType = filters.methodType.length > 0;
        const showCountry = filters.country.length > 0;

        const showFilters = (filters.studyType.length + filters.methodType.length + filters.country.length) > 0;

        if(hidden) {
            return null;
        }

        return <div className={classNames('FilterForm', overlay && 'Overlay', className)} style={style}>
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={() => {}}
            >
                <div className="FilterBlock">
                    <h2>Search studies</h2>

                    <Input
                        name="search"
                        onChange={this.handleChange}
                        value={data.search}
                        placeholder="Search ..."
                    />
                </div>
                {showFilters > 0 && <div className="FilterBlock">
                    <h2>Filter</h2>

                    {showStudyType && <FormItem label="Type">
                        <CheckboxGroup
                            options={options.studyType}
                            value={data.studyType}
                            name="studyType"
                            onChange={this.handleChange}
                        />
                    </FormItem>}

                    {showMethodType && <FormItem label="Method">
                        <CheckboxGroup
                            options={options.methodType}
                            value={data.methodType}
                            name="methodType"
                            onChange={this.handleChange}
                        />
                    </FormItem>}

                    {showCountry && <FormItem label="Country">
                        <Dropdown
                            isMulti
                            options={options.country}
                            name="country"
                            onChange={(e) => {this.handleSelectChange('country', e)}}
                            width="fullWidth"
                        />
                    </FormItem>}
                </div>}
            </ValidatorForm>
        </div>;
    }
}