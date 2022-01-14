import React, {Component} from 'react'
import './StudyListItem.scss'
import {RecruitmentStatus} from "../MetadataItem/EnumMappings";
import ListItem from "./index";

export default class StudyListItem extends Component {
    render() {
        const {
            link,
            name,
            state,
            recruitmentStatus,
            badge,
            description,
            condition,
            intervention,
            centers,
            newWindow = false
        } = this.props;

        let badgeText = badge;

        if (recruitmentStatus) {
            badgeText = RecruitmentStatus[recruitmentStatus];
        }

        let tags = [];

        if (condition !== null && condition !== '') {
            tags.push(condition);
        }
        if (intervention !== null && intervention !== '') {
            tags.push(intervention);
        }

        return <ListItem
            title={name}
            description={description}
            badge={badgeText}
            tags={tags}
            link={link}
            state={state}
            newWindow={newWindow}
        />;
    }
}