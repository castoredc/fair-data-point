import React from 'react';
import { Box, Chip, Stack } from '@mui/material';

type TagsProps = {
    className?: string;
    tags: string[];
};

const Tags: React.FC<TagsProps> = ({ tags }) => {
    return (
        <Stack 
            direction="row" 
            spacing={1.5} 
            flexWrap="wrap"
            sx={{ 
                '& > *': {
                    mb: 0.75
                }
            }}
        >
            {tags.map((tag, index) => (
                <Chip
                    key={index}
                    label={tag}
                    variant="outlined"
                    size="small"
                    sx={{
                        fontWeight: 600,
                        color: 'text.primary',
                        borderColor: 'divider',
                        '&:hover': {
                            bgcolor: 'action.hover'
                        }
                    }}
                />
            ))}
        </Stack>
    );
};

export default Tags;
