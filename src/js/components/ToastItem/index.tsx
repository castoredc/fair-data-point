import React, { FC, useState } from 'react';
import { ToastMessage } from '@castoredc/matter';
import { ToastMessageProps } from '@castoredc/matter/lib/types/src/ToastMessage/ToastMessage';
import './ToastItem.scss';

const ToastItem: FC<ToastMessageProps> = ({ ...props }) => {
    const [hide, setHide] = useState(false);

    if(hide) {
        return null;
    }

    return <ToastMessage
        {...props}
        onClose={() => setHide(true)}
    />;
};

export default ToastItem;
