import React, {Component} from 'react'

import './MetadataItem.scss'
import MetadataItem from "./index";
import {AttributionControl, Map, Marker, TileLayer} from "react-leaflet";
import {getCenterFromDegrees} from "../../util";
import Organization from "./Organization";

class Organizations extends Component {
    render() {
        const { organizations } = this.props;

        const label = 'Organization' + (organizations.length > 1 ? 's' : '');

        const coordinates = organizations.filter((organization) => {
            return ! (organization.coordinates === null);
        }).map((organization)  => {
            return [organization.coordinates.lat, organization.coordinates.long];
        });

        return <MetadataItem label={label} className="Organizations">
            {organizations.map((organization, index) => {
                return <Organization key={index} {...organization} />
            })}

            {coordinates.length > 0 && <div className="Map">
                <Map
                    center={getCenterFromDegrees(coordinates)}
                    zoom={7}
                    dragging={false}
                    doubleClickZoom={false}
                    keyboard={false}
                    scrollWheelZoom={false}
                    zoomControl={false}
                    attributionControl={false}
                >
                    <TileLayer
                        url="https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png"
                        attribution='&copy; <a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap contributors</a>, &copy; <a href="https://carto.com/attributions" target="_blank">CARTO</a>'
                    />
                    {coordinates.map((coordinate, index) => {
                        return <Marker key={index} position={coordinate} />
                    })}

                    <AttributionControl position="bottomright" prefix={false} />
                </Map>
            </div>}
        </MetadataItem>
    }
}

export default Organizations