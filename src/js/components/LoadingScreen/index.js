import React, { Component } from 'react';
import LoadingSpinner from './LoadingSpinner';
import EventListener from '../EventListener';
import { preventDefault } from '../../util';
import './LoadingScreen.scss';

const MINIMUM_DURATION = 2500;
const LENGTHY_OPERATION = 6000;

const INITIAL_STATE = {
  canClose: false,
  lengthyOperation: false,
  open: false,
};

class LoadingScreen extends Component {
  state = {
    ...INITIAL_STATE,
    open: this.props.showLoading,
  };

  componentDidMount() {
    if (this.state.open) {
      this.showLoadingScreen();
    }
  }

  showLoadingScreen = () => {
    this.startTimers();
    this.setState({ open: true });
  };

  closeLoadingScreenIfPossible = () => {
    if (this.state.canClose) {
      this.setState(INITIAL_STATE);
      this.clearTimers();
    }
  };

  startTimers = () => {
    this.minimumDurationTimeout = setTimeout(() => {
      this.setState({ canClose: true });
      if (!this.props.showLoading) {
        this.closeLoadingScreenIfPossible();
      }
    }, MINIMUM_DURATION);

    this.lengthyOperationTimeout = setTimeout(() => {
      this.setState({ lengthyOperation: true });
    }, LENGTHY_OPERATION);
  };

  componentDidUpdate(prevProps) {
    const { showLoading } = this.props;
    const { open } = this.state;

    if (prevProps.showLoading === showLoading) {
      return;
    }

    if (!open && showLoading) {
      this.showLoadingScreen();
      return;
    }

    if (open && !showLoading) {
      this.closeLoadingScreenIfPossible();
      return;
    }
  }

  clearTimers = () => {
    clearTimeout(this.minimumDurationTimeout);
    clearTimeout(this.lengthyOperationTimeout);
  };

  componentWillUnmount() {
    this.clearTimers();
  }

  render() {
    const { open, lengthyOperation } = this.state;
    const { message } = this.props;

    if (!open) {
      return null;
    }

    return (
      <section className="LoadingScreen" aria-live="assertive" role="alert">
        <EventListener
          target={document}
          type="keydown"
          listener={preventDefault}
        />
        <LoadingSpinner />
        <h1>
          {lengthyOperation
            ? 'Oops, this is slower than expected. Please wait a bit longer.'
            : message}
        </h1>
      </section>
    );
  }
}

export default LoadingScreen;
