import React, {Component} from 'react'
import {Link} from "react-router-dom";
import './StudyListItem.scss'
import {RecruitmentStatus} from "../MetadataItem/EnumMappings";
import Tags from "../Tags";

class StudyListItem extends Component {
    render() {
        const { link, name, logo, recruitmentStatus, badge, description, condition, intervention, centers, newWindow = false}  = this.props;

        let badgeText = badge;

        if(recruitmentStatus)
        {
            badgeText = RecruitmentStatus[recruitmentStatus];
        }

        let tags = [];

        if(condition !== null)
        {
            tags.push(condition.text);
        }
        if(intervention !== null)
        {
            tags.push(intervention.text);
        }

        return <Link to={link} className="StudyListItem" target={newWindow ? '_blank' : null}>
            <span className="StudyListItemHeader">
                <span className="StudyListItemName">{name}</span>
                {badgeText && <span className="StudyListItemBadge">{badgeText}</span>}
            </span>
            <span className="StudyListItemDescription">{description}</span>
            {tags.length > 0 && <Tags tags={tags} className="StudyListItemTags" />}
        </Link>;
    }
}

export default StudyListItem