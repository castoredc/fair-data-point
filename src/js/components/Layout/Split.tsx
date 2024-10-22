import React from 'react';
import { default as SplitWrapper } from 'react-split';
import './Split.scss';

interface SplitProps {
    sizes: number[];
    children: React.ReactNode;
}

const Split: React.FC<SplitProps> = ({ sizes, children }) => {
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