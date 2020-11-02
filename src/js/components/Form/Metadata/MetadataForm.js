import React, {Component} from 'react';
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import axios from "axios";
import FormItem from "../FormItem";
import Dropdown from "../../Input/Dropdown";
import LocalizedTextInput from "../../Input/LocalizedTextInput";
import {ValidatorForm} from "react-form-validator-core";
import MetadataVersionModal from "../../../modals/MetadataVersionModal";
import {Button, DataTable, Stack, Tabs} from "@castoredc/matter";
import {mergeData, replaceAt, ucfirst} from "../../../util";
import PublisherModal from "../../../modals/PublisherModal";

export default class MetadataForm extends Component {
    constructor(props) {
        super(props);

        const extendedDefaultData = props.defaultData ? props.defaultData : {};

        const mergedDefaultMetadata = {
            ...defaultData,
            ...extendedDefaultData,
        };

        this.state = {
            data:             props.object.hasMetadata ? mergeData(mergedDefaultMetadata, props.object.metadata) : mergedDefaultMetadata,
            currentVersion:   props.object.hasMetadata ? props.object.metadata.version.metadata : null,
            validation:       {},
            isSaved:          false,
            submitDisabled:   false,
            isLoading:        false,
            showModal:        {
                save:      false,
                publisher: false,
            },
            languages:        [],
            licenses:         [],
            countries:        [],
            currentPublisher: null,
        };
    }

    componentDidMount() {
        this.getLanguages();
        this.getLicenses();
        this.getCountries();
    }

    getLanguages = () => {
        axios.get('/api/languages')
            .then((response) => {
                this.setState({
                    languages: response.data,
                });
            })
            .catch((error) => {
                toast.error(<ToastContent type="error" message="An error occurred"/>);
            });
    };

    getLicenses = () => {
        axios.get('/api/licenses')
            .then((response) => {
                this.setState({
                    licenses: response.data,
                });
            })
            .catch(() => {
                toast.error(<ToastContent type="error" message="An error occurred"/>);
            });
    };

