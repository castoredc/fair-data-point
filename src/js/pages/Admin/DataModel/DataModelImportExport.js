import React, {Component} from "react";
import {Button, FileSelector, Heading} from "@castoredc/matter";
import axios from "axios";
import {downloadFile, isNumeric} from "../../../util";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import {ValidatorForm} from "react-form-validator-core";
import FormItem from "../../../components/Form/FormItem";
import Input from "../../../components/Input";
import {Redirect} from "react-router-dom";

export default class DataModelImportExport extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isExporting: false,
            isImporting: false,
            file:        null,
            model:       null,
            data:        {
                version: '',
            },
            newVersion:  null,
        };
    }

    componentDidMount() {
        ValidatorForm.addValidationRule('isValidVersion', (value) => {
            const parsedVersion = value.split('.');

            if (parsedVersion.length !== 3) {
                return false;
            }

            return (isNumeric(parsedVersion[0]) && isNumeric(parsedVersion[1]) && isNumeric(parsedVersion[2]));
        });
        ValidatorForm.addValidationRule('isNonExistentVersion', (value) => {
            const {dataModel} = this.props;
            return dataModel.versions.find(({version}) => version === value) === undefined
        });
    }

    export = () => {
        const {dataModel, version} = this.props;

        this.setState({isExporting: true});

        axios({
            url:          '/api/model/' + dataModel.id + '/v/' + version + '/export',
            method:       'GET',
            responseType: 'blob',
        }).then((response) => {
            const contentDisposition = response.headers["content-disposition"];
            const match = contentDisposition.match(/filename\s*=\s*"(.+)"/i);
            const filename = match[1];

            this.setState({isExporting: false});

            downloadFile(response.data, filename);
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

    onFileChange = (e) => {
        if (e.target.files.length > 0) {
            const file = e.target.files[0];

            if (file.type === 'application/json') {

                let reader = new FileReader();
                reader.readAsText(file);

                reader.onload = () => {
                    const result = reader.result;

                    const json = JSON.parse(result.toString());

                    if ('model' in json) {
                        this.setState({
                            file:  file,
                            model: json.model,
                            data:  {
                                version: json.version.version,
                            },
                        });
                    } else {
                        toast.error(<ToastContent type="error"
                                                  message="Please upload a valid model export."/>);
                    }
                };
            }
        }
    };

    import = () => {
        const {dataModel, getDataModel} = this.props;
        const {file, data} = this.state;

        if (this.form.isFormValid()) {
            this.setState({isImporting: true});

            const formData = new FormData();

            formData.append('file', file);
            formData.append('version', data.version);

            axios.post('/api/model/' + dataModel.id + '/import', formData, {
                headers: {
                    'content-type': 'multipart/form-data',
                },
            })
                .then((response) => {
                    this.setState({isImporting: false, newVersion: response.data});

                    getDataModel();

                    toast.success(<ToastContent type="success" message="The model was successfully imported."/>, {
                        position: "top-right",
                    });
                })
                .catch((error) => {
                    this.setState({isImporting: false});

                    if (error.response && typeof error.response.data.error !== "undefined") {
                        toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                    } else {
                        toast.error(<ToastContent type="error"
                                                  message="An error occurred while importing the model."/>);
                    }
                });
        }
    };

    render() {
        const {isExporting, isImporting, file, data, newVersion} = this.state;
        const required = "This field is required";
        const invalidVersion = "This version is invalid, please format the version in X.X.X.";
        const versionExists = "This version already exists, please enter a different version number.";

        if (newVersion !== null) {
            return <Redirect push to={`/admin/model/${newVersion.dataModel}/${newVersion.version}/modules`}/>;
        }

        return <div className="PageBody">
            <div>
                <Heading type="Subsection">Export model</Heading>

                <Button onClick={this.export} icon="download" disabled={isExporting}>Export model</Button>
            </div>

            <hr/>

            <div>
                <Heading type="Subsection">Import model</Heading>
                <ValidatorForm
                    ref={node => (this.form = node)}
                    onSubmit={this.import}
                    method="post"
                >
                    <FileSelector onChange={this.onFileChange} accept="application/json"/>

                    <FormItem label="New version number">
                        <Input
                            validators={['required', 'isValidVersion', 'isNonExistentVersion']}
                            errorMessages={[required, invalidVersion, versionExists]}
                            name="version"
                            onChange={this.handleChange}
                            value={data.version}
                        />
                    </FormItem>

                    <Button type="submit" icon="upload" disabled={(file === null || isImporting)}>
                        Import model
                    </Button>
                </ValidatorForm>
            </div>


        </div>;
    }

}