import React, {Component} from "react";
import CatalogForm from "../../../components/Form/Admin/CatalogForm";

export default class CatalogDetails extends Component {
    render() {
        const { catalog } = this.props;

        return <div>
            <CatalogForm
                catalog={catalog}
            />
        </div>;
    }

}