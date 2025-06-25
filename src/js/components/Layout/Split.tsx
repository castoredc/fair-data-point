import React from 'react';
import { default as SplitWrapper } from 'react-split';
import { Box } from '@mui/material';
import { alpha } from '@mui/material/styles';

interface SplitProps {
    sizes: number[];
    children: React.ReactNode;
}

const Split: React.FC<SplitProps> = ({ sizes, children }) => {
    return (
        <Box
            sx={{
                '& > .Split': {
                    width: '100%',
                    height: '100%',
                    display: 'flex',
                    flexDirection: 'row',
                },
                '& .gutter': {
                    '& .chip': {
                        bgcolor: theme => alpha(theme.palette.text.primary, 0.2),
                        borderRadius: 0.5,
                        opacity: 0.2,
                        transition: theme => theme.transitions.create('opacity'),
                    },
                    '&:hover .chip': {
                        opacity: 1,
                    },
                },
                '& .gutter.gutter-horizontal': {
                    cursor: 'col-resize',
                    display: 'flex',
                    alignItems: 'center',
                    '& .chip': {
                        width: '3px',
                        height: '80%',
                        margin: 'auto',
                    },
                },
            }}
        >
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
        </Box>
    );
};

export default Split;
