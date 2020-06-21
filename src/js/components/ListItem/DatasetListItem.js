import React, {Component} from 'react'
import './StudyListItem.scss'
import ListItem from "./index";

export default class DatasetListItem extends Component {
    render() {
        const { link, name, fdp, catalog, study, description, newWindow = false}  = this.props;

        return <ListItem
            title={name}
            description={description}
            link={newWindow ? link : {
                pathname: link,
                state: {
                    fdp: fdp,
                    catalog: catalog,
                    study: study
                }
            }}
            newWindow={newWindow}
        />;
    }
}