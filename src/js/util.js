import React from "react";

export const by = key => (object, item) => {
    object[item[key]] = item;
    return object;
};

export const classNames = (...args) => args.filter(identity).join(' ');

export const debounce = (fn, delay) => {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            fn(...args);
        }, delay);
    };
};

export const identity = x => x;

export const log = (value, prefix = '') => {
    console.log(prefix, value);
    return value;
};

export const isNumeric = number =>
    !isNaN(parseFloat(number)) && isFinite(number);

export const preventDefault = e => {
    e.preventDefault();
};

export const paragraphText = (text) => {
    return text.split('\n').map((item, i) => {
        return (item.length > 1) ? <p key={i}>{item}</p> : null;
    });
};

export const localizedText = (texts, language = 'en', paragraph = false) => {
    if (texts === null) return '';

    for (const text of texts) {
        if (text.language === language) {
            if (paragraph) {
                return paragraphText(text.text);
            }
            return text.text;
        }
    }

    if (paragraph) {
        return paragraphText(texts[0].text);
    }
    return texts[0].text;
};

export const isURL = (str) => {
    var pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
        '(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
    return !!pattern.test(str);
};
