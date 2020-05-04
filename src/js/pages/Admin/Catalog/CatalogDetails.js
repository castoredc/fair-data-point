import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {classNames, localizedText} from "../../../util";
import {LinkContainer} from "react-router-bootstrap";
import Button from "react-bootstrap/Button";
import AdminPage from "../../../components/AdminPage";
import AdminStudyListItem from "../../../components/ListItem/AdminStudyListItem";
import Container from "react-bootstrap/Container";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import Pagination from "react-bootstrap/Pagination";
import Filters from "../../../components/Filters";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import ButtonGroup from "react-bootstrap/ButtonGroup";
import Icon from "../../../components/Icon";
import arrowLeft from "../../../components/Icon/icons/arrow-left.svg";
import FAIRDataInformation from "../../../components/FAIRDataInformation";

export default class CatalogDetails extends Component {
    render() {
        const { catalog } = this.props;

        return <div>
            {catalog.description && <div> {localizedText(catalog.description, 'en', true)}</div>}
        </div>;
    }

}