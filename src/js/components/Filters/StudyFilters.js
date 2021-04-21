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
import {Heading} from "@castoredc/matter";

export default class StudyFilters extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoadingCountries: true,
            isLoadingFilters: true,
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
        this.getCountries();
    };

    getCountries = () => {
        axios.get('/api/countries')
            .then((response) => {
                this.setState({
                    countries: response.data,
                    isLoadingCountries: false,
                }, () => {
                    this.getFilters();
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

    getFilters = () => {
        const {catalog, agent} = this.props;
        const {countries} = this.state;

        let url = '/api/study/filters';

        if(catalog) {
            url = '/api/catalog/' + catalog.slug + '/study/filters';
        } else if(agent) {
            url = '/api/agent/details/' + agent.slug + '/study/filters'
        }

        axios.get(url)
            .then((response) => {
                const filters = response.data;

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

                const countryOptions = filters.country.map((country) => {
                    return {
                        value: country,
                        label: countries.filter(({value}) => value === country)[0].label,
                    }
                });

                this.setState({
                    options: {
                        studyType: studyTypes,
                        methodType: methodTypes,
                        country: countryOptions
                    },
                    isLoadingFilters: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingFilters: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the filters';
                toast.error(<ToastContent type="error" message={message}/>);
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
        const { style, className, overlay, sticky, hidden, isLoading } = this.props;
        const { isLoadingCountries, data, options } = this.state;

        if(isLoadingCountries || isLoading)
        {
            return <InlineLoader />;
        }

        const showStudyType = options.studyType.length > 0;
        const showMethodType = options.methodType.length > 0;
        const showCountry = options.country.length > 0;

        const showFilters = (options.studyType.length + options.methodType.length + options.country.length) > 0;

        if(hidden) {
            return null;
        }

        return <div className={classNames('FilterForm', overlay && 'Overlay', sticky && 'Sticky', className)} style={style}>
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={() => {}}
            >
                <div className="FilterBlock">
                    <Input
                        name="search"
                        onChange={this.handleChange}
                        value={data.search}
                        placeholder="Search ..."
                    />
                </div>
                {showFilters > 0 && <div className="FilterBlock">
                    <Heading type="Subsection">Filter</Heading>

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
                        />
                    </FormItem>}
                </div>}
            </ValidatorForm>
        </div>;
    }
}