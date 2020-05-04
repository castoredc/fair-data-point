import React, {Component} from 'react';
import {classNames} from "../../util";
import DocumentTitle from "../DocumentTitle";
import './AdminPage.scss';

export default class AdminPage extends Component {
    render() {
        const {className, title, children} = this.props;
        return <div className={classNames(className, 'AdminPageContainer')}>
            <DocumentTitle title={'Admin | ' + title}/>
            <div className="PageHeader">
                <h1 className="Title">
                    {title}
                </h1>
            </div>
            <div className="PageContent">
                {children}
            </div>
        </div>;
    }
}