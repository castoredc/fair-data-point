import React, {Component} from 'react';
import './BackButton.scss';
import {LinkContainer} from "react-router-bootstrap";
import {ArrowLeftIcon} from "@castoredc/matter-icons";
import {classNames} from "../../util";

export default class BackButton extends Component {
    render() {
        const { to, children, sidebar } = this.props;
        return <div className={classNames('BackButton', sidebar && 'Sidebar')}>
            <LinkContainer to={to}>
                <button>
                    <span className="circle">
                        <ArrowLeftIcon height="10px" width="10px" />
                    </span>

                    {children}
                </button>
            </LinkContainer>
        </div>;
    }
}