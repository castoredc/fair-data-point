import React, {Component} from 'react'
import './FullScreenSteppedForm.scss'
import {classNames} from "../../util";
import DocumentTitle from "../DocumentTitle";
import {CastorLogo, Stack} from "@castoredc/matter";
import MainBody from "../Layout/MainBody";

export default class FullScreenSteppedForm extends Component {
    getBreadCrumbs = () => {
        let elements = [];
        for (let step = 0; step < this.props.numberOfSteps; step++) {
            let stepNumber = step + 1;
            elements.push(<div key={'step-' + step}
                               className={classNames('FullScreenSteppedFormBreadcrumb', stepNumber === this.props.currentStep && 'Active')}>
                {stepNumber}
            </div>);

            if (stepNumber !== this.props.numberOfSteps) {
                elements.push(<div key={'sep-' + step} className="FullScreenSteppedFormBreadcrumbSeparator"/>);
            }
        }
        return elements;
    };

    render() {
        const {brandText, currentStep, smallHeading, heading, description, children} = this.props;

        return <MainBody className="FullScreenSteppedFormContainer">
            <DocumentTitle title={brandText + ' | ' + heading}/>
            <div className="FullScreenSteppedFormTop">
                <Stack distribution="equalSpacing">
                    <div className="FullScreenSteppedFormBrand">
                        <div className="FullScreenSteppedFormBrandLogo">
                            <CastorLogo className="Logo" />
                        </div>
                        <div className="FullScreenSteppedFormBrandText">
                            {brandText}
                        </div>
                    </div>

                    {currentStep && <div className="FullScreenSteppedFormBreadcrumbs">
                        {this.getBreadCrumbs()}
                    </div>}
                </Stack>
            </div>
            <div className="FullScreenSteppedForm">
                <div className="FullScreenSteppedFormHeader">
                    {smallHeading && <div className="FullScreenSteppedFormStepNumber">{smallHeading}</div>}
                    <h1 className="FullScreenSteppedFormStepName">{heading}</h1>
                    <div className="FullScreenSteppedFormStepDescription">{description}</div>
                </div>

                <div className="FullScreenSteppedFormWrapper">
                    {children}
                </div>
            </div>
        </MainBody>;
    }
}