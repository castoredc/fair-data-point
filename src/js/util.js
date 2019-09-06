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

export const isURL = (str) => {
  var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
      '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
      '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
      '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
      '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
      '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
  return !!pattern.test(str);
};
