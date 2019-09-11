import React from 'react';
import { classNames } from '../../../util';
import './StatusBadge.scss';

const StatusBadge = props => (
  <div
    {...props}
    style={{ height: props.height, width: props.width }}
    className={classNames(props.className, 'status-badge')}
  />
);

export const StatusBadgeComplete = props => <StatusBadge {...props} />;
export const StatusBadgeNotStarted = props => (
  <StatusBadge {...props} height={props.height - 1} width={props.width - 1} />
);
