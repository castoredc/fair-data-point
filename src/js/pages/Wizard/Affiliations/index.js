import React, {Component} from "react";
import './Affiliations.scss';
import Emoji from "../../../components/Emoji";
import {CastorLogo} from "@castoredc/matter";
import queryString from "query-string";
import AffiliationsForm from "../../../components/Form/Agent/AffiliationsForm";

export default class Affiliations extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isSaved: false,
        };
    }

    handleSave = () => {
        this.setState({
            isSaved: true
        });
    };

    render() {
        const {location, user} = this.props;
        const {isSaved} = this.state;

        if (isSaved) {
            const params = queryString.parse(location.search);
            window.location.href = (typeof params.origin !== 'undefined') ? params.origin : '/';
        }

        return <>
            <div className="WizardBrand">
                <div className="WizardBrandLogo">
                    <CastorLogo className="Logo"/>
                </div>
                <div className="WizardBrandText">
                    FAIR Data Point
                </div>
            </div>

            <header>
                <h1>
                    <Emoji symbol="🏥"/>&nbsp;
                    Where do you work, {user.details.firstName}?
                </h1>
                <div className="Description">
                    Please add your affiliation(s) below.
                </div>
            </header>

            <AffiliationsForm onSaved={this.handleSave}/>
        </>;
    }
}
