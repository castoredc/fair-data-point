import React from 'react';
import {shallow} from 'enzyme'; //todo is shallow enough?
import DocumentTitle from './';

describe('DocumentTitle', () => {
  const originalTitle = window.document.title;

  afterEach(() => {
    window.document.title = originalTitle;
  });

  it('renders nothing', () => {
    const documentTitle = shallow(<DocumentTitle title="Title" />);
    expect(documentTitle.html()).toBe(null);
  });

  it('updates document.title on mount', () => {
    const TITLE = 'Castor EDC - testing title';

    shallow(<DocumentTitle title={TITLE} />);
    expect(window.document.title).toBe(TITLE);
  });

  it('updates document.title on update', () => {
    const FIRST_TITLE = 'Castor EDC - first title';
    const SECOND_TITLE = 'Castor EDC - second title';

    const documentTitle = shallow(<DocumentTitle title={FIRST_TITLE} />);
    expect(window.document.title).toBe(FIRST_TITLE);

    documentTitle.setProps({ title: SECOND_TITLE });
    expect(window.document.title).toBe(SECOND_TITLE);
  });
});
