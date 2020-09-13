import React, {Component} from 'react';
import '../../pages/Main/Main.scss';
import InlineLoader from "../LoadingScreen/InlineLoader";
import {classNames} from "../../util";

export default class MainBody extends Component {
    render() {
        const {children, isLoading, className} = this.props;

        if(isLoading) {
            return <InlineLoader />;
        }

        return <main className={classNames('container', className)}>
            {children}
        </main>;
    }
}