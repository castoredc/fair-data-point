import { PureComponent } from 'react';

class EventListener extends PureComponent {
  componentDidMount() {
    const { target, type, listener } = this.props;
    target.addEventListener(type, listener);
  }

  componentWillUnmount() {
    const { target, type, listener } = this.props;
    target.removeEventListener(type, listener);
  }

  render() {
    return null;
  }
}

export default EventListener;
