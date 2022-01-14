import React, {cloneElement} from "react";

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
    if (typeof texts === 'string') {
        return texts;
    }

    if (texts === null || typeof texts === 'undefined') {
        return '';
    }

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
    if (typeof str !== 'string') {
        return false;
    }

    var pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
        '(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
    return !!pattern.test(str);
};

export const getCenterFromDegrees = (data) => {
    if (!(data.length > 0)) {
        return false;
    }

    let num_coords = data.length;

    let X = 0.0;
    let Y = 0.0;
    let Z = 0.0;

    for (let i = 0; i < data.length; i++) {
        let lat = data[i][0] * Math.PI / 180;
        let lon = data[i][1] * Math.PI / 180;

        let a = Math.cos(lat) * Math.cos(lon);
        let b = Math.cos(lat) * Math.sin(lon);
        let c = Math.sin(lat);

        X += a;
        Y += b;
        Z += c;
    }

    X /= num_coords;
    Y /= num_coords;
    Z /= num_coords;

    let centerLon = Math.atan2(Y, X);
    let centerHyp = Math.sqrt(X * X + Y * Y);
    let centerLat = Math.atan2(Z, centerHyp);

    let newX = (centerLat * 180 / Math.PI);
    let newY = (centerLon * 180 / Math.PI);

    return [newX, newY];
};

export const replaceAt = (array, index, value) => {
    const ret = array.slice(0);
    ret[index] = value;
    return ret;
};

export const mergeData = (defaultData, newData) => {
    return Object.keys(defaultData).reduce((a, key) => (
        {...a, [key]: (key in newData ? newData[key] : defaultData[key])}
    ), defaultData);
};

export const ucfirst = (text) => {
    return text.charAt(0).toUpperCase() + text.slice(1);
};

export const downloadFile = (contents, filename) => {
    if (!window.navigator.msSaveOrOpenBlob) {
        const url = window.URL.createObjectURL(new Blob([contents]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
    } else {
        // IE
        const url = window.navigator.msSaveOrOpenBlob(new Blob([contents]), filename);
    }
};

export const cloneIfComposite = (children, props) => {
    return children && children.type && typeof children.type !== 'string'
        ? cloneElement(children, props)
        : children;
}