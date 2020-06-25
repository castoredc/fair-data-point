import React, {Component} from "react";
import {Route, Switch} from "react-router-dom";
import FAIRDataPointMain from "./FAIRDataPointMain";
import Study from "./Study";
import Catalog from "./Catalog";
import Dataset from "./Dataset";
import Distribution from "./Distribution";
import Query from "./Query";
import MetadataXmlParse from "./Tools/MetadataXmlParse";
import {PrivateRoute} from "../../components/Route";
import queryString from "query-string";

export default class Main extends Component {
    render() {
        const {user} = this.props;

        const params = queryString.parse(this.props.location.search);
        const embedded = (typeof params.embed !== 'undefined');

        return <Switch>
            <Route path="/fdp" exact render={(props) => <FAIRDataPointMain {...props} embedded={embedded} user={user} />} />

            <Route path="/study/:study" exact render={(props) => <Study {...props} embedded={embedded} user={user} />} />

            <Route path="/fdp/catalog/:catalog" render={(props) => <Catalog {...props} embedded={embedded} user={user} />} />

            <Route path="/fdp/dataset/:dataset" exact render={(props) => <Dataset {...props} embedded={embedded} user={user} />} />

            <Route path="/fdp/dataset/:dataset/distribution/:distribution" exact render={(props) => <Distribution {...props} embedded={embedded} user={user} />} />
            
            <PrivateRoute path="/fdp/dataset/:dataset/distribution/:distribution/query" exact component={Query} embedded={embedded} user={user} />

            /* Tools */
            <Route path="/tools/metadata-xml-parse" exact render={(props) => <MetadataXmlParse {...props} embedded={embedded} user={user} />} />

        </Switch>;
    }
}
