import React, {Component} from "react";
import {AttributionControl, Map, Marker, Popup, TileLayer} from "react-leaflet";
import MarkerClusterGroup from 'react-leaflet-markercluster'
import {getCenterFromDegrees} from "../../util";

import './Map.scss';
import {Link} from "react-router-dom";

export default class StudiesMap extends Component {
    render() {
        const { studies } = this.props;

        const coordinates = studies.map((study) => {
            return [study.coordinates.lat, study.coordinates.long];
        });

        if(studies.length === 0) {
            return <div className="NoResults">There is no map data available.</div>;
        }

        return <div className="DatasetMap">
            <Map
                center={getCenterFromDegrees(coordinates)}
                zoom={3}
                attributionControl={false}
            >
                <TileLayer
                    url="https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png"
                    attribution='&copy; <a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap contributors</a>, &copy; <a href="https://carto.com/attributions" target="_blank">CARTO</a>'
                />

                <MarkerClusterGroup>
                    {studies.map((study, index) => {
                        return <Marker key={index} position={[study.coordinates.lat, study.coordinates.long]}>
                            <Popup>
                                <Link to={study.relativeUrl} className="PopupDatasetTitle" target="_blank">
                                    <h3>{study.title}</h3>
                                </Link>
                                <div className="PopupOrganization">
                                    <strong>{study.organization}</strong><br />
                                    {study.city}, {study.country}
                                </div>
                            </Popup>
                        </Marker>
                    })}
                </MarkerClusterGroup>

                <AttributionControl position="bottomright" prefix={false} />
            </Map>
        </div>
    }
}