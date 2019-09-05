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

export const localizedText = (texts, language = 'en') => {
  for (const text of texts) {
    if(text.language === language) return text.text;
  }
  return texts[0].text;
};
