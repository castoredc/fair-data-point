import React from 'react';
import { shallow, mount } from 'enzyme';
import LoadingScreen from '../LoadingScreen';
import renderer from 'react-test-renderer';
import { expectSnapshotMatch } from '../../../test/util';

const INITIAL_MESSAGE = 'Your action is pending...';

beforeEach(() => {
  jest.useFakeTimers();
});

afterEach(() => {
  jest.clearAllTimers();
});

describe('<LoadingScreen />', () => {
  it('Renders null when initially passing showLoading prop', () => {
    const loadingScreen = <LoadingScreen showLoading={false} />;
    expectSnapshotMatch(loadingScreen);
  });

  it('Renders loading screen when passing showLoading prop', () => {
    const loadingScreen = (
      <LoadingScreen showLoading={true} message={INITIAL_MESSAGE} />
    );
    expectSnapshotMatch(loadingScreen);
  });

  it('Is displayed for a minimum 2.5 seconds initially open', () => {
    const loadingScreen = shallow(<LoadingScreen showLoading={true} />);
    loadingScreen.setProps({ showLoading: false });

    // Loading screen is still shown after showLoading prop set to false
    expect(loadingScreen.getElement()).not.toBeNull();

    // 2 seconds not enough to close the screen
    jest.advanceTimersByTime(2000);
    expect(loadingScreen.getElement()).not.toBeNull();

    jest.advanceTimersByTime(501);

    // Loading screen is now closed
    expect(loadingScreen.getElement()).toBeNull();
  });

  it('Is displayed for a minimum 2.5 seconds initially closed', () => {
    const loadingScreen = shallow(<LoadingScreen />);
    loadingScreen.setProps({ showLoading: true });
    loadingScreen.setProps({ showLoading: false });

    // Loading screen is still shown after showLoading prop set to false
    expect(loadingScreen.getElement()).not.toBeNull();

    // 2 seconds not enough to close the screen
    jest.advanceTimersByTime(2000);
    expect(loadingScreen.getElement()).not.toBeNull();

    jest.advanceTimersByTime(501);

    // Loading screen is now closed
    expect(loadingScreen.getElement()).toBeNull();
  });

  it('Is closeable instantly at any time after the initial 2.5 seconds initially open', () => {
    const loadingScreen = shallow(<LoadingScreen showLoading={true} />);

    jest.advanceTimersByTime(3000);
    loadingScreen.setProps({ showLoading: false });

    expect(loadingScreen.getElement()).toBeNull();
  });

  it('Is closeable instantly at any time after the initial 2.5 seconds initially closed', () => {
    const loadingScreen = shallow(<LoadingScreen showLoading={false} />);
    loadingScreen.setProps({ showLoading: true });
    loadingScreen.setProps({ showLoading: false });

    jest.advanceTimersByTime(3000);
    loadingScreen.setProps({ showLoading: false });

    expect(loadingScreen.getElement()).toBeNull();
  });

  it('Can be closed and and re-opened, initially open', () => {
    const loadingScreen = shallow(<LoadingScreen showLoading={true} />);
    expect(loadingScreen.getElement()).not.toBeNull();

    jest.advanceTimersByTime(3000);
    loadingScreen.setProps({ showLoading: false });

    // Closed
    expect(loadingScreen.getElement()).toBeNull();

    loadingScreen.setProps({ showLoading: true });

    // Re-opened
    expect(loadingScreen.getElement()).not.toBeNull();
  });

  it('Can be opened, closed and and re-opened, initially closed', () => {
    const loadingScreen = shallow(<LoadingScreen showLoading={false} />);
    expect(loadingScreen.getElement()).toBeNull();

    loadingScreen.setProps({ showLoading: true });

    //Opened
    expect(loadingScreen.getElement()).not.toBeNull();

    jest.advanceTimersByTime(3000);
    loadingScreen.setProps({ showLoading: false });

    // Closed
    expect(loadingScreen.getElement()).toBeNull();

    loadingScreen.setProps({ showLoading: true });

    // Re-opened
    expect(loadingScreen.getElement()).not.toBeNull();
  });

  it('Displays originally passed message for first 6 seconds', () => {
    const loadingScreen = renderer.create(
      <LoadingScreen showLoading={true} message={INITIAL_MESSAGE} />
    );

    expect(loadingScreen).toMatchSnapshot();
  });

  it('Displays lengthy operation message after the loading screen has been active for 6 seconds', () => {
    const loadingScreen = renderer.create(
      <LoadingScreen showLoading={true} message={INITIAL_MESSAGE} />
    );

    jest.advanceTimersByTime(6001);
    expect(loadingScreen).toMatchSnapshot();
  });
});
