import React from 'react';
import './Tags.scss';
import { classNames } from '../../util';

type TagsProps = {
    className?: string;
    tags: string[];
};

const Tags: React.FC<TagsProps> = ({ className, tags }) => {
    return (
        <span className={classNames(className, 'Tags')}>
            {tags.map((tag, index) => (
                <span className="Tag" key={index}>
                    {tag}
                </span>
            ))}
        </span>
    );
};

export default Tags;
