import React from 'react';
import {classNames} from '../../util';
import icons from './icons';
import './Icon.scss';

const SIZE = 16;

export default ({
  className,
  type,
  width = SIZE,
  height = SIZE,
  onClick,
  role,
}) => {
  const CustomIcon = icons[type];
  return (
    <CustomIcon
      className={classNames('Icon', className, type)}
      width={width}
      height={height}
      onClick={onClick}
      role={role}
    />
  );
};
