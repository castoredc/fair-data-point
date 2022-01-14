import React, {Component} from 'react';
import '../../pages/Main/Main.scss';
import {classNames} from "../../util";
import {LoadingOverlay} from "@castoredc/matter";

export default class MainBody extends Component {
    render() {
        const {children, isLoading, className} = this.props;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading" content=""/>;
        }

        return <main className={classNames('container', className)}>
            {children}
        </main>;
    }
}