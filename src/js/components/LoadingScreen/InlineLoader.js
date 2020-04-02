import React from 'react';
import './InlineLoader.scss';
import LoadingSpinner from "./LoadingSpinner";
import {classNames} from "../../util";

export default ({overlay = false}) => {
    return (
        <div className={classNames('Loader', overlay && 'Overlay')}>
            <LoadingSpinner />
        </div>
    );
};
