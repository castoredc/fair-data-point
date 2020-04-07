import React, {Component} from "react";
import {AttributionControl, Map, Marker, Popup, TileLayer} from "react-leaflet";
import MarkerClusterGroup from 'react-leaflet-markercluster'
import {getCenterFromDegrees, localizedText} from "../../util";

import './DatasetMap.scss';
import {Link} from "react-router-dom";

export default class DatasetMap extends Component {
    render() {
        const { datasets } = this.props;

        const coordinates = datasets.map((dataset) => {
            return [dataset.coordinates.lat, dataset.coordinates.long];
        });

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
                    {datasets.map((dataset, index) => {
                        return <Marker key={index} position={[dataset.coordinates.lat, dataset.coordinates.long]}>
                            <Popup>
                                <Link to={dataset.relative_url} className="PopupDatasetTitle" target="_blank">
                                    <h3>{localizedText(dataset.title, 'en')}</h3>
                                </Link>
                                <div className="PopupOrganization">
                                    <strong>{dataset.organization}</strong><br />
                                    {dataset.city}, {dataset.country}
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