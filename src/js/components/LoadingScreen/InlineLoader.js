import React from 'react';
import './InlineLoader.scss';
import LoadingSpinner from "./LoadingSpinner";
import {classNames} from "../../util";
import {Spinner} from "@castoredc/matter";

export default ({overlay = false}) => {
    return (
        <div className={classNames('Loader', overlay && 'Overlay')}>
            <Spinner
                accessibleLabel="Action in progress…"
                ariaValueMax={100}
                ariaValueMin={0}
            />
        </div>
    );
};
