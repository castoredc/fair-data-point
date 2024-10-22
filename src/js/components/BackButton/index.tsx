import React, { FC } from 'react';
import './BackButton.scss';
import { ArrowLeftIcon } from '@castoredc/matter-icons';
import { classNames } from '../../util';
import { useHistory } from 'react-router-dom';
import * as H from 'history';

export type BackButtonProps =
    | {
          to?: string | (() => void);
          returnButton: true; // If returnButton is true, history can be optional
          children?: React.ReactNode;
          sidebar?: boolean;
          history?: undefined;
      }
    | {
          to?: string | (() => void);
          returnButton?: undefined;
          children?: React.ReactNode;
          sidebar?: boolean;
          history: H.History;
      };

const BackButton: FC<BackButtonProps> = ({ to, returnButton, children, sidebar, history }) => {
    if (returnButton) {
        let history = useHistory();
        to = () => history.go(-1);
    } else if (typeof to === 'string') {
        to = () => history.push(to as string);
    }

    return (
        <div className={classNames('BackButton', sidebar && 'Sidebar')}>
            <button onClick={typeof to === 'function' ? to : undefined}>
                <span className="circle">
                    <ArrowLeftIcon height="10px" width="10px" />
                </span>
                {children}
            </button>
        </div>
    );
};

export default BackButton;
