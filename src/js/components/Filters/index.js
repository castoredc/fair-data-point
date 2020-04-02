import React, {Component} from "react";
import Input from "../Input";
import {ValidatorForm} from "react-form-validator-core";

import './Filters.scss';
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import FormItem from "../Form/FormItem";
import {MethodType, StudyType} from "../MetadataItem/EnumMappings";
import {CheckboxGroup} from "../Input/Checkbox";
import InlineLoader from "../LoadingScreen/InlineLoader";
import {classNames} from "../../util";

export default class Filters extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoadingFilters: true,
            isLoadingCountries: true,
            hasLoadedFilters: false,
            hasLoadedCountries: false,
            data: {
                search: '',
                country: [],
                studyType: [],
                methodType: []
            },
            countries: [],
            filters: {}
        };

        this.timer = null;
    }

    componentDidMount() {
        this.getFilters();
        this.getCountries();
    }

    getFilters = () => {
        axios.get('/api/catalog/' + this.props.catalog + '/filters')
            .then((response) => {
                this.setState({
                    filters: response.data,
                    isLoadingFilters: false,
                    hasLoadedFilters: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingFilters: false
                });

                if(error.response && typeof error.response.data.error !== "undefined")
                {
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred" />);
                }
            });
    };

    getCountries = () => {
        axios.get('/api/countries')
            .then((response) => {
                this.setState({
                    countries: response.data,
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

    handleCheckboxChange = (item, event) => {
        const { data } = this.state;
        let selection  = data[item];
        const index    = selection.indexOf(event.target.name);

        if (index > -1 && ! event.target.value) {
            selection.splice(index, 1);
        }
        else if (event.target.value) {
            selection.push(event.target.name);
        }

        const newData = {
            ...data,
            [item]: selection
        };

        this.setState({
            data: newData
        });

        this.props.onFilter(newData);
    };

    render() {
        const { style, className } = this.props;

        if(this.state.isLoadingFilters || this.state.isLoadingCountries)
        {
            return <InlineLoader />;
        }

        const studyTypes = this.state.filters.studyType.map((studyType) => {
            return {
                name: studyType,
                label: StudyType[studyType],
                value: this.state.data.studyType.includes(studyType),
                onChange: (e) => {this.handleCheckboxChange('studyType', e)}
            }
        });

        const methodTypes = this.state.filters.methodType.map((methodType) => {
            return {
                name: methodType,
                label: MethodType[methodType],
                value: this.state.data.methodType.includes(methodType),
                onChange: (e) => {this.handleCheckboxChange('methodType', e)}
            }
        });

        const countries = this.state.filters.country.map((country) => {
            return {
                name: country,
                label: this.state.countries.filter(({value}) => value === country)[0].label,
                value: this.state.data.country.includes(country),
                onChange: (e) => {this.handleCheckboxChange('country', e)}
            }
        });

        return <div className={classNames('Filters' && className)} style={style}>
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={() => {}}
            >
                <div className="FilterBlock">
                    <h2>Search studies</h2>

                    <Input
                        name="search"
                        onChange={this.handleChange}
                        value={this.state.data.search}
                        placeholder="Search ..."
                    />
                </div>
                {(this.state.filters.studyType.length + this.state.filters.methodType.length + this.state.filters.country.length) > 0 && <div className="FilterBlock">
                    <h2>Filter</h2>

                    {this.state.filters.studyType.length > 0 && <FormItem label="Type">
                        <CheckboxGroup checkboxes={studyTypes} />
                    </FormItem>}

                    {this.state.filters.methodType.length > 0 && <FormItem label="Method">
                        <CheckboxGroup checkboxes={methodTypes} />
                    </FormItem>}

                    {this.state.filters.country.length > 0 && <FormItem label="Country">
                        <CheckboxGroup checkboxes={countries} />
                    </FormItem>}
                </div>}
            </ValidatorForm>
        </div>;
    }
}