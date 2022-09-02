import React, { Component } from 'react';
import './Tags.scss';
import { classNames } from '../../util';

class Tags extends Component {
    render() {
        const { className, tags } = this.props;

        return (
            <span className={classNames(className, 'Tags')}>
                {tags.map((tag, index) => {
                    return (
                        <span className="Tag" key={index}>
                            {tag}
                        </span>
                    );
                })}
            </span>
        );
    }
}

export default Tags;
