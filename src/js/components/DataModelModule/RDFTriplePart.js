// import React, {Component} from 'react'
// import Icon from "../Icon";
// import {FieldIcons} from "../Icon/FieldIcons";
//
// export default class RDFTriplePart extends Component {
//     render() {
//         const { type, value, handleEdit } = this.props;
//
//         let part = null;
//         let icon = '';
//         let label = '';
//         let description = '';
//
//         if(type === 'record') {
//             icon = 'closed';
//             label = 'Record';
//         }
//
//         if(type === 'uri') {
//             icon = 'globe';
//             label = value.uri;
//         }
//
//         if(type === 'entity') {
//             if(value.type.value === 'field') {
//                 icon = FieldIcons[value.entity.type];
//                 label = value.entity.label;
//                 description = 'Field';
//             } else {
//                 if(value.type.parent === 'study') {
//                     icon = 'form';
//                     description = value.type.type === 'form' ? 'Phase' : 'Study step';
//                 } else if(value.type.parent === 'report') {
//                     icon = 'study';
//                     description = value.type.type === 'form' ? 'Report' : 'Report step';
//                 } else if(value.type.parent === 'survey') {
//                     icon = 'surveys';
//                     description = value.type.type === 'form' ? 'Survey' : 'Survey step';
//                 }
//
//                 label = value.entity.label;
//             }
//         }
//
//         if(type === 'value') {
//             icon = FieldIcons[value.entity.type];
//             label = value.entity.label;
//             description = value.type.label;
//         }
//
//         return <div className="TriplePart" onClick={handleEdit} tabIndex="0" role="button">
//             <div className="TriplePartIcon">
//                 <Icon type={icon} />
//             </div>
//             <div className="TriplePartInfo">
//                 {label}
//                 {description && <div className="EntityDescription">{description}</div>}
//             </div>
//             <div className="EditIcon">
//                 <Icon type="edit" />
//             </div>
//         </div>;
//     }
// }