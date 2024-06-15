import { paragraphText } from '../util';
import { useMemo } from 'react';

export const localizedText = (texts, language = 'en', paragraph = false) => {
    if (texts === null || texts === undefined || Object.keys(texts).length === 0) {
        return '';
    }

    if(Array.isArray(texts)) {
        for (const text of texts) {
            if (text['@language'] === language) {
                return paragraph ? paragraphText(text['@value']) : text['@value'];
            }
        }

        return paragraph ? paragraphText(texts[0]['@value']) : texts[0]['@value'];
    }

    return paragraph ? paragraphText(texts['@value']) : texts['@value'];
};

export const titleAndDescriptionContext = {
    title: 'http://purl.org/dc/terms/title',
    description: 'http://purl.org/dc/terms/description',
};