    getCountries = () => {
        axios.get('/api/countries')
            .then((response) => {
                this.setState({
                    countries: response.data,
                });
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }
            });
    };

    openModal = (type, id) => {
        const {showModal} = this.state;

        this.setState({
            showModal:        {
                ...showModal,
                [type]: true,
            },
            currentPublisher: id,
        });
    };

    closeModal = (type) => {
        const {showModal} = this.state;

        this.setState({
            showModal:        {
                ...showModal,
                [type]: false,
            },
            currentPublisher: null,
        });
    };

    handleChange = (event) => {
        const {data} = this.state;

        this.setState({
            data:       {
                ...data,
                [event.target.name]: event.target.value,
            },
            validation: {
                [event.target.name]: false,
            },
        });
    };

    handlePublisherUpdate = (publisher) => {
        const {data, currentPublisher} = this.state;

        let publishers = data.publishers;

        if (currentPublisher !== null) {
            publishers = replaceAt(publishers, currentPublisher, publisher);
        } else {
            publishers.push(publisher);
        }

        this.setState({
            data: {
                ...data,
                publishers: publishers,
            },
        });

        this.closeModal('publisher');
    };

    handlePublisherDelete = () => {
        const {data, currentPublisher} = this.state;

        let publishers = data.publishers;
        publishers.splice(currentPublisher, 1);

        this.setState({
            data: {
                ...data,
                publishers: publishers,
            },
        });

        this.closeModal('publisher');
    };

    handleVersionUpdate = (versionType) => {
        this.closeModal('save');

        const {data} = this.state;

        this.setState({
            data: {
                ...data,
                versionUpdate: versionType,
            },
        }, () => {
            this.submitMetadata();
        });
    };

    handleClick = (event, rowID, index) => {
        const {data} = this.state;
        const publishers = data.publishers;

        if (typeof index !== "undefined" && publishers.length > 0) {
            this.openModal('publisher', index);
        }
    };

    handleSubmit = (event) => {
        event.preventDefault();

        if (this.form.isFormValid()) {
            const {currentVersion} = this.state;

            if (currentVersion === null) {
                this.handleVersionUpdate('major');
            } else {
                this.openModal('save');
            }
        }
    };

    submitMetadata = () => {
        const {object, type, onSave} = this.props;
        const {data} = this.state;

        if (this.form.isFormValid()) {
            this.setState({
                submitDisabled: true,
                isLoading:      true,
            });

            axios.post('/api/metadata/' + type + (type === 'fdp' ? '' : '/' + object.id), data)
                .then((response) => {
                    this.setState({
                        isSaved:        true,
                        isLoading:      false,
                        submitDisabled: false,
                    });

                    toast.success(<ToastContent type="success" message="The metadata are saved successfully"/>, {
                        position: "top-right",
                    });

                    onSave();
                })
                .catch((error) => {
                    if (error.response && error.response.status === 400) {
                        this.setState({
                            validation: error.response.data.fields,
                        });
                    } else {
                        toast.error(<ToastContent type="error" message="An error occurred"/>, {
                            position: "top-center",
                        });
                    }
                    this.setState({
                        submitDisabled: false,
                        isLoading:      false,
                    });
                });
        }

        return false;
    };

    render() {
        const {data, validation, languages, licenses, countries, submitDisabled, currentVersion, showModal, currentPublisher} = this.state;
        const {children} = this.props;

        const required = "This field is required";

        const publisherRows = new Map(data.publishers.map((publisher, index) => {
            let name = '';
            let additionalInfo = '';

            if (publisher.type === 'organization') {
                name = publisher.organization.name;
                additionalInfo = typeof publisher.department !== 'undefined' ? publisher.department.name : '';
            } else if (publisher.type === 'person') {
                name = [publisher.person.firstName, publisher.person.middleName, publisher.person.lastName].filter(Boolean).join(' ');
                additionalInfo = publisher.person.orcid;
            }

            return [
                index,
                {
                    cells: [
                        name,
                        ucfirst(publisher.type),
                        additionalInfo
                    ],
                },
            ];
        }));

        return (
            <ValidatorForm
                className="FullHeightForm"
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                <MetadataVersionModal
                    show={showModal.save}
                    currentVersion={currentVersion}
                    handleClose={() => {
                        this.closeModal('save')
                    }}
                    onSave={this.handleVersionUpdate}
                />

                <PublisherModal
                    show={showModal.publisher}
                    handleClose={() => {
                        this.closeModal('publisher')
                    }}
                    save={this.handlePublisherUpdate}
                    deletePublisher={this.handlePublisherDelete}
                    countries={countries}
                    edit={(currentPublisher !== null && typeof data.publishers[currentPublisher] !== 'undefined')}
                    type={(currentPublisher !== null && typeof data.publishers[currentPublisher] !== 'undefined') ? data.publishers[currentPublisher].type : null}
                    data={(currentPublisher !== null && typeof data.publishers[currentPublisher] !== 'undefined') ? data.publishers[currentPublisher] : null}
                />

                <div className="PageTabs">
                    <Tabs
                        tabs={{
                            metadata:   {
                                title:   'Metadata',
                                content: <div>
                                             <FormItem label="Title">
                                                 <LocalizedTextInput
                                                     validators={['required']}
                                                     errorMessages={[required]}
                                                     name="title"
                                                     onChange={this.handleChange}
                                                     value={data.title}
                                                     serverError={validation.title}
                                                     languages={languages}
                                                 />
                                             </FormItem>
                                             <FormItem label="Description">
                                                 <LocalizedTextInput
                                                     validators={['required']}
                                                     errorMessages={[required]}
                                                     name="description"
                                                     onChange={this.handleChange}
                                                     value={data.description}
                                                     serverError={validation.description}
                                                     languages={languages}
                                                     as="textarea"
                                                     rows="8"
                                                 />
                                             </FormItem>

                                             <FormItem label="Language">
                                                 <Dropdown
                                                     validators={['required']}
                                                     errorMessages={[required]}
                                                     options={languages}
                                                     name="language"
                                                     onChange={(e) => {
                                                         this.handleChange({target: {name: 'language', value: e.value}})
                                                     }}
                                                     value={languages.filter(({value}) => value === data.language)}
                                                     serverError={validation.language}
                                                 />
                                             </FormItem>

                                             <FormItem label="License">
                                                 <Dropdown
                                                     options={licenses}
                                                     name="license"
                                                     onChange={(e) => {
                                                         this.handleChange({target: {name: 'license', value: e.value}})
                                                     }}
                                                     value={licenses.filter(({value}) => value === data.license)}
                                                     serverError={validation.license}
                                                 />
                                             </FormItem>

                                             {children && children(this.handleChange, data, validation)}
                                         </div>,
                            },
                            publishers: {
                                title:   'Publishers',
                                content: <div>
                                             <Stack distribution="trailing">
                                                 <Button icon="add" onClick={() => {
                                                     this.openModal('publisher', null)
                                                 }}>
                                                     Add publisher
                                                 </Button>
                                             </Stack>

                                             <div className="SelectableDataTable DataTableWrapper">
                                                 <DataTable
                                                     emptyTableMessage="No publishers found"
                                                     highlightRowOnHover
                                                     cellSpacing="default"
                                                     onClick={this.handleClick}
                                                     rows={publisherRows}
                                                     structure={{
                                                         title: {
                                                             header:    'Name',
                                                             resizable: true,
                                                             template:  'text',
                                                         },
                                                         type:  {
                                                             header:    'Type',
                                                             resizable: true,
                                                             template:  'text',
                                                         },
                                                         info:  {
                                                             header:    'Additional Information',
                                                             resizable: true,
                                                             template:  'text',
                                                         },
                                                     }}
                                                 />
                                             </div>
                                         </div>,
                            },
                        }}
                    />
                </div>

                <div className="FormButtons">
                    <Stack distribution="trailing">
                        <Button type="submit" disabled={submitDisabled}>
                            Save
                        </Button>
                    </Stack>
                </div>
            </ValidatorForm>
        );
    }
}

const defaultData = {
    'title':         null,
    'description':   null,
    'language':      null,
    'license':       null,
    'versionUpdate': '',
    'publishers':    [],
};