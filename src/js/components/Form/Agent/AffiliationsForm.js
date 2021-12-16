import React, {Component} from 'react';

import '../Form.scss'
import {replaceAt} from "../../../util";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import {ValidatorForm} from "react-form-validator-core";
import {Button, LoadingOverlay, Stack} from "@castoredc/matter";
import AffiliationForm from "./AffiliationForm";
import InlineLoader from "../../LoadingScreen/InlineLoader";
import Toggle from "../../Toggle";
import AffiliationOrganizationModal from "../../../modals/AffiliationOrganizationModal";
import {Formik} from "formik";

export default class AffiliationsForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            // affiliations: [],
            countries: null,
            // metadataSource: null,
            // visitedFields: {},
            validation: [
                defaultValidation,
            ],
            isSaved: false,
            isLoading: true,
            openModal: false,
        };
    }

    openModal = () => {
        this.setState({
            openModal: true
        })
    };

    closeModal = () => {
        this.setState({
            openModal: false
        })
    };

    // handleNewAffiliation = (e) => {
    //     e.preventDefault();
    //
    //     this.form.isFormValid(false).then(valid => {
    //         if (valid) {
    //             this.setState({
    //                 affiliations: [
    //                     ...this.state.affiliations,
    //                     defaultData,
    //                 ],
    //                 validation: [
    //                     ...this.state.validation,
    //                     defaultValidation,
    //                 ],
    //             });
    //         }
    //     });
    // };

    // removeAffiliation = (e, index) => {
    //     const {affiliations, validation} = this.state;
    //     e.preventDefault();
    //
    //     affiliations.splice(index, 1);
    //     validation.splice(index, 1);
    //
    //     this.setState({
    //         affiliations: affiliations,
    //         validation: validation,
    //     });
    // };

    // handleChange = (index, event, group, callback = () => {
    // }) => {
    //     const {affiliations, validation} = this.state;
    //
    //     let newAffiliation = {
    //         ...affiliations[index],
    //         [group]: {
    //             ...affiliations[index][group],
    //             [event.target.name]: event.target.value,
    //         }
    //     };
    //
    //     const newAffiliations = replaceAt(affiliations, index, newAffiliation);
    //
    //     const newValidation = replaceAt(validation, index, {
    //         ...validation[index],
    //         [event.target.name]: false,
    //     });
    //
    //     this.setState({
    //         affiliations: newAffiliations,
    //         validation: newValidation,
    //     }, callback);
    // };

    // handleDataChange = (index, group, newData) => {
    //     const {affiliations} = this.state;
    //
    //     let newAffiliation = {
    //         ...affiliations[index],
    //         [group]: {
    //             ...affiliations[index][group],
    //             ...newData
    //         }
    //     };
    //
    //     if (group === 'organization') {
    //         newAffiliation.department = {
    //             ...defaultData.department,
    //             source: newAffiliation.organization.id === null ? 'manual' : 'database'
    //         };
    //     }
    //
    //     const newAffiliations = replaceAt(affiliations, index, newAffiliation);
    //
    //     this.setState({
    //         affiliations: newAffiliations,
    //     });
    // };

    getCountries = () => {
        axios.get('/api/countries')
            .then((response) => {
                this.setState({
                    countries: response.data,
                    isLoading: false
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

    componentDidMount() {
        this.getCountries();
    }

    handleSubmit = (event) => {
        // const {affiliations} = this.state;
        // const {onSaved} = this.props;
        //
        // event.preventDefault();
        //
        // if (this.form.isFormValid()) {
        //     this.setState({
        //         isLoading: true,
        //     });
        //
        //     axios.post('/api/user/affiliations', affiliations)
        //         .then(() => {
        //             this.setState({
        //                 isLoading: false,
        //             }, () => {
        //                 onSaved();
        //             });
        //         })
        //         .catch((error) => {
        //             if (error.response && error.response.status === 400) {
        //                 this.setState({
        //                     validation: error.response.data.fields,
        //                 });
        //             } else {
        //                 toast.error(<ToastContent type="error"
        //                                           message="An error occurred while updating your affiliations"/>, {
        //                     position: "top-center",
        //                 });
        //             }
        //             this.setState({
        //                 isLoading: false,
        //             });
        //         });
        // }
        //
        // return false;
    };

    render() {
        const {affiliations, validation, countries, isLoading, openModal} = this.state;

        if (isLoading && countries == null) {
            return <LoadingOverlay accessibleLabel="Loading"/>;
        }

        const initialValues = {
            affiliations: [defaultData]
        };

        return <>
            <AffiliationOrganizationModal open={openModal} onClose={this.closeModal} countries={countries} handleSubmit={() => {}}/>

            <Formik
                initialValues={initialValues}
                onSubmit={this.handleSubmit}
            >
                {({
                      values,
                      errors,
                      touched,
                      handleChange,
                      handleBlur,
                      handleSubmit,
                      isSubmitting,
                      setValues,
                      setFieldValue
                  }) => {
                    return <>
                        <div className="FormContent">
                            <div className="Organizations">
                                {values.affiliations.map((affiliation, index) => {
                                    const title = 'Affiliation ' + (index + 1) + (affiliation.organization.name.length > 0 ? ': ' + affiliation.organization.name : '');

                                    return <div>{title}</div>

                                    // return <>
                                    //     <AffiliationForm
                                    //         data={affiliation}
                                    //         validation={validation[index]}
                                    //         countries={countries}
                                    //         handleChange={(group, event, callback) => this.handleChange(index, event, group, callback)}
                                    //         handleDataChange={(group, newData) => this.handleDataChange(index, group, newData)}
                                    //     />
                                    //
                                    //     {index > 0 && <Stack alignment="end" distribution="trailing">
                                    //         <Button buttonType="danger" className="RemoveButton" icon="cross"
                                    //                 onClick={(e) => {
                                    //                     this.removeAffiliation(e, index)
                                    //                 }}>Delete affiliation</Button>
                                    //     </Stack>}
                                    // </>;
                                })}
                            </div>
                            <Stack distribution="trailing" alignment="end">
                                <Button buttonType="secondary" icon="add" onClick={this.openModal}>
                                    Add another affiliation
                                </Button>
                            </Stack>
                        </div>

                        <div className="FormButtons">
                            <Stack distribution="trailing">
                                <Button type="submit" disabled={isLoading}>
                                    Save details
                                </Button>
                            </Stack>
                        </div>
                    </>;
                }}
            </Formik>
        </>;
    }
}

const defaultData = {
    organization: {
        id: null,
        name: '',
        source: null,
        country: null,
        city: '',
    },
    department: {
        id: null,
        name: '',
        source: null,
    },
    position: {
        position: ''
    },
};

const defaultValidation = {
    name: null,
    country: null,
    city: null,
    department: null,
    additionalInformation: null,
};