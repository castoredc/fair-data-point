import React, {Component} from "react";
import CatalogSteppedForm from "../../../components/Form/CatalogSteppedForm";
import {localizedText} from "../../../util";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import LoadingScreen from "../../../components/LoadingScreen";
import {Button} from "@castoredc/matter";

export default class Finished extends Component {
    constructor(props) {
        super(props);

        this.state = {
            consent: {
                publish: false,
                socialMedia: false
            },
            isLoading: false
        };
    }

    componentDidMount() {
        this.getConsent();
    }

    getConsent = () => {
        this.setState({
            isLoading: true,
        });

        axios.get('/api/study/' + this.props.match.params.studyId + '/consent')
            .then((response) => {
                this.setState({
                    consent: response.data,
                    isLoading: false
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                if(error.response && typeof error.response.data.error !== "undefined")
                {
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred" />);
                }
            });
    };

    render() {
        if(this.state.isLoading)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        const description = 'Your study has been successfully submitted' + (this.state.consent.publish ? ' and is now available in our public database' : '') + '.';

        return <CatalogSteppedForm
            catalog={this.props.catalog}
            heading="Thanks, all done!"
            description={description}
        >
            <Button variant="primary" href={'/fdp/' + this.props.catalog.slug}>Visit the {localizedText(this.props.catalog.metadata.title, 'en')}</Button>
        </CatalogSteppedForm>
    }
}
