import React, {Component} from 'react'
import './StudyListItem.scss'
import ListItem from "./index";
import {localizedText} from "../../util";

export default class DistributionListItem extends Component {
    render() {
        const { link, name, state, description, smallIcon, newWindow = false}  = this.props;

        return <ListItem
            title={name}
            description={description}
            link={newWindow ? link : {
                pathname: link,
                state: state
            }}
            newWindow={newWindow}
            smallIcon={smallIcon}
        />;
    }
}