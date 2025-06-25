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
    let onClickFunction: () => any;

    if (returnButton) {
        let history = useHistory();
        onClickFunction = () => history.go(-1);
    } else if (typeof to === 'string') {
        onClickFunction = () => {
            history.push(to as string);
        }
    } else if(to !== undefined) {
        onClickFunction = () => to();
    } else {
        return null;
    }

    return (
        <div className={classNames('BackButton', sidebar && 'Sidebar')}>
            <button onClick={onClickFunction}>
                <span className="circle">
                    <ArrowLeftIcon height="10px" width="10px" />
                </span>
                {children}
            </button>
        </div>
    );
};

export default BackButton;
