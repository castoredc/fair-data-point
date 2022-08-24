import React from 'react';
import SplitWrapper from 'react-split';
import './Split.scss';

const Split = ({ sizes, children }) => {
    return (
        <SplitWrapper
            className="Split"
            sizes={sizes}
            cursor="col-resize"
            gutterSize={40}
            gutter={(index, direction) => {
                const gutter = document.createElement('div');
                const chip = document.createElement('div');

                gutter.className = `gutter gutter-${direction}`;
                chip.className = `chip`;

                gutter.appendChild(chip);
                return gutter;
            }}
        >
            {children}
        </SplitWrapper>
    );
};

export default Split;
