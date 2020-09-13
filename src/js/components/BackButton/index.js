import React, {Component} from 'react';
import './BackButton.scss';
import {Icon} from "@castoredc/matter";
import {LinkContainer} from "react-router-bootstrap";

export default class BackButton extends Component {
    render() {
        const { to, children } = this.props;
        return <div className="BackButton">
            <LinkContainer to={to}>
                <button>
                    <span className="circle">
                        <Icon type="arrowLeft" height="10px" width="10px" />
                    </span>

                    {children}
                </button>
            </LinkContainer>
        </div>;
    }
}