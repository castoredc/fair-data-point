import React, {FC} from 'react';
import {classNames} from '../../util';
import icons from './icons';
import './Icon.scss';

const SIZE = 16;


type CustomIconProps = {
  className?: string;
  type: string;
  width?: number;
  height?: number;
  onClick?: (e) => void;
  role?: string;
};

const CustomIcon: FC<CustomIconProps> = ({
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

export default CustomIcon